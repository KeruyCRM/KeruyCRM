<?php

class fieldtype_digital_signature
{
    public $options;

    function __construct()
    {
        $this->options = ['title' => TEXT_FIELDTYPE_DIGITAL_SIGNATURE_TITLE];
    }

    function get_configuration()
    {
        $cfg = [];

        //module choices

        $choices = [];

        if (is_ext_installed()) {
            $modules = new modules('digital_signature');
            $choices = $modules->get_active_modules();
        }

        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_MODULE,
            'name' => 'module_id',
            'tooltip' => (!is_ext_installed() ? TEXT_EXTENSION_REQUIRED : ''),
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => ['class' => 'form-control input-large required']
        ];


        //signature fields        

        $choices = [];
        $exclude_types = " and f.type not in ('fieldtype_action','fieldtype_digital_signature','fieldtype_google_map','fieldtype_google_map_directions','fieldtype_iframe','fieldtype_image_map','fieldtype_mapbbcode','fieldtype_mind_map','fieldtype_signature','fieldtype_section','fieldtype_todo_list')";
        $fields_query = fields::get_query($_POST['entities_id'], $exclude_types);
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = fields_types::get_option($fields['type'], 'name', $fields['name']);
        }

        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_FIELDS_FOR_SIGNATURE,
            'name' => 'signature_fields',
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => ['class' => 'form-control input-xlarge required chosen-select', 'multiple' => 'multiple']
        ];


        //assigned to fields

        $choices = [];
        $choices[0] = '';

        $fields_query = db_query(
            "select f.id,f.name, e.name as entity_name from app_fields f, app_entities e where e.id='" . $_POST['entities_id'] . "' and e.id=f.entities_id and type in ('fieldtype_users','fieldtype_users_ajax')  order by e.sort_order, e.name, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = $fields['name'];
        }

        if (count($choices) > 1) {
            $cfg[TEXT_SETTINGS][] = [
                'title' => TEXT_ASSIGNED_TO,
                'name' => 'assigned_to',
                'type' => 'dropdown',
                'choices' => $choices,
                'params' => ['class' => 'form-control input-large']
            ];
        }

        //process chocies

        $choices = [];
        $choices[0] = '';

        if (is_ext_installed()) {
            $processes_query = db_query(
                "select id, name from app_ext_processes where entities_id='" . $_POST['entities_id'] . "' order by sort_order, name"
            );
            while ($processes = db_fetch_array($processes_query)) {
                $choices[$processes['id']] = $processes['name'];
            }
        }

        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_ACTION,
            'name' => 'run_process',
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => ['class' => 'form-control input-large']
        ];


        $cfg[TEXT_BUTTON][] = [
            'title' => TEXT_BUTTON_TITLE,
            'name' => 'button_title',
            'type' => 'input',
            'params' => ['class' => 'form-control input-medium'],
            'tooltip_icon' => TEXT_DEFAULT . ': ' . TEXT_DIGITAL_SIGNATURE
        ];
        $cfg[TEXT_BUTTON][] = [
            'title' => TEXT_ICON,
            'name' => 'button_icon',
            'type' => 'input',
            'params' => ['class' => 'form-control input-medium'],
            'tooltip' => TEXT_MENU_ICON_TITLE_TOOLTIP
        ];
        $cfg[TEXT_BUTTON][] = ['title' => TEXT_COLOR, 'name' => 'button_color', 'type' => 'colorpicker'];

        $cfg[TEXT_BUTTON][] = [
            'title' => TEXT_HIDE_FIELD_NAME,
            'name' => 'hide_field_name',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_HIDE_FIELD_NAME_TIP
        ];


        return $cfg;
    }

    function render($field, $obj, $params = [])
    {
    }

    function process($options)
    {
    }

    function output($options)
    {
        global $app_user, $fields_access_schema_holder, $app_module_path, $app_fields_cache;

        $html = '';

        $cfg = new fields_types_cfg($options['field']['configuration']);
        $entities_id = $options['field']['entities_id'];

        $path_info = items::get_path_info($options['field']['entities_id'], $options['item']['id'], $options['item']);

        $has_sign_button = true;

        $cryptopro_certificates_query = db_query(
            "select certbase64 from app_ext_cryptopro_certificates where users_id='" . $app_user['id'] . "'"
        );
        if (!$cryptopro_certificates = db_fetch_array($cryptopro_certificates_query)) {
            //$has_sign_button = false;
        }

        //check if sign exist
        $check_query = db_query(
            "select id from  app_ext_signed_items where fields_id='" . $options['field']['id'] . "' and entities_id='" . $options['field']['entities_id'] . "' and items_id='" . $options['item']['id'] . "' and users_id='" . $app_user['id'] . "'"
        );
        if ($check = db_fetch_array($check_query)) {
            $has_sign_button = false;
        } elseif ($cfg->get('assigned_to') > 0 and isset(
                $app_fields_cache[$options['field']['entities_id']][$cfg->get(
                    'assigned_to'
                )]
            )) {
            $item_query = db_query(
                "select  e.field_" . $cfg->get(
                    'assigned_to'
                ) . " as assigned_users from app_entity_" . $options['field']['entities_id'] . " e where id='" . $options['item']['id'] . "'"
            );
            if ($item = db_fetch_array($item_query)) {
                if (!in_array($app_user['id'], explode(',', $item['assigned_users']))) {
                    $has_sign_button = false;
                }
            }
        }

        //check if there are data to sign
        if ($has_sign_button and is_array($cfg->get('signature_fields'))) {
            $has_data_to_sign = false;
            foreach ($cfg->get('signature_fields') as $fields_id) {
                if (isset($options['item']['field_' . $fields_id]) and strlen(
                        $options['item']['field_' . $fields_id]
                    )) {
                    $has_data_to_sign = true;
                }
            }

            $has_sign_button = $has_data_to_sign;
        }

        //check button filter
        if ($has_sign_button and !self::check_button_filter($options)) {
            $has_sign_button = false;
        }


        if ($has_sign_button) {
            if (!isset($fields_access_schema_holder[$entities_id])) {
                $fields_access_schema_holder[$entities_id] = users::get_fields_access_schema(
                    $entities_id,
                    $app_user['group_id']
                );
            }

            //add signature button
            if (!isset($fields_access_schema_holder[$entities_id][$options['field']['id']])) {
                $button_title = (strlen($cfg->get('button_icon')) ? app_render_icon(
                            $cfg->get('button_icon')
                        ) . ' ' : '') . (strlen($cfg->get('button_title')) ? $cfg->get(
                        'button_title'
                    ) : TEXT_DIGITAL_SIGNATURE);

                $btn_css = 'btn-color-' . $options['field']['id'];

                $redirect_to = '&redirect_to=items';

                if (isset($options['redirect_to'])) {
                    if (strlen($options['redirect_to']) > 0) {
                        $redirect_to = '&redirect_to=' . $options['redirect_to'];
                    }
                } elseif ($app_module_path == 'items/info') {
                    $redirect_to = '&redirect_to=items_info';
                }

                $redirect_to .= (isset($_POST['page']) ? '&gotopage[' . $options['reports_id'] . ']=' . $_POST['page'] : '');

                $button_html = button_tag(
                    $button_title,
                    url_for(
                        'items/digital_signature',
                        'fields_id=' . $options['field']['id'] . '&path=' . $path_info['full_path'] . $redirect_to
                    ),
                    true,
                    ['class' => 'btn btn-primary btn-sm ' . $btn_css]
                );

                $html .= '<div>' . $button_html . app_button_color_css($cfg->get('button_color'), $btn_css) . '</div>';
            }
        }

        //output signed users
        $export_list = [];

        $html .= '<ul class="list">';

        $signed_query = db_query(
            "select * from  app_ext_signed_items where fields_id='" . $options['field']['id'] . "' and entities_id='" . $options['field']['entities_id'] . "' and items_id='" . $options['item']['id'] . "' order by id"
        );
        while ($signed = db_fetch_array($signed_query)) {
            $html .= '<li><a href="javascript: open_dialog(\'' . url_for(
                    'items/digital_signature_info',
                    'path=' . $path_info['full_path'] . '&signed_items_id=' . $signed['id'] . '&fields_id=' . $options['field']['id']
                ) . '\')"><i class="la la-certificate"></i> ' . $signed['name'] . '</a></li>';

            $export_list[] = $signed['name'];
        }

        $html .= '</ul>';


        if (isset($options['is_export']) or isset($options['is_email'])) {
            return implode(', ', $export_list);
        }

        return $html;
    }

    static function check_button_filter($options)
    {
        global $sql_query_having;

        $field_id = $options['field']['id'];
        $entities_id = $options['field']['entities_id'];

        $reports_info_query = db_query(
            "select * from app_reports where entities_id='" . db_input(
                $entities_id
            ) . "' and reports_type='fieldfilter" . $field_id . "'"
        );
        if ($reports_info = db_fetch_array($reports_info_query)) {
            $reports_fileds = [];
            $filtes_query = db_query(
                "select fields_id from app_reports_filters where reports_id='" . $reports_info['id'] . "'"
            );
            while ($filtes = db_fetch_array($filtes_query)) {
                $reports_fileds[] = $filtes['fields_id'];
            }

            $listing_sql_query = "e.id='" . $options['item']['id'] . "'";
            $listing_sql_query_having = '';

            $listing_sql_select = fieldtype_formula::prepare_query_select(
                $reports_info['entities_id'],
                '',
                false,
                ['fields_in_query' => implode(',', $reports_fileds)]
            );

            $listing_sql_query = reports::add_filters_query($reports_info['id'], $listing_sql_query);

            //prepare having query for formula fields
            if (isset($sql_query_having[$reports_info['entities_id']])) {
                $listing_sql_query_having = reports::prepare_filters_having_query(
                    $sql_query_having[$reports_info['entities_id']]
                );
            }

            $listing_sql = "select  e.* " . $listing_sql_select . " from app_entity_" . $reports_info['entities_id'] . " e where " . $listing_sql_query . $listing_sql_query_having;
            $items_query = db_query($listing_sql, false);
            if ($item = db_fetch_array($items_query)) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    static function has_sign_access($entities_id, $items_id, $fields_id)
    {
        global $app_user;

        //check if field exist
        $field_query = db_query("select * from app_fields where id='" . $fields_id . "'");
        if (!$field = db_fetch_array($field_query)) {
            return false;
        }

        //check access to field
        $fields_access_schema = users::get_fields_access_schema($entities_id, $app_user['group_id']);

        if (isset($fields_access_schema[$fields_id])) {
            return false;
        }

        //check button filters
        $options['field']['id'] = $fields_id;
        $options['field']['entities_id'] = $entities_id;
        $options['item']['id'] = $items_id;

        if (!self::check_button_filter($options)) {
            return false;
        }

        return true;
    }

    static function reset_signature_if_data_changed($entities_id, $items_id, $previous_item_info)
    {
        global $app_fields_cache;

        if (isset($app_fields_cache[$entities_id])) {
            foreach ($app_fields_cache[$entities_id] as $fields) {
                if ($fields['type'] == 'fieldtype_digital_signature') {
                    $cfg = new fields_types_cfg($fields['configuration']);

                    if (!is_array($cfg->get('signature_fields'))) {
                        continue;
                    }

                    $item_info_query = db_query("select e.* from app_entity_{$entities_id} e where e.id={$items_id}");
                    if ($item_info = db_fetch_array($item_info_query)) {
                        foreach ($cfg->get('signature_fields') as $field_id) {
                            //remove signatures if field value changed                            
                            if (isset($item_info['field_' . $field_id]) and $item_info['field_' . $field_id] != $previous_item_info['field_' . $field_id]) {
                                $signed_items_query = db_query(
                                    "select id from  app_ext_signed_items where fields_id='" . $fields['id'] . "' and entities_id='" . $entities_id . "' and items_id='" . $items_id . "'"
                                );
                                while ($signed_items = db_fetch_array($signed_items_query)) {
                                    db_query("delete from app_ext_signed_items where id='" . $signed_items['id'] . "'");
                                    db_query(
                                        "delete from app_ext_signed_items_signatures where signed_items_id='" . $signed_items['id'] . "'"
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}