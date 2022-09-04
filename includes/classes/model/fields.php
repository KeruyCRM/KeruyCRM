<?php

class fields
{

    //get heading fields chace for all entities 
    public static function get_heading_fields()
    {
        $cache = [];
        $fields_query = db_query("select * from app_fields where is_heading=1");
        while ($fields = db_fetch_array($fields_query)) {
            $cache['id'][$fields['id']] = $fields;
            $cache['entities_id'][$fields['entities_id']] = $fields['id'];
        }

        return $cache;
    }

    static function not_formula_fields_cache()
    {
        $cache = [];
        $fields_query = db_query(
            "select * from app_fields where type not in ('fieldtype_formula','fieldtype_dynamic_date')"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $cache[$fields['entities_id']][] = $fields['id'];
        }

        return $cache;
    }

    static function formula_fields_cache()
    {
        $cache = [];
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_formula','fieldtype_dynamic_date')"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $cache[$fields['entities_id']][] = [
                'id' => $fields['id'],
                'name' => $fields['name'],
                'configuration' => $fields['configuration'],
            ];
        }

        return $cache;
    }

    static function get_cache()
    {
        $cache = [];
        $fields_query = db_query("select id, type, name, entities_id, configuration from app_fields");
        while ($fields = db_fetch_array($fields_query)) {
            $cache[$fields['entities_id']][$fields['id']] = [
                'id' => $fields['id'],
                'type' => $fields['type'],
                'name' => (strlen($fields['name']) ? $fields['name'] : str_replace('fieldtype_', '', $fields['type'])),
                'entities_id' => $fields['entities_id'],
                'configuration' => $fields['configuration'],
            ];

            if (in_array(
                $fields['type'],
                [
                    'fieldtype_id',
                    'fieldtype_date_added',
                    'fieldtype_date_updated',
                    'fieldtype_created_by',
                    'fieldtype_parent_item_id'
                ]
            )) {
                $cache[$fields['entities_id']][$fields['type']] = [
                    'id' => $fields['id'],
                    'type' => $fields['type'],
                    'name' => (strlen($fields['name']) ? $fields['name'] : str_replace(
                        'fieldtype_',
                        '',
                        $fields['type']
                    )),
                    'entities_id' => $fields['entities_id'],
                    'configuration' => $fields['configuration'],
                ];
            }
        }

        return $cache;
    }

    public static function get_choices($entities_id, $cfg = [])
    {
        global $app_entities_cache;

        $cfg = new settings($cfg, [
            'include_paretns' => 0,
        ]);

        $choices = [];

        if ($cfg->get('include_parents') == true) {
            $parents = entities::get_parents($entities_id, [$entities_id]);

            //print_rr($parents);

            foreach ($parents as $entity_id) {
                if ($entity_id == $entities_id) {
                    $where_sql = " and f.type not in ('fieldtype_action')";
                } else {
                    $where_sql = " and f.type not in ('" . implode("','", fields_types::get_reserved_types()) . "')";
                }

                $fields_query = self::get_query($entity_id, $where_sql);
                while ($v = db_fetch_array($fields_query)) {
                    $choices[$app_entities_cache[$entity_id]['name']][$v['id']] = fields_types::get_option(
                        $v['type'],
                        'name',
                        $v['name']
                    );
                }
            }
        } else {
            $fields_query = self::get_query($entities_id);
            while ($v = db_fetch_array($fields_query)) {
                $choices[$v['id']] = fields_types::get_option($v['type'], 'name', $v['name']);
            }
        }

        return $choices;
    }

    public static function get_available_fields($entities_id, $required_types, $warn_message)
    {
        $html = '';
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in (" . $required_types . ") and f.entities_id='" . $entities_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $html .= '
        <tr>
          <td>' . $fields['id'] . '</td>
          <td>' . $fields['name'] . '</td>
        </tr>
      ';
        }

        if (strlen($html) > 0) {
            return '
        <table class="table">
          <tr>
            <th>' . TEXT_ID . '</th>
            <th>' . TEXT_NAME . '</th>
          </tr>
          ' . $html . '
        </table>
      ';
        } else {
            return '<div class="alert alert-warning">' . $warn_message . '</div>';
        }
    }

    public static function check_before_delete($entity_id, $id)
    {
        global $app_fields_cache;

        $msg = '';

        //check formulas
        $use_in_formula = [];
        foreach ($app_fields_cache[$entity_id] as $field) {
            $cfg = new fields_types_cfg($field['configuration']);

            if (strlen($cfg->get('formula')) and strstr($cfg->get('formula'), '[' . $id . ']')) {
                $use_in_formula[] = $field['name'];
            }
        }

        if (count($use_in_formula)) {
            $msg .= TEXT_FIELD_USING_IN_FORMULA . ': ' . implode(', ', $use_in_formula);
        }

        if (strlen($msg)) {
            $name = $app_fields_cache[$entity_id][$id]['name'];
            $msg = sprintf(
                    TEXT_YOU_CANT_DELETE_FIELD,
                    $name
                ) . '<br><br><p class="alert alert-warning">' . $msg . '</p>';
        }

        return $msg;
    }

    public static function get_name($v)
    {
        return fields_types::get_option($v['type'], 'name', $v['name']);
    }

    public static function get_name_by_id($id)
    {
        $field_query = db_query("select id, type, name from app_fields where id='" . $id . "'");
        if ($field = db_fetch_array($field_query)) {
            return fields_types::get_option($field['type'], 'name', $field['name']);
        } else {
            return '';
        }
    }

    public static function get_name_cache()
    {
        $cache = [];
        $fields_query = db_query("select * from app_fields");
        while ($fields = db_fetch_array($fields_query)) {
            $cache[$fields['id']] = fields_types::get_option($fields['type'], 'name', $fields['name']);
        }

        return $cache;
    }

    public static function get_heading_id($entity_id)
    {
        global $app_heading_fields_id_cache;

        if (isset($app_heading_fields_id_cache[$entity_id])) {
            return $app_heading_fields_id_cache[$entity_id];
        } else {
            return false;
        }
    }

    public static function get_last_sort_number($forms_tabls_id)
    {
        $v = db_fetch_array(
            db_query(
                "select max(sort_order) as max_sort_order from app_fields where forms_tabs_id = '" . db_input(
                    $forms_tabls_id
                ) . "'"
            )
        );

        return $v['max_sort_order'];
    }

    public static function render_required_messages($entities_id)
    {
        $html = '';

        $fields_query = db_query(
            "select f.id, f.type, f.required_message, f.configuration from app_fields f where f.type not in (" . fields_types::get_reserved_types_list(
            ) . ") and  f.entities_id='" . db_input($entities_id) . "' order by f.sort_order, f.name"
        );
        while ($v = db_fetch_array($fields_query)) {
            $cfg = new fields_types_cfg($v['configuration']);

            $attributes = [];

            if (strlen($cfg->get('min_value'))) {
                $attributes[] = 'min: jQuery.validator.format("' . htmlspecialchars(TEXT_MIN_VALUE_WARNING) . '")';
            }

            if (strlen($cfg->get('max_value'))) {
                $attributes[] = 'max: jQuery.validator.format("' . htmlspecialchars(TEXT_MAX_VALUE_WARNING) . '")';
            }

            if (strlen($v['required_message']) > 0) {
                $attributes[] = 'required: "' . str_replace(["\n", "\r", "\n\r", '<br><br>'],
                        "<br>",
                        htmlspecialchars($v['required_message'])) . '"';
            }

            if (count($attributes)) {
                switch ($v['type']) {
                    case 'fieldtype_dropdown_multiple':
                    case 'fieldtype_checkboxes':
                        $name = 'fields[' . $v['id'] . '][]';
                        break;
                    default:
                        $name = 'fields[' . $v['id'] . ']';
                        break;
                }

                $html .= '\'' . $name . '\':{' . implode(',', $attributes) . '},' . "\n";
            }
        }

        return $html;
    }

    public static function render_required_ckeditor_rules($entities_id)
    {
        $html = '';

        $fields_query = db_query(
            "select f.* from app_fields f where f.type = 'fieldtype_textarea_wysiwyg' and is_required=1 and  f.entities_id='" . db_input(
                $entities_id
            ) . "' order by f.sort_order, f.name"
        );
        while ($v = db_fetch_array($fields_query)) {
            $html .= '
          "fields[' . $v['id'] . ']": { 
            required: function(element){
              CKEDITOR_holders["fields_' . $v['id'] . '"].updateElement();              
              return true;             
            }
          },' . "\n";
        }

        return $html;
    }

    public static function render_unique_fields_rules($entities_id, $item_id = false)
    {
        global $app_items_form_name, $public_form, $app_session_token, $app_path;

        if ($app_items_form_name == 'registration_form') {
            $url = url_for("users/registration", "action=check_unique&entities_id=1");
        } elseif ($app_items_form_name == 'public_form') {
            $url = url_for(
                "ext/public/form",
                "action=check_unique&entities_id=" . $public_form["entities_id"] . "&id=" . $public_form['id']
            );
        } elseif ($app_items_form_name == 'account_form') {
            $url = url_for("users/account", "action=check_unique&entities_id=1");
        } else {
            $url = url_for(
                "items/items",
                "action=check_unique&path=" . $app_path . ($item_id ? "&id=" . $item_id : "")
            );
        }

        $html = '';

        $fields_query = db_query(
            "select f.* from app_fields f where  f.entities_id='" . db_input(
                $entities_id
            ) . "' order by f.sort_order, f.name"
        );
        while ($v = db_fetch_array($fields_query)) {
            $cfg = new settings($v['configuration']);
            if ($cfg->get('is_unique') > 0) {
                $html .= '
                    "fields[' . $v['id'] . ']": { 
                        remote:{
                            type: "POST",
                            url: "' . $url . '",
                            data: {
                                fields_id: ' . $v['id'] . ',
                                fields_value:  function () { 
                                  return $("#fields_' . $v['id'] . '").val() 
                                },
                                form_session_token: "' . $app_session_token . '",
                                unique_for_each_parent: function () { 
                                    return $("#fields_' . $v['id'] . '").attr("unique-for-each-parent") 
                                },
                                parent_item_id: $("#parent_item_id").length>0 ? $("#parent_item_id").val() : 0, //using for public form
                            },
                            beforeSend: function(){
                                $("#is-unique-checking-success-' . $v['id'] . '").remove()
                                $("#fields_' . $v['id'] . '").after(\'<div id="is-unique-checking-process-' . $v['id'] . '" class="fa fa-spinner fa-spin is-unique-checking-process"></div>\')
                            },
                            complete: function(data){
                                $("#is-unique-checking-process-' . $v['id'] . '").remove()                                    
                                if(data.responseText=="true")
                                {
                                    $("#fields_' . $v['id'] . '").after(\'<div id="is-unique-checking-success-' . $v['id'] . '" class="fa fa-check is-unique-checking-success"></div>\')
                                }
                            }
                      }
                    },' . "\n";
            }
        }

        return $html;
    }

    public static function get_search_feidls($entity_id)
    {
        global $app_user;

        $fields_access_schema = users::get_fields_access_schema($entity_id, $app_user['group_id']);

        $search_fields = [];

        $fields_query = db_query(
            "select f.id, f.type, f.configuration, f.name, f.is_heading, t.name as tab_name from app_fields f, app_forms_tabs t where f.entities_id='" . db_input(
                $entity_id
            ) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($v = db_fetch_array($fields_query)) {
            //check field access
            if (isset($fields_access_schema[$v['id']])) {
                if ($fields_access_schema[$v['id']] == 'hide') {
                    continue;
                }
            }

            $cfg = fields_types::parse_configuration($v['configuration']);
            if (isset($cfg['allow_search'])) {
                $search_fields[] = [
                    'id' => $v['id'],
                    'type' => $v['type'],
                    'name' => fields_types::get_option($v['type'], 'name', $v['name']),
                    'is_heading' => $v['is_heading'],
                    'configuration' => $v['configuration'],
                ];
            }
        }

        return $search_fields;
    }

    public static function get_filters_choices($entity_id, $show_parent_item_fitler = true, $exclude = "")
    {
        global $app_user, $app_redirect_to;

        $entity_info = db_find('app_entities', $entity_id);

        $fields_access_schema = users::get_fields_access_schema($entity_id, $app_user['group_id']);

        $types_for_filters_list = fields_types::get_types_for_filters_list();

        $filters_panels_fields = ((isset($_GET['path']) and $app_redirect_to == 'listing') ? filters_panels::get_fields_list(
            $entity_id
        ) : []);

        //include fieldtype_parent_item_id only for sub entities
        if ($entity_info['parent_id'] > 0 and $show_parent_item_fitler) {
            $types_for_filters_list .= ", 'fieldtype_parent_item_id'";
        }

        //include special filters for Users
        if ($entity_id == 1) {
            $types_for_filters_list .= ", 'fieldtype_user_accessgroups', 'fieldtype_user_status'";
        }

        $choices = [];
        $choices[''] = '';
        $fields_query = db_query(
            "select f.*, t.name as tab_name, if(f.type in ('fieldtype_id','fieldtype_date_added','fieldtype_date_updated','fieldtype_created_by','fieldtype_parent_item_id'),-1,t.sort_order) as tab_sort_order from app_fields f, app_forms_tabs t where f.type in (" . $types_for_filters_list . ") " . (strlen(
                $exclude
            ) ? " and f.type not in ({$exclude})" : '') . " and f.entities_id='" . db_input(
                $entity_id
            ) . "' and f.forms_tabs_id=t.id order by tab_sort_order, t.name, f.sort_order, f.name"
        );
        while ($v = db_fetch_array($fields_query)) {
            //check field access
            if (isset($fields_access_schema[$v['id']])) {
                if ($fields_access_schema[$v['id']] == 'hide') {
                    continue;
                }
            }

            //skip fields in quick filter panel
            if (in_array($v['id'], $filters_panels_fields)) {
                continue;
            }

            $choices[$v['id']] = fields_types::get_option($v['type'], 'name', $v['name']);
        }

        return $choices;
    }

    public static function check_if_type_changed($field_id, $new_type)
    {
        $field_info_query = db_query("select * from app_fields where id='" . db_input($field_id) . "'");
        if ($field_info = db_fetch_array($field_info_query)) {
            //check if field type changed
            if ($field_info['type'] != $new_type) {
                //delete index
                $check_query = db_query(
                    "SHOW INDEX FROM app_entity_" . $field_info['entities_id'] . " WHERE KEY_NAME = 'idx_field_" . $field_info['id'] . "'"
                );
                if ($check = db_fetch_array($check_query)) {
                    db_query(
                        "ALTER TABLE app_entity_" . $field_info['entities_id'] . " DROP INDEX idx_field_" . $field_info['id']
                    );
                }

                //prepare db field type
                db_query(
                    "ALTER TABLE app_entity_" . $field_info['entities_id'] . " CHANGE field_" . $field_info['id'] . " field_" . $field_info['id'] . " " . entities::prepare_field_type(
                        $new_type
                    ) . " NOT NULL;"
                );

                //delete all filters for this field type since they are will not work correclty
                db_delete_row('app_reports_filters', $field_id, 'fields_id');

                //add index
                entities::prepare_field_index($field_info['entities_id'], $field_info['id'], $new_type);
            }
        }
    }

    public static function get_items_fields_data_by_id(
        $item,
        $fields_list = '',
        $entities_id = 0,
        $fields_access_schema = []
    ) {
        $data = [];

        if (strlen($fields_list) > 0) {
            $fields_query = db_query(
                "select f.* from app_fields f, app_forms_tabs t where  f.id in (" . $fields_list . ") and  f.entities_id='" . db_input(
                    $entities_id
                ) . "' and f.forms_tabs_id=t.id order by field(f.id," . $fields_list . ")"
            );
            while ($field = db_fetch_array($fields_query)) {
                //check field access
                if (isset($fields_access_schema[$field['id']])) {
                    if ($fields_access_schema[$field['id']] == 'hide') {
                        continue;
                    }
                }

                if (in_array($field['type'], fields_types::get_reserved_data_types())) {
                    $value = $item[fields_types::get_reserved_filed_name_by_type($field['type'])];
                } else {
                    $value = $item['field_' . $field['id']];
                }

                $output_options = [
                    'class' => $field['type'],
                    'value' => $value,
                    'field' => $field,
                    'item' => $item,
                    'is_listing' => true,
                    'is_export' => true,
                    'is_print' => true,
                    'redirect_to' => '',
                    'reports_id' => 0,
                    'path' => ''
                ];

                $data[] = [
                    'name' => fields_types::get_option($field['type'], 'name', $field['name']),
                    'value' => fields_types::output($output_options),
                    'type' => $field['type'],
                ];
            }
        }

        return $data;
    }

    public static function get_items_fields_fresh_data(
        $item,
        $fields_list = '',
        $entities_id = 0,
        $fields_access_schema = []
    ) {
        $data = [];

        if (strlen($fields_list) > 0) {
            $fields_query = db_query(
                "select f.* from app_fields f, app_forms_tabs t where  f.id in (" . $fields_list . ") and  f.entities_id='" . db_input(
                    $entities_id
                ) . "' and f.forms_tabs_id=t.id order by field(f.id," . $fields_list . ")"
            );
            while ($field = db_fetch_array($fields_query)) {
                //check field access
                if (isset($fields_access_schema[$field['id']])) {
                    if ($fields_access_schema[$field['id']] == 'hide') {
                        continue;
                    }
                }

                if (in_array($field['type'], fields_types::get_reserved_data_types())) {
                    $value = $item[fields_types::get_reserved_filed_name_by_type($field['type'])];
                } else {
                    $value = $item['field_' . $field['id']];
                }

                $output_options = [
                    'class' => $field['type'],
                    'value' => $value,
                    'field' => $field,
                    'item' => $item,
                    'is_listing' => true,
                    'redirect_to' => '',
                    'reports_id' => 0,
                    'path' => ''
                ];

                $data[] = [
                    'name' => fields_types::get_option($field['type'], 'name', $field['name']),
                    'value' => fields_types::output($output_options),
                    'type' => $field['type'],
                ];
            }
        }

        return $data;
    }

    public static function get_field_choices_background_data($field_id)
    {
        $data = [];

        $field_info_query = db_query("select * from app_fields where id='" . $field_id . "'");
        if ($field_info = db_fetch_array($field_info_query)) {
            $cfg = new fields_types_cfg($field_info['configuration']);
            if ($cfg->get('use_global_list') > 0) {
                $choices_query = db_query(
                    "select * from app_global_lists_choices where lists_id = '" . db_input(
                        $cfg->get('use_global_list')
                    ) . "' and length(bg_color)>0"
                );
            } else {
                $choices_query = db_query(
                    "select * from app_fields_choices where fields_id = '" . db_input(
                        $field_id
                    ) . "' and length(bg_color)>0"
                );
            }

            while ($choices = db_fetch_array($choices_query)) {
                $rgb = convert_html_color_to_RGB($choices['bg_color']);

                if (($rgb[0] + $rgb[1] + $rgb[2]) < 480) {
                    $data[$choices['id']] = ['background' => $choices['bg_color'], 'color' => '#ffffff'];
                } else {
                    $data[$choices['id']] = ['background' => $choices['bg_color']];
                }
            }

            return $data;
        }
    }

    public static function get_item_info_tooltip($field)
    {
        $text = '';

        if (strlen($field['tooltip']) and $field['tooltip_in_item_page'] == 1) {
            $text = $field['tooltip'];
        } elseif (strlen($field['tooltip_item_page'])) {
            $text = $field['tooltip_item_page'];
        }

        if (strlen($text)) {
            return ' ' . tooltip_icon($text, 'top');
        } else {
            return '';
        }
    }

    static function get_fields_in_popup_choices($entities_id, $app_id = false)
    {
        $choices = [];
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where is_heading = 0 and f.type not in ('fieldtype_action','fieldtype_parent_item_id') and  f.entities_id='" . $entities_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($v = db_fetch_array($fields_query)) {
            $choices[$v['id']] = fields_types::get_option(
                    $v['type'],
                    'name',
                    $v['name']
                ) . ($app_id ? ' (#' . $v['id'] . ')' : '');
        }

        return $choices;
    }

    static function get_fields_in_listing_choices($entities_id, $app_id = false)
    {
        $choices = [];
        $exclude_fields_types_sql = " and f.type not in ('fieldtype_section','fieldtype_mapbbcode','fieldtype_mind_map')";
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.entities_id='" . db_input(
                $entities_id
            ) . "' and f.forms_tabs_id=t.id {$exclude_fields_types_sql} order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($v = db_fetch_array($fields_query)) {
            $choices[$v['id']] = fields_types::get_option(
                    $v['type'],
                    'name',
                    $v['name']
                ) . ($app_id ? ' (#' . $v['id'] . ')' : '');
        }

        return $choices;
    }

    static function get_available_fields_helper(
        $entities_id,
        $template_field_id,
        $dropdown_title = TEXT_AVAILABLE_FIELDS,
        $use_fieldtypes = [],
        $skip_reserved = false,
        $include_parent = false
    ) {
        global $app_entities_cache;

        $entities_info = db_find('app_entities', $entities_id);

        $unique_id = $entities_id . rand(1000, 9999);

        $where_sql = '';
        if (count($use_fieldtypes)) {
            array_walk($use_fieldtypes, function (&$v, $k) {
                $v = "'{$v}'";
            });

            $where_sql = " and f.type in (" . implode(',', $use_fieldtypes) . ")";
        }

        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_reserved_types_list(
            ) . ") and f.entities_id='" . $entities_id . "' and f.forms_tabs_id=t.id {$where_sql} order by t.sort_order, t.name, f.sort_order, f.name"
        );

        if (db_num_rows($fields_query) == 0) {
            return '';
        }

        $html = '
  			<div class="dropdown">
				  <button class="btn btn-default btn-sm dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				    ' . $dropdown_title . '
				    <span class="caret"></span>
				  </button>
  			<ul class="dropdown-menu" aria-labelledby="dropdownMenu1" style="max-height: 250px; overflow-y: auto">';


        if (!count($use_fieldtypes) and !$skip_reserved) {
            $html .= '  			  			
	  	    <li>
	  				<a href="#" class="insert_to_template_' . $unique_id . '" data-field="[id]">' . TEXT_FIELDTYPE_ID_TITLE . ' [id]</a>
	  	    </li>
	  	    <li>
	  	      <a href="#" class="insert_to_template_' . $unique_id . '" data-field="[date_added]">' . TEXT_FIELDTYPE_DATEADDED_TITLE . ' [date_added]</a>
	  	    </li>
	  	    <li>
	  	      <a href="#" class="insert_to_template_' . $unique_id . '" data-field="[created_by]">' . TEXT_FIELDTYPE_CREATEDBY_TITLE . ' [created_by]</a>
	  	    </li>';


            if ($entities_info['parent_id'] > 0) {
                $html .= '
	  				<li>
		  	      <a href="#" class="insert_to_template_' . $unique_id . '" data-field="[parent_item_id]">' . TEXT_FIELDTYPE_PARENT_ITEM_ID_TITLE . ' [parent_item_id]</a>
		  	    </li>';
            }
        }

        while ($v = db_fetch_array($fields_query)) {
            if ($v['type'] == 'fieldtype_dropdown_multilevel' and count($use_fieldtypes)) {
                $html .= fieldtype_dropdown_multilevel::output_export_template($v);
            } else {
                $html .= '
  		    <li>
  		  		<a href="#"  class="insert_to_template_' . $unique_id . '" data-field="[' . $v['id'] . ']">' . fields_types::get_option(
                        $v['type'],
                        'name',
                        $v['name']
                    ) . ' [' . $v['id'] . ']</a>
  		    </li>';
            }
        }

        //parent fields
        if ($include_parent and $entities_info['parent_id'] > 0) {
            $parent_entity_name = $app_entities_cache[$entities_info['parent_id']]['name'];

            $html .= '
                <li class="divider"></li>
                <li>
                    <a href="#" ><b>' . $parent_entity_name . '</b></a>  		      
                </li>';

            $fields_query = fields::get_query(
                $entities_info['parent_id'],
                "and f.type not in (" . fields_types::get_reserved_types_list() . ")"
            );

            while ($v = db_fetch_array($fields_query)) {
                if ($v['type'] == 'fieldtype_dropdown_multilevel') {
                    $html .= fieldtype_dropdown_multilevel::output_export_template($v);
                } else {
                    $html .= '
                        <li>
                                    <a href="#"  class="insert_to_template_' . $unique_id . '" data-field="[' . $v['id'] . ']">' . fields_types::get_option(
                            $v['type'],
                            'name',
                            $v['name']
                        ) . ' [' . $v['id'] . ']</a>  		      
                        </li>';
                }
            }
        }


        $html .= '</ul></div>';

        $html .= '
  			<script>
  			$(".insert_to_template_' . $unique_id . '").click(function(){
                                
                                html = $(this).attr("data-field").trim();  				
                                
                                if($("#' . $template_field_id . '").hasClass("is_codemirror"))
                                {
                                    editor = myCodeMirror' . str_replace(
                'fields_configuration_',
                '',
                $template_field_id
            ) . ' 
                                    let doc = editor.getDoc();
                                    let cursor = doc.getCursor();
                                    doc.replaceRange(html, cursor);
                                }
                                else
                                {                                    
                                    textarea_insert_at_caret("' . $template_field_id . '",html)  					
                                }
  					
                                //CKEDITOR.instances.description.insertText(html);
                            
                                //return false;
			  })
  			</script>
  			';

        return $html;
    }

    static function get_unique_fields_list($entities_id)
    {
        $list = [];
        $fields_query = db_query("select id, configuration from app_fields where entities_id='" . $entities_id . "'");
        while ($fields = db_fetch_array($fields_query)) {
            $cfg = new fields_types_cfg($fields['configuration']);
            if ($cfg->get('is_unique')) {
                $list[] = $fields['id'];
            }
        }

        return $list;
    }

    static function prepare_field_db_name_by_type($entities_id, $fields_id, $alias = 'e')
    {
        global $app_fields_cache;

        switch ($app_fields_cache[$entities_id][$fields_id]['type']) {
            case 'fieldtype_id':
                $sql = $alias . ".id";
                break;
            case 'fieldtype_created_by':
                $sql = $alias . ".created_by";
                break;
            case 'fieldtype_date_added':
                $sql = $alias . ".date_added";
                break;
            case 'fieldtype_date_updated':
                $sql = $alias . ".date_updated";
                break;
            case 'fieldtype_parent_item_id':
                $sql = $alias . ".parent_item_id";
                break;
            default:
                $sql = $alias . ".field_" . $fields_id;
                break;
        }

        return $sql;
    }

    static function get_query($entities_id, $where_sql = '')
    {
        $reserverd_fields_types = array_merge(fields_types::get_reserved_data_types(), fields_types::get_users_types());
        $reserverd_fields_types_list = "'" . implode("','", $reserverd_fields_types) . "'";

        $fields_query = db_query(
            "select f.*, t.name as tab_name, if(f.type in (" . $reserverd_fields_types_list . "),-1,t.sort_order) as tab_sort_order, fr.sort_order as form_rows_sort_order,right(f.forms_rows_position,1) as forms_rows_pos  from app_fields f left join app_forms_rows fr on fr.id=LEFT(f.forms_rows_position,length(f.forms_rows_position)-2), app_forms_tabs t where f.entities_id='" . $entities_id . "' {$where_sql} and f.forms_tabs_id=t.id order by tab_sort_order, t.name, form_rows_sort_order, forms_rows_pos, f.sort_order, f.name",
            false
        );

        return $fields_query;
    }
}
