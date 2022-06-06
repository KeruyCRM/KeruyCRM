<?php

class xml_import
{
    public $filename, $template_info, $entities_id, $is_preview, $count_new_items, $count_updated_items;

    function __construct($filename, $template_info)
    {
        $this->filename = $filename;
        $this->template_info = $template_info;
        $this->is_preview = false;
        $this->count_new_items = 0;
        $this->count_updated_items = 0;
    }

    function set_preview_mode()
    {
        $this->is_preview = true;
    }

    function get_file_by_path()
    {
        $this->filename = 'xml_imort_' . time() . '.xml';

        if (strstr($this->template_info['filepath'], 'http://') or strstr(
                $this->template_info['filepath'],
                'https://'
            )) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $this->template_info['filepath']);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            $data = curl_exec($curl);
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($code != '200') {
                die('Error ' . $code . ': can\'t open file ' . $this->template_info['filepath']);
            }


            file_put_contents(DIR_FS_TMP . $this->filename, $data);
        } else {
            file_put_contents(DIR_FS_TMP . $this->filename, file_get_contents($this->template_info['filepath']));
        }
    }

    function has_xml_errors()
    {
        //check file
        if (!is_file(DIR_FS_TMP . $this->filename)) {
            return alert_error(TEXT_FILE_NOT_FOUND);
        }

        //validate xml
        libxml_use_internal_errors(true);

        $doc = new DOMDocument();
        $doc->loadXML(file_get_contents(DIR_FS_TMP . $this->filename));

        $errors = libxml_get_errors();
        if (count($errors)) {
            $html = '<ul>';
            foreach ($errors as $error) {
                $html .= '<li>' . sprintf(
                        TEXT_EXT_ERROR_ON_LINE_COLUMN,
                        $error->line,
                        $error->column
                    ) . ': <b>' . $error->message . '</b></li>';
            }

            $html .= '</ul>';

            libxml_clear_errors();

            return '<p><b>' . TEXT_EXT_FILE_CONTAINS_ERRORS . ':</b></p>' . alert_error($html);
        }

        return '';
    }

    function unlink_import_file()
    {
        if (is_file(DIR_FS_TMP . $this->filename)) {
            unlink(DIR_FS_TMP . $this->filename);
        }
    }

    function import_data()
    {
        $doc = new DOMDocument;
        $doc->preserveWhiteSpace = false;
        $doc->load(DIR_FS_TMP . $this->filename);
        $xpath = new DOMXPath($doc);

        $xml_data_path = $this->template_info['data_path'];

        if ($xml_data_path[strlen($xml_data_path) - 1] == '/') {
            $xml_data_path = substr($xml_data_path, 0, -1);
        }

        //get items
        $items = $xpath->query($xml_data_path);

        $import_fields = (strlen($this->template_info['import_fields']) ? json_decode(
            $this->template_info['import_fields'],
            true
        ) : []);
        $import_fields_path = (strlen($this->template_info['import_fields_path']) ? json_decode(
            $this->template_info['import_fields_path'],
            true
        ) : []);

        $import_fields_queries = [];

        //update by field path
        if ($this->template_info['update_by_field'] > 0 and strlen($this->template_info['update_by_field_path'])) {
            $xml_field_path = $this->template_info['update_by_field_path'];

            if (substr($xml_field_path, 0, 1) == '/') {
                $xml_field_path = substr($xml_field_path, 1);
            }

            if (strstr($xml_field_path, '[')) {
                $import_fields_queries[$this->template_info['update_by_field']] = str_replace(['[', ']'],
                    '',
                    $xml_field_path);
            } else {
                $import_fields_queries[$this->template_info['update_by_field']] = $xpath->query(
                    $xml_data_path . '/' . $xml_field_path
                );
            }
        }

        //prepare itmes fields
        foreach ($import_fields as $k => $field_id) {
            $xml_field_path = $import_fields_path[$k];

            if (substr($xml_field_path, 0, 1) == '/') {
                $xml_field_path = substr($xml_field_path, 1);
            }

            if (strstr($xml_field_path, '[')) {
                $import_fields_queries[$field_id] = str_replace(['[', ']'], '', $xml_field_path);
            } else {
                $import_fields_queries[$field_id] = $xpath->query($xml_data_path . '/' . $xml_field_path);
            }
        }

        //print_rr($import_fields_queries);

        $html = '';

        foreach ($items as $item_key => $item) {
            if ($this->is_preview) {
                $html .= $this->prepare_preview($item_key, $import_fields_queries, $item);
            } else {
                $this->prepare_sql_data($item_key, $import_fields_queries, $item);
            }
        }

        if (!$this->is_preview) {
            $html = TEXT_EXT_IMPORT_COMPLETED . '.';

            if ($this->count_new_items > 0) {
                $html .= ' ' . TEXT_EXT_NEW_RECORDS_ADDED . ': ' . $this->count_new_items;
            }

            if ($this->count_updated_items > 0) {
                $html .= ' ' . TEXT_EXT_UPDATED_RECORDS . ': ' . $this->count_updated_items;
            }
        }

        //exit();

        if (strlen($html)) {
            return $html;
        }
    }

    function prepare_sql_data($item_key, $import_fields_queries, $item_node)
    {
        global $app_fields_cache, $app_entities_cache, $app_logged_users_id, $choices_names_to_id, $parent_entity_item_id;

        $sql_data = [];
        $choices_values = [];
        $is_unique_item = true;

        $entities_id = $this->template_info['entities_id'];

        $unique_fields = fields::get_unique_fields_list($entities_id);

        foreach ($import_fields_queries as $field_id => $queries) {
            if (is_object($queries)) {
                $xml_field_value = $this->get_field_value_by_key($field_id, $item_key, $import_fields_queries);
            } else {
                $xml_field_value = $item_node->getAttribute($queries);
            }

            //skip field import if field ID not the uses Entity
            if (!isset($app_fields_cache[$entities_id][$field_id])) {
                continue;
            }

            //skip update field by ID
            if ($this->template_info['update_by_field'] == $field_id and $app_fields_cache[$entities_id][$field_id]['type'] == 'fieldtype_id') {
                continue;
            }

            $filed_info_query = db_query("select * from app_fields where id='" . db_input($field_id) . "'");
            if ($filed_info = db_fetch_array($filed_info_query)) {
                $cfg = new fields_types_cfg($filed_info['configuration']);

                switch ($filed_info['type']) {
                    case 'fieldtype_entity':
                    case 'fieldtype_entity_ajax':
                    case 'fieldtype_entity_multilevel':
                        $values_list = [];

                        if ($heading_id = fields::get_heading_id($cfg->get('entity_id'))) {
                            $heading_field_info = db_find('app_fields', $heading_id);
                            if (in_array(
                                $heading_field_info['type'],
                                [
                                    'fieldtype_input',
                                    'fieldtype_input_masked',
                                    'fieldtype_text_pattern_static',
                                    'fieldtype_input_url'
                                ]
                            )) {
                                $value_array = (is_array($xml_field_value) ? $xml_field_value : [$xml_field_value]);

                                foreach ($value_array as $value_name) {
                                    $item_query = db_query(
                                        "select id from app_entity_" . $cfg->get(
                                            'entity_id'
                                        ) . " where field_" . $heading_id . "='" . db_input($value_name) . "'"
                                    );
                                    if ($item = db_fetch_array($item_query)) {
                                        $values_list[] = $item['id'];
                                    } else {
                                        if (($parent_entities_id = $app_entities_cache[$cfg->get(
                                                'entity_id'
                                            )]['parent_id']) > 0) {
                                            $check_query = db_query(
                                                "select id from app_entity_" . $cfg->get('entity_id')
                                            );
                                            if ($check = db_fetch_array($check_query)) {
                                                $parent_entities_item_id = $check['id'];
                                            }
                                        } else {
                                            $parent_entities_item_id = 0;
                                        }

                                        $item_sql_data = [];
                                        $item_sql_data['field_' . $heading_id] = trim($value_name);
                                        $item_sql_data['date_added'] = time();
                                        $item_sql_data['created_by'] = $app_logged_users_id;
                                        $item_sql_data['parent_item_id'] = $parent_entities_item_id;

                                        db_perform('app_entity_' . $cfg->get('entity_id'), $item_sql_data);

                                        $item_id = db_insert_id();

                                        $values_list[] = $item_id;
                                    }
                                }

                                //prepare choices values
                                $choices_values[$field_id] = $values_list;

                                $sql_data['field_' . $field_id] = implode(',', $values_list);
                            }
                        }
                        break;
                    case 'fieldtype_dropdown':
                    case 'fieldtype_radioboxes':
                    case 'fieldtype_stages':
                        $value = (is_array($xml_field_value) ? implode(',', $xml_field_value) : $xml_field_value);

                        if ($cfg->get('use_global_list') > 0) {
                            if (isset($global_choices_names_to_id[$cfg->get('use_global_list')][$value])) {
                                $sql_data['field_' . $field_id] = $global_choices_names_to_id[$cfg->get(
                                    'use_global_list'
                                )][$value];
                            } else {
                                $fields_choices_info_query = db_query(
                                    "select * from app_global_lists_choices where name='" . db_input(
                                        $value
                                    ) . "' and lists_id='" . db_input($cfg->get('use_global_list')) . "'"
                                );
                                if ($fields_choices_info = db_fetch_array($fields_choices_info_query)) {
                                    $sql_data['field_' . $field_id] = $fields_choices_info['id'];

                                    $global_choices_names_to_id[$cfg->get(
                                        'use_global_list'
                                    )][$value] = $fields_choices_info['id'];
                                } else {
                                    $field_sql_data = [
                                        'lists_id' => $cfg->get('use_global_list'),
                                        'parent_id' => 0,
                                        'name' => $value
                                    ];
                                    db_perform('app_global_lists_choices', $field_sql_data);

                                    $item_id = db_insert_id();

                                    $sql_data['field_' . $field_id] = $item_id;

                                    $global_choices_names_to_id[$cfg->get('use_global_list')][$value] = $item_id;
                                }
                            }
                        } else {
                            if (isset($choices_names_to_id[$field_id][$value])) {
                                $sql_data['field_' . $field_id] = $choices_names_to_id[$field_id][$value];
                            } else {
                                $fields_choices_info_query = db_query(
                                    "select * from app_fields_choices where name='" . db_input(
                                        $value
                                    ) . "' and fields_id='" . db_input($field_id) . "'"
                                );
                                if ($fields_choices_info = db_fetch_array($fields_choices_info_query)) {
                                    $sql_data['field_' . $field_id] = $fields_choices_info['id'];

                                    $choices_names_to_id[$field_id][$value] = $fields_choices_info['id'];
                                } else {
                                    $field_sql_data = [
                                        'fields_id' => $field_id,
                                        'parent_id' => 0,
                                        'name' => $value
                                    ];
                                    db_perform('app_fields_choices', $field_sql_data);

                                    $item_id = db_insert_id();

                                    $sql_data['field_' . $field_id] = $item_id;

                                    $choices_names_to_id[$field_id][$value] = $item_id;
                                }
                            }
                        }

                        //prepare choices values
                        $choices_values[$field_id][] = $sql_data['field_' . $field_id];

                        break;
                    case 'fieldtype_dropdown_multilevel':
                        $values_list = [];
                        $value = (is_array($xml_field_value) ? implode(',', $xml_field_value) : $xml_field_value);

                        if (strlen($value)) {
                            $value_id = 0;

                            if ($cfg->get('use_global_list') > 0) {
                                if (isset($global_choices_names_to_id[$cfg->get('use_global_list')][$value])) {
                                    $value_id = $global_choices_names_to_id[$cfg->get('use_global_list')][$value];
                                } else {
                                    $fields_choices_info_query = db_query(
                                        "select * from app_global_lists_choices where name='" . db_input(
                                            trim($value)
                                        ) . "' and lists_id='" . db_input($cfg->get('use_global_list')) . "'"
                                    );
                                    if ($fields_choices_info = db_fetch_array($fields_choices_info_query)) {
                                        $value_id = $fields_choices_info['id'];
                                        $global_choices_names_to_id[$cfg->get('use_global_list')][$value] = $value_id;
                                    }
                                }
                            } else {
                                if (isset($choices_names_to_id[$field_id][$value])) {
                                    $value_id = $choices_names_to_id[$field_id][$value];
                                } else {
                                    $fields_choices_info_query = db_query(
                                        "select * from app_fields_choices where name='" . db_input(
                                            trim($value)
                                        ) . "' and fields_id='" . db_input($field_id) . "'"
                                    );
                                    if ($fields_choices_info = db_fetch_array($fields_choices_info_query)) {
                                        $value_id = $fields_choices_info['id'];
                                        $choices_names_to_id[$field_id][$value] = $value_id;
                                    }
                                }
                            }

                            if ($value_id > 0) {
                                if ($cfg->get('use_global_list')) {
                                    if (isset($global_choices_parents_to_id[$value_id])) {
                                        $value_array = $global_choices_parents_to_id[$value_id];
                                    } else {
                                        $value_array = global_lists::get_paretn_ids($value_id);

                                        $global_choices_parents_to_id[$value_id] = $value_array;
                                    }
                                } else {
                                    if (isset($choices_parents_to_id[$field_id][$value_id])) {
                                        $value_array = $choices_parents_to_id[$field_id][$value_id];
                                    } else {
                                        $value_array = fields_choices::get_paretn_ids($value_id);

                                        $choices_parents_to_id[$field_id][$value_id] = $value_array;
                                    }
                                }

                                $values_list = array_reverse($value_array);

                                //prepare choices values
                                $choices_values[$field_id] = $values_list;

                                $sql_data['field_' . $field_id] = implode(',', $values_list);
                            }
                        }

                        break;
                    case 'fieldtype_grouped_users':
                    case 'fieldtype_dropdown_multiple':
                    case 'fieldtype_checkboxes':
                    case 'fieldtype_tags':
                        $values_list = [];
                        $value = (is_array($xml_field_value) ? $xml_field_value : [$xml_field_value]);

                        if ($cfg->get('use_global_list') > 0) {
                            foreach ($value as $value_name) {
                                $fields_choices_info_query = db_query(
                                    "select * from app_global_lists_choices where name='" . db_input(
                                        trim($value_name)
                                    ) . "' and lists_id='" . db_input($cfg->get('use_global_list')) . "'"
                                );
                                if ($fields_choices_info = db_fetch_array($fields_choices_info_query)) {
                                    $values_list[] = $fields_choices_info['id'];
                                } else {
                                    $field_sql_data = [
                                        'lists_id' => $cfg->get('use_global_list'),
                                        'parent_id' => 0,
                                        'name' => trim($value_name)
                                    ];
                                    db_perform('app_global_lists_choices', $field_sql_data);

                                    $item_id = db_insert_id();

                                    $values_list[] = $item_id;
                                }
                            }
                        } else {
                            foreach ($value as $value_name) {
                                $fields_choices_info_query = db_query(
                                    "select * from app_fields_choices where name='" . db_input(
                                        trim($value_name)
                                    ) . "' and fields_id='" . db_input($field_id) . "'"
                                );
                                if ($fields_choices_info = db_fetch_array($fields_choices_info_query)) {
                                    $values_list[] = $fields_choices_info['id'];
                                } else {
                                    $field_sql_data = [
                                        'fields_id' => $field_id,
                                        'parent_id' => 0,
                                        'name' => trim($value_name)
                                    ];
                                    db_perform('app_fields_choices', $field_sql_data);

                                    $item_id = db_insert_id();

                                    $values_list[] = $item_id;
                                }
                            }
                        }

                        //prepare choices values
                        $choices_values[$field_id] = $values_list;

                        $sql_data['field_' . $field_id] = implode(',', $values_list);

                        break;
                    case 'fieldtype_input_numeric':
                        $xml_field_value = (is_array($xml_field_value) ? implode(
                            '',
                            $xml_field_value
                        ) : $xml_field_value);
                        $sql_data['field_' . $field_id] = str_replace([',', ' '], ['.', ''], $xml_field_value);
                        break;
                    case 'fieldtype_input_date':
                    case 'fieldtype_input_datetime':
                        $xml_field_value = (is_array($xml_field_value) ? implode(
                            ' ',
                            $xml_field_value
                        ) : $xml_field_value);
                        $sql_data['field_' . $field_id] = (is_string($xml_field_value) ? strtotime(
                            $xml_field_value
                        ) : '');
                        break;
                    default:
                        $sql_data['field_' . $field_id] = (is_array($xml_field_value) ? implode(
                            ', ',
                            $xml_field_value
                        ) : $xml_field_value);
                        break;
                }

                //check uniques
                if (in_array($filed_info['id'], $unique_fields) and strlen($sql_data['field_' . $field_id])) {
                    $check_query = db_query(
                        "select id from app_entity_{$entities_id} where field_{$field_id}='" . db_input(
                            $sql_data['field_' . $field_id]
                        ) . "' limit 1"
                    );
                    if ($check = db_fetch_array($check_query)) {
                        $is_unique_item = false;
                    }
                }
            }
        }


        //print_rr($sql_data);
        //exit();

        $item_id = false;
        $item_has_updated = false;

        if ($this->template_info['import_action'] == 'update' or $this->template_info['import_action'] == 'update_import') {
            $field_info = db_find('app_fields', $this->template_info['update_by_field']);

            if (is_object($import_fields_queries[$this->template_info['update_by_field']])) {
                $xml_field_value = $this->get_field_value_by_key(
                    $this->template_info['update_by_field'],
                    $item_key,
                    $import_fields_queries
                );
            } else {
                $xml_field_value = $item_node->getAttribute(
                    $import_fields_queries[$this->template_info['update_by_field']]
                );
            }


            //echo $xml_field_value . '<br>';

            $xml_field_value = (is_array($xml_field_value) ? implode('', $xml_field_value) : $xml_field_value);

            if ($field_info['type'] == 'fieldtype_id') {
                $where_sql = " where id='" . db_input($xml_field_value) . "'";
            } else {
                $where_sql = " where field_" . $field_info['id'] . "='" . db_input($xml_field_value) . "'";
            }

            $where_sql .= " and parent_item_id = '" . $parent_entity_item_id . "'";

            $item_query = db_query("select id from app_entity_" . $entities_id . $where_sql);
            if ($item = db_fetch_array($item_query) and count($sql_data)) {
                db_perform('app_entity_' . $entities_id, $sql_data, 'update', "id=" . $item['id']);

                $item_has_updated = true;

                $item_id = $item['id'];

                $this->count_updated_items++;
            }
        }

        //do insert
        if (!$item_has_updated and ($this->template_info['import_action'] == 'import' or $this->template_info['import_action'] == 'update_import')) {
            //skip not unique items
            if ($is_unique_item) {
                //set other values
                $sql_data['date_added'] = time();
                $sql_data['created_by'] = $app_logged_users_id;
                $sql_data['parent_item_id'] = (int)$parent_entity_item_id;


                //print_rr($sql_data);
                //exit();

                db_perform('app_entity_' . $entities_id, $sql_data);

                $item_id = db_insert_id();

                $this->count_new_items++;
            }
        }

        //insert choices values if exist
        if (count($choices_values) > 0 and $item_id) {
            //reset current choices values if action is "update"
            if ($this->template_info['import_action'] != 'import') {
                db_query(
                    "delete from app_entity_" . $entities_id . "_values where items_id = '" . $item_id . "' and fields_id='" . $field_id . "'"
                );
            }

            foreach ($choices_values as $field_id => $values) {
                foreach ($values as $value) {
                    db_query(
                        "INSERT INTO app_entity_" . $entities_id . "_values (items_id, fields_id, value) VALUES ('" . $item_id . "', '" . $field_id . "', '" . $value . "');"
                    );
                }
            }
        }

        //prepare item
        if ($item_id) {
            //autoupdate all field types
            fields_types::update_items_fields($entities_id, $item_id);

            if (!$item_has_updated) {
                //run actions after item insert
                $processes = new processes($entities_id);
                $processes->run_after_insert($item_id);
            }
        }
    }


    function get_field_value_by_key($field_id, $item_key, $import_fields_queries)
    {
        foreach ($import_fields_queries[$field_id] as $field_itme_key => $field_query) {
            if ($item_key == $field_itme_key) {
                $is_array = true;
                $data_array = [];

                foreach ($field_query->childNodes as $child) {
                    if ($child->childNodes == '') {
                        $is_array = false;
                    } else {
                        $data_array[] = trim($child->nodeValue);
                    }
                }

                if ($is_array) {
                    return $data_array;
                } else {
                    return trim($field_query->nodeValue);
                }
            }
        }

        return '';
    }

    function prepare_preview($item_key, $import_fields_queries, $item_node)
    {
        global $app_fields_cache;

        $html = '<div class="panel panel-default"><div class="panel-body"><table >';

        foreach ($import_fields_queries as $field_id => $queries) {
            $field = $app_fields_cache[$this->template_info['entities_id']][$field_id];

            if (is_object($queries)) {
                $field_value = $this->get_field_value_by_key($field_id, $item_key, $import_fields_queries);
            } else {
                $field_value = $item_node->getAttribute($queries);
            }

            if (is_array($field_value)) {
                $field_value = implode('<br>', $field_value);
            }

            $html .= '
                <tr>
                   <th valign="top">' . fields_types::get_option($field['type'], 'name', $field['name']) . ':</th>
                   <td style="padding-left: 15px;">' . $field_value . '</td>
                </tr>     
              ';
        }

        $html .= '</table></div></div>';

        return $html;
    }

    static function get_position_choices()
    {
        $choices = [];
        $choices['default'] = TEXT_DEFAULT;
        $choices['menu_more_actions'] = TEXT_EXT_MENU_MORE_ACTIONS;
        $choices['in_listing'] = TEXT_IN_LISTING;

        return $choices;
    }

    static function get_users_templates_by_position($entities_id, $position, $url_params = '')
    {
        global $app_user;

        $templates_list = [];

        $html = '';

        $templates_query = db_query(
            "select ep.* from app_ext_xml_import_templates ep, app_entities e where ep.is_active=1 and e.id=ep.entities_id and find_in_set('" . str_replace(
                '_dashboard',
                '',
                $position
            ) . "',ep.button_position) and ep.entities_id='" . db_input(
                $entities_id
            ) . "' and (find_in_set(" . $app_user['group_id'] . ",users_groups) or find_in_set(" . $app_user['id'] . ",assigned_to)) order by ep.sort_order, ep.name"
        );
        while ($templates = db_fetch_array($templates_query)) {
            $button_title = (strlen($templates['button_title']) ? $templates['button_title'] : $templates['name']);
            $button_icon = (strlen($templates['button_icon']) ? $templates['button_icon'] : 'fa-file-code-o');

            $style = (strlen($templates['button_color']) ? 'color: ' . $templates['button_color'] : '');

            switch ($position) {
                case 'default':
                    $html .= '<li>' . button_tag(
                            $button_title,
                            url_for('items/xml_import', 'path=' . $_GET['path'] . '&templates_id=' . $templates['id']),
                            true,
                            ['class' => 'btn btn-primary btn-sm btn-template-' . $templates['id']],
                            $button_icon
                        ) . '</li>';
                    $html .= self::prepare_button_css($templates);
                    break;
                case 'in_listing':
                    $html .= '&nbsp;&nbsp;' . button_tag(
                            $button_title,
                            url_for('items/xml_import', 'path=' . $_GET['path'] . '&templates_id=' . $templates['id']),
                            true,
                            ['class' => 'btn btn-primary btn-template-' . $templates['id']],
                            $button_icon
                        );
                    $html .= self::prepare_button_css($templates);
                    break;
                case 'menu_more_actions':
                    $templates_list[] = [
                        'id' => $templates['id'],
                        'name' => $button_title,
                        'entities_id' => $templates['entities_id'],
                        'button_icon' => $button_icon
                    ];
                    break;
            }
        }

        switch ($position) {
            case 'default':
            case 'in_listing':
                return $html;
                break;
            case 'menu_more_actions':
                return $templates_list;
                break;
        }
    }

    static public function prepare_button_css($buttons)
    {
        $css = '';

        if (strlen($buttons['button_color'])) {
            $rgb = convert_html_color_to_RGB($buttons['button_color']);
            $rgb[0] = $rgb[0] - 25;
            $rgb[1] = $rgb[1] - 25;
            $rgb[2] = $rgb[2] - 25;
            $css = '
					<style>
						.btn-template-' . $buttons['id'] . '{
							background-color: ' . $buttons['button_color'] . ';
						  border-color: ' . $buttons['button_color'] . ';
						}
						.btn-primary.btn-template-' . $buttons['id'] . ':hover,
						.btn-primary.btn-template-' . $buttons['id'] . ':focus,
						.btn-primary.btn-template-' . $buttons['id'] . ':active,
						.btn-primary.btn-template-' . $buttons['id'] . '.active{
						  background-color: rgba(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ',1);
						  border-color: rgba(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ',1);
						}
					</style>
			';
        }

        return $css;
    }

    static function has_users_access($entities_id, $templates_id)
    {
        global $app_user;

        $templates_query = db_query(
            "select ep.* from app_ext_xml_import_templates ep, app_entities e where e.id=ep.entities_id and ep.is_active=1 and ep.entities_id='" . db_input(
                $entities_id
            ) . "' and (find_in_set(" . db_input($app_user['group_id']) . ",users_groups) or find_in_set(" . db_input(
                $app_user['id']
            ) . ",assigned_to)) and ep.id='" . db_input($templates_id) . "' order by ep.sort_order, ep.name"
        );
        if ($templates = db_fetch_array($templates_query)) {
            return true;
        } else {
            return false;
        }
    }
}