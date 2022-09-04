<?php

switch ($app_module_action) {
    case 'save':
        $access_rules = new access_rules($current_entity_id, $current_item_id);

        //checking access
        if (isset($_GET['id']) and !users::has_comments_access('update', $access_rules->get_comments_access_schema())) {
            redirect_to('dashboard/access_forbidden');
        } elseif (!users::has_comments_access('create', $access_rules->get_comments_access_schema())) {
            redirect_to('dashboard/access_forbidden');
        }

        //check access for edit comment
        if (isset($_GET['id'])) {
            //check if comment exist
            $comment_query = db_query("select created_by from app_comments where id='" . _GET('id') . "'");
            if (!$comment = db_fetch_array($comment_query)) {
                redirect_to('dashboard/page_not_found');
            }

            //check if user can edit comment
            if ($app_user['group_id'] > 0 and $comment['created_by'] != $app_user['id'] and !users::has_comments_access(
                    'full',
                    $access_rules->get_comments_access_schema()
                )) {
                redirect_to('dashboard/access_forbidden');
            }
        }

        $entity_cfg = new entities_cfg($current_entity_id);

        $attachments = (isset($_POST['fields']['attachments']) ? $_POST['fields']['attachments'] : '');

        if (isset($_GET['is_quick_comment'])) {
            $description = $_POST['quick_comments_description'];
        } else {
            $description = $_POST['description'];
        }

        if (isset($_GET['is_quick_comment']) and $entity_cfg->get('use_editor_in_comments') == 1) {
            $description = nl2br($description);
        }

        $sql_data = [
            'description' => db_prepare_html_input($description),
            'entities_id' => $current_entity_id,
            'items_id' => $current_item_id,
            'attachments' => fields_types::process(['class' => 'fieldtype_attachments', 'value' => $attachments]),
        ];

        if (isset($_GET['id'])) {
            db_perform('app_comments', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            $sql_data['date_added'] = time();
            $sql_data['created_by'] = $app_user['id'];

            db_perform('app_comments', $sql_data);

            $comments_id = db_insert_id();

            //get item info befor update
            $item_info_query = db_query(
                "select * from app_entity_" . $current_entity_id . " where id='" . $current_item_id . "'"
            );
            $item_info = db_fetch_array($item_info_query);

            //update fields in comments form if they are exist
            if (isset($_POST['fields'])) {
                $fields_values_cache = items::get_fields_values_cache(
                    $_POST['fields'],
                    $current_path_array,
                    $current_entity_id
                );

                $fields_access_schema = users::get_fields_access_schema($current_entity_id, $app_user['group_id']);

                $sql_data = [];

                $updated_fields = [];

                $fields_query = db_query(
                    "select f.* from app_fields f where f.type not in (" . fields_types::get_reserved_types_list(
                    ) . ',' . fields_types::get_users_types_list() . ") and  f.entities_id='" . db_input(
                        $current_entity_id
                    ) . "' and f.comments_status = 1 order by f.comments_sort_order, f.name"
                );
                while ($field = db_fetch_array($fields_query)) {
                    //check field access
                    if (isset($fields_access_schema[$field['id']])) {
                        continue;
                    }

                    $cfg = new fields_types_cfg($field['configuration']);

                    $value = (isset($_POST['fields'][$field['id']]) ? $_POST['fields'][$field['id']] : '');

                    $process_options = [
                        'class' => $field['type'],
                        'value' => $value,
                        'fields_cache' => $fields_values_cache,
                        'field' => $field,
                        'is_new_item' => false,
                        'current_field_value' => ''
                    ];

                    $fields_value = fields_types::process($process_options);

                    if (in_array($field['type'], ['fieldtype_input_date', 'fieldtype_input_datetime', 'fieldtype_time']
                        ) and $fields_value == 0) {
                        $fields_value = '';
                    }

                    if (strlen($fields_value) > 0) {
                        $updated_fields[$field['id']] = $fields_value;

                        //insert comment history
                        db_perform(
                            'app_comments_history',
                            [
                                'comments_id' => $comments_id,
                                'fields_id' => $field['id'],
                                'fields_value' => $fields_value
                            ]
                        );

                        if ($field['type'] == 'fieldtype_time' and $cfg->get('sum_in_comments') == 1) {
                            $sql_data['field_' . $field['id']] = fieldtype_time::get_fields_sum_in_comments(
                                $current_entity_id,
                                $current_item_id,
                                $field['id']
                            );
                        } elseif ($field['type'] == 'fieldtype_input_numeric_comments') {
                            $filed_type = new $field['type'];
                            $sql_data['field_' . $field['id']] = $filed_type->get_fields_sum(
                                $current_entity_id,
                                $current_item_id,
                                $field['id']
                            );
                        } else {
                            $sql_data['field_' . $field['id']] = $fields_value;

                            //update choices values
                            $choices_values = new choices_values($current_entity_id);
                            $choices_values->process_by_field_id(
                                $current_item_id,
                                $field['id'],
                                $field['type'],
                                $fields_value
                            );
                        }
                    }
                }

                //update item if there are fiedls to change
                if (count($sql_data) > 0) {
                    $sql_data['date_updated'] = time();
                    db_perform(
                        'app_entity_' . $current_entity_id,
                        $sql_data,
                        'update',
                        "id='" . db_input($current_item_id) . "'"
                    );

                    $app_changed_fields = [];

                    //autoupdate all field types
                    fields_types::update_items_fields($current_entity_id, $current_item_id);

                    //run actions after item update
                    $processes = new processes($current_entity_id);
                    $processes->run_after_update($current_item_id);

                    //autostatus insert change in history if exist
                    foreach ($app_changed_fields as $field) {
                        db_perform(
                            'app_comments_history',
                            [
                                'comments_id' => $comments_id,
                                'fields_id' => $field['fields_id'],
                                'fields_value' => $field['fields_value']
                            ]
                        );
                    }
                } else {
                    db_perform(
                        'app_entity_' . $current_entity_id,
                        ['date_updated' => time()],
                        'update',
                        "id='" . db_input($current_item_id) . "'"
                    );
                }


                if (is_ext_installed()) {
                    //check public form notification
                    //using $item_info as item with previous values          
                    public_forms::send_client_notification($current_entity_id, $item_info, true);

                    //sending sms          
                    $modules = new modules('sms');
                    $sms = new sms($current_entity_id, $current_item_id);
                    $sms->send_to = false;
                    $sms->send_edit_msg($item_info);
                }
            }


            //send notificaton
            app_send_new_comment_notification($comments_id, $current_item_id, $current_entity_id);

            //track changes
            if (is_ext_installed()) {
                $log = new track_changes($current_entity_id, $current_item_id);
                $log->log_comment($comments_id, (isset($_POST['fields']) ? $updated_fields : []));

                //email rules
                $email_rules = new email_rules($current_entity_id, $current_item_id);
                $email_rules->send_edit_msg($item_info);
                $email_rules->send_comments_msg($item_info);
            }
        }

        redirect_to('items/info', 'path=' . $_POST['path']);
        break;
    case 'delete':
        $access_rules = new access_rules($current_entity_id, $current_item_id);

        if (!users::has_comments_access('delete', $access_rules->get_comments_access_schema())) {
            redirect_to('dashboard/access_forbidden');
        }

        if (isset($_GET['id'])) {
            attachments::delete_comments_attachments($_GET['id']);

            db_delete_row('app_comments', $_GET['id']);

            db_query("delete from app_comments_history where comments_id = '" . db_input($_GET['id']) . "'");

            fields_types::recalculate_numeric_comments_sum($current_entity_id, $current_item_id);

            $alerts->add(TEXT_COMMENT_WAS_DELETED, 'success');

            redirect_to('items/info', 'path=' . $_GET['path']);
        }
        break;
}