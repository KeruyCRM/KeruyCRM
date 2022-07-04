<?php

class filters_panels
{

    public $entities_id, $reports_id, $listing_container, $vertical_width, $fields_access_schema, $parent_entity_item_id;

    function __construct($entities_id, $reports_id, $listing_container, $parent_entity_item_id = false)
    {
        global $app_user;

        $this->entities_id = $entities_id;
        $this->reports_id = $reports_id;
        $this->listing_container = $listing_container;
        $this->parent_entity_item_id = $parent_entity_item_id;

        $this->vertical_width = $this->get_vertical_width();

        $this->fields_access_schema = users::get_fields_access_schema($entities_id, $app_user['group_id']);

        $this->type = '';
        $this->load_items_listing_funciton_name = 'load_items_listing';
        $this->custom_panel_id = '';
        $this->custom_panel_css = '';
    }

    static function get_fields_list($entities_id)
    {
        global $app_user;

        $list = [];

        $panels_query = db_query(
            "select * from app_filters_panels where length(type)=0 and (length(users_groups)=0 or find_in_set(" . $app_user['group_id'] . ",users_groups)) and is_active=1 and entities_id='" . $entities_id . "' order by sort_order"
        );
        $count_panels = db_num_rows($panels_query);
        while ($panels = db_fetch_array($panels_query)) {
            $fields_query = db_query(
                "select * from app_filters_panels_fields where panels_id='" . $panels['id'] . "' order by sort_order"
            );
            while ($fields = db_fetch_array($fields_query)) {
                $list[] = $fields['fields_id'];
            }
        }

        return $list;
    }

    function set_type($type)
    {
        $this->type = $this->custom_panel_id = $type;
        $this->custom_panel_css = '.' . $type;
    }

    function set_items_listing_funciton_name($name)
    {
        $this->load_items_listing_funciton_name = $name;
    }

    function render_horizontal()
    {
        global $app_user, $app_module_path;

        $html = '<div class="filters-panels horizontal-filters-panels">';

        $panels_query = db_query(
            "select f.* from app_filters_panels f where (select count(*) from app_filters_panels_fields fp where fp.panels_id=f.id)>0 and f.position='horizontal' and f.type='" . $this->type . "' and (length(f.users_groups)=0 or find_in_set(" . $app_user['group_id'] . ",f.users_groups)) and f.is_active=1 and f.entities_id='" . $this->entities_id . "' order by f.sort_order"
        );
        $count_panels = db_num_rows($panels_query);
        while ($panels = db_fetch_array($panels_query)) {
            $html .= '<ul class="list-inline filters-panels-' . $panels['id'] . '">';

            $fields_query = db_query(
                "select fp.*, f.type from app_filters_panels_fields fp, app_fields f where f.id=fp.fields_id  and fp.panels_id='" . $panels['id'] . "' order by fp.sort_order"
            );
            while ($fields = db_fetch_array($fields_query)) {
                //check field access
                if (isset($this->fields_access_schema[$fields['fields_id']])) {
                    if ($this->fields_access_schema[$fields['fields_id']] == 'hide') {
                        continue;
                    }
                }

                //skip filter by parent in main listing
                if ($app_module_path == 'items/items' and $fields['type'] == 'fieldtype_parent_item_id') {
                    continue;
                }

                $html .= '<li>' . $this->render_fields($fields, $panels) . '</li>';
            }

            if ($panels['is_active_filters'] == 0) {
                $html .= '<li><br><a href="javascript: apply_panel_filters(' . $panels['id'] . ')" class="btn btn-info" title="' . TEXT_SEARCH . '"><i class="fa fa-search" aria-hidden="true"></i> ' . TEXT_SEARCH . '</a></li>';
            }

            $html .= '<li class="hidden-in-mobile"><br><a href="javascript: reset_panel_filters' . $this->custom_panel_id . '(' . $panels['id'] . ')" class="btn btn-default" title="' . TEXT_RESET_FILTERS . '"><i class="fa fa-refresh" aria-hidden="true"></i></a></li>';
            $html .= '<li class="display-in-mobile"><a href="javascript: reset_panel_filters' . $this->custom_panel_id . '(' . $panels['id'] . ')" class="btn btn-default btn-reset-filters" >' . TEXT_RESET_FILTERS . ' <i class="fa fa-refresh" aria-hidden="true"></i></a></li>';

            $html .= '</ul>';
        }

        $html .= '</div>';


        $html .= $this->render_js();


        return $html;
    }

    function get_vertical_width()
    {
        global $app_user;

        $panels_query = db_query(
            "select max(width) as max_width from app_filters_panels where position='vertical' and (length(users_groups)=0 or find_in_set(" . $app_user['group_id'] . ",users_groups)) and is_active=1 and entities_id='" . $this->entities_id . "' order by sort_order"
        );
        $panels = db_fetch_array($panels_query);

        return (int)$panels['max_width'];
    }

    function render_vertical()
    {
        global $app_user, $app_module_path;

        if ($this->vertical_width == 0) {
            return '';
        }

        $html = '
			<div class="col-sm-' . $this->vertical_width . ' filters-panels vertical-filters-panels">	
				';

        $panels_query = db_query(
            "select * from app_filters_panels where position='vertical' and (length(users_groups)=0 or find_in_set(" . $app_user['group_id'] . ",users_groups)) and is_active=1 and entities_id='" . $this->entities_id . "' order by sort_order"
        );
        while ($panels = db_fetch_array($panels_query)) {
            $html .= '
					<div class="filters-panels-' . $panels['id'] . '">
					';

            $fields_query = db_query(
                "select *, f.type from app_filters_panels_fields fp, app_fields f where f.id=fp.fields_id  and fp.panels_id='" . $panels['id'] . "' order by fp.sort_order"
            );
            while ($fields = db_fetch_array($fields_query)) {
                //check field access
                if (isset($this->fields_access_schema[$fields['fields_id']])) {
                    if ($this->fields_access_schema[$fields['fields_id']] == 'hide') {
                        continue;
                    }
                }

                //skip filter by parent in main listing
                if ($app_module_path == 'items/items' and $fields['type'] == 'fieldtype_parent_item_id') {
                    continue;
                }

                $html .= '<div class="fields-container">' . $this->render_fields($fields, $panels) . '</div>';
            }

            $html .= '
						<div class="buttons">
							' . ($panels['is_active_filters'] == 0 ? '<a href="javascript: apply_panel_filters(' . $panels['id'] . ')" class="btn btn-info" title="' . TEXT_SEARCH . '"><i class="fa fa-search" aria-hidden="true"></i> ' . TEXT_SEARCH . '</a>' : '') . '
							<a href="javascript: reset_panel_filters(' . $panels['id'] . ')" class="btn btn-default" title="' . TEXT_RESET_FILTERS . '"><i class="fa fa-refresh" aria-hidden="true"></i> ' . TEXT_RESET . '</a>
						</div>
					</div>';
        }

        $html .= '				
			</div>';

        return $html;
    }

    function render_fields($panel_field, $panel_info)
    {
        global $app_module_path, $app_entities_cache;

        $field_info_query = db_query("select * from app_fields where id='" . $panel_field['fields_id'] . "'");
        if (!$field_info = db_fetch_array($field_info_query)) {
            return '';
        }

        $panels_id_str = ($panel_info['is_active_filters'] != 1 ? '-' . $panel_info['id'] : '');

        $filters_values = '';
        $reports_id = filters_panels::get_report_id_by_field_id($this->reports_id, $field_info['id']);

        //skip parent filters if parent item selected
        if ($app_module_path == 'items/items' and $field_info['entities_id'] != $this->entities_id) {
            return '';
        }

        $reports_filters_query = db_query(
            "select * from app_reports_filters where fields_id='" . $field_info['id'] . "' and reports_id='" . $reports_id . "' and filters_condition!='exclude'"
        );
        if ($reports_filters = db_fetch_array($reports_filters_query)) {
            $filters_values = $reports_filters['filters_values'];
        }

        if (strlen($panel_field['title'])) {
            $field_name = $panel_field['title'];
        } else {
            $field_name = strlen($field_info['short_name']) ? $field_info['short_name'] : fields_types::get_option(
                $field_info['type'],
                'name',
                $field_info['name']
            );
        }

        $html = '				
				<div class="heading">
					' . $field_name . ': <a href="javascript:delete_field_fielter_value' . $this->custom_panel_id . '(' . $field_info['id'] . ')" title="' . TEXT_RESET . '"><i class="fa fa-times" aria-hidden="true"></i></a>						
			    </div>';

        switch ($field_info['type']) {
            case 'fieldtype_parent_item_id_off':
                $choices = [];

                $entity_info = db_find('app_entities', $field_info['entities_id']);

                if ($entity_info['parent_id'] > 0) {
                    $items_query = db_query(
                        "select e.* from app_entity_" . $entity_info['parent_id'] . " e where e.id>0 " . items::add_access_query(
                            $entity_info['parent_id'],
                            ''
                        ) . ' ' . items::add_access_query_for_parent_entities(
                            $entity_info['parent_id']
                        ) . ' ' . items::add_listing_order_query_by_entity_id($entity_info['parent_id'])
                    );
                    while ($items = db_fetch_array($items_query)) {
                        $choices[$items['id']] = items::get_heading_field($entity_info['parent_id'], $items['id']);
                    }
                }

                break;
            case 'fieldtype_jalali_calendar':
                $filters_values = explode(',', $filters_values);
                //print_r($reports_filters);

                $html .= '
                        <form  action="' . url_for(
                        'reports/filters',
                        'action=set_field_fielter_value&reports_id=' . $this->reports_id
                    ) . '" method="post">
                                ' . input_hidden_tag('field_id', $field_info['id']) . '
                                <div class="input-group input-medium daterange-filter-' . $field_info['id'] . '">												
                                        ' . input_tag(
                        'field_val[]',
                        (isset($filters_values[1]) ? $filters_values[1] : ''),
                        [
                            'class' => 'form-control jalali-datepicker filters-panels-date-fields' . $panels_id_str . ' filters-panels-date-field-' . $field_info['id'],
                            'data-field-id' => $field_info['id'],
                            'placeholder' => TEXT_DATE_FROM
                        ]
                    ) . '
                                        <span class="input-group-addon" style="width: 1px; padding:0;">									
                                        </span>
                                        ' . input_tag(
                        'field_val[]',
                        (isset($filters_values[2]) ? $filters_values[2] : ''),
                        [
                            'class' => 'form-control jalali-datepicker filters-panels-date-fields' . $panels_id_str . ' filters-panels-date-field-' . $field_info['id'],
                            'data-field-id' => $field_info['id'],
                            'placeholder' => TEXT_DATE_TO
                        ]
                    ) . '			
                                </div>
                        </form>
                       
			';
                break;
            case 'fieldtype_date_added':
            case 'fieldtype_date_updated':
            case 'fieldtype_input_date':
            case 'fieldtype_input_datetime':
            case 'fieldtype_dynamic_date':

                $filters_values = explode(',', $filters_values);
                //print_r($reports_filters);

                $html .= '
						<form  action="' . url_for(
                        'reports/filters',
                        'action=set_field_fielter_value&reports_id=' . $this->reports_id
                    ) . '" method="post">
							' . input_hidden_tag('field_id', $field_info['id']) . '
							<div class="input-group input-medium datepicker input-daterange daterange-filter-' . $field_info['id'] . '">												
								' . input_tag(
                        'field_val[]',
                        (isset($filters_values[1]) ? $filters_values[1] : ''),
                        [
                            'class' => 'form-control filters-panels-date-fields' . $panels_id_str . ' filters-panels-date-field-' . $field_info['id'],
                            'data-field-id' => $field_info['id'],
                            'placeholder' => TEXT_DATE_FROM
                        ]
                    ) . '
								<span class="input-group-addon" style="width: 1px; padding:0;">									
								</span>
								' . input_tag(
                        'field_val[]',
                        (isset($filters_values[2]) ? $filters_values[2] : ''),
                        [
                            'class' => 'form-control filters-panels-date-fields' . $panels_id_str . ' filters-panels-date-field-' . $field_info['id'],
                            'data-field-id' => $field_info['id'],
                            'placeholder' => TEXT_DATE_TO
                        ]
                    ) . '			
							</div>
						</form>
						';
                break;

            case 'fieldtype_access_group':
                $choices = fieldtype_access_group::get_choices($field_info);
                break;

            case 'fieldtype_color':
                $cfg = new fields_types_cfg($field_info['configuration']);

                if ($cfg->get('use_global_list') > 0) {
                    $choices = global_lists::get_choices_with_color($cfg->get('use_global_list'), false);
                } else {
                    $choices = fields_choices::get_choices_with_color($field_info['id'], false);
                }

                //exlude values
                if (strlen($panel_field['exclude_values'])) {
                    foreach (explode(',', $panel_field['exclude_values']) as $id) {
                        if (isset($choices[$id])) {
                            unset($choices[$id]);
                        }
                    }
                }

                if ($panel_info['position'] == 'vertical') {
                    $panel_field['width'] = '';
                }

                switch ($panel_field['display_type']) {
                    case 'dropdown':
                        $attributes = [
                            'class' => 'form-control filters-panels-fields' . $panels_id_str . ' filters-panels-field-' . $field_info['id'] . ' chosen-select ' . $panel_field['width'],
                            'data-field-id' => $field_info['id']
                        ];
                        $html .= select_tag_with_color('values[]', ['' => ''] + $choices, $filters_values, $attributes);
                        break;
                    case 'dropdown_multiple':
                        $attributes = [
                            'class' => 'form-control filters-panels-fields' . $panels_id_str . ' filters-panels-field-' . $field_info['id'] . ' chosen-select ' . $panel_field['width'],
                            'multiple' => 'multiple',
                            'style' => 'height:24px; visibility: hidden',
                            'data-field-id' => $field_info['id']
                        ];
                        $html .= select_tag_with_color('values[]', $choices, $filters_values, $attributes);
                        break;
                }

                return $html;


                break;

            case 'fieldtype_image_map':
            case 'fieldtype_autostatus':
            case 'fieldtype_checkboxes':
            case 'fieldtype_radioboxes':
            case 'fieldtype_dropdown':
            case 'fieldtype_dropdown_multiple':
            case 'fieldtype_dropdown_multilevel':
            case 'fieldtype_grouped_users':
            case 'fieldtype_tags':
            case 'fieldtype_stages':

                $cfg = new fields_types_cfg($field_info['configuration']);

                if ($cfg->get('use_global_list') > 0) {
                    $choices = global_lists::get_choices($cfg->get('use_global_list'), false);
                } else {
                    $choices = fields_choices::get_choices($field_info['id'], false);
                }

                //exlude values
                if (strlen($panel_field['exclude_values'])) {
                    foreach (explode(',', $panel_field['exclude_values']) as $id) {
                        if (isset($choices[$id])) {
                            unset($choices[$id]);
                        }
                    }
                }

                break;

            case 'fieldtype_user_accessgroups':
                if (!$choices = fieldtype_user_accessgroups::get_choices_by_rules()) {
                    $choices = access_groups::get_choices(true);
                }
                break;

            case 'fieldtype_user_status':
                $choices = ['1' => TEXT_ACTIVE, '0' => TEXT_INACTIVE];
                break;

            case 'fieldtype_user_roles':
                $choices = fieldtype_user_roles::get_choices(
                    $field_info,
                    ['parent_entity_item_id' => $this->parent_entity_item_id]
                );
                break;

            case 'fieldtype_users_approve':
                $choices = fieldtype_users_approve::get_choices(
                    $field_info,
                    ['parent_entity_item_id' => $this->parent_entity_item_id]
                );
                break;

            case 'fieldtype_users':
                $choices = fieldtype_users::get_choices(
                    $field_info,
                    ['parent_entity_item_id' => $this->parent_entity_item_id]
                );
                break;

            case 'fieldtype_created_by':
                $choices = users::get_choices_by_entity($this->entities_id, 'create');
                break;

            case 'fieldtype_entity_ajax':
            case 'fieldtype_entity_multilevel':
            case 'fieldtype_users_ajax':
            case 'fieldtype_parent_item_id':
                $choices = [];

                if ($field_info['type'] == 'fieldtype_parent_item_id') {
                    $field_entity_id = $app_entities_cache[$field_info['entities_id']]['parent_id'];
                } else {
                    $cfg = new settings($field_info['configuration']);
                    $field_entity_id = $cfg->get('entity_id');
                }

                //prepare selected values
                if (strlen($filters_values)) {
                    foreach (explode(',', $filters_values) as $item_id) {
                        $choices[$item_id] = items::get_heading_field($field_entity_id, $item_id);
                    }
                }

                switch ($panel_field['display_type']) {
                    case 'dropdown':
                        $attributes = [
                            'id' => 'values_' . $field_info['id'],
                            'class' => 'form-control entity-ajax-select filters-panels-fields' . $panels_id_str . ' filters-panels-field-' . $field_info['id'] . ' ' . $panel_field['width'],
                            'data-field-id' => $field_info['id']
                        ];
                        $html .= select_tag('values[]', ['' => ''] + $choices, $filters_values, $attributes);
                        break;
                    case 'dropdown_multiple':
                        $attributes = [
                            'id' => 'values_' . $field_info['id'],
                            'class' => 'form-control entity-ajax-select filters-panels-fields' . $panels_id_str . ' filters-panels-field-' . $field_info['id'] . ' ' . $panel_field['width'],
                            'multiple' => 'multiple',
                            'style' => 'height:24px; visibility: hidden',
                            'data-field-id' => $field_info['id']
                        ];
                        $html .= select_tag('values[]', $choices, $filters_values, $attributes);
                        break;
                }


                $html .= '
    <script>
        $(function(){	

            
            $(".filters-panels-field-' . $field_info['id'] . '").select2({		      
                width: $(".filters-panels-field-' . $field_info['id'] . '").parents(".vertical-filters-panels").size()>0 ? "100%":' . fieldtype_entity_ajax::get_select2_width_by_class(
                        $panel_field['width']
                    ) . ',		                      
                "language":{
                  "noResults" : function () { return "' . addslashes(TEXT_NO_RESULTS_FOUND) . '"; },
                            "searching" : function () { return "' . addslashes(TEXT_SEARCHING) . '"; },
                            "errorLoading" : function () { return "' . addslashes(TEXT_RESULTS_COULD_NOT_BE_LOADED) . '"; },
                            "loadingMore" : function () { return "' . addslashes(TEXT_LOADING_MORE_RESULTS) . '"; }		    				
                },	
                allowClear: true,
                placeholder: \'' . addslashes(TEXT_SELECT_SOME_VALUES) . '\',
                ajax: {
                        url: "' . url_for(
                        'items/select2_entities_filter',
                        'action=select_items&path=' . $field_entity_id
                    ) . '",
                        dataType: "json",  
                        delay: 250,
                        type: "POST",
                        data: function (params) {
                            var query = {
                              search: params.term,
                              page: params.page || 1, 
                              entity_id: ' . $field_info['entities_id'] . ',
                              field_entity_id: ' . $field_entity_id . ',
                              parent_item_id: ' . (int)$this->parent_entity_item_id . ',
                              panel_field_id: ' . $panel_field['id'] . ',
                              field_id: ' . $field_info['id'] . ',   
                              reports_id: ' . $this->reports_id . ',    
                            }

                          // Query parameters will be ?search=[term]&page=[page]
                          return query;
                        },        				        				
                    },        				
                    templateResult: function (d) { return $(d.html); },      		        			
            });               
        })
    </script>
';

                return $html;

                break;

            case 'fieldtype_entity':
                $parent_entity_item_is_the_same = false;
                $choices_tmp = fieldtype_entity::get_choices(
                    $field_info,
                    ['parent_entity_item_id' => $this->parent_entity_item_id],
                    '',
                    $parent_entity_item_is_the_same
                );

                $choices = [];
                foreach ($choices_tmp as $k => $v) {
                    if ($k > 0) {
                        $choices[$k] = $v;
                    }
                }
                break;
            case 'fieldtype_boolean':
            case 'fieldtype_boolean_checkbox':
                $cfg = new fields_types_cfg($field_info['configuration']);

                $choices = [];
                $choices[''] = '';
                $choices['true'] = (strlen($cfg->get('text_boolean_true')) > 0 ? $cfg->get(
                    'text_boolean_true'
                ) : TEXT_BOOLEAN_TRUE);
                $choices['false'] = (strlen($cfg->get('text_boolean_true')) > 0 ? $cfg->get(
                    'text_boolean_false'
                ) : TEXT_BOOLEAN_FALSE);

                $panel_field['display_type'] = 'dropdown';
                $panel_field['width'] = 'input-small';
                break;
            case 'fieldtype_barcode':

                $input_width = ($panel_info['position'] == 'vertical' ? '' : 'input-medium');

                $html .= '
                    <form class="filters-panels-form" id="test" action="' . url_for(
                        'reports/filters',
                        'action=set_field_fielter_value&reports_id=' . $this->reports_id
                    ) . '" method="post">
                            ' . input_hidden_tag('field_id', $field_info['id']) . '
                            <div class="input-group ' . $input_width . '">
                                    ' . input_tag(
                        'field_val',
                        $filters_values,
                        [
                            'class' => 'form-control filters-panels-input-fields' . $panels_id_str . ' filters-panels-input-field-' . $field_info['id'],
                            'data-field-id' => $field_info['id']
                        ]
                    ) . '
                                    ' . ($panel_field['search_type_match'] == 1 ? input_hidden_tag(
                        'search_type_match',
                        1
                    ) : '') . '		
                                    <span class="input-group-btn">
                                        ' . (is_mobile() ? button_tag(
                        '<i class="fa fa-barcode" aria-hidden="true"></i>',
                        url_for('dashboard/barcodescan', 'field_id=' . $field_info['id']),
                        true,
                        ['class' => 'btn btn-default']
                    ) : '') . '
                                        <button class="btn btn-default" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                                    </span>
                            </div>


                    </form>
                    
            <script>
            $(function(){
                $(".filters-panels-input-field-' . $field_info['id'] . '").scannerDetection({                    
                    onComplete: function(barcode, qty){ 
                        let form = $(this).parents("form:first");
                        setTimeout(function(){ form.submit() },100);
                    },                    
                }).focus();
            })
            </script>
                    ';

                break;
            case 'fieldtype_input_ip':

                $input_width = ($panel_info['position'] == 'vertical' ? '' : 'input-ip');

                $html .= '
                    <form class="filters-panels-form" action="' . url_for(
                        'reports/filters',
                        'action=set_field_fielter_value&reports_id=' . $this->reports_id
                    ) . '" method="post">
                            ' . input_hidden_tag('field_id', $field_info['id']) . '
                            <div class="input-group ' . $input_width . '">
                                    ' . input_tag(
                        'field_val',
                        $filters_values,
                        [
                            'class' => 'form-control filters-panels-input-fields' . $panels_id_str . ' filters-panels-input-field-' . $field_info['id'],
                            'data-field-id' => $field_info['id']
                        ]
                    ) . '
                                    ' . ($panel_field['search_type_match'] == 1 ? input_hidden_tag(
                        'search_type_match',
                        1
                    ) : '') . '		
                                    <span class="input-group-btn">
                                            <button class="btn btn-default" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                                    </span>
                            </div>


                    </form>
                    
            <script>
            $(function(){
                $(".filters-panels-input-field-' . $field_info['id'] . '").inputmask({
                    alias: "ip",
                    greedy: false,
                    clearIncomplete:true,
                });
            })
            </script>
                    ';

                break;
            default:

                $input_width = 'input-medium';
                if (in_array(
                    $field_info['type'],
                    [
                        'fieldtype_id',
                        'fieldtype_formula',
                        'fieldtype_input_numeric',
                        'fieldtype_input_numeric_comments',
                        'fieldtype_years_difference',
                        'fieldtype_months_difference',
                        'fieldtype_hours_difference',
                        'fieldtype_days_difference',
                        'fieldtype_mysql_query',
                        'fieldtype_auto_increment'
                    ]
                )) {
                    $input_width = 'input-small';
                }

                if (in_array(
                        $field_info['type'],
                        ['fieldtype_input', 'fieldtype_text_pattern_static', 'fieldtype_input_encrypted']
                    ) and strlen($panel_field['width'])) {
                    $input_width = $panel_field['width'];
                }

                if ($panel_info['position'] == 'vertical') {
                    $input_width = '';
                }

                $html .= '
                    <form class="filters-panels-form" action="' . url_for(
                        'reports/filters',
                        'action=set_field_fielter_value&reports_id=' . $this->reports_id
                    ) . '" method="post">
                            ' . input_hidden_tag('field_id', $field_info['id']) . '
                            <div class="input-group ' . $input_width . '">
                                    ' . input_tag(
                        'field_val',
                        $filters_values,
                        [
                            'class' => 'form-control filters-panels-input-fields' . $panels_id_str . ' filters-panels-input-field-' . $field_info['id'],
                            'data-field-id' => $field_info['id']
                        ]
                    ) . '
                                    ' . ($panel_field['search_type_match'] == 1 ? input_hidden_tag(
                        'search_type_match',
                        1
                    ) : '') . '		
                                    <span class="input-group-btn">
                                            <button class="btn btn-default" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                                    </span>
                            </div>


                    </form>
                    ';
                break;
        }


        if ($panel_info['position'] == 'vertical') {
            $panel_field['width'] = '';
        }

        switch ($panel_field['display_type']) {
            case 'dropdown':
                $attributes = [
                    'class' => 'form-control filters-panels-fields' . $panels_id_str . ' filters-panels-field-' . $field_info['id'] . ' chosen-select ' . $panel_field['width'],
                    'data-field-id' => $field_info['id']
                ];
                $html .= select_tag('values[]', ['' => ''] + $choices, $filters_values, $attributes);
                break;
            case 'dropdown_multiple':
                $attributes = [
                    'class' => 'form-control filters-panels-fields' . $panels_id_str . ' filters-panels-field-' . $field_info['id'] . ' chosen-select ' . $panel_field['width'],
                    'multiple' => 'multiple',
                    'style' => 'height:24px; visibility: hidden',
                    'data-field-id' => $field_info['id']
                ];
                $html .= select_tag('values[]', $choices, $filters_values, $attributes);
                break;
            case 'checkboxes':
                $attributes = [
                    'class' => 'filters-panels-checkbox-fields' . $panels_id_str . ' filters-panels-checkbox-field-' . $field_info['id'] . '',
                    'data-field-id' => $field_info['id']
                ];
                $html .= '<div class="panel-field-container" ' . ($panel_field['height'] ? 'style="max-height:' . $panel_field['height'] . 'px; overflow-y: scroll "' : '') . '>' . select_checkboxes_tag(
                        'values',
                        $choices,
                        $filters_values,
                        $attributes
                    ) . '</div>';
                break;
            case 'radioboxes':
                $attributes = [
                    'class' => 'filters-panels-checkbox-fields' . $panels_id_str . ' filters-panels-checkbox-field-' . $field_info['id'] . '',
                    'data-field-id' => $field_info['id']
                ];
                $html .= '<div class="panel-field-container" ' . '>' . select_radioboxes_tag(
                        'values',
                        $choices,
                        $filters_values,
                        $attributes
                    ) . '</div>';
                break;
        }

        return $html;
    }

    function render_js()
    {
        $html = '
        <script>			
                $(function(){
                       
                        //dorpdowns		
                        $("' . $this->custom_panel_css . ' .filters-panels-fields").change(function(){
                                field_id = $(this).attr("data-field-id")
                                field_val = $(this).val();                                                                
                                
                                $.ajax({
                                        method: "POST",
                                        url: "' . url_for(
                'reports/filters',
                'action=set_field_fielter_value&reports_id=' . $this->reports_id
            ) . '",
                                        data: {field_id:field_id,field_val:field_val}								
                                }).done(function(){
                                        ' . $this->load_items_listing_funciton_name . '("' . $this->listing_container . '",1)
                                })						
                        })

                        //input 
                        $("' . $this->custom_panel_css . ' .filters-panels-form").submit(function(){
                                $.ajax({
                                        method: "POST",
                                        url: "' . url_for(
                'reports/filters',
                'action=set_field_fielter_value&reports_id=' . $this->reports_id
            ) . '",
                                        data: $(this).serializeArray()								
                                }).done(function(){
                                        ' . $this->load_items_listing_funciton_name . '("' . $this->listing_container . '",1)
                                })
                          return false;
                        })

                        //checkoxes & radiboxes				
                        $("' . $this->custom_panel_css . ' .filters-panels-checkbox-fields").change(function(){
                                field_id = $(this).attr("data-field-id")

                                field_val = [];			
                                $(".filters-panels-checkbox-field-"+field_id+":checked").each(function(){
                                        field_val.push($(this).val())
                                })										

                                $.ajax({
                                        method: "POST",
                                        url: "' . url_for(
                'reports/filters',
                'action=set_field_fielter_value&reports_id=' . $this->reports_id
            ) . '",
                                        data: {field_id:field_id,field_val:field_val}								
                                }).done(function(){
                                        ' . $this->load_items_listing_funciton_name . '("' . $this->listing_container . '",1)
                                })
                        })		

                        //dates
                        var filters_panels_date_fields_is_init = false;

                        $("' . $this->custom_panel_css . ' .filters-panels-date-fields").click(function(){
                                 filters_panels_date_fields_is_init = true;										
                        })	

                        //jalali
                        $("' . $this->custom_panel_css . ' .jalali-datepicker").on("change", function(e) {
                                var field_id = $(this).attr("data-field-id")

                                setTimeout(function(){			

                                        field_val = [];
                                        field_val.push("")
                                        $(".filters-panels-date-field-"+field_id).each(function(){
                                                field_val.push($(this).val())
                                        })		

                                        //alert(field_val)

                                        $.ajax({
                                                method: "POST",
                                                url: "' . url_for(
                'reports/filters',
                'action=set_field_fielter_value&reports_id=' . $this->reports_id
            ) . '",
                                                data: {field_id:field_id,field_val:field_val}								
                                        }).done(function(){
                                                ' . $this->load_items_listing_funciton_name . '("' . $this->listing_container . '",1)
                                        })

                                },100);	
                         })

                        $("' . $this->custom_panel_css . ' .filters-panels-date-fields").on("changeDate", function(e) {

                                //skip ajax load for first load			
                                if(filters_panels_date_fields_is_init==false) return false;			

                                var field_id = $(this).attr("data-field-id")

                                setTimeout(function(){			

                                        field_val = [];
                                        field_val.push("")
                                        $(".filters-panels-date-field-"+field_id).each(function(){
                                                field_val.push($(this).val())
                                        })		

                                        //alert(field_val)

                                        $.ajax({
                                                method: "POST",
                                                url: "' . url_for(
                'reports/filters',
                'action=set_field_fielter_value&reports_id=' . $this->reports_id
            ) . '",
                                                data: {field_id:field_id,field_val:field_val}								
                                        }).done(function(){
                                                ' . $this->load_items_listing_funciton_name . '("' . $this->listing_container . '",1)
                                        })

                                },100);			

                        })				
                })

                function apply_panel_filters' . $this->custom_panel_id . '(panel_id)
                {						
                        fields_values = {};

                        $(".filters-panels-fields-"+panel_id).each(function(){
                                field_id = $(this).attr("data-field-id")
                                fields_values[field_id] = $(this).val();
                        })	

                        $(".filters-panels-input-fields-"+panel_id).each(function(){
                                field_id = $(this).attr("data-field-id")
                                fields_values[field_id] = $(this).val();
                        })				

                        $(".filters-panels-date-fields-"+panel_id).each(function(){
                                field_id = $(this).attr("data-field-id")

                                if(!fields_values[field_id])
                                { 			

                                        field_val = [];
                                        field_val.push("")
                                        $(".filters-panels-date-field-"+field_id).each(function(){
                                                field_val.push($(this).val())
                                        })				

                                        fields_values[field_id] = field_val;
                                }
                        })	

                        $(".filters-panels-checkbox-fields-"+panel_id).each(function(){
                                field_id = $(this).attr("data-field-id")

                                if(!fields_values[field_id])
                                { 													
                                        field_val = [];			
                                        $(".filters-panels-checkbox-field-"+field_id+":checked").each(function(){
                                                field_val.push($(this).val())
                                        })	


                                        if(field_val.length>0)
                                        {			
                                                fields_values[field_id] = field_val;
                                        }
                                        else
                                        {
                                                fields_values[field_id] = "";		
                                        }			
                                }
                        })				

                        //console.log(fields_values)														

                        $.ajax({
                                        method: "POST",
                                        url: "' . url_for(
                'reports/filters',
                'action=set_multiple_fields_fielter_values&reports_id=' . $this->reports_id
            ) . '",
                                        data: {fields_values: fields_values}								
                                }).done(function(){
                                        ' . $this->load_items_listing_funciton_name . '("' . $this->listing_container . '",1)
                                })				
                }					

                function delete_field_fielter_value' . $this->custom_panel_id . '(field_id)
                {
                        $(".filters-panels-field-"+field_id).val("").trigger("change");

                        //reset date				
                        $(".filters-panels-date-field-"+field_id).val("");										

                        //reset input				
                        $(".filters-panels-input-field-"+field_id).val("");				

                        //reset chosen				
                        if($(".filters-panels-field-"+field_id).hasClass("chosen-select"))
                        {
                                $(".filters-panels-field-"+field_id).trigger("chosen:updated");			
                        }

                        //reset checkboxes				
                        $(".filters-panels-checkbox-field-"+field_id+":checked").each(function(){
                                $(this).prop("checked",false)
                                id = $(this).val();										
                                $("#uniform-values_"+id+" span").removeClass("checked")			
                        })

                        $.ajax({
                                        method: "POST",
                                        url: "' . url_for(
                'reports/filters',
                'action=delete_field_fielter_value&reports_id=' . $this->reports_id
            ) . '",
                                        data: {field_id:field_id}								
                                }).done(function(){
                                        ' . $this->load_items_listing_funciton_name . '("' . $this->listing_container . '",1)
                                })				
                }	

                function reset_panel_filters' . $this->custom_panel_id . '(panel_id)
                {
                        $(".filters-panels-"+panel_id+" .form-control").val("").trigger("change");
                        $(".filters-panels-"+panel_id+" .chosen-select").trigger("chosen:updated");

                        //reset checkboxes				
                        $(".filters-panels-"+panel_id+" input:checked").each(function(){
                                $(this).prop("checked",false)
                                id = $(this).val();										
                                $("#uniform-values_"+id+" span").removeClass("checked")			
                        })				

                        $.ajax({
                                        method: "POST",
                                        url: "' . url_for(
                'reports/filters',
                'action=reset_panel_filters&reports_id=' . $this->reports_id
            ) . '",
                                        data: {panels_id:panel_id}		
                                }).done(function(){
                                        ' . $this->load_items_listing_funciton_name . '("' . $this->listing_container . '",1)
                                })				
                }					
        </script>
        ';

        return $html;
    }

    static function get_position_choices()
    {
        return ['horizontal' => TEXT_HORIZONTAL, 'vertical' => TEXT_VERTICAL];
    }

    static function get_position_name($type)
    {
        $choices = self::get_position_choices();

        return $choices[$type];
    }

    static function get_field_width_choices()
    {
        return [
            'input-small' => TEXT_INPUT_SMALL,
            'input-medium' => TEXT_INPUT_MEDIUM,
            'input-large' => TEXT_INPUT_LARGE,
            'input-xlarge' => TEXT_INPUT_XLARGE
        ];
    }

    static function get_width_choices()
    {
        return ['1' => '10%', '2' => '20%', '3' => '30%', '4' => '40%'];
    }

    static function get_width_name($key)
    {
        $choices = self::get_width_choices();

        return $choices[$key];
    }

    static function get_field_display_type_name($key)
    {
        $choices = [];
        $choices['dropdown'] = TEXT_FIELDTYPE_DROPDOWN_TITLE;
        $choices['dropdown_multiple'] = TEXT_FIELDTYPE_DROPDOWN_MULTIPLE_TITLE;
        $choices['checkboxes'] = TEXT_FIELDTYPE_CHECKBOXES_TITLE;
        $choices['radioboxes'] = TEXT_FIELDTYPE_RADIOBOXES_TITLE;

        return isset($choices[$key]) ? $choices[$key] : '';
    }

    //check if user has any panels setup
    static function has_any($entity_id, $entity_cfg)
    {
        global $app_user;

        $common_filters_query = db_query(
            "select id, name from app_reports where entities_id='" . $entity_id . "' and reports_type='common_filters' and (length(users_groups)=0 or find_in_set(" . $app_user['group_id'] . ",users_groups)) limit 1"
        );

        $panels_query = db_query(
            "select * from app_filters_panels where (length(users_groups)=0 or find_in_set(" . $app_user['group_id'] . ",users_groups)) and is_active=1 and entities_id='" . $entity_id . "' and length(type)=0 limit 1"
        );

        if ($panels = db_fetch_array($panels_query) or $common_filters = db_fetch_array(
                $common_filters_query
            ) or filters_preview::has_default_panel_access($entity_cfg)) {
            return true;
        } else {
            return false;
        }
    }

    static function get_report_id_by_field_id($reports_id, $fields_id)
    {
        $field_info_query = db_query("select entities_id from app_fields where id='" . $fields_id . "'");
        if ($field_info = db_fetch_array($field_info_query)) {
            foreach (reports::get_parent_reports($reports_id, [$reports_id]) as $report_id) {
                $report_query = db_query("select id, entities_id from app_reports where id='" . $report_id . "'");
                if ($report = db_fetch_array($report_query)) {
                    if ($field_info['entities_id'] == $report['entities_id']) {
                        $reports_id = $report['id'];

                        break;
                    }
                }
            }
        }

        return $reports_id;
    }

    static function get_id_by_type($entities_id, $type)
    {
        $panels_query = db_query(
            "select * from app_filters_panels where entities_id='" . $entities_id . "' and type='" . $type . "'"
        );
        if (!$panels = db_fetch_array($panels_query)) {
            $sql_data = [
                'position' => 'horizontal',
                'entities_id' => $entities_id,
                'type' => $type,
                'is_active' => 1,
                'is_active_filters' => 1,
                'users_groups' => '',
                'width' => '',
                'sort_order' => 0,
            ];

            db_perform('app_filters_panels', $sql_data);
            $panels_id = db_insert_id();
        } else {
            $panels_id = $panels['id'];
        }

        return $panels_id;
    }

    static function exclude_values_not_in_listing_sql($panel_field_id, $reports_id)
    {
        global $app_fields_cache, $sql_query_having;

        $sql = "";

        $panel_field_query = db_query(
            "select * from app_filters_panels_fields where id='" . $panel_field_id . "' and exclude_values='exclude_values_not_in_listing'",
            false
        );
        if ($panel_field = db_fetch_array($panel_field_query)) {
            $current_entity_id = $panel_field['entities_id'];
            $field = $app_fields_cache[$current_entity_id][$panel_field['fields_id']];

            $cfg = new settings($field['configuration']);
            $field_entity_id = $cfg->get('entity_id');

            $listing_sql_query = reports::add_filters_query($reports_id, '', 'e', false, [$field['id']]);


            $formulas_sql = false;

            if (isset($sql_query_having[$current_entity_id])) {
                $listing_sql_query .= reports::prepare_filters_having_query($sql_query_having[$current_entity_id]);

                $formulas_sql = fieldtype_formula::prepare_query_select($current_entity_id, '');
            }


            if ($field['type'] == 'fieldtype_parent_item_id') {
                if ($formulas_sql) {
                    $listing_sql = "select e.parent_item_id {$formulas_sql} from app_entity_" . $current_entity_id . " e where e.id>0 " . $listing_sql_query;
                    $listing_sql = "select sb.parent_item_id from ({$listing_sql}) as sb";
                } else {
                    $listing_sql = "select e.parent_item_id from app_entity_" . $current_entity_id . " e where e.id>0 " . $listing_sql_query;
                }

                $sql = " and e.id in ({$listing_sql})";
            } elseif ($field['type'] == 'fieldtype_entity_multilevel') {
                if ($formulas_sql) {
                    $listing_sql = "select e.field_{$field['id']} {$formulas_sql} from app_entity_" . $current_entity_id . " e where e.id>0 " . $listing_sql_query;
                    $listing_sql = "select sb.field_{$field['id']} from ({$listing_sql}) as sb";
                } else {
                    $listing_sql = "select e.field_{$field['id']} from app_entity_" . $current_entity_id . " e where e.id>0 " . $listing_sql_query;
                }

                $sql = " and e.id in ({$listing_sql})";
            } else {
                if ($formulas_sql) {
                    $listing_sql = "select e.id {$formulas_sql} from app_entity_" . $current_entity_id . " e where e.id>0 " . $listing_sql_query;
                    $listing_sql = "select sb.id from ({$listing_sql}) as sb";
                } else {
                    $listing_sql = "select e.id from app_entity_" . $current_entity_id . " e where e.id>0 " . $listing_sql_query;
                }

                $sql = " and (select count(*) as total from app_entity_" . $current_entity_id . "_values cv where  cv.fields_id={$field['id']} and cv.value=e.id and cv.items_id in ({$listing_sql}))>0";
            }
        }

        return $sql;
    }

}
