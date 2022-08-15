<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Fields extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        if (!\K::$fw->GET['entities_id']) {
            \Helpers\Urls::redirect_to('main/entities');
        }

        $fields_sql_query = '';

        $entity_info = \K::model()->db_find('app_entities', \K::$fw->GET['entities_id']);

        //include fieldtype_parent_item_id only for sub entities
        if ($entity_info['parent_id'] == 0) {
            $fields_sql_query .= " and f.type not in ('fieldtype_parent_item_id')";
        }

        \K::$fw->reserved_fields_types = array_merge(
            \Models\Main\Fields_types::get_reserved_data_types(),
            \Models\Main\Fields_types::get_users_types()
        );
        $reserved_fields_types_list = \K::model()->quoteToString(\K::$fw->reserved_fields_types);

        \K::$fw->fields_query = \K::model()->db_query_exec(
            "select f.*, fr.sort_order as form_rows_sort_order,right(f.forms_rows_position,1) as forms_rows_pos, t.name as tab_name, if(f.type in (" . $reserved_fields_types_list . "),-1,t.sort_order) as tab_sort_order from app_fields f left join app_forms_rows fr on fr.id = LEFT(f.forms_rows_position,length(f.forms_rows_position)-2), app_forms_tabs t where f.type not in ('fieldtype_action') and f.entities_id = ? and f.forms_tabs_id = t.id {$fields_sql_query} order by tab_sort_order, t.name, form_rows_sort_order, forms_rows_pos, f.sort_order, f.name",
            \K::$fw->GET['entities_id'],
            'app_fields,app_forms_rows,app_forms_tabs'
        );

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function set_heading_field_id()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['entities_id']) and isset(\K::$fw->POST['heading_field_id'])) {
            \K::model()->begin();
            //reset heading
            //db_query("update app_fields set is_heading=0 where entities_id ='" . db_input(\K::$fw->GET['entities_id']) . "'");
            \K::model()->db_update('app_fields', ['is_heading' => 0], ['entities_id = ?', \K::$fw->GET['entities_id']]);

            //set new heading
            /*db_query(
                "update app_fields set is_heading=1 where id='" . \K::$fw->POST['heading_field_id'] . "' and entities_id ='" . db_input(
                    \K::$fw->GET['entities_id']
                ) . "'"
            );*/
            \K::model()->db_update(
                'app_fields',
                ['is_heading' => 1],
                ['id = ? and entities_id = ?', \K::$fw->POST['heading_field_id'], \K::$fw->GET['entities_id']]
            );

            \K::model()->commit();
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function set_heading_field_width()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['entities_id']) and isset(\K::$fw->POST['heading_width_based_content'])) {
            \Models\Main\Entities::set_cfg(
                'heading_width_based_content',
                \K::$fw->POST['heading_width_based_content'],
                \K::$fw->GET['entities_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function set_number_fixed_field_in_listing()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['entities_id']) and isset(\K::$fw->POST['number_fields'])) {
            \Models\Main\Entities::set_cfg(
                'number_fixed_field_in_listing',
                \K::$fw->POST['number_fields'],
                \K::$fw->GET['entities_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function set_change_col_width_in_listing()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['entities_id']) and isset(\K::$fw->POST['change_col_width_in_listing'])) {
            \Models\Main\Entities::set_cfg(
                'change_col_width_in_listing',
                \K::$fw->POST['change_col_width_in_listing'],
                \K::$fw->GET['entities_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function editable_fields_in_listing()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['entities_id']) and isset(\K::$fw->POST['editable_fields_in_listing'])) {
            \Models\Main\Entities::set_cfg(
                'editable_fields_in_listing',
                \K::$fw->POST['editable_fields_in_listing'],
                \K::$fw->GET['entities_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function sort_fields()
    {
        if (\K::$fw->VERB == 'POST') {
            \K::model()->begin();

            if (isset(\K::$fw->POST['fields_in_listing'])) {
                $sort_order = 0;
                $exp = explode(',', \K::$fw->POST['fields_in_listing']);

                foreach ($exp as $v) {
                    $sql_data = ['listing_status' => 1, 'listing_sort_order' => $sort_order];

                    \K::model()->db_update(
                        'app_fields',
                        $sql_data,
                        ['id = ?', str_replace('form_fields_', '', $v)]
                    );
                    $sort_order++;
                }
            }

            if (isset(\K::$fw->POST['fields_excluded_from_listing'])) {
                $exp = explode(',', \K::$fw->POST['fields_excluded_from_listing']);

                foreach ($exp as $v) {
                    $sql_data = ['listing_status' => 0, 'listing_sort_order' => 0];

                    \K::model()->db_update(
                        'app_fields',
                        $sql_data,
                        ['id = ?', str_replace('form_fields_', '', $v)]
                    );
                }
            }

            \K::model()->commit();
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function save_internal()
    {
        if (\K::$fw->VERB == 'POST') {
            $sql_data = [
                'name' => \K::$fw->POST['name'],
                'short_name' => \K::$fw->POST['short_name'],
                'is_heading' => (\K::$fw->POST['is_heading'] ?? 0),
                'sort_order' => \K::$fw->POST['sort_order'],
                'configuration' => (isset(\K::$fw->POST['fields_configuration']) ? \Models\Main\Fields_types::prepare_configuration(
                    \K::$fw->POST['fields_configuration']
                ) : ''),
            ];

            \K::model()->begin();

            //reset heading fields, only one field can be heading
            if (isset(\K::$fw->POST['is_heading'])) {
                /*db_query(
                    "update app_fields set is_heading=0 where entities_id ='" . db_input(
                        \K::$fw->POST['entities_id']
                    ) . "'"
                );*/

                \K::model()->db_update(
                    'app_fields',
                    ['is_heading' => 0],
                    ['entities_id = ?', \K::$fw->POST['entities_id']]
                );
            }

            if (isset(\K::$fw->GET['id'])) {
                //db_perform('app_fields', $sql_data, 'update', "id='" . db_input(\K::$fw->GET['id']) . "'");

                \K::model()->db_update(
                    'app_fields',
                    $sql_data,
                    ['id = ?', \K::$fw->GET['id']]
                );
            }

            \K::model()->commit();

            \Helpers\Urls::redirect_to('main/entities/fields', 'entities_id=' . \K::$fw->POST['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $fields_configuration = \K::$fw->POST['fields_configuration'];

            //prepare upload
            if (isset(\K::$fw->FILES['fields_configuration'])) {
                $upload_configuration = [];

                foreach (\K::$fw->FILES['fields_configuration']['name'] as $k => $v) {
                    $upload_folder = '';

                    //prepare upload folder
                    if (strstr($k, 'icon_')) {
                        $upload_folder = 'icons/';
                    }

                    //check if delete file
                    if (isset(\K::$fw->POST['delete_file'][$k])) {
                        unlink(\K::$fw->DIR_WS_UPLOADS . $upload_folder . \K::$fw->POST['delete_file'][$k]);
                        $upload_configuration[$k] = '';
                    }

                    //upload file
                    if (strlen($v)) {
                        $filename = str_replace(' ', '_', $v);
                        if (move_uploaded_file(
                            \K::$fw->FILES['fields_configuration']['tmp_name'][$k],
                            \K::$fw->DIR_WS_UPLOADS . $upload_folder . $filename
                        )) {
                            $upload_configuration[$k] = $filename;
                        }
                    }
                }

                if (count($upload_configuration)) {
                    $fields_configuration = array_merge($fields_configuration, $upload_configuration);
                }
            }

            $sql_data = [
                'forms_tabs_id' => \K::$fw->POST['forms_tabs_id'],
                'name' => \K::$fw->POST['name'],
                'type' => \K::$fw->POST['type'],
                'short_name' => \K::$fw->POST['short_name'],
                'notes' => strip_tags(\K::$fw->POST['notes']),
                'is_heading' => (\K::$fw->POST['is_heading'] ?? 0),
                'is_required' => (\K::$fw->POST['is_required'] ?? 0),
                'required_message' => \K::$fw->POST['required_message'],
                'tooltip' => \K::$fw->POST['tooltip'],
                'tooltip_display_as' => (\K::$fw->POST['tooltip_display_as'] ?? ''),
                'tooltip_in_item_page' => (\K::$fw->POST['tooltip_in_item_page'] ?? ''),
                'tooltip_item_page' => \K::$fw->POST['tooltip_item_page'],
                'configuration' => (isset(\K::$fw->POST['fields_configuration']) ? \Models\Main\Fields_types::prepare_configuration(
                    $fields_configuration
                ) : ''),
                'entities_id' => \K::$fw->POST['entities_id']
            ];

            \K::model()->begin();

            //reset heading fields, only one field can be heading
            if (isset(\K::$fw->POST['is_heading'])) {
                /*db_query(
                    "update app_fields set is_heading=0 where entities_id ='" . db_input(
                        \K::$fw->POST['entities_id']
                    ) . "'"
                );*/

                \K::model()->db_update(
                    'app_fields',
                    ['is_heading' => 0],
                    ['entities_id = ?', \K::$fw->POST['entities_id']]
                );
            }

            if (isset(\K::$fw->GET['id'])) {
                //check if field type changed and do action required when field type changed
                \Models\Main\Fields::check_if_type_changed(\K::$fw->GET['id'], \K::$fw->POST['type']);

                //db_perform('app_fields', $sql_data, 'update', "id='" . db_input(\K::$fw->GET['id']) . "'");
                \K::model()->db_update('app_fields', $sql_data, ['id = ?', \K::$fw->GET['id']]);

                $fields_id = \K::$fw->GET['id'];
            } else {
                $sql_data['sort_order'] = (\Models\Main\Fields::get_last_sort_number(
                        \K::$fw->POST['forms_tabs_id']
                    ) + 1);

                $mapper = \K::model()->db_perform('app_fields', $sql_data);
                $fields_id = \K::model()->db_insert_id($mapper);

                \Models\Main\Entities::prepare_field(\K::$fw->POST['entities_id'], $fields_id, \K::$fw->POST['type']);
            }

            //create app_related_items_#_# table
            \Tools\Related_records::prepare_entities_related_items_table(\K::$fw->POST['entities_id'], $fields_id);

            //set field access
            if (isset(\K::$fw->POST['access'])) {
                foreach (\K::$fw->POST['access'] as $access_groups_id => $access) {
                    if (in_array($access, ['view', 'hide'])) {
                        $sql_data = ['access_schema' => $access];

                        /*$acess_info_query = db_query(
                            "select access_schema from app_fields_access where entities_id='" . db_input(
                                \K::$fw->POST['entities_id']
                            ) . "' and access_groups_id='" . db_input(
                                $access_groups_id
                            ) . "' and fields_id='" . db_input(
                                $fields_id
                            ) . "'"
                        );
                        if ($acess_info = db_fetch_array($acess_info_query)) {
                            db_perform(
                                'app_fields_access',
                                $sql_data,
                                'update',
                                "entities_id='" . db_input(
                                    \K::$fw->POST['entities_id']
                                ) . "' and access_groups_id='" . db_input(
                                    $access_groups_id
                                ) . "'  and fields_id='" . db_input($fields_id) . "'"
                            );
                        } else {
                            $sql_data['entities_id'] = \K::$fw->POST['entities_id'];
                            $sql_data['access_groups_id'] = $access_groups_id;
                            $sql_data['fields_id'] = $fields_id;
                            db_perform('app_fields_access', $sql_data);
                        }*/

                        $sql_data['entities_id'] = \K::$fw->POST['entities_id'];
                        $sql_data['access_groups_id'] = $access_groups_id;
                        $sql_data['fields_id'] = $fields_id;

                        \K::model()->db_perform('app_fields_access', $sql_data, [
                            'entities_id = ? and access_groups_id = ? and fields_id = ?',
                            \K::$fw->POST['entities_id'],
                            $access_groups_id,
                            $fields_id
                        ]);
                    } else {
                        /*db_query(
                            "delete from app_fields_access where entities_id='" . db_input(
                                \K::$fw->POST['entities_id']
                            ) . "' and access_groups_id='" . db_input(
                                $access_groups_id
                            ) . "'  and fields_id='" . db_input(
                                $fields_id
                            ) . "'"
                        );*/

                        \K::model()->db_delete('app_fields_access', [
                            'entities_id = ? and access_groups_id = ? and fields_id = ?',
                            \K::$fw->POST['entities_id'],
                            $access_groups_id,
                            $fields_id
                        ]);
                    }
                }
            }

            \K::model()->commit();

            if (isset(\K::$fw->POST['redirect_to']) and \K::$fw->POST['redirect_to'] == 'forms') {
                \Helpers\Urls::redirect_to('main/entities/forms', 'entities_id=' . \K::$fw->POST['entities_id']);
            }

            \Helpers\Urls::redirect_to('main/entities/fields', 'entities_id=' . \K::$fw->POST['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['id'])) {
            $msg = \Models\Main\Fields::check_before_delete(\K::$fw->GET['entities_id'], \K::$fw->GET['id']);

            if (strlen($msg) > 0) {
                \K::flash()->addMessage($msg, 'error');
            } else {
                $name = \Models\Main\Fields::get_name_by_id(\K::$fw->GET['id']);

                \K::model()->begin();

                \K::model()->db_delete_row('app_fields', \K::$fw->GET['id']);

                \K::model()->db_delete_row('app_fields_choices', \K::$fw->GET['id'], 'fields_id');

                \K::model()->db_delete_row('app_reports_filters', \K::$fw->GET['id'], 'fields_id');

                \Models\Main\Choices_values::delete_by_field_id(\K::$fw->GET['entities_id'], \K::$fw->GET['id']);

                \Models\Main\Entities::delete_field(\K::$fw->GET['entities_id'], \K::$fw->GET['id']);

                /*db_query(
                    "delete from app_reports_filters_templates where fields_id='" . db_input(\K::$fw->GET['id']) . "'"
                );*/

                \K::model()->db_delete_row('app_reports_filters_templates', \K::$fw->GET['id'], 'fields_id');

                //db_query("delete from app_forms_fields_rules where fields_id='" . db_input(\K::$fw->GET['id']) . "'");

                \K::model()->db_delete_row('app_forms_fields_rules', \K::$fw->GET['id'], 'fields_id');

                //delete approved records
                /*db_query(
                    "delete from app_approved_items where entities_id='" . _get::int(
                        'entities_id'
                    ) . "' and fields_id='" . _get::int('id') . "'"
                );*/

                \K::model()->db_delete('app_approved_items', [
                    'entities_id = ? and fields_id = ?',
                    \K::$fw->GET['entities_id'],
                    \K::$fw->GET['id']
                ]);

                //access rules
                //db_query("delete from app_access_rules where fields_id='" . db_input(\K::$fw->GET['id']) . "'");
                //db_query("delete from app_access_rules_fields where fields_id='" . db_input(\K::$fw->GET['id']) . "'");

                \K::model()->db_delete_row('app_access_rules', \K::$fw->GET['id'], 'fields_id');

                \K::model()->db_delete_row('app_access_rules_fields', \K::$fw->GET['id'], 'fields_id');

                \Tools\Maps\Mind_map::delete_by_fields_id(\K::$fw->GET['entities_id'], \K::$fw->GET['id']);
                \Tools\Maps\Image_map_nested::delete_by_fields_id(\K::$fw->GET['id']);

                \K::model()->db_delete_row('app_listing_highlight_rules', \K::$fw->GET['id'], 'fields_id');

                if (\Helpers\App::is_ext_installed()) {
                    \K::model()->db_delete_row('app_ext_processes_actions_fields', \K::$fw->GET['id'], 'fields_id');
                }

                \K::model()->commit();

                \K::flash()->addMessage(sprintf(\K::$fw->TEXT_WARN_DELETE_SUCCESS, $name), 'success');
            }

            if (isset(\K::$fw->POST['redirect_to'])) {
                switch (\K::$fw->POST['redirect_to']) {
                    case 'forms':
                        \Helpers\Urls::redirect_to('main/entities/forms', 'entities_id=' . \K::$fw->GET['entities_id']);
                        break;
                }
            }

            \Helpers\Urls::redirect_to('main/entities/fields', 'entities_id=' . \K::$fw->GET['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function get_entities_form_tabs()
    {
        $choices = forms_tabs::get_choices(\K::$fw->POST['entities_id']);

        if (count($choices) == 1) {
            $html = input_hidden_tag('copy_to_form_tabs_id', key($choices));
        } else {
            $html = '
         <div class="form-group">
          	<label class="col-md-4 control-label" for="type">' . TEXT_SELECT_FORM_TAB . '</label>
            <div class="col-md-8">	
          	  ' . select_tag('copy_to_form_tabs_id', $choices, '', ['class' => 'form-control']) . '        
            </div>			
          </div>        
        ';
        }

        echo $html;
    }

    public function mulitple_edit()
    {
        if (strlen(\K::$fw->POST['selected_fields'])) {
            $fields_query = db_query(
                "select * from app_fields where entities_id='" . \K::$fw->GET['entities_id'] . "' and id in (" . \K::$fw->POST['selected_fields'] . ")"
            );
            while ($fields = db_fetch_array($fields_query)) {
                if (\K::$fw->POST['is_required'] == 'yes') {
                    db_query("update app_fields set is_required=1 where id='" . $fields['id'] . "'");
                } elseif (\K::$fw->POST['is_required'] == 'no') {
                    db_query("update app_fields set is_required=0 where id='" . $fields['id'] . "'");
                }
            }
        }

        redirect_to('entities/fields', 'entities_id=' . \K::$fw->GET['entities_id']);
    }

    public function copy_selected()
    {
        if (strlen(\K::$fw->POST['selected_fields']) > 0 and \K::$fw->POST['copy_to_entities_id'] > 0) {
            $fields_query = db_query(
                "select * from app_fields where entities_id='" . \K::$fw->GET['entities_id'] . "' and id in (" . \K::$fw->POST['selected_fields'] . ")"
            );
            while ($fields = db_fetch_array($fields_query)) {
                //prepare sql data
                $sql_data = $fields;
                unset($sql_data['id']);
                $sql_data['entities_id'] = \K::$fw->POST['copy_to_entities_id'];
                $sql_data['forms_tabs_id'] = \K::$fw->POST['copy_to_form_tabs_id'];
                $sql_data['is_heading'] = 0;
                $sql_data['forms_rows_position'] = '';

                db_perform('app_fields', $sql_data);
                $new_fields_id = db_insert_id();

                entities::prepare_field(\K::$fw->POST['copy_to_entities_id'], $new_fields_id, $fields['type']);

                //create app_related_items_#_# table
                related_records::prepare_entities_related_items_table(
                    \K::$fw->POST['copy_to_entities_id'],
                    $new_fields_id
                );

                $choices_parent_id_to_replace = [];

                //check fields choices
                $fields_choices_query = db_query(
                    "select * from app_fields_choices where fields_id='" . $fields['id'] . "'"
                );
                while ($fields_choices = db_fetch_array($fields_choices_query)) {
                    //prepare sql data
                    $sql_data = $fields_choices;
                    unset($sql_data['id']);
                    $sql_data['fields_id'] = $new_fields_id;

                    db_perform('app_fields_choices', $sql_data);
                    $new_fields_choices_id = db_insert_id();

                    $choices_parent_id_to_replace[$fields_choices['id']] = $new_fields_choices_id;
                }

                foreach ($choices_parent_id_to_replace as $from_id => $to_id) {
                    db_query(
                        "update app_fields_choices set parent_id='" . $to_id . "' where parent_id='" . $from_id . "' and fields_id='" . $new_fields_id . "'"
                    );
                }
            }

            $alerts->add(TEXT_FIELDS_COPY_SUCCESS, 'success');
        }

        redirect_to('entities/fields', 'entities_id=' . \K::$fw->POST['copy_to_entities_id']);
    }

    public function import()
    {
        //rename file (issue with HTML.php:495 if file have UTF symbols)
        $filepath = DIR_WS_UPLOADS . 'import_fields.xml';

        if (move_uploaded_file(\K::$fw->FILES['filename']['tmp_name'], $filepath)) {
            $data = file_get_contents($filepath);
            $xml = simplexml_load_string($data);
            $json = json_encode($xml);
            $fields = json_decode($json, true);

            unlink($filepath);

            //print_rr($fields);
            //exit();

            $entities_id = _get::int('entities_id');
            $imported_fields = 0;

            $tab_query = db_query(
                "select forms_tabs_id from app_fields where entities_id='" . db_input(
                    $entities_id
                ) . "' and type='fieldtype_id'"
            );
            $tab = db_fetch_array($tab_query);
            $default_forms_tabs_id = $tab['forms_tabs_id'];

            if (isset($fields['Field'])) {
                $fields_list = [];
                if (isset($fields['Field']['forms_tabs_id'])) {
                    $fields_list[] = $fields['Field'];
                } else {
                    $fields_list = $fields['Field'];
                }

                foreach ($fields_list as $field) {
                    //print_rr($field);

                    $sql_data = ['entities_id' => $entities_id];

                    foreach ($field as $k => $v) {
                        if (!is_array($v)) {
                            $sql_data[$k] = $v;
                        }
                    }

                    //check if tab id exist for this entity
                    $tab_query = db_query(
                        "select id from app_forms_tabs where entities_id='" . db_input(
                            $entities_id
                        ) . "'  and id='" . $sql_data['forms_tabs_id'] . "'"
                    );
                    if (!$tab = db_fetch_array($tab_query)) {
                        $sql_data['forms_tabs_id'] = $default_forms_tabs_id;
                    }

                    $sql_data['forms_rows_position'] = '';

                    //print_rr($sql_data);
                    //exit();

                    db_perform('app_fields', $sql_data);
                    $fields_id = db_insert_id();

                    $imported_fields++;

                    entities::prepare_field($sql_data['entities_id'], $fields_id, $sql_data['type']);

                    //create app_related_items_#_# table
                    related_records::prepare_entities_related_items_table($sql_data['entities_id'], $fields_id);

                    //check choices
                    if (isset($field['Choices'])) {
                        $choices_list = [];
                        if (isset($field['Choices']['Choice']['name'])) {
                            $choices_list[] = $field['Choices']['Choice'];
                        } else {
                            $choices_list = $field['Choices']['Choice'];
                        }

                        foreach ($choices_list as $choice) {
                            $sql_data = ['fields_id' => $fields_id];

                            foreach ($choice as $k => $v) {
                                if (!is_array($v)) {
                                    $sql_data[$k] = $v;
                                }
                            }

                            db_perform('app_fields_choices', $sql_data);
                        }
                    }
                }
            }

            $alerts->add(sprintf(TEXT_IMPORTED_FIELDS, $imported_fields), 'success');
            redirect_to('entities/fields', 'entities_id=' . \K::$fw->GET['entities_id']);
        } else {
            $alerts->add(TEXT_FILE_NOT_LOADED, 'warning');
            redirect_to('entities/fields', 'entities_id=' . \K::$fw->GET['entities_id']);
        }
    }

    public function export()
    {
        if (strlen(\K::$fw->POST['selected_fields'])) {
            $writer = new XMLWriter();
            $writer->openMemory();
            $writer->setIndent(true);
            $writer->startDocument('1.0', 'UTF-8');

            $writer->startElement('Fields');
            $fields_query = db_query(
                "select * from app_fields where entities_id='" . \K::$fw->GET['entities_id'] . "' and id in (" . \K::$fw->POST['selected_fields'] . ")"
            );
            while ($fields = db_fetch_array($fields_query)) {
                //export field data
                $writer->startElement('Field');

                foreach ($fields as $k => $v) {
                    if (in_array($k, ['id', 'entities_id', 'is_heading'])) {
                        continue;
                    }

                    $writer->writeElement($k, $v);
                }

                //export field choices data
                $choices_query = db_query("select * from app_fields_choices where fields_id='" . $fields['id'] . "'");

                if (db_num_rows($choices_query)) {
                    $writer->startElement('Choices');

                    while ($choices = db_fetch_array($choices_query)) {
                        $writer->startElement('Choice');

                        foreach ($choices as $k => $v) {
                            if (in_array($k, ['id', 'fields_id', 'filename', 'parent_id'])) {
                                continue;
                            }

                            $writer->writeElement($k, $v);
                        }

                        $writer->endElement();
                    }

                    $writer->endElement();
                }

                $writer->endElement();
            }

            $writer->endElement();

            $filename = str_replace([" ", ","], "_", trim(\K::$fw->POST['filename']));

            header('Content-Type: application/xml; charset=utf-8');
            header('Content-Disposition: attachment;filename="' . $filename . '.xml"');
            header('Cache-Control: max-age=0');

            echo $writer->outputMemory();

            exit();
        }

        redirect_to('entities/fields', 'entities_id=' . \K::$fw->GET['entities_id']);
    }
}