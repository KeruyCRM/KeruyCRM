<?php

namespace Tools\FieldsTypes;

class Fieldtype_users_ajax
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_USERS_AJAX_TITLE];
    }

    public function get_configuration($params = [])
    {
        $entity_info = db_find('app_entities', $params['entities_id']);

        $cfg = [];
        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DISPLAY_USERS_AS,
            'name' => 'display_as',
            'type' => 'dropdown',
            'params' => ['class' => 'form-control input-xlarge'],
            'choices' => [
                'dropdown' => \K::$fw->TEXT_DISPLAY_USERS_AS_DROPDOWN,
                'dropdown_multiple' => \K::$fw->TEXT_DISPLAY_USERS_AS_DROPDOWN_MULTIPLE
            ]
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DEFAULT_TEXT,
            'name' => 'default_text',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_DEFAULT_TEXT_INFO,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_NAME,
            'name' => 'hide_field_name',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_NAME_TIP
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DISABLE_NOTIFICATIONS,
            'name' => 'disable_notification',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_DISABLE_NOTIFICATIONS_FIELDS_INFO
        ];

        if ($entity_info['parent_id'] > 0) {
            $cfg[\K::$fw->TEXT_SETTINGS][] = [
                'title' => \K::$fw->TEXT_DISABLE_USERS_DEPENDENCY,
                'name' => 'disable_dependency',
                'type' => 'checkbox',
                'tooltip_icon' => \K::$fw->TEXT_DISABLE_USERS_DEPENDENCY_INFO
            ];
        }

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_PLUS_BUTTON,
            'name' => 'hide_plus_button',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_ADMIN,
            'name' => 'hide_admin',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_AUTHORIZED_USER_BY_DEFAULT,
            'name' => 'authorized_user_by_default',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_AUTHORIZED_USER_BY_DEFAULT_INFO
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_USERS_GROUPS,
            'name' => 'use_groups',
            'type' => 'dropdown',
            'choices' => access_groups::get_choices(false),
            'tooltip_icon' => \K::$fw->TEXT_USE_GROUPS_TIP,
            'params' => ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
        ];

        $choices = [];

        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in (" . fields_types::get_types_for_search_list(
            ) . ") and  f.entities_id=1 and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = fields_types::get_option($fields['type'], 'name', $fields['name']);
        }

        $cfg[\K::$fw->TEXT_LIST][] = [
            'title' => \K::$fw->TEXT_SEARCH_BY_FIELDS,
            'name' => 'fields_for_search',
            'type' => 'dropdown',
            'choices' => $choices,
            'tooltip_icon' => \K::$fw->TEXT_SEARCH_BY_FIELDS_INFO,
            'params' => ['class' => 'form-control chosen-select input-xlarge', 'multiple' => 'multiple']
        ];

        $cfg[\K::$fw->TEXT_LIST][] = [
            'title' => \K::$fw->TEXT_HEADING_TEMPLATE . fields::get_available_fields_helper(
                    1,
                    'fields_configuration_heading_template'
                ),
            'name' => 'heading_template',
            'type' => 'textarea',
            'tooltip_icon' => \K::$fw->TEXT_HEADING_TEMPLATE_INFO,
            'tooltip' => \K::$fw->TEXT_ENTER_TEXT_PATTERN_INFO,
            'params' => ['class' => 'form-control code']
        ];

        $cfg[\K::$fw->TEXT_LIST][] = [
            'title' => \K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_WHERE_QUERY,
            'name' => 'mysql_query_where',
            'type' => 'textarea',
            'tooltip' => \K::$fw->TEXT_FIELDTYPE_ENTITY_MYSQL_QUERY_TIP,
            'params' => ['class' => 'form-control code']
        ];

        $choices = [];

        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where is_heading = 0 and f.type not in ('fieldtype_action','fieldtype_parent_item_id') and  f.entities_id=1 and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = fields_types::get_option(
                    $fields['type'],
                    'name',
                    $fields['name']
                ) . ' (#' . $fields['id'] . ')';
        }

        $cfg[\K::$fw->TEXT_DISPLAY_SETTINGS][] = [
            'title' => \K::$fw->TEXT_FIELDS_IN_POPUP,
            'name' => 'fields_in_popup',
            'type' => 'dropdown',
            'choices' => $choices,
            'tooltip_icon' => \K::$fw->TEXT_FIELDS_IN_POPUP_RELATED_ITEMS,
            'tooltip' => \K::$fw->TEXT_SORT_ITEMS_IN_LIST,
            'params' => ['class' => 'form-control chosen-select chosen-sortable input-xlarge', 'multiple' => 'multiple']
        ];

        $cfg[\K::$fw->TEXT_DISPLAY_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HEADING_TEMPLATE . fields::get_available_fields_helper(
                    1,
                    'fields_configuration_heading_template_display'
                ),
            'name' => 'heading_template_display',
            'type' => 'textarea',
            'tooltip_icon' => \K::$fw->TEXT_HEADING_TEMPLATE_INFO,
            'tooltip' => \K::$fw->TEXT_ENTER_TEXT_PATTERN_INFO,
            'params' => ['class' => 'form-control code']
        ];

        $cfg[\K::$fw->TEXT_DISPLAY_SETTINGS][] = [
            'title' => \K::$fw->TEXT_COPY_VALUES .
                fields::get_available_fields_helper(
                    1,
                    'fields_configuration_copy_values',
                    entities::get_name_by_id(1)
                ) .
                '<div style="padding-top: 2px;">' . fields::get_available_fields_helper(
                    $_POST['entities_id'],
                    'fields_configuration_copy_values',
                    entities::get_name_by_id($_POST['entities_id'])
                ) . '</div>',
            'name' => 'copy_values',
            'type' => 'textarea',
            'tooltip' => \K::$fw->TEXT_COPY_FIELD_VALUES_INFO,
            'params' => ['class' => 'form-control input-xlarge code']
        ];

        $cfg[\K::$fw->TEXT_DISPLAY_SETTINGS][] = [
            'type' => 'html',
            'html' => input_hidden_tag('fields_configuration[entity_id]', 1)
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        global $app_module_path, $app_layout, $current_path_array, $app_action, $app_session_token, $app_users_cache, $app_user;

        $cfg = new \Tools\Fields_types_cfg($field['configuration']);

        $html_on_change = '';

        $entity_info = db_find('app_entities', 1);
        $field_entity_info = db_find('app_entities', $field['entities_id']);

        $add_empty = ($field['is_required'] == 1 ? false : true);

        $attributes = [
            'class' => 'form-control ' . $cfg->get(
                    'width'
                ) . ' fieldtype_entity_ajax field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')
        ];

        if ($cfg->get('display_as') == 'dropdown_multiple') {
            $attributes['multiple'] = 'multiple';
            $attributes['data-placeholder'] = \K::$fw->TEXT_ENTER_VALUE;
            $add_empty = false;

            $field_name = 'fields[' . $field['id'] . '][]';
        } else {
            $field_name = 'fields[' . $field['id'] . ']';
        }

        $choices = [];

        $value = '';

        if (strlen($obj['field_' . $field['id']])) {
            $value = $obj['field_' . $field['id']];
        } elseif ($cfg->get('authorized_user_by_default') == 1) {
            $value = $app_user['id'];
        }

        if (strlen($value)) {
            $listing_sql = "select  e.* from app_entity_1 e  where id in (" . $value . ")";

            $items_query = db_query($listing_sql, false);
            while ($item = db_fetch_array($items_query)) {
                $heading = self::render_heading_template($item);

                $choices[$item['id']] = $heading['text'];
            }

            if (isset($params['is_new_item']) and $params['is_new_item'] == 1 and is_numeric($value)) {
                $html_on_change .= '$("#fields_' . $field['id'] . '_select2_on").load("' . url_for(
                        'dashboard/select2_json',
                        'action=copy_values&form_type=items/render_field_value&entity_id=1&field_id=' . $field['id']
                    ) . '",{item_id:' . $value . '})' . "\n";
            }
        }

        $button_add_html = '';
        if ($cfg->get(
                'hide_plus_button'
            ) != 1 and isset($current_path_array) and $app_action != 'account' and $app_action != 'comments_form' and $app_action != 'processes' and $app_layout != 'public_layout.php' and users::has_access_to_entity(
                1,
                'create'
            ) and !isset($_GET['is_submodal']) and ($entity_info['parent_id'] == 0)) {
            $url_params = 'is_submodal=true&redirect_to=parent_modal&refresh_field=' . $field['id'] . '&path=1';

            $submodal_url = url_for('items/form', $url_params);

            $button_add_html = '<button type="button" class="btn btn-default btn-submodal-open btn-submodal-open-chosen" data-parent-entity-item-id=0 data-field-id="' . $field['id'] . '" data-submodal-url="' . $submodal_url . '"><i class="fa fa-plus" aria-hidden="true"></i></button>';
        }

        if (strlen($button_add_html)) {
            $html = '
                <div class="dropdown-with-plus-btn ' . $cfg->get('width') . '">
                    <div class="left">' . select_tag($field_name, $choices, $value, $attributes) . '</div>
                    <div class="right">' . $button_add_html . '</div>
                 </div>';
        } else {
            $html = select_tag($field_name, $choices, $value, $attributes);
        }

        $html .= '<div id="fields_' . $field['id'] . '_select2_on"></div>';

        $html_width = '';

        if (is_mobile()) {
            $html_width = '
    			$("#field_' . $field['id'] . '_td").width($("#ajax-modal").width());
    			';
        }

        //remove ruquired errro msg
        $html_on_change .= '
    			$("#fields_' . $field['id'] . '").change(function (e) {
                            $("#fields_' . $field['id'] . '-error").remove();
                        });
    			';

        if (strlen($cfg->get('copy_values'))) {
            $html_on_change .= '
    			$("#fields_' . $field['id'] . '").on("select2:select", function (e) {
      			var data = e.params.data;
    				$("#fields_' . $field['id'] . '_select2_on").load("' . url_for(
                    'dashboard/select2_json',
                    'action=copy_values&form_type=' . $app_module_path . '&entity_id=1&field_id=' . $field['id']
                ) . '",{item_id:data.id})	
      		});
    			';
        }

        $html .= '
    	<script>
    	var current_from_id = $("#fields_' . $field['id'] . '").closest("form").attr("id");
    	    
    	$(function(){
    	    
	    	$("#fields_' . $field['id'] . '").select2({
		      width: ' . (is_mobile() ? '$("body").width()-70' : '"100%"') . ',
		      ' . ((in_array($app_layout, ['public_layout.php']) or in_array($app_module_path, ['users/account']
                )) ? '' : 'dropdownParent: $("#ajax-modal"),') . '
		      "language":{
		        "noResults" : function () { return "' . addslashes(TEXT_NO_RESULTS_FOUND) . '"; },
		    		"searching" : function () { return "' . addslashes(TEXT_SEARCHING) . '"; },
		    		"errorLoading" : function () { return "' . addslashes(TEXT_RESULTS_COULD_NOT_BE_LOADED) . '"; },
		    		"loadingMore" : function () { return "' . addslashes(TEXT_LOADING_MORE_RESULTS) . '"; }
		      },
		    	' . ($cfg->get('display_as') == 'dropdown' ? 'allowClear: true,' : '') . '
		    	placeholder: "' . addslashes($cfg->get('default_text')) . '",
		      ajax: {
        		url: "' . url_for(
                'dashboard/select2_users_json',
                'action=select_items&form_type=' . $app_module_path . '&entity_id=1&field_id=' . $field['id'] . '&parent_entity_item_id=' . $params['parent_entity_item_id']
            ) . '",
        		dataType: "json",
        		type: "POST",
        		data: function (params) {
				      var query = {
				        search: params.term,
				        page: params.page || 1,
        		        form_data: $("#"+current_from_id).serializeArray(),
				      }
        		    
				      // Query parameters will be ?search=[term]&page=[page]
				      return query;
				    },
        	},
					templateResult: function (d) { return $(d.html); },
	    	});
        		    
        ' . $html_on_change . '
            
        ' . $html_width . '
      })
            
    	</script>
    ';

        return $html;
    }

    public function process($options)
    {
        global $app_send_to, $app_send_to_new_assigned;

        $cfg = new \Tools\Fields_types_cfg($options['field']['configuration']);

        if ($cfg->get('disable_notification') != 1) {
            if (is_array($options['value'])) {
                $app_send_to = array_merge($options['value'], $app_send_to);
            } else {
                $app_send_to[] = $options['value'];
            }
        }

        $value = (is_array($options['value']) ? implode(',', $options['value']) : $options['value']);

        //check if value changed
        if ($cfg->get('disable_notification') != 1) {
            if (!$options['is_new_item']) {
                if ($value != $options['current_field_value']) {
                    foreach (array_diff(explode(',', $value), explode(',', $options['current_field_value'])) as $v) {
                        $app_send_to_new_assigned[] = $v;
                    }
                }
            }
        }

        return $value;
    }

    public function output($options)
    {
        global $app_users_cache, $app_user;

        if (!strlen($options['value'])) {
            return '';
        }

        $cfg = new \Tools\Fields_types_cfg($options['field']['configuration']);

        //return just name if export
        if (isset($options['is_export']) or isset($options['is_email'])) {
            $users_list = [];
            foreach (explode(',', $options['value']) as $id) {
                if (isset($app_users_cache[$id])) {
                    $users_list[] = $app_users_cache[$id]['name'];
                }
            }

            return implode(', ', $users_list);
        }

        $fields_in_popup_cfg = '';

        if (is_array($cfg->get('fields_in_popup'))) {
            $fields_in_popup_cfg = implode(',', $cfg->get('fields_in_popup'));
        }

        $html = '<ul class="list">';

        $items_query = db_query("select e.* from app_entity_1 e where e.id in (" . $options['value'] . ")");
        while ($items = db_fetch_array($items_query)) {
            $fields_access_schema = users::get_fields_access_schema(1, $app_user['group_id']);

            $fields_in_popup = fields::get_items_fields_data_by_id(
                $items,
                $fields_in_popup_cfg,
                1,
                $fields_access_schema
            );
            $popup_html = '';
            if (count($fields_in_popup) > 0) {
                $popup_html = app_render_fields_popup_html($fields_in_popup);
            }

            if (strlen($cfg->get('heading_template_display'))) {
                $heading = self::render_heading_template($items, $cfg->get('heading_template_display'));
                $html .= '<li ' . $popup_html . '>' . $heading['html'] . '</li>';
            } else {
                $heading = self::render_heading_template($items);
                $html .= '<li><span ' . $popup_html . '>' . $heading['html'] . '</span></li>';
            }
        }

        $html .= '</ul>';

        return $html;
    }

    public function reports_query($options)
    {
        global $app_user;

        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        if (strlen($filters['filters_values']) > 0) {
            $filters['filters_values'] = str_replace('current_user_id', $app_user['id'], $filters['filters_values']);

            $sql_query[] = "(select count(*) from app_entity_" . $options['entities_id'] . "_values as cv where cv.items_id=e.id and cv.fields_id='" . db_input(
                    $options['filters']['fields_id']
                ) . "' and cv.value in (" . $filters['filters_values'] . ")) " . ($filters['filters_condition'] == 'include' ? '>0' : '=0');
        }

        return $sql_query;
    }

    public static function render_heading_template($item, $heading_template = '')
    {
        global $app_users_cache;

        $html = '';
        $text = '';

        if (strlen($heading_template)) {
            $fieldtype_text_pattern = new fieldtype_text_pattern();
            $html = $fieldtype_text_pattern->output_singe_text($heading_template, 1, $item);
        }

        $text = $app_users_cache[$item['id']]['name'];

        return ['text' => $text, 'html' => '<div>' . (strlen($html) ? $html : $text) . '</div>'];
    }
}