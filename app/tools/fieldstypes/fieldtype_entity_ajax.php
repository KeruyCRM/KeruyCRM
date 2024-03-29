<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_entity_ajax
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_ENTITY_AJAX_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_SELECT_ENTITY,
            'name' => 'entity_id',
            'tooltip_icon' => \K::$fw->TEXT_FIELDTYPE_ENTITY_SELECT_ENTITY_TOOLTIP,
            'type' => 'dropdown',
            'choices' => \Models\Main\Entities::get_choices(),
            'params' => [
                'class' => 'form-control input-xlarge',
                'onChange' => 'fields_types_ajax_configuration(\'fields_for_search_box\',this.value)'
            ],
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_WIDTH,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => [
                'input-medium' => \K::$fw->TEXT_INPUT_MEDIUM,
                'input-large' => \K::$fw->TEXT_INPUT_LARGE,
                'input-xlarge' => \K::$fw->TEXT_INPUT_XLARGE
            ],
            'tooltip_icon' => \K::$fw->TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DISPLAY_AS,
            'name' => 'display_as',
            'type' => 'dropdown',
            'choices' => [
                'dropdown' => \K::$fw->TEXT_FIELDTYPE_DROPDOWN_TITLE,
                'dropdown_multiple' => \K::$fw->TEXT_FIELDTYPE_DROPDOWN_MULTIPLE_TITLE
            ],
            'default' => 'dropdown',
            'params' => ['class' => 'form-control input-xlarge']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DEFAULT_TEXT,
            'name' => 'default_text',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_DEFAULT_TEXT_INFO,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DISPLAY_ONLY_ASSIGNED_RECORDS,
            'tooltip_icon' => \K::$fw->TEXT_DISPLAY_ONLY_ASSIGNED_RECORDS_INFO,
            'name' => 'display_assigned_records_only',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_PLUS_BUTTON,
            'name' => 'hide_plus_button',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_DISPLAY_NAME_AS_LINK_INFO
                ) . \K::$fw->TEXT_DISPLAY_NAME_AS_LINK,
            'name' => 'display_as_link',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        //TEXT_FIELDS
        $cfg[\K::$fw->TEXT_FIELDS][] = [
            'name' => 'fields_for_search_box',
            'type' => 'ajax',
            'html' => '<script>fields_types_ajax_configuration(\'fields_for_search_box\',$("#fields_configuration_entity_id").val())</script>'
        ];

        $cfg[\K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_WHERE_QUERY][] = [
            'title' => \K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_WHERE_QUERY,
            'name' => 'mysql_query_where',
            'type' => 'textarea',
            'tooltip' => \K::$fw->TEXT_FIELDTYPE_ENTITY_MYSQL_QUERY_TIP,
            'params' => ['class' => 'form-control code']
        ];

        return $cfg;
    }

    public function get_ajax_configuration($name, $value)
    {
        $cfg = [];

        switch ($name) {
            case 'fields_for_search_box':
                $entities_id = $value;

                if (\Models\Main\Listing_types::has_tree_table($entities_id)) {
                    $cfg[] = [
                        'title' => \K::$fw->TEXT_DISPLAY_AS . ' "' . \K::$fw->TEXT_TREE_TABLE . '"',
                        'name' => 'display_as_tree_table',
                        'type' => 'checkbox'
                    ];
                }

                $choices = [];

                $typeNotIn = \K::model()->quoteToString(['fieldtype_action', 'fieldtype_parent_item_id']);

                $fields_query = \K::model()->db_query_exec(
                    "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where is_heading = 0 and f.type not in (" . $typeNotIn . ") and  f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
                    $entities_id,
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

                $cfg[] = [
                    'title' => \K::$fw->TEXT_FIELDS_IN_POPUP,
                    'name' => 'fields_in_popup',
                    'type' => 'dropdown',
                    'choices' => $choices,
                    'tooltip_icon' => \K::$fw->TEXT_FIELDS_IN_POPUP_RELATED_ITEMS,
                    'tooltip' => \K::$fw->TEXT_SORT_ITEMS_IN_LIST,
                    'params' => [
                        'class' => 'form-control chosen-select chosen-sortable input-xlarge',
                        'multiple' => 'multiple'
                    ]
                ];

                $choices = [];

                $fields_query = \K::model()->db_query_exec(
                    "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in (" . \Models\Main\Fields_types::get_types_for_search_list(
                    ) . ") and  f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
                    $entities_id,
                    'app_fields,app_forms_tabs'
                );

                //while ($fields = db_fetch_array($fields_query)) {
                foreach ($fields_query as $fields) {
                    $choices[$fields['id']] = \Models\Main\Fields_types::get_option(
                        $fields['type'],
                        'name',
                        $fields['name']
                    );
                }

                $cfg[] = [
                    'title' => \K::$fw->TEXT_SEARCH_BY_FIELDS,
                    'name' => 'fields_for_search',
                    'type' => 'dropdown',
                    'choices' => $choices,
                    'tooltip_icon' => \K::$fw->TEXT_SEARCH_BY_FIELDS_INFO,
                    'params' => ['class' => 'form-control chosen-select input-xlarge', 'multiple' => 'multiple']
                ];

                $cfg[] = [
                    'title' => \K::$fw->TEXT_HEADING_TEMPLATE . \Models\Main\Fields::get_available_fields_helper(
                            $entities_id,
                            'fields_configuration_heading_template'
                        ),
                    'name' => 'heading_template',
                    'type' => 'textarea',
                    'tooltip_icon' => \K::$fw->TEXT_HEADING_TEMPLATE_INFO,
                    'tooltip' => \K::$fw->TEXT_ENTER_TEXT_PATTERN_INFO,
                    'params' => ['class' => 'form-control input-xlarge']
                ];

                $cfg[] = [
                    'title' => \K::$fw->TEXT_COPY_VALUES .
                        \Models\Main\Fields::get_available_fields_helper(
                            $entities_id,
                            'fields_configuration_copy_values',
                            \Models\Main\Entities::get_name_by_id($entities_id)
                        ) .
                        '<div style="padding-top: 2px;">' . \Models\Main\Fields::get_available_fields_helper(
                            \K::$fw->POST['entities_id'],
                            'fields_configuration_copy_values',
                            \Models\Main\Entities::get_name_by_id(\K::$fw->POST['entities_id'])
                        ) . '</div>',
                    'name' => 'copy_values',
                    'type' => 'textarea',
                    'tooltip' => \K::$fw->TEXT_COPY_FIELD_VALUES_INFO,
                    'params' => ['class' => 'form-control input-xlarge code']
                ];
                break;
        }

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $entity_info = \K::model()->db_find('app_entities', $cfg->get('entity_id'));
        $field_entity_info = \K::model()->db_find('app_entities', $field['entities_id']);

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

        $value = ($obj['field_' . $field['id']] > 0 ? $obj['field_' . $field['id']] : '');

        $html_on_change = '';

        if (strlen($value)) {
            /* $listing_sql = "select  e.* from app_entity_" . $cfg->get('entity_id') . " e  where id in (" . $value . ")";

             $items_query = db_query($listing_sql, false);*/

            $items_query = \K::model()->db_fetch('app_entity_' . (int)$cfg->get('entity_id'), [
                'id in (' . $value . ')'
            ]);

            //while ($item = db_fetch_array($items_query)) {
            foreach ($items_query as $item) {
                $item = $item->cast();

                $heading = self::render_heading_template($item, $entity_info, $field_entity_info, $cfg, false);
                $choices[$item['id']] = $heading['text'];
            }

            if (isset($params['is_new_item']) and $params['is_new_item'] == 1 and is_numeric($value)) {
                $html_on_change .= '$("#fields_' . $field['id'] . '_select2_on").load("' . \Helpers\Urls::url_for(
                        'main/dashboard/select2_json/copy_values',
                        'form_type=items/render_field_value&entity_id=' . $cfg->get(
                            'entity_id'
                        ) . '&field_id=' . $field['id']
                    ) . '",{item_id:' . $value . '})' . "\n";
            }
        }

        //prepare button add
        $parent_entity_item_id = $params['parent_entity_item_id'];
        $parent_entity_item_is_the_same = false;

        //if parent entity is the same then select records from paretn items only
        if ($parent_entity_item_id > 0 and $entity_info['parent_id'] > 0 and $entity_info['parent_id'] == $field_entity_info['parent_id']) {
            $parent_entity_item_is_the_same = true;
        }

        //TODO $app_layout != 'public_layout.php'
        $button_add_html = '';
        if ($cfg->get(
                'hide_plus_button'
            ) != 1 and isset(\K::$fw->current_path_array) and \K::$fw->app_action != 'account' and \K::$fw->app_action != 'comments_form' and \K::$fw->app_action != 'processes' and \K::$fw->app_layout != 'public_layout.php' and \Models\Main\Users\Users::has_access_to_entity(
                $cfg->get('entity_id'),
                'create'
            ) and !isset($_GET['is_submodal']) and ($entity_info['parent_id'] == 0 or ($entity_info['parent_id'] > 0 and $parent_entity_item_is_the_same))) {
            $url_params = 'is_submodal=true&redirect_to=parent_modal&refresh_field=' . $field['id'];

            if ($entity_info['parent_id'] == 0) {
                $url_params .= '&path=' . $cfg->get('entity_id');
            } else {
                $path_array = \K::$fw->current_path_array;
                unset($path_array[count($path_array) - 1]);

                $url_params .= '&path=' . implode('/', $path_array) . '/' . $cfg->get('entity_id');
            }

            $submodal_url = \Helpers\Urls::url_for('main/items/form', $url_params);

            $button_add_html = '<button type="button" class="btn btn-default btn-submodal-open btn-submodal-open-chosen" data-parent-entity-item-id="' . $parent_entity_item_id . '" data-field-id="' . $field['id'] . '" data-submodal-url="' . $submodal_url . '"><i class="fa fa-plus" aria-hidden="true"></i></button>';
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

        if (strlen($cfg->get('copy_values'))) {
            $html_on_change .= '
    			$("#fields_' . $field['id'] . '").on("select2:select", function (e) {
      			var data = e.params.data;
    				$("#fields_' . $field['id'] . '_select2_on").load("' . \Helpers\Urls::url_for(
                    'main/dashboard/select2_json/copy_values',
                    'form_type=' . \K::$fw->app_module_path . '&entity_id=' . $cfg->get(
                        'entity_id'
                    ) . '&field_id=' . $field['id']
                ) . '",{item_id:data.id})	
      		});
    			';
        }

        //remove required error msg
        $html_on_change .= '
    			$("#fields_' . $field['id'] . '").change(function (e) {
                            $("#fields_' . $field['id'] . '-error").remove();
                        });
    			';

        //handle scanner detection

        $html_on_change .= '
    			$("#fields_' . $field['id'] . '").on("select2:open", function (e) {
                            $(".select2-search__field").scannerDetection({
                                onComplete: function(barcode, qty){
                                    $(this).addClass("scanner-detected")
                                },
                                onError: function(){
                                   $(this).removeClass("scanner-detected")
                                }
                            })
                        });
    			';

        $html .= '
    	<script>	
    	var current_from_id = $("#fields_' . $field['id'] . '").closest("form").attr("id");
	
    	$(function(){
            let is_form_row_' . $field['id'] . ' = $("#fields_' . $field['id'] . '").parents(".forms-rows").size();

	    	$("#fields_' . $field['id'] . '").select2({		      
		      width: (is_form_row_' . $field['id'] . '==0 ? ' . self::get_select2_width_by_class(
                $cfg->get('width'),
                (strlen($button_add_html) ? true : false)
            ) . ':"100%"),                          
		      ' . ((in_array(\K::$fw->app_layout, ['public_layout.php']) or in_array(
                    \K::$fw->app_module_path,
                    ['users/account']
                ) or \K::$fw->app_user['id'] == 0) ? '' : 'dropdownParent: $("#ajax-modal"),') . '
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
                'main/dashboard/select2_json/select_items',
                'form_type=' . \K::$fw->app_module_path . '&entity_id=' . $cfg->get(
                    'entity_id'
                ) . '&field_id=' . $field['id'] . '&parent_entity_item_id=' . $params['parent_entity_item_id']
            ) . '",        		        
        		dataType: "json",
                        delay: 250,
        		type: "POST",
        		data: function (params) {                        
				      var query = {
                        search: params.term,
                        page: params.page || 1,
                        form_data: $("#"+current_from_id).serializeArray(),
                        is_tree_view: ' . (int)$cfg->get('display_as_tree_table') . '
				      }
				      // Query parameters will be ?search=[term]&page=[page]
				      return query;
				    },        				        				
        	},        				
			templateResult: function (d) { return $(d.html); },      		        			
	    	});
        ' . $html_on_change . '
      })
    	</script>
    ';

        return $html;
    }

    public function process($options)
    {
        return (is_array($options['value']) ? implode(',', $options['value']) : $options['value']);
    }

    public function output($options)
    {
        if (strlen($options['value']) == 0) {
            return '';
        }

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        $fields_in_popup_cfg = '';

        if (is_array($cfg->get('fields_in_popup'))) {
            $fields_in_popup_cfg = implode(',', $cfg->get('fields_in_popup'));
        }

        //prepare sql if not export
        $items_info_formula_sql = '';
        if (!isset($options['is_export'])) {
            $fields_access_schema = \Models\Main\Users\Users::get_fields_access_schema(
                $cfg->get('entity_id'),
                \K::$fw->app_user['group_id']
            );

            $fields_in_listing = \Models\Main\Fields::get_heading_id($cfg->get('entity_id')) . (strlen(
                    $fields_in_popup_cfg
                ) ? ',' . $fields_in_popup_cfg : '');

            $items_info_formula_sql = fieldtype_formula::prepare_query_select(
                $cfg->get('entity_id'),
                '',
                false,
                ['fields_in_listing' => $fields_in_listing]
            );
        }

        $output = [];
        $exp = explode(',', $options['value']);
        foreach ($exp as $item_id) {
            //TODO Add cache?
            $item = \K::model()->db_query_exec_one(
                "select e.* {$items_info_formula_sql} from app_entity_" . (int)$cfg->get(
                    'entity_id'
                ) . " e where e.id = ?",
                $item_id
            );

            if ($item) {
                $name = \Models\Main\Items\Items::get_heading_field($cfg->get('entity_id'), $item['id']);

                //get fields in popup in not export
                if (!isset($options['is_export'])) {
                    $fields_in_popup = \Models\Main\Fields::get_items_fields_data_by_id(
                        $item,
                        $fields_in_popup_cfg,
                        $cfg->get('entity_id'),
                        $fields_access_schema
                    );

                    if (count($fields_in_popup) > 0) {
                        $popup_html = \Helpers\App::app_render_fields_popup_html($fields_in_popup);

                        $name = '<span ' . $popup_html . '>' . $name . '</span>';
                    }

                    if ($cfg->get('display_as_link') == 1) {
                        $path_info = \Models\Main\Items\Items::get_path_info($cfg->get('entity_id'), $item['id']);

                        $name = '<a href="' . \Helpers\Urls::url_for(
                                'main/items/info',
                                'path=' . $path_info['full_path']
                            ) . '">' . $name . '</a>';
                    }
                }

                $output[] = $name;
            }
        }

        if (isset($options['is_export'])) {
            return implode(', ', $output);
        } else {
            return implode('<br>', $output);
        }
    }

    public function reports_query($options)
    {
        return \Models\Main\Reports\Reports::getReportsQueryValues($options);
    }

    public static function get_select2_width_by_class($class, $has_add_button = false)
    {
        //reset field width if field under form row
        if (\Helpers\App::is_mobile()) {
            $class = '';
        }

        switch ($class) {
            case 'input-small':
                $width = '"120px"';
                break;
            case 'input-medium':
                $width = '"240px"';
                break;
            case 'input-large':
                $width = '"320px"';
                break;
            case 'input-xlarge':
                $width = '"480px"';
                break;
            default:
                $width = '"100%"';
                break;
        }

        return $width;
    }

    public static function render_heading_template($item, $entity_info, $field_entity_info, $cfg, $get_html = true)
    {
        $html = '';

        $field_heading_id = \Models\Main\Fields::get_heading_id($entity_info['id']);

        if (strlen($heading_template = $cfg->get('heading_template')) and $get_html) {
            $fieldtype_text_pattern = new \Tools\FieldsTypes\Fieldtype_text_pattern();
            $html = $fieldtype_text_pattern->output_singe_text($heading_template, $entity_info['id'], $item);
        }

        if ($cfg->get('entity_id') == 1) {
            if ($field_heading_id) {
                $text = \Models\Main\Items\Items::get_heading_field_value($field_heading_id, $item);
            } else {
                $text = \K::$fw->app_users_cache[$item['id']]['name'];
            }
        } elseif ($field_heading_id > 0) {
            //add parent item name if exist
            $parent_name = '';
            if ($entity_info['parent_id'] > 0 and $entity_info['parent_id'] != $field_entity_info['parent_id']) {
                $parent_name = \Models\Main\Items\Items::get_heading_field(
                        $entity_info['parent_id'],
                        $item['parent_item_id']
                    ) . ' > ';
            }

            $text = $parent_name . \Models\Main\Items\Items::get_heading_field_value($field_heading_id, $item);
        } else {
            $text = $item['id'];
        }

        return ['text' => $text, 'html' => '<div>' . (strlen($html) ? $html : $text) . '</div>'];
    }

    public static function mysql_query_where($cfg, $field, $parent_entity_item_id)
    {
        if (!strlen($cfg->get('mysql_query_where'))) {
            return '';
        }

        $mysql_query_where = ' and (' . $cfg->get('mysql_query_where') . ')';

        if ($parent_entity_item_id > 0 and \K::$fw->app_entities_cache[$field['entities_id']]['parent_id'] > 0) {
            /*$item_info_query = db_query(
                "select * from app_entity_" . \K::$fw->app_entities_cache[$field['entities_id']]['parent_id'] . " where id=" . $parent_entity_item_id
            );*/

            $item_info = \K::model()->db_fetch_one(
                'app_entity_' . (int)\K::$fw->app_entities_cache[$field['entities_id']]['parent_id'],
                [
                    'id = ?',
                    $parent_entity_item_id
                ],
                [],
                'parent_item_id'
            );

            if ($item_info) {
                foreach ($item_info as $k => $v) {
                    $k = str_replace('field_', '', $k);
                    $mysql_query_where = str_replace('[' . $k . ']', $v, $mysql_query_where);
                }

                //check next parent
                $parent_entity_id = \K::$fw->app_entities_cache[$field['entities_id']]['parent_id'];

                if (\K::$fw->app_entities_cache[$parent_entity_id]['parent_id'] > 0 and $item_info['parent_item_id'] > 0) {
                    /*$item_info_query = db_query(
                        "select * from app_entity_" . \K::$fw->app_entities_cache[$parent_entity_id]['parent_id'] . " where id=" . $item_info['parent_item_id']
                    );*/

                    $item_info = \K::model()->db_fetch_one(
                        'app_entity_' . (int)\K::$fw->app_entities_cache[$parent_entity_id]['parent_id'],
                        [
                            'id = ?',
                            $item_info['parent_item_id']
                        ]
                    );

                    if ($item_info) {
                        foreach ($item_info as $k => $v) {
                            $k = str_replace('field_', '', $k);
                            $mysql_query_where = str_replace('[' . $k . ']', $v, $mysql_query_where);
                        }
                    }
                }
            }
        }

        $mysql_query_where = str_replace('[current_user_id]', \K::$fw->app_user['id'], $mysql_query_where);
        $mysql_query_where = str_replace(
            '[TODAY]',
            \Helpers\App::get_date_timestamp(date('Y-m-d')),
            $mysql_query_where
        );

        if (isset(\K::$fw->POST['form_data'])) {
            if (is_array(\K::$fw->POST['form_data'])) {
                foreach (\K::$fw->POST['form_data'] as $k => $v) {
                    $key = str_replace(['fields[', ']'], '', $v['name']);
                    $mysql_query_where = str_replace('[' . $key . ']', $v['value'], $mysql_query_where);
                }
            }
        }

        //check if all fields replaces and skip query if not
        if (strstr($mysql_query_where, '[')) {
            return '';
        }

        return $mysql_query_where;
    }
}