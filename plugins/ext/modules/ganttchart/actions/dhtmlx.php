<?php

//check if report exist
$reports_query = db_query("select * from app_ext_ganttchart where id='" . db_input((int)$_GET['id']) . "'");
if (!$reports = db_fetch_array($reports_query)) {
    redirect_to('dashboard/page_not_found');
}

if (!ganttchart::users_has_access($reports['id'])) {
    redirect_to('dashboard/access_forbidden');
}

//check if pivot gant report
if ($app_entities_cache[$reports['entities_id']]['parent_id'] > 0 and (!isset($_GET['path']) or $app_path == $reports['entities_id'])) {
    $reports_type = 'parentganttreport' . $reports['id'];
} else {
    $reports_type = 'ganttreport' . $reports['id'];
}

//create default entity report for logged user
$reports_info_query = db_query(
    "select * from app_reports where entities_id='" . db_input(
        $reports['entities_id']
    ) . "' and reports_type='{$reports_type}' and created_by='" . $app_logged_users_id . "'"
);
if (!$reports_info = db_fetch_array($reports_info_query)) {
    $sql_data = [
        'name' => '',
        'entities_id' => $reports['entities_id'],
        'reports_type' => $reports_type,
        'in_menu' => 0,
        'in_dashboard' => 0,
        'listing_order_fields' => '',
        'created_by' => $app_logged_users_id,
    ];

    db_perform('app_reports', $sql_data);
    $fiters_reports_id = db_insert_id();
} else {
    $fiters_reports_id = $reports_info['id'];
}

$heading_field_id = fields::get_heading_id($reports['entities_id']);

if (!$heading_field_id) {
    $alerts->add(TEXT_ERROR_NO_HEADING_FIELD, 'warning');
}


switch ($app_module_action) {
    case 'set_grid_width':
        db_query(
            "update app_ext_ganttchart set grid_width='" . _post::int(
                'grid_width'
            ) . "' where id='" . $reports['id'] . "'"
        );
        exit();
        break;
    case 'save':

        //check mode
        switch ($_GET['gantt_mode']) {
            case 'tasks':

                //check editor status
                switch ($_POST['!nativeeditor_status']) {
                    case 'order':
                        $sql_data = ['parent_id' => $_POST['parent']];

                        db_perform(
                            "app_entity_" . $reports['entities_id'],
                            $sql_data,
                            'update',
                            "id='" . $_POST['id'] . "'"
                        );


                        $next_task_id = (strstr($_POST['target'], 'next:') ? false : $_POST['target']);

                        if ($next_task_id) {
                            //get next item sort order
                            $item_info_query = db_query(
                                "select sort_order from app_entity_" . $reports['entities_id'] . " where id='" . $next_task_id . "'"
                            );
                            $item_info = db_fetch_array($item_info_query);

                            //set next sort order to current item
                            db_query(
                                "update app_entity_" . $reports['entities_id'] . " set sort_order='" . $item_info['sort_order'] . "' where id='" . $_POST['id'] . "'"
                            );

                            //update all other sort orders in current parent
                            db_query(
                                "update app_entity_" . $reports['entities_id'] . " set sort_order=sort_order+1 where (sort_order>'" . $item_info['sort_order'] . "' or id='" . $next_task_id . "') and parent_id='" . $_POST['parent'] . "'"
                            );
                        } else {
                            $max_info_query = db_query(
                                "select max(sort_order) as value from app_entity_" . $reports['entities_id'] . " where parent_id='" . $_POST['parent'] . "'"
                            );
                            $max_info = db_fetch_array($max_info_query);

                            //set next sort order to current item
                            db_query(
                                "update app_entity_" . $reports['entities_id'] . " set sort_order='" . ($max_info['value'] + 1) . "' where id='" . $_POST['id'] . "'"
                            );
                        }


                        break;
                    case 'updated':

                        $sql_data = [
                            'field_' . $reports['start_date'] => get_date_timestamp($_POST['start_date']),
                            'field_' . $reports['end_date'] => strtotime(
                                "-1 day",
                                get_date_timestamp($_POST['end_date'])
                            ),
                        ];

                        if (ganttchart::get_duration_unit($reports) == 'hour') {
                            $sql_data['field_' . $reports['end_date']] = strtotime(
                                "-1 hour",
                                get_date_timestamp($_POST['end_date'])
                            );
                        }

                        $sql_data['parent_id'] = $_POST['parent'];

                        db_perform(
                            "app_entity_" . $reports['entities_id'],
                            $sql_data,
                            'update',
                            "id='" . $_POST['id'] . "'"
                        );

                        break;
                    case 'inserted':

                        $sql_data = ['parent_id' => $_POST['parent']];
                        $item_id = $_POST['sort_order'];

                        //handle sort order
                        $max_sort_order_query = db_query(
                            "select max(sort_order) as value from app_entity_" . $reports['entities_id'] . " where parent_id='" . (int)$_POST['parent'] . "'"
                        );
                        $max_sort_order = db_fetch_array($max_sort_order_query);
                        $sql_data['sort_order'] = ($max_sort_order['value'] + 1);

                        db_perform(
                            "app_entity_" . $reports['entities_id'],
                            $sql_data,
                            'update',
                            "id='" . $item_id . "'"
                        );

                        echo json_encode(['action' => 'inserted', 'tid' => $item_id]);

                        break;
                    case 'deleted':
                        items::delete($reports['entities_id'], $_POST['id']);

                        db_query(
                            "delete from app_ext_ganttchart_depends where (item_id='" . $_POST['id'] . "' or depends_id='" . $_POST['id'] . "') and ganttchart_id='" . $reports['id'] . "' and entities_id='" . $reports['entities_id'] . "'"
                        );
                        break;
                }

                break;

            case 'links':

                //check editor status
                switch ($_POST['!nativeeditor_status']) {
                    case 'inserted':
                        $sql_data_insert[] = [
                            'item_id' => $_POST['target'],
                            'depends_id' => $_POST['source'],
                            'type' => $_POST['type'],
                            'ganttchart_id' => $reports['id'],
                            'entities_id' => $reports['entities_id'],
                        ];

                        db_batch_insert('app_ext_ganttchart_depends', $sql_data_insert);
                        $depends_id = db_insert_id();

                        echo json_encode(['action' => 'inserted', 'tid' => $depends_id]);

                        break;
                    case 'deleted':
                        db_delete_row('app_ext_ganttchart_depends', $_POST['id']);
                        break;
                }

                break;
        }

        exit();
        break;
    case 'load_data':

        $fields_access_schema = users::get_fields_access_schema($reports['entities_id'], $app_user['group_id']);
        $entity_info = db_find('app_entities', $reports['entities_id']);

        if ($reports['use_background']) {
            $choices_colors = fields::get_field_choices_background_data($reports['use_background']);
        }

        $ganttcahrt_data = [];
        $ganttcahrt_data['tasks'] = [];

        $listing_sql_query_select = '';
        $listing_sql_query = '';
        $listing_sql_query_having = '';
        $sql_query_having = [];

        $listing_sql_query = reports::add_filters_query($fiters_reports_id, $listing_sql_query);

        //prepare dynamic dates
        if ($app_fields_cache[$reports['entities_id']][$reports['start_date']]['type'] == 'fieldtype_dynamic_date') {
            $sql_query_having[$reports['entities_id']][] = "field_" . $reports['start_date'] . ">0";
        }

        if ($app_fields_cache[$reports['entities_id']][$reports['end_date']]['type'] == 'fieldtype_dynamic_date') {
            $sql_query_having[$reports['entities_id']][] = "field_" . $reports['end_date'] . ">0";
        }

        //prepare having query for formula fields
        if (isset($sql_query_having[$reports['entities_id']])) {
            $listing_sql_query_having = reports::prepare_filters_having_query(
                $sql_query_having[$reports['entities_id']]
            );
        }

        if (isset($_GET['path'])) {
            $path_info = items::parse_path($_GET['path']);
            if ($path_info['parent_entity_item_id'] > 0) {
                $listing_sql_query .= " and e.parent_item_id='" . $path_info['parent_entity_item_id'] . "'";
            }
        }

        //prepare forumulas query
        $listing_sql_query_select = fieldtype_formula::prepare_query_select(
            $reports['entities_id'],
            $listing_sql_query_select
        );

        //check view assigned only access
        $listing_sql_query = items::add_access_query($reports['entities_id'], $listing_sql_query);

        if ($app_fields_cache[$reports['entities_id']][$reports['start_date']]['type'] != 'fieldtype_dynamic_date' and !ganttchart::has_date_added(
                $reports
            )) {
            $listing_sql_query .= " and e.field_" . $reports['start_date'] . ">0";
        }

        if ($app_fields_cache[$reports['entities_id']][$reports['end_date']]['type'] != 'fieldtype_dynamic_date') {
            $listing_sql_query .= " and e.field_" . $reports['end_date'] . ">0";
        }

        //add having query
        $listing_sql_query .= $listing_sql_query_having;

        //handle parent items query
        $display_parent_item = false;
        $parent_item_list = [];
        $listing_order_query = '';

        if ($entity_info['parent_id'] > 0 and $app_path == $entity_info['id']) {
            $listing_sql_query_select .= ", (select min(parent." . (ganttchart::has_date_added(
                    $reports
                ) ? 'date_added' : 'field_' . $reports['start_date']) . ") from app_entity_{$reports['entities_id']} as parent where parent.parent_item_id=e.parent_item_id) as parent_min_start_date";
            $listing_order_query = " parent_min_start_date, ";

            $display_parent_item = true;
        }

        $listing_sql = "select e.* " . $listing_sql_query_select . " from app_entity_" . $reports['entities_id'] . " e  where  e.id>0 " . $listing_sql_query . " order by " . $listing_order_query . " e.sort_order," . (ganttchart::has_date_added(
                $reports
            ) ? "e.date_added" : "e.field_" . $reports['start_date']);

        $result = [
            "data" => [],
            "links" => []
        ];

        $sort_order = 0;

        $items_query = db_query($listing_sql, false);
        while ($item = db_fetch_array($items_query)) {
            //handle paretn item display
            if ($display_parent_item) {
                if (!in_array($item['parent_item_id'], $parent_item_list)) {
                    $parent_item_list[] = $item['parent_item_id'];

                    $path_info = items::get_path_info($entity_info['id'], $item['id']);

                    $row = [
                        'open' => true,
                        'id' => 'parent_' . $item['parent_item_id'],
                        'text' => '<a href="' . url_for(
                                'ext/ganttchart/dhtmlx',
                                'id=' . $reports['id'] . '&path=' . $path_info['path_to_entity']
                            ) . '" target="_blank"><b>' . $path_info['parent_name'] . '</b></a>',
                        'start_date' => '',
                        'duration' => '',
                        'progress' => '',
                        'parent' => 0,
                        'sortorder' => 0,
                        'type' => 'project',
                        'color' => '#eeeeee',
                    ];

                    array_push($result["data"], $row);
                }

                if ($item['parent_id'] == 0) {
                    $item['parent_id'] = 'parent_' . $item['parent_item_id'];
                }
            }

            //hanlde sort order
            $sort_order++;
            if ($item['sort_order'] == 0) {
                db_query(
                    "update app_entity_" . $reports['entities_id'] . " set sort_order='" . $sort_order . "' where id='" . $item['id'] . "'"
                );
            }

            //start build row
            $row = [];

            if (ganttchart::has_date_added($reports)) {
                $start_date = $item['date_added'];
            } else {
                $start_date = $item['field_' . $reports['start_date']];
            }

            $end_date = $item['field_' . $reports['end_date']];

            if (ganttchart::get_duration_unit($reports) == 'hour') {
                $duration = hour_diff($start_date, $end_date) + 1;
            } else {
                $duration = day_diff($start_date, $end_date) + 1;
            }

            $heading_field_value = $item['id'];

            if ($heading_field_id > 0) {
                $field = db_find('app_fields', $heading_field_id);

                $value = items::prepare_field_value_by_type($field, $item);

                $output_options = [
                    'class' => $field['type'],
                    'value' => $value,
                    'field' => $field,
                    'item' => $item,
                    'is_export' => true,
                    'is_print' => true,
                ];

                $heading_field_value = fields_types::output($output_options);
            }

            //add custom colors
            $heading_link_color = '';

            if (isset($item['field_' . $reports['use_background']])) {
                $choices_id = $item['field_' . $reports['use_background']];

                if (isset($choices_colors[$choices_id])) {
                    $row['color'] = $choices_colors[$choices_id]['background'];

                    if (isset($choices_colors[$choices_id]['color'])) {
                        $heading_link_color = 'class="color-white"';
                    }
                }
            }

            $path_info = items::get_path_info($reports['entities_id'], $item['id'], $item);

            //main task data
            $row += [
                'open' => true,
                'id' => $item['id'],
                'text' => '<a ' . $heading_link_color . ' href="' . url_for(
                        'items/info',
                        'path=' . $path_info['full_path']
                    ) . '" target="_blank">' . $heading_field_value . '</a>',
                'start_date' => date('Y-m-d H:i:s', $start_date),
                'duration' => $duration,
                'progress' => 0,
                'parent' => $item['parent_id'],
                'sortorder' => $item['sort_order'],
            ];

            //add fields in listing
            if (strlen($reports['fields_in_listing'])) {
                foreach (explode(',', $reports['fields_in_listing']) as $k) {
                    $field = db_find('app_fields', $k);

                    $value = items::prepare_field_value_by_type($field, $item);

                    if ($field['type'] == 'fieldtype_progress') {
                        $row['field_' . $k] = $value;
                    } else {
                        $is_export = in_array($field['type'], ['fieldtype_color']) ? false : true;

                        $output_options = [
                            'class' => $field['type'],
                            'value' => $value,
                            'field' => $field,
                            'item' => $item,
                            'path' => $path_info['full_path'],
                            'is_export' => $is_export,
                            'is_print' => true,
                            'redirect_to' => 'ganttreport' . $reports['id'],
                            'reports_id' => 0,
                        ];

                        $row['field_' . $k] = fields_types::output($output_options);
                    }
                }
            }

            //add progress value
            if (isset($item['field_' . $reports['progress']])) {
                $row['progress'] = ($item['field_' . $reports['progress']] / 100);
            }

            array_push($result["data"], $row);
        }

        $depends_query = db_query(
            "select * from app_ext_ganttchart_depends where ganttchart_id='" . db_input($reports['id']) . "'"
        );
        while ($depends = db_fetch_array($depends_query)) {
            $row = [
                'id' => $depends['id'],
                'source' => $depends['depends_id'],
                'target' => $depends['item_id'],
                'type' => (!strlen($depends['type']) ? 0 : $depends['type']),
            ];

            array_push($result["links"], $row);
        }

        echo json_encode($result);

        exit();
        break;
}		