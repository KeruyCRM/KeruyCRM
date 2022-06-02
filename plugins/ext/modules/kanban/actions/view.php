<?php

//check if report exist
$reports_query = db_query("select * from app_ext_kanban where id='" . db_input((int)$_GET['id']) . "'");
if (!$reports = db_fetch_array($reports_query)) {
    redirect_to('dashboard/page_not_found');
}

if (!in_array($app_user['group_id'], explode(',', $reports['users_groups'])) and $app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}


//create default entity report for logged user
$reports_info_query = db_query(
    "select * from app_reports where entities_id='" . db_input(
        $reports['entities_id']
    ) . "' and reports_type='kanban" . $reports['id'] . "' and created_by='" . $app_logged_users_id . "'"
);
if (!$reports_info = db_fetch_array($reports_info_query)) {
    $sql_data = [
        'name' => '',
        'entities_id' => $reports['entities_id'],
        'reports_type' => 'kanban' . $reports['id'],
        'in_menu' => 0,
        'in_dashboard' => 0,
        'listing_order_fields' => '',
        'created_by' => $app_logged_users_id,
    ];

    db_perform('app_reports', $sql_data);
    $fiters_reports_id = db_insert_id();

    $reports_info = db_find('app_reports', $fiters_reports_id);
} else {
    $fiters_reports_id = $reports_info['id'];
}

switch ($app_module_action) {
    case 'sort':

        //get report entity access schema
        $access_schema = users::get_entities_access_schema($reports['entities_id'], $app_user['group_id']);

        $choices_id = _post::int('choices_id');
        $item_id = _post::int('item_id');

        $field_info = db_find('app_fields', $reports['group_by_field']);
        $field_cfg = new fields_types_cfg($field_info['configuration']);


        $cfg = new fields_types_cfg($field_info['configuration']);

        //use global lists if exsit
        if ($cfg->get('use_global_list') > 0) {
            $kanban_choices = global_lists::get_choices($cfg->get('use_global_list'), false);
        } else {
            $kanban_choices = fields_choices::get_choices($field_info['id'], false);
        }

        $kanban_info_choices = [];
        $output = [];

        $item_info = db_find("app_entity_" . $reports['entities_id'], $item_id);
        if (isset($item_info['field_' . $reports['group_by_field']])) {
            //get previous choices ID
            $previous_choices_id = $item_info['field_' . $reports['group_by_field']];

            //update item
            db_query(
                "update app_entity_" . $reports['entities_id'] . " set field_" . $reports['group_by_field'] . " = " . $choices_id . " where id='" . $item_id . "'"
            );

            //autoupdate all field types
            fields_types::update_items_fields($reports['entities_id'], $item_id);

            $app_send_to = users::get_assigned_users_by_item($reports['entities_id'], $item_id);

            //sms
            $modules = new modules('sms');
            $sms = new sms($reports['entities_id'], $item_id);
            $sms->send_to = $app_send_to;
            $sms->send_edit_msg($item_info);

            //email rules
            $email_rules = new email_rules($reports['entities_id'], $item_id);
            $email_rules->send_edit_msg($item_info);

            //send notification
            if ($field_cfg->get('notify_when_changed') == 1) {
                $app_changed_fields = [];

                $app_changed_fields[] = [
                    'name' => $field_info['name'],
                    'value' => $kanban_choices[$choices_id],
                    'fields_id' => $field_info['id'],
                    'fields_value' => $choices_id,
                ];

                //autocreate comment
                comments::add_comment_notify_when_fields_changed(
                    $reports['entities_id'],
                    $item_id,
                    $app_changed_fields
                );

                /**
                 * Start email notification code
                 **/


                //include sender in notification
                if (CFG_EMAIL_COPY_SENDER == 1) {
                    $app_send_to[] = $app_user['id'];
                }


                //Send notification if there are assigned users and there are changed fields or new assigned users
                if (count($app_send_to) > 0 and count($app_changed_fields) > 0) {
                    $breadcrumb = items::get_breadcrumb_by_item_id($reports['entities_id'], $item_info['id']);
                    $item_name = $breadcrumb['text'];

                    $entity_cfg = new entities_cfg($reports['entities_id']);

                    //prepare subject for update itme
                    $subject = (strlen($entity_cfg->get('email_subject_updated_item')) > 0 ? $entity_cfg->get(
                            'email_subject_updated_item'
                        ) . ' ' . $item_name : TEXT_DEFAULT_EMAIL_SUBJECT_UPDATED_ITEM . ' ' . $item_name);

                    //add changed field values in subject
                    $extra_subject = [];
                    foreach ($app_changed_fields as $v) {
                        $extra_subject[] = $v['name'] . ': ' . $v['value'];
                    }

                    $subject .= ' [' . implode(' | ', $extra_subject) . ']';

                    $path_info = items::get_path_info($reports['entities_id'], $item_id);

                    //default email heading
                    $heading = users::use_email_pattern_style(
                        '<div><a href="' . url_for(
                            'items/info',
                            'path=' . $path_info['full_path'],
                            true
                        ) . '"><h3>' . $subject . '</h3></a></div>',
                        'email_heading_content'
                    );

                    //if only users fields changed then send notification to new assigned users
                    if (count($app_changed_fields) == 0 and count($app_send_to_new_assigned) > 0) {
                        $app_send_to = $app_send_to_new_assigned;
                    }

                    //start sending email
                    foreach (array_unique($app_send_to) as $send_to) {
                        //prepare body
                        if ($entity_cfg->get('item_page_details_columns', '2') == 1) {
                            $body = users::use_email_pattern(
                                'single_column',
                                [
                                    'email_single_column' => items::render_info_box(
                                        $reports['entities_id'],
                                        $item_id,
                                        $send_to,
                                        false
                                    )
                                ]
                            );
                        } else {
                            $body = users::use_email_pattern(
                                'single',
                                [
                                    'email_body_content' => items::render_content_box(
                                        $reports['entities_id'],
                                        $item_id,
                                        $send_to
                                    ),
                                    'email_sidebar_content' => items::render_info_box(
                                        $reports['entities_id'],
                                        $item_id,
                                        $send_to
                                    )
                                ]
                            );
                        }

                        if (users_cfg::get_value_by_users_id($send_to, 'disable_notification') != 1) {
                            users::send_to([$send_to], $subject, $heading . $body);
                        }

                        //add users notification
                        users_notifications::add(
                            $subject,
                            'updated_item',
                            $send_to,
                            $reports_info['entities_id'],
                            $item_id
                        );
                    }
                }
                /**
                 * End email notification code
                 **/
            }


            //calculate totals
            $kanban_info_choices[$choices_id]['count'] = 0;
            $kanban_info_choices[$previous_choices_id]['count'] = 0;

            if (strlen($reports['sum_by_field'])) {
                foreach (explode(',', $reports['sum_by_field']) as $k) {
                    $kanban_info_choices[$choices_id][$k] = 0;
                    $kanban_info_choices[$previous_choices_id][$k] = 0;
                }
            }

            //current choice totals
            $items_query = kanban::get_items_query(
                $reports['group_by_field'] . ':' . $choices_id,
                $reports,
                $fiters_reports_id
            );
            while ($items = db_fetch_array($items_query)) {
                $kanban_info_choices[$choices_id]['count']++;

                //prepare sum by field
                if (strlen($reports['sum_by_field'])) {
                    foreach (explode(',', $reports['sum_by_field']) as $k) {
                        if (strlen($items['field_' . $k])) {
                            $kanban_info_choices[$choices_id][$k] += $items['field_' . $k];
                        }
                    }
                }
            }

            $sum_html = '';
            if (strlen($reports['sum_by_field'])) {
                $sum_html = '<table class="kanban-heading-sum">';
                foreach (explode(',', $reports['sum_by_field']) as $id) {
                    $sum_html .= '
  					<tr>
  						<td>' . $app_fields_cache[$reports['entities_id']][$id]['name'] . ':&nbsp;</td>
  						<th>' . fieldtype_input_numeric::number_format(
                            $kanban_info_choices[$choices_id][$id],
                            $app_fields_cache[$reports['entities_id']][$id]['configuration']
                        ) . '</th>
  					</tr>';
                }
                $sum_html .= '</table>';
            }

            $add_button = '';
            if (users::has_access(
                    'create',
                    $access_schema
                ) and $app_fields_cache[$reports['entities_id']][$reports['group_by_field']]['type'] != 'fieldtype_autostatus') {
                $add_button = '<a class="btn btn-default btn-xs purple kanban-add-button" href="#" onClick="open_dialog(\'' . url_for(
                        'items/form',
                        'path=' . $app_path . '&redirect_to=kanban' . $reports['id'] . '&fields[' . $reports['group_by_field'] . ']=' . $choices_id
                    ) . '\')"><i class="fa fa-plus" aria-hidden="true"></i></a>';
            }

            $html = '
					<div class="heading">' . $add_button . $kanban_choices[$choices_id] . ' (' . $kanban_info_choices[$choices_id]['count'] . ')</div>
  				<div>' . $sum_html . '</div>
					';

            $output[$choices_id] = trim($html);


            //preivous choice totals
            $items_query = kanban::get_items_query(
                $reports['group_by_field'] . ':' . $previous_choices_id,
                $reports,
                $fiters_reports_id
            );
            while ($items = db_fetch_array($items_query)) {
                $kanban_info_choices[$previous_choices_id]['count']++;

                //prepare sum by field
                if (strlen($reports['sum_by_field'])) {
                    foreach (explode(',', $reports['sum_by_field']) as $k) {
                        if (strlen($items['field_' . $k])) {
                            $kanban_info_choices[$previous_choices_id][$k] += $items['field_' . $k];
                        }
                    }
                }
            }

            $sum_html = '';
            if (strlen($reports['sum_by_field'])) {
                $sum_html = '<table class="kanban-heading-sum">';
                foreach (explode(',', $reports['sum_by_field']) as $id) {
                    $sum_html .= '
  					<tr>
  						<td>' . $app_fields_cache[$reports['entities_id']][$id]['name'] . ':&nbsp;</td>
  						<th>' . fieldtype_input_numeric::number_format(
                            $kanban_info_choices[$previous_choices_id][$id],
                            $app_fields_cache[$reports['entities_id']][$id]['configuration']
                        ) . '</th>
  					</tr>';
                }
                $sum_html .= '</table>';
            }

            $add_button = '';
            if (users::has_access(
                    'create',
                    $access_schema
                ) and $app_fields_cache[$reports['entities_id']][$reports['group_by_field']]['type'] != 'fieldtype_autostatus') {
                $add_button = '<a class="btn btn-default btn-xs purple kanban-add-button" href="#" onClick="open_dialog(\'' . url_for(
                        'items/form',
                        'path=' . $app_path . '&redirect_to=kanban' . $reports['id'] . '&fields[' . $reports['group_by_field'] . ']=' . $previous_choices_id
                    ) . '\')"><i class="fa fa-plus" aria-hidden="true"></i></a>';
            }

            $html = '
					<div class="heading">' . $add_button . $kanban_choices[$previous_choices_id] . ' (' . $kanban_info_choices[$previous_choices_id]['count'] . ')</div>
  				<div>' . $sum_html . '</div>
					';

            $output[$previous_choices_id] = trim($html);


            echo json_encode($output);
        }

        exit();
        break;
}
