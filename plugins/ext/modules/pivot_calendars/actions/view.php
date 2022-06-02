<?php

//check if report exist
$reports_query = db_query("select * from app_ext_pivot_calendars where id='" . db_input(_GET('id')) . "'");
if (!$reports = db_fetch_array($reports_query)) {
    redirect_to('dashboard/page_not_found');
}

if (!pivot_calendars::has_access($reports['users_groups'])) {
    redirect_to('dashboard/access_forbidden');
}

switch ($app_module_action) {
    case 'resize':
        if (strstr($_POST['end'], 'T')) {
            $end = get_date_timestamp($_POST['end']);
        } else {
            $end = strtotime('-1 day', get_date_timestamp($_POST['end']));
        }

        $id_info = explode('-', $_POST['id']);
        $entities_id = (int)$id_info[0];
        $itesm_id = (int)$id_info[1];

        $reports_entities_query = db_query(
            "select ce.*, e.name from app_ext_pivot_calendars_entities ce, app_entities e where e.id=ce.entities_id and ce.calendars_id='" . $reports['id'] . "' and ce.entities_id='" . $entities_id . "'"
        );
        if ($reports_entities = db_fetch_array($reports_entities_query)) {
            $sql_data = ['field_' . $reports_entities['end_date'] => $end];

            db_perform("app_entity_" . $entities_id, $sql_data, 'update', "id='" . $itesm_id . "'");
        }

        exit();
        break;

    case 'drop':

        if (isset($_POST['end'])) {
            if (strstr($_POST['end'], 'T')) {
                $end = get_date_timestamp($_POST['end']);
            } else {
                $end = strtotime('-1 day', get_date_timestamp($_POST['end']));
            }
        } else {
            $end = get_date_timestamp($_POST['start']);
        }

        $id_info = explode('-', $_POST['id']);
        $entities_id = (int)$id_info[0];
        $itesm_id = (int)$id_info[1];

        $reports_entities_query = db_query(
            "select ce.*, e.name from app_ext_pivot_calendars_entities ce, app_entities e where e.id=ce.entities_id and ce.calendars_id='" . $reports['id'] . "' and ce.entities_id='" . $entities_id . "'"
        );
        if ($reports_entities = db_fetch_array($reports_entities_query)) {
            $sql_data = [
                'field_' . $reports_entities['start_date'] => get_date_timestamp($_POST['start']),
                'field_' . $reports_entities['end_date'] => $end
            ];

            db_perform("app_entity_" . $entities_id, $sql_data, 'update', "id='" . $itesm_id . "'");
        }

        exit();
        break;

    case 'get_events':
        $list = [];

        $reports_entities_query = db_query(
            "select ce.*, e.name from app_ext_pivot_calendars_entities ce, app_entities e where e.id=ce.entities_id and ce.calendars_id='" . $reports['id'] . "' order by e.name"
        );
        while ($reports_entities = db_fetch_array($reports_entities_query)) {
            if (!users::has_users_access_name_to_entity('view', $reports_entities['entities_id'])) {
                continue;
            }

            if ($reports_entities['use_background'] > 0) {
                $use_background_field_info = db_find('app_fields', $reports_entities['use_background']);
            }


            $entity_info = db_find('app_entities', $reports_entities['entities_id']);

            if ($_GET['mode'] == 'view') {
                $cfg_editable = false;
            } else {
                $cfg_editable = users::has_users_access_name_to_entity('update', $reports_entities['entities_id']);
            }

            $date_from = substr($_GET['start'], 0, 10);
            $date_to = substr($_GET['end'], 0, 10);

            $listing_sql_query = '';
            $listing_sql_query_having = '';
            $sql_query_having = [];

            $is_start_date_dynamic = ($app_fields_cache[$reports_entities['entities_id']][$reports_entities['start_date']]['type'] == 'fieldtype_dynamic_date' ? true : false);
            $is_end_date_dynamic = ($app_fields_cache[$reports_entities['entities_id']][$reports_entities['end_date']]['type'] == 'fieldtype_dynamic_date' ? true : false);


            $listing_sql_query_filter = " and ( (FROM_UNIXTIME(e.field_" . $reports_entities['start_date'] . ",'%Y-%m-%d')>='" . $date_from . "' and  FROM_UNIXTIME(e.field_" . $reports_entities['end_date'] . ",'%Y-%m-%d')<='" . $date_to . "') or
	                           (FROM_UNIXTIME(e.field_" . $reports_entities['start_date'] . ",'%Y-%m-%d')<'" . $date_from . "' and  FROM_UNIXTIME(e.field_" . $reports_entities['end_date'] . ",'%Y-%m-%d')>'" . $date_to . "') or
	                           (FROM_UNIXTIME(e.field_" . $reports_entities['start_date'] . ",'%Y-%m-%d')<'" . $date_from . "' and  FROM_UNIXTIME(e.field_" . $reports_entities['end_date'] . ",'%Y-%m-%d')<='" . $date_to . "' and  FROM_UNIXTIME(e.field_" . $reports_entities['end_date'] . ",'%Y-%m-%d')>='" . $date_from . "') or
	                           (FROM_UNIXTIME(e.field_" . $reports_entities['start_date'] . ",'%Y-%m-%d')>='" . $date_from . "' and FROM_UNIXTIME(e.field_" . $reports_entities['start_date'] . ",'%Y-%m-%d')<='" . $date_to . "' and  FROM_UNIXTIME(e.field_" . $reports_entities['end_date'] . ",'%Y-%m-%d')>'" . $date_to . "')
	                           ) ";

            if (!$is_start_date_dynamic and !$is_end_date_dynamic) {
                $listing_sql_query = $listing_sql_query_filter;
            }

            $listing_sql_query = reports::add_filters_query(
                pivot_calendars::get_reports_id_by_calendar_entity(
                    $reports_entities['id'],
                    $reports_entities['entities_id']
                ),
                $listing_sql_query
            );

            $listing_sql_query = items::add_access_query($reports_entities['entities_id'], $listing_sql_query);

            //prepare forumulas query
            $listing_sql_query_select = fieldtype_formula::prepare_query_select($reports_entities['entities_id'], '');

            //prepare dynamic dates
            if ($is_start_date_dynamic) {
                $sql_query_having[$reports_entities['entities_id']][] = "field_" . $reports_entities['start_date'] . ">0";
                $cfg_editable = false;
            }

            if ($is_end_date_dynamic) {
                $sql_query_having[$reports_entities['entities_id']][] = "field_" . $reports_entities['end_date'] . ">0";
                $cfg_editable = false;
            }

            if ($is_start_date_dynamic or $is_end_date_dynamic) {
                $sql_query_having[$reports_entities['entities_id']][] = substr(
                    str_replace('e.', '', $listing_sql_query_filter),
                    4
                );
            }

            //prepare having query for formula fields
            if (isset($sql_query_having[$reports_entities['entities_id']])) {
                $listing_sql_query_having = reports::prepare_filters_having_query(
                    $sql_query_having[$reports_entities['entities_id']]
                );
            }

            if ($app_fields_cache[$reports_entities['entities_id']][$reports_entities['start_date']]['type'] != 'fieldtype_dynamic_date') {
                $listing_sql_query .= " and e.field_" . $reports_entities['start_date'] . ">0";
            }

            if ($app_fields_cache[$reports_entities['entities_id']][$reports_entities['end_date']]['type'] != 'fieldtype_dynamic_date') {
                $listing_sql_query .= " and e.field_" . $reports_entities['end_date'] . ">0";
            }

            //add having query
            $listing_sql_query .= $listing_sql_query_having;

            $events_query = db_query(
                "select e.* " . $listing_sql_query_select . " from app_entity_" . $reports_entities['entities_id'] . " e where e.id>0 " . $listing_sql_query
            );
            while ($events = db_fetch_array($events_query)) {
                $start = date('Y-m-d H:i', $events['field_' . $reports_entities['start_date']]);
                $end = date('Y-m-d H:i', $events['field_' . $reports_entities['end_date']]);

                if (strstr($end, ' 00:00')) {
                    $end = date('Y-m-d H:i', strtotime('+1 day', $events['field_' . $reports_entities['end_date']]));
                }

                if (strlen($reports_entities['heading_template']) > 0) {
                    $options = [
                        'custom_pattern' => $reports_entities['heading_template'],
                        'item' => $events
                    ];

                    $options['field']['configuration'] = '';

                    $options['field']['entities_id'] = $reports_entities['entities_id'];

                    $fieldtype_text_pattern = new fieldtype_text_pattern();
                    $title = $fieldtype_text_pattern->output($options);
                } else {
                    $title = items::get_heading_field($reports_entities['entities_id'], $events['id']);
                }

                //map color
                $className = 'fc-item-css';
                $color = (strlen(
                        $reports_entities['bg_color']
                    ) ? $reports_entities['bg_color'] : '#3a87ad') . ' !important';
                if ($reports_entities['use_background'] > 0) {
                    if (isset($events['field_' . $reports_entities['use_background']])) {
                        $value_id = $events['field_' . $reports_entities['use_background']];

                        $cfg = new fields_types_cfg($use_background_field_info['configuration']);

                        if ($cfg->get('use_global_list') > 0) {
                            $choices_cache = $app_global_choices_cache;
                        } else {
                            $choices_cache = $app_choices_cache;
                        }

                        if (isset($choices_cache[$value_id])) {
                            if (strlen($choices_cache[$value_id]['bg_color']) > 0) {
                                $color = $choices_cache[$value_id]['bg_color'] . ' !important';

                                $className = 'fc-item-css-' . $reports_entities['entities_id'] . '-' . $value_id;
                            }
                        }
                    }
                }

                //prepare description
                $description = '';

                if (strlen($reports_entities['fields_in_popup'])) {
                    $description .= '<table>';

                    foreach (explode(',', $reports_entities['fields_in_popup']) as $fields_id) {
                        $field_query = db_query("select * from app_fields where id='" . $fields_id . "'");
                        if ($field = db_fetch_array($field_query)) {
                            //prepare field value
                            $value = items::prepare_field_value_by_type($field, $events);

                            $output_options = [
                                'class' => $field['type'],
                                'value' => $value,
                                'field' => $field,
                                'item' => $events,
                                'is_export' => true,
                                'path' => ''
                            ];

                            $value = trim(strip_tags(fields_types::output($output_options)));

                            if (strlen($value) > 255) {
                                $value = mb_substr($value, 0, 255) . '...';
                            }

                            if (strlen($value)) {
                                $description .= '
			        			<tr>
			        				<td valign="top" style="padding-right: 7px;">' . ($field['short_name'] ? $field['short_name'] : fields_types::get_option(
                                        $field['type'],
                                        'name',
                                        $field['name']
                                    )) . '</td>
			        				<td valign="top">' . $value . '</td>
			        			</tr>';
                            }
                        }
                    }
                    $description .= '</table>';

                    $description = str_replace(["\n", "\r", "\r\n", "\t"], '', $description);
                }

                if ($entity_info['parent_id'] > 0 and !isset($_GET['path'])) {
                    $path_info = items::get_path_info($entity_info['id'], $events['id']);

                    $title = str_replace('<br>', ' / ', $path_info['parent_name']) . ' / ' . $title;
                }

                $list[] = [
                    'id' => $reports_entities['entities_id'] . '-' . $events['id'],
                    'entities_id' => $reports_entities['entities_id'],
                    'title' => $title,
                    'description' => $description,
                    'start' => str_replace(' 00:00', '', $start),
                    'end' => str_replace(' 00:00', '', $end),
                    'editable' => $cfg_editable,
                    'allDay' => (strstr($start, '00:00') and strstr($end, '00:00')),
                    'url' => url_for('items/info', 'path=' . $reports_entities['entities_id'] . '-' . $events['id']),
                    'className' => $className,
                    'color' => $color,

                ];
            }
        }

        echo json_encode($list);

        db_dev_log();

        exit();

        break;
}