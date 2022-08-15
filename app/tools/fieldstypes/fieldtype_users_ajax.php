<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

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
        $entity_info = \K::model()->db_find('app_entities', $params['entities_id']);

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
            'choices' => \Models\Main\Access_groups::get_choices(false),
            'tooltip_icon' => \K::$fw->TEXT_USE_GROUPS_TIP,
            'params' => ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
        ];

        $choices = [];

        $fields_query = \K::model()->db_query_exec(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in (" . \Models\Main\Fields_types::get_types_for_search_list(
            ) . ") and f.entities_id = 1 and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
            null,
            'app_fields,app_forms_tabs'
        );

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $choices[$fields['id']] = \Models\Main\Fields_types::get_option($fields['type'], 'name', $fields['name']);
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
            'title' => \K::$fw->TEXT_HEADING_TEMPLATE . \Models\Main\Fields::get_available_fields_helper(
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

        $fields_query = \K::model()->db_query_exec(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where is_heading = 0 and f.type not in (" . \K::model(
            )->quoteToString(['fieldtype_action', 'fieldtype_parent_item_id']
            ) . ") and f.entities_id = 1 and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
            null,
            'app_fields,app_forms_tabs'
        );

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $choices[$fields['id']] = \Models\Main\Fields_types::get_option(
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
            'title' => \K::$fw->TEXT_HEADING_TEMPLATE . \Models\Main\Fields::get_available_fields_helper(
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
                \Models\Main\Fields::get_available_fields_helper(
                    1,
                    'fields_configuration_copy_values',
                    \Models\Main\Entities::get_name_by_id(1)
                ) .
                '<div style="padding-top: 2px;">' . \Models\Main\Fields::get_available_fields_helper(
                    $_POST['entities_id'],
                    'fields_configuration_copy_values',
                    \Models\Main\Entities::get_name_by_id($_POST['entities_id'])
                ) . '</div>',
            'name' => 'copy_values',
            'type' => 'textarea',
            'tooltip' => \K::$fw->TEXT_COPY_FIELD_VALUES_INFO,
            'params' => ['class' => 'form-control input-xlarge code']
        ];

        $cfg[\K::$fw->TEXT_DISPLAY_SETTINGS][] = [
            'type' => 'html',
            'html' => \Helpers\Html::input_hidden_tag('fields_configuration[entity_id]', 1)
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $html_on_change = '';

        $entity_info = \K::model()->db_find('app_entities', 1);

        $attributes = [
            'class' => 'form-control ' . $cfg->get(
                    'width'
                ) . ' fieldtype_entity_ajax field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')
        ];

        if ($cfg->get('display_as') == 'dropdown_multiple') {
            $attributes['multiple'] = 'multiple';
            $attributes['data-placeholder'] = \K::$fw->TEXT_ENTER_VALUE;

            $field_name = 'fields[' . $field['id'] . '][]';
        } else {
            $field_name = 'fields[' . $field['id'] . ']';
        }

        $choices = [];

        $value = '';

        if (strlen($obj['field_' . $field['id']])) {
            $value = $obj['field_' . $field['id']];
        } elseif ($cfg->get('authorized_user_by_default') == 1) {
            $value = (int)\K::$fw->app_user['id'];
        }

        if (strlen($value)) {
            /*$listing_sql = "select  e.* from app_entity_1 e  where id in (" . $value . ")";

            $items_query = db_query($listing_sql, false);*/

            $items_query = \K::model()->db_fetch('app_entity_1', [
                'id in (' . $value . ')'
            ]);

            //while ($item = db_fetch_array($items_query)) {
            foreach ($items_query as $item) {
                $item = $item->cast();

                $heading = self::render_heading_template($item);

                $choices[$item['id']] = $heading['text'];
            }

            if (isset($params['is_new_item']) and $params['is_new_item'] == 1 and is_numeric($value)) {
                $html_on_change .= '$("#fields_' . $field['id'] . '_select2_on").load("' . \Helpers\Urls::url_for(
                        'main/dashboard/select2_json/copy_values',
                        'form_type=items/render_field_value&entity_id=1&field_id=' . $field['id']
                    ) . '",{item_id:' . $value . '})' . "\n";
            }
        }

        $button_add_html = '';
        if ($cfg->get(
                'hide_plus_button'
            ) != 1 and isset(\K::$fw->current_path_array) and \K::$fw->app_action != 'account' and \K::$fw->app_action != 'comments_form' and \K::$fw->app_action != 'processes' and \K::$fw->app_layout != 'public_layout.php' and \Models\Main\Users\Users::has_access_to_entity(
                1,
                'create'
            ) and !isset(\K::$fw->GET['is_submodal']) and ($entity_info['parent_id'] == 0)) {
            $url_params = 'is_submodal=true&redirect_to=parent_modal&refresh_field=' . $field['id'] . '&path=1';

            $submodal_url = \Helpers\Urls::url_for('main/items/form', $url_params);

            $button_add_html = '<button type="button" class="btn btn-default btn-submodal-open btn-submodal-open-chosen" data-parent-entity-item-id=0 data-field-id="' . $field['id'] . '" data-submodal-url="' . $submodal_url . '"><i class="fa fa-plus" aria-hidden="true"></i></button>';
        }

        if (strlen($button_add_html)) {
            $html = '
                <div class="dropdown-with-plus-btn ' . $cfg->get('width') . '">
                    <div class="left">' . \Helpers\Html::select_tag($field_name, $choices, $value, $attributes) . '</div>
                    <div class="right">' . $button_add_html . '</div>
                 </div>';
        } else {
            $html = \Helpers\Html::select_tag($field_name, $choices, $value, $attributes);
        }

        $html .= '<div id="fields_' . $field['id'] . '_select2_on"></div>';

        $html_width = '';

        if (\Helpers\App::is_mobile()) {
            $html_width = '
    			$("#field_' . $field['id'] . '_td").width($("#ajax-modal").width());
    			';
        }

        //remove required errro msg
        $html_on_change .= '
    			$("#fields_' . $field['id'] . '").change(function (e) {
                            $("#fields_' . $field['id'] . '-error").remove();
                        });
    			';

        if (strlen($cfg->get('copy_values'))) {
            $html_on_change .= '
    			$("#fields_' . $field['id'] . '").on("select2:select", function (e) {
      			var data = e.params.data;
    				$("#fields_' . $field['id'] . '_select2_on").load("' . \Helpers\Urls::url_for(
                    'main/dashboard/select2_json/copy_values',
                    'form_type=' . \K::$fw->app_module_path . '&entity_id=1&field_id=' . $field['id']
                ) . '",{item_id:data.id})	
      		});
    			';
        }

        $html .= '
    	<script>
    	var current_from_id = $("#fields_' . $field['id'] . '").closest("form").attr("id");
    	    
    	$(function(){
    	    
	    	$("#fields_' . $field['id'] . '").select2({
		      width: ' . (\Helpers\App::is_mobile() ? '$("body").width()-70' : '"100%"') . ',
		      ' . ((in_array(\K::$fw->app_layout, ['public_layout.php']) or in_array(
                    \K::$fw->app_module_path,
                    ['users/account']
                )) ? '' : 'dropdownParent: $("#ajax-modal"),') . '
		      "language":{
		        "noResults" : function () { return "' . addslashes(\K::$fw->TEXT_NO_RESULTS_FOUND) . '"; },
		    		"searching" : function () { return "' . addslashes(\K::$fw->TEXT_SEARCHING) . '"; },
		    		"errorLoading" : function () { return "' . addslashes(\K::$fw->TEXT_RESULTS_COULD_NOT_BE_LOADED) . '"; },
		    		"loadingMore" : function () { return "' . addslashes(\K::$fw->TEXT_LOADING_MORE_RESULTS) . '"; }
		      },
		    	' . ($cfg->get('display_as') == 'dropdown' ? 'allowClear: true,' : '') . '
		    	placeholder: "' . addslashes($cfg->get('default_text')) . '",
		      ajax: {
        		url: "' . \Helpers\Urls::url_for(
                'main/dashboard/select2_users_json/select_items',
                'form_type=' . \K::$fw->app_module_path . '&entity_id=1&field_id=' . $field['id'] . '&parent_entity_item_id=' . $params['parent_entity_item_id']
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
        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        if ($cfg->get('disable_notification') != 1) {
            if (is_array($options['value'])) {
                \K::$fw->app_send_to = array_merge($options['value'], \K::$fw->app_send_to);
            } else {
                \K::$fw->app_send_to[] = $options['value'];
            }
        }

        $value = (is_array($options['value']) ? implode(',', $options['value']) : $options['value']);

        //check if value changed
        if ($cfg->get('disable_notification') != 1) {
            if (!$options['is_new_item']) {
                if ($value != $options['current_field_value']) {
                    $array = array_diff(explode(',', $value), explode(',', $options['current_field_value']));

                    \K::$fw->app_send_to_new_assigned = array_merge(\K::$fw->app_send_to_new_assigned, $array);
                    /*foreach (array_diff(explode(',', $value), explode(',', $options['current_field_value'])) as $v) {
                        $app_send_to_new_assigned[] = $v;
                    }*/
                }
            }
        }

        return $value;
    }

    public function output($options)
    {
        if (!strlen($options['value'])) {
            return '';
        }

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        //return just name if export
        if (isset($options['is_export']) or isset($options['is_email'])) {
            $users_list = [];
            $exp = explode(',', $options['value']);

            foreach ($exp as $id) {
                if (isset(\K::$fw->app_users_cache[$id])) {
                    $users_list[] = \K::$fw->app_users_cache[$id]['name'];
                }
            }

            return implode(', ', $users_list);
        }

        $fields_in_popup_cfg = '';

        if (is_array($cfg->get('fields_in_popup'))) {
            $fields_in_popup_cfg = implode(',', $cfg->get('fields_in_popup'));
        }

        $html = '<ul class="list">';

        //$items_query = db_query("select e.* from app_entity_1 e where e.id in (" . $options['value'] . ")");

        $items_query = \K::model()->db_fetch('app_entity_1', [
            'id in (' . $options['value'] . ')'
        ]);

        //while ($items = db_fetch_array($items_query)) {
        foreach ($items_query as $items) {
            $items = $items->cast();

            $fields_access_schema = \Models\Main\Users\Users::get_fields_access_schema(
                1,
                \K::$fw->app_user['group_id']
            );

            $fields_in_popup = \Models\Main\Fields::get_items_fields_data_by_id(
                $items,
                $fields_in_popup_cfg,
                1,
                $fields_access_schema
            );
            $popup_html = '';
            if (count($fields_in_popup) > 0) {
                $popup_html = \Helpers\App::app_render_fields_popup_html($fields_in_popup);
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
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        if (strlen($filters['filters_values']) > 0) {
            $filters['filters_values'] = str_replace(
                'current_user_id',
                \K::$fw->app_user['id'],
                $filters['filters_values']
            );

            $sql_query[] = "(select count(*) from app_entity_" . (int)$options['entities_id'] . "_values as cv where cv.items_id = e.id and cv.fields_id = " . (int)$options['filters']['fields_id'] . " and cv.value in (" . $filters['filters_values'] . ")) " . ($filters['filters_condition'] == 'include' ? ' > 0' : ' = 0');
        }

        return $sql_query;
    }

    public static function render_heading_template($item, $heading_template = '')
    {
        $html = '';

        if (strlen($heading_template)) {
            $fieldtype_text_pattern = new \Tools\FieldsTypes\Fieldtype_text_pattern();
            $html = $fieldtype_text_pattern->output_singe_text($heading_template, 1, $item);
        }

        $text = \K::$fw->app_users_cache[$item['id']]['name'];

        return ['text' => $text, 'html' => '<div>' . (strlen($html) ? $html : $text) . '</div>'];
    }
}