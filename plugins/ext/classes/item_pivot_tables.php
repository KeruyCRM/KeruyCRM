<?php

class item_pivot_tables
{
    public $position, $entities_id, $reports;

    function __construct($entities_id, $position)
    {
        $this->entities_id = $entities_id;
        $this->position = $position;
    }

    function render()
    {
        global $app_user, $app_path;

        $html = '';

        $reports_query = db_query(
            "select * from app_ext_item_pivot_tables where entities_id='" . $this->entities_id . "' and position='" . $this->position . "' and find_in_set(" . $app_user['group_id'] . ",allowed_groups)  order by sort_order, name"
        );
        while ($reports = db_fetch_array($reports_query)) {
            $html .= '
					<div class="portlet">
							<div class="portlet-title">
								<div class="caption">
				          ' . $reports['name'] . '
				        </div>
				        <div class="tools">
									<a href="javascript:;" class="collapse"></a>
								</div>
							</div>
							<div class="portlet-body">				          	
				      	<div id="item_pivot_tables_' . $reports['id'] . '"></div>
				      </div>
					</div>
				      			
				  <script>
				    $(function(){	
				   			load_item_pivot_tables(' . $reports['id'] . ',1);
				   	})   			
				  </script>
				  				  
				  ';
        }

        $html .= '
				<script>
				   function load_item_pivot_tables(reports_id,page)
				   {
						//alert(reports_id)
				   	 $("#item_pivot_tables_"+reports_id).append(\'<div class="loading_data"></div>\');
    
    				 $("#item_pivot_tables_"+reports_id).css("opacity", 0.5);
			
				   	 $("#item_pivot_tables_"+reports_id).load("' . url_for(
                'items/item_pivot_tables_report',
                'path=' . $app_path
            ) . '&reports_id="+reports_id+"&page="+page,function(){
				   	 		$("#item_pivot_tables_"+reports_id).css("opacity", 1);	
				   	 		appHandleChosen();
						 });				   	 				   	 	
				   }				   
				  </script>				
				';

        return $html;
    }

    function render_report()
    {
        global $app_entities_cache, $app_user, $app_users_cache, $sql_query_having;

        if (!strlen($this->reports['related_entities_fields'])) {
            return '';
        }

        $fields_list = [];
        $fields_id_list = [];
        $filters_panels_html = '';

        $fields_query = db_query(
            "select f.* from app_fields f where f.id in (" . $this->reports['related_entities_fields'] . ") order by field(f.id," . $this->reports['related_entities_fields'] . ")"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $cfg = new fields_types_cfg($fields['configuration']);

            $fields_choices = [];

            switch ($fields['type']) {
                case 'fieldtype_users_ajax':
                case 'fieldtype_users':

                    $field_name = $app_entities_cache[1]['name'];

                    $access_schema = users::get_entities_access_schema_by_groups($this->reports['related_entities_id']);

                    $choices = [];
                    $order_by_sql = (CFG_APP_DISPLAY_USER_NAME_ORDER == 'firstname_lastname' ? 'u.field_7, u.field_8' : 'u.field_8, u.field_7');
                    $users_query = db_query(
                        "select u.*,a.name as group_name from app_entity_1 u left join app_access_groups a on a.id=u.field_6 where u.field_5=1 order by " . $order_by_sql
                    );
                    while ($users = db_fetch_array($users_query)) {
                        if (!isset($access_schema[$users['field_6']])) {
                            $access_schema[$users['field_6']] = [];
                        }

                        if ($users['field_6'] == 0 or in_array('view', $access_schema[$users['field_6']]) or in_array(
                                'view_assigned',
                                $access_schema[$users['field_6']]
                            )) {
                            $fields_choices[$users['id']] = $app_users_cache[$users['id']]['name'];
                        }
                    }
                    break;
                case 'fieldtype_entity':
                case 'fieldtype_entity_multilevel':
                case 'fieldtype_entity_ajax':

                    $field_name = $app_entities_cache[$cfg->get('entity_id')]['name'];

                    $listing_sql_query = 'e.id>0 ';
                    $listing_sql_query_order = '';
                    $listing_sql_query_join = '';
                    $listing_sql_query_having = '';
                    $listing_sql_select = '';

                    $parent_entity_item_is_the_same = false;

                    $default_reports_query = db_query(
                        "select * from app_reports where entities_id='" . db_input(
                            $cfg->get('entity_id')
                        ) . "' and reports_type='item_pivot_tables_" . $this->reports['id'] . "_" . $fields['id'] . "'"
                    );
                    if ($default_reports = db_fetch_array($default_reports_query)) {
                        //print_rr($fields);
                        //print_rr($default_reports);
                        $listing_sql_select = fieldtype_formula::prepare_query_select($cfg->get('entity_id'), '');

                        $listing_sql_query = reports::add_filters_query($default_reports['id'], $listing_sql_query);

                        //prepare having query for formula fields
                        if (isset($sql_query_having[$cfg->get('entity_id')])) {
                            $listing_sql_query_having = reports::prepare_filters_having_query(
                                $sql_query_having[$cfg->get('entity_id')]
                            );
                        }

                        $info = reports::add_order_query(
                            $default_reports['listing_order_fields'],
                            $cfg->get('entity_id')
                        );
                        $listing_sql_query_order .= $info['listing_sql_query'];
                        $listing_sql_query_join .= $info['listing_sql_query_join'];
                    } else {
                        $listing_sql_query_order .= " order by e.id";
                    }

                    $listing_sql = "select  e.* " . $listing_sql_select . " from app_entity_" . $cfg->get(
                            'entity_id'
                        ) . " e " . $listing_sql_query_join . " where " . $listing_sql_query . $listing_sql_query_having . $listing_sql_query_order;
                    $items_query = db_query($listing_sql, false);
                    while ($item = db_fetch_array($items_query)) {
                        if (users::has_users_access_to_entity($cfg->get('entity_id'))) {
                            $fields_choices[$item['id']] = '<a href="' . url_for(
                                    'items/info',
                                    'path=' . $cfg->get('entity_id') . '-' . $item['id']
                                ) . '">' . items::get_heading_field(
                                    $cfg->get('entity_id'),
                                    $item['id'],
                                    $item
                                ) . '</a>';
                        } else {
                            $fields_choices[$item['id']] = items::get_heading_field(
                                $cfg->get('entity_id'),
                                $item['id'],
                                $item
                            );
                        }
                    }

                    $type = 'item_pivot_tables_' . $this->reports['id'] . "_" . $fields['id'];
                    $filters_panels = new filters_panels(
                        $default_reports['entities_id'],
                        $default_reports['id'],
                        $this->reports['id'],
                        0
                    );
                    $filters_panels->set_type($type);
                    $filters_panels->set_items_listing_funciton_name('load_item_pivot_tables');
                    $filters_panels_html .= '<div class="' . $type . '">' . $filters_panels->render_horizontal(
                        ) . '</div>';

                    break;
                case 'fieldtype_dropdown':
                case 'fieldtype_dropdown_multiple':
                case 'fieldtype_radioboxes':
                case 'fieldtype_checkboxes':
                case 'fieldtype_grouped_users':

                    $field_name = $fields['name'];

                    if ($cfg->get('use_global_list') > 0) {
                        $fields_choices = global_lists::get_choices($cfg->get('use_global_list'), false);
                    } else {
                        $fields_choices = fields_choices::get_choices($fields['id'], false);
                    }

                    break;
            }

            $fields_list[$fields['id']] = [
                'choices' => $fields_choices,
                'name' => $field_name,
                'type' => $fields['type'],
                'cfg' => $cfg,
            ];

            $fields_id_list[] = $fields['id'];
        }

        //echo $field_name;
        //print_rr($fields_list);

        $level = 1;
        $level_choices = [];
        foreach ($fields_id_list as $field_id) {
            if ($level == 1) {
                foreach ($fields_list[$field_id]['choices'] as $choices_id => $value) {
                    $level_choices[$level][] = [
                        [
                            'fileds_id' => $field_id,
                            'type' => $fields_list[$field_id]['type'],
                            'cfg' => $fields_list[$field_id]['cfg'],
                            'choices_id' => $choices_id,
                            'choices_value' => $value
                        ]
                    ];
                }
            } else {
                foreach ($level_choices[$level - 1] as $previous_value) {
                    foreach ($fields_list[$field_id]['choices'] as $choices_id => $value) {
                        $level_choices[$level][] = array_merge(
                            $previous_value,
                            [
                                [
                                    'fileds_id' => $field_id,
                                    'type' => $fields_list[$field_id]['type'],
                                    'cfg' => $fields_list[$field_id]['cfg'],
                                    'choices_id' => $choices_id,
                                    'choices_value' => $value
                                ]
                            ]
                        );
                    }
                }
            }

            $level++;
        }

        $table_rows = $level_choices[count($fields_id_list)];

        //print_rr($table_rows);

        $html = $filters_panels_html . '
			<div class="table-scrollable item-pivot-tables">					
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>';

        foreach ($fields_list as $field) {
            $html .= '<th>' . $field['name'] . '</th>';

            //fields in listing headings
            switch ($field['type']) {
                case 'fieldtype_entity':
                case 'fieldtype_entity_multilevel':
                case 'fieldtype_entity_ajax':
                    if (strlen($this->reports['fields_in_listing'])) {
                        $cfg = $field['cfg'];

                        $fields_in_listing = json_decode($this->reports['fields_in_listing'], true);

                        if (isset($fields_in_listing[$cfg->get('entity_id')])) {
                            foreach ($fields_in_listing[$cfg->get('entity_id')] as $fields_id) {
                                $field_info_query = db_query(
                                    "select id, type, name from app_fields where id='" . $fields_id . "'"
                                );
                                if ($field_info = db_fetch_array($field_info_query)) {
                                    $html .= '<th>' . fields_types::get_option(
                                            $field_info['type'],
                                            'name',
                                            $field_info['name']
                                        ) . '</th>';
                                }
                            }
                        }
                    }

                    break;
            }
        }

        $columns_query = db_query(
            "select * from app_ext_item_pivot_tables_calcs where type='column' and reports_id='" . $this->reports['id'] . "' order by sort_order, name"
        );
        while ($columns = db_fetch_array($columns_query)) {
            $html .= '<th>' . $columns['name'] . '</th>';
        }

        $html .= '
						</tr>
					</thead>
					
					<tbody>
				';

        $html .= $this->build_table_rows($table_rows);

        $html .= '
					</tbody>
				</table>				
			</div>
				';

        if ($this->number_of_rows_per_page > 0 and count($table_rows) > $this->number_of_rows_per_page) {
            $html .= '
					<div class="row">
					  <div class="col-md-5 col-sm-12">' . $this->display_page_count() . '</div>
					  <div class="col-md-7 col-sm-12">' . $this->display_page_number_links() . '</div>
					</div>
					';
        }

        return $html;
    }

    // display number of total products found
    function display_page_count()
    {
        $to_num = ($this->number_of_rows_per_page * $this->current_page_number);
        if ($to_num > $this->number_of_rows) {
            $to_num = $this->number_of_rows;
        }

        $from_num = ($this->number_of_rows_per_page * ($this->current_page_number - 1));

        if ($to_num == 0) {
            $from_num = 0;
        } else {
            $from_num++;
        }

        return sprintf(TEXT_DISPLAY_NUMBER_OF_ITEMS, $from_num, $to_num, $this->number_of_rows);
    }

    function display_page_number_links()
    {
        $max_page_links = 5;

        $this->number_of_pages = ceil($this->number_of_rows / $this->number_of_rows_per_page);

        $html = '<div class="dataTables_paginate paging_bootstrap"><ul class="pagination">';

        // previous button - not displayed on first page
        if ($this->current_page_number > 1) {
            $html .= '<li><a href="#" onClick="load_item_pivot_tables(' . $this->reports['id'] . ',' . ($this->current_page_number - 1) . '); return false;"  title=" ' . PREVNEXT_TITLE_PREVIOUS_PAGE . ' "><i class="fa fa-angle-left"></i></a></li>';
        } else {
            $html .= '<li class="active"><a href="#" onClick="return false"><i class="fa fa-angle-left"></i></a></li>';
        }

        // check if number_of_pages > $max_page_links
        $cur_window_num = intval($this->current_page_number / $max_page_links);
        if ($this->current_page_number % $max_page_links) {
            $cur_window_num++;
        }

        $max_window_num = intval($this->number_of_pages / $max_page_links);
        if ($this->number_of_pages % $max_page_links) {
            $max_window_num++;
        }

        // previous window of pages
        if ($cur_window_num > 1) {
            $html .= '<li><a href="#" onClick="load_item_pivot_tables(' . $this->reports['id'] . ',' . (($cur_window_num - 1) * $max_page_links) . '); return false;" title=" ' . sprintf(
                    PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE,
                    $max_page_links
                ) . ' ">...</a></li>';
        }

        // page nn button
        for ($jump_to_page = 1 + (($cur_window_num - 1) * $max_page_links); ($jump_to_page <= ($cur_window_num * $max_page_links)) && ($jump_to_page <= $this->number_of_pages); $jump_to_page++) {
            if ($jump_to_page == $this->current_page_number) {
                $html .= '<li class="active"><a href="#"  onClick="return false">' . $jump_to_page . '</a></li>';
            } else {
                $html .= '<li><a href="#" onClick="load_item_pivot_tables(' . $this->reports['id'] . ',' . $jump_to_page . '); return false;" title=" ' . sprintf(
                        PREVNEXT_TITLE_PAGE_NO,
                        $jump_to_page
                    ) . ' ">' . $jump_to_page . '</a></li>';
            }
        }

        // next window of pages
        if ($cur_window_num < $max_window_num) {
            $html .= '<li><a href="#"  onClick="load_item_pivot_tables(' . $this->reports['id'] . ',' . ($cur_window_num * $max_page_links + 1) . ')" title=" ' . sprintf(
                    PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE,
                    $max_page_links
                ) . ' ">...</a></li>';
        }

        // next button
        if (($this->current_page_number < $this->number_of_pages) && ($this->number_of_pages != 1)) {
            $html .= '<li><a href="#"  onClick="load_item_pivot_tables(' . $this->reports['id'] . ',' . ($this->current_page_number + 1) . '); return false;" title=" ' . PREVNEXT_TITLE_NEXT_PAGE . ' "><i class="fa fa-angle-right"></i></a></li>';
        } else {
            $html .= '<li class="active"><a href="#"  onClick="return false"><i class="fa fa-angle-right"></i></a></li>';
        }

        $html .= '</ul></div>';

        return $html;
    }


    function build_table_rows($table_rows)
    {
        global $app_user;

        $start_of_rows = 0;
        $this->number_of_rows = $offset = count($table_rows);

        if ($this->number_of_rows_per_page > 0) {
            $start_of_rows = ($this->current_page_number * $this->number_of_rows_per_page) - $this->number_of_rows_per_page;

            $offset = ($this->current_page_number * $this->number_of_rows_per_page);

            if ($offset > $this->number_of_rows) {
                $offset = $this->number_of_rows;
            }
        }

        //print_rr($table_rows);

        $html = '';
        //foreach($table_rows as $row)
        for ($i = $start_of_rows; $i < $offset; $i++) {
            $row = $table_rows[$i];

            $html .= '<tr>';

            foreach ($row as $choices) {
                $html .= '<td>' . $choices['choices_value'] . '</td>';

                //fields in listing headings
                switch ($choices['type']) {
                    case 'fieldtype_entity':
                    case 'fieldtype_entity_multilevel':
                    case 'fieldtype_entity_ajax':
                        if (strlen($this->reports['fields_in_listing'])) {
                            $cfg = $choices['cfg'];

                            $fields_in_listing = json_decode($this->reports['fields_in_listing'], true);

                            if (isset($fields_in_listing[$cfg->get('entity_id')])) {
                                $item_query = db_query(
                                    "select e.* from app_entity_" . $cfg->get(
                                        'entity_id'
                                    ) . " e where e.id='" . $choices['choices_id'] . "'"
                                );
                                if ($item = db_fetch_array($item_query)) {
                                    $fields_access_schema = users::get_fields_access_schema(
                                        $cfg->get('entity_id'),
                                        $app_user['group_id']
                                    );

                                    foreach ($fields_in_listing[$cfg->get('entity_id')] as $fields_id) {
                                        $field_query = db_query(
                                            "select id, type, name, configuration, entities_id from app_fields where id='" . $fields_id . "'"
                                        );
                                        if ($field = db_fetch_array($field_query)) {
                                            //check field access
                                            if (isset($fields_access_schema[$field['id']])) {
                                                if ($fields_access_schema[$field['id']] == 'hide') {
                                                    continue;
                                                }
                                            }

                                            if (in_array($field['type'], fields_types::get_reserved_data_types())) {
                                                $value = $item[fields_types::get_reserved_filed_name_by_type(
                                                    $field['type']
                                                )];
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
                                                'path' => $field['entities_id']
                                            ];

                                            $html .= '<td>' . fields_types::output($output_options) . '</td>';
                                        }
                                    }
                                }
                                /*foreach($fields_in_listing[$cfg->get('entity_id')] as $fields_id)
                                {
                                    $field_info_query = db_query("select id, type, name from app_fields where id='" . $fields_id . "'");
                                    if($field_info = db_fetch_array($field_info_query))
                                    {
                                        $html .= '<td>' . fields_types::get_option($field_info['type'],'name',$field_info['name']) . '</td>';
                                    }
                                }*/
                            }
                        }

                        break;
                }
            }

            $html .= $this->build_table_columns($row);

            $html .= '</tr>';
        }

        return $html;
    }

    function build_table_columns($row)
    {
        global $current_item_id;

        $calculations = $this->build_calculations($row);

        //print_rr($this->reports);

        $html = '';
        $columns_query = db_query(
            "select * from app_ext_item_pivot_tables_calcs where type='column' and reports_id='" . $this->reports['id'] . "' order by sort_order, name"
        );
        while ($columns = db_fetch_array($columns_query)) {
            $html .= '<td>' . $this->eval_formula($columns['formula'], $calculations) . '</td>';
        }

        return $html;
    }

    function eval_formula($formula, $calculations)
    {
        if (!strlen($formula)) {
            return '';
        }

        foreach ($calculations as $id => $value) {
            $formula = str_replace('{' . $id . '}', $value, $formula);
        }

        $eval_str = '$value = ' . $formula . ';';

        //echo htmlspecialchars($eval_str) . '<br>';

        eval($eval_str);


        return $value;
    }

    function build_calculations($row)
    {
        global $app_not_formula_fields_cache, $app_formula_fields_cache, $app_user, $current_item_id;

        $calculations_list = [];

        $calculations_query = db_query(
            "select * from app_ext_item_pivot_tables_calcs where type='calc' and reports_id='" . $this->reports['id'] . "' order by  name"
        );
        while ($calculations = db_fetch_array($calculations_query)) {
            if (strlen($calculations['where_query'])) {
                $where_query = json_decode($calculations['where_query'], true);

                $mysql_query = "";
                $count = 0;
                foreach ($where_query as $entity_id => $entity_where_query) {
                    $prefix = ($count == 0 ? 'e' : 'e' . $count);

                    if ($count == 0) {
                        $mysql_query .= "select (" . $calculations['select_query'] . ") as total from app_entity_" . $entity_id . " {$prefix} where {$prefix}.id>0 " . (strlen(
                                $entity_where_query
                            ) ? ' and ' . $entity_where_query : '');


                        //prepare formulas for first entity
                        $formulas_fields = [];

                        if (isset($app_formula_fields_cache[$entity_id])) {
                            foreach ($app_formula_fields_cache[$entity_id] as $formula_field) {
                                $formula_cfg = fields_types::parse_configuration($formula_field['configuration']);

                                if (strlen($formula_cfg['formula'])) {
                                    $formulas_fields[$formula_field['id']] = '(' . $formula_cfg['formula'] . ')';
                                }
                            }
                        }


                        $mysql_query = fieldtype_formula::prepare_formula_fields($formulas_fields, $mysql_query);

                        if (strstr($mysql_query, '{')) {
                            $mysql_query = functions::prepare_formula_query($entity_id, $mysql_query);
                        }
                    } else {
                        //build query for parent entities
                        $prefix2 = ($count < 2 ? 'e' : 'e' . ($count - 1));

                        $mysql_query .= " and {$prefix2}.parent_item_id in (select {$prefix}.id from app_entity_" . $entity_id . " {$prefix} where {$prefix}.id>0  " . (strlen(
                                $entity_where_query
                            ) ? ' and ' . $entity_where_query : '');
                    }


                    //prepare fields for current entity
                    foreach ($app_not_formula_fields_cache[$entity_id] as $fields_id) {
                        $fields_type = isset($app_fields_cache[$entity_id][$fields_id]['type']) ? $app_fields_cache[$entity_id][$fields_id]['type'] : '';
                        if (in_array(
                            $fields_type,
                            ['fieldtype_input_numeric', 'fieldtype_input_numeric_comments', 'fieldtype_js_formula']
                        )) {
                            $mysql_query = str_replace(
                                '[' . $fields_id . ']',
                                '(' . $prefix . '.field_' . $fields_id . '+0)',
                                $mysql_query
                            );
                        } else {
                            $mysql_query = str_replace(
                                '[' . $fields_id . ']',
                                $prefix . '.field_' . $fields_id,
                                $mysql_query
                            );
                        }
                    }

                    $mysql_query = str_replace('[id]', $prefix . '.id', $mysql_query);
                    $mysql_query = str_replace('[date_added]', $prefix . '.date_added', $mysql_query);
                    $mysql_query = str_replace('[created_by]', $prefix . '.created_by', $mysql_query);
                    $mysql_query = str_replace('[parent_item_id]', $prefix . '.parent_item_id', $mysql_query);


                    $count++;
                }

                $mysql_query .= str_repeat(')', ($count - 1));
            }

            foreach ($row as $choices) {
                $mysql_query = str_replace(
                    '[field_' . $choices['fileds_id'] . '_value]',
                    $choices['choices_id'],
                    $mysql_query
                );
            }

            //prepare [TODAY]
            $mysql_query = str_replace('[TODAY]', get_date_timestamp(date('Y-m-d')), $mysql_query);
            $mysql_query = str_replace('[current_user_id]', $app_user['id'], $mysql_query);
            $mysql_query = str_replace('[current_item_id]', $current_item_id, $mysql_query);

            //debug query
            //echo $mysql_query . '<br>';

            $calc_query = db_query($mysql_query);
            $calc = db_fetch_array($calc_query);

            $total = (isset($calc['total']) ? $calc['total'] : 0);
            //exit();

            $calculations_list[$calculations['id']] = $total;
        }

        return $calculations_list;
    }


}