<?php

class recurring_tasks
{
    static function run()
    {
        $server = [];
        $server['date'] = get_date_timestamp(date('Y-m-d'));
        $server['day'] = date('N');
        $server['hour'] = (int)date('H');

        $tasks_query = db_query(
            "select * from app_ext_recurring_tasks where is_active=1 and repeat_time=" . $server['hour'] . " and repeat_start<" . $server['date'] . " and (repeat_end>=" . $server['date'] . " or repeat_end=0) order by id"
        );
        while ($tasks = db_fetch_array($tasks_query)) {
            $is_repeat = false;

            //debug
            //echo '<pre>';
            //print_r($tasks);
            //print_r($server);

            //check repeat type
            switch ($tasks['repeat_type']) {
                case 'daily':
                    $days_diff = ($server['date'] - $tasks['repeat_start']) / 86400;
                    $repeat_count = floor($days_diff / $tasks['repeat_interval']);

                    if ($days_diff / $tasks['repeat_interval'] == floor(
                            $days_diff / $tasks['repeat_interval']
                        ) and (($repeat_count <= $tasks['repeat_limit'] and $tasks['repeat_limit'] > 0) or $tasks['repeat_limit'] == 0)) {
                        $is_repeat = true;
                    }
                    break;
                case 'weekly':
                    $week_diff = floor(($server['date'] - $tasks['repeat_start']) / 604800);
                    if ($week_diff > 0) {
                        $repeat_count = floor($week_diff / $tasks['repeat_interval']);
                        if (in_array(
                                $server['day'],
                                explode(',', $tasks['repeat_days'])
                            ) and $week_diff / $tasks['repeat_interval'] == floor(
                                $week_diff / $tasks['repeat_interval']
                            ) and (($repeat_count <= $tasks['repeat_limit'] and $tasks['repeat_limit'] > 0) or $tasks['repeat_limit'] == 0)) {
                            $is_repeat = true;
                        }
                    }
                    break;
                case 'monthly':
                    $d1 = new DateTime(date('Y-m-d', $tasks['repeat_start']));
                    $d2 = new DateTime(date('Y-m-d', $server['date']));

                    $months_diff = $d1->diff($d2)->m + ($d1->diff($d2)->y * 12);

                    if ($months_diff > 0) {
                        $repeat_count = floor($months_diff / $tasks['repeat_interval']);
                        if ($server['date'] == strtotime(
                                "+{$months_diff} month",
                                $tasks['repeat_start']
                            ) and $months_diff / $tasks['repeat_interval'] == floor(
                                $months_diff / $tasks['repeat_interval']
                            ) and (($repeat_count <= $tasks['repeat_limit'] and $tasks['repeat_limit'] > 0) or $tasks['repeat_limit'] == 0)) {
                            $is_repeat = true;
                        }
                    }
                    break;
                case 'yearly':
                    $d1 = new DateTime(date('Y-m-d', $tasks['repeat_start']));
                    $d2 = new DateTime(date('Y-m-d', $server['date']));

                    $year_diff = $d1->diff($d2)->y;

                    if ($year_diff > 0) {
                        $repeat_count = floor($year_diff / $tasks['repeat_interval']);
                        if ($server['date'] == strtotime(
                                "+{$year_diff} year",
                                $tasks['repeat_start']
                            ) and $year_diff / $tasks['repeat_interval'] == floor(
                                $year_diff / $tasks['repeat_interval']
                            ) and (($repeat_count <= $tasks['repeat_limit'] and $tasks['repeat_limit'] > 0) or $tasks['repeat_limit'] == 0)) {
                            $is_repeat = true;
                        }
                    }
                    break;
            }

            //echo '<br>is_repeat: ' . (int)$is_repeat;

            if ($is_repeat) {
                self::repeat($tasks['entities_id'], $tasks['items_id'], $tasks['id']);
            }
        }
    }

    static function repeat($entities_id, $items_id, $tasks_id)
    {
        global $app_user, $app_users_cache;

        $item_info_query = db_query("select * from app_entity_" . $entities_id . " where id='" . $items_id . "'");
        if ($item_info = db_fetch_array($item_info_query)) {
            $app_user['id'] = $item_info['created_by'];

            $users_info_query = db_query("select * from app_entity_1 where id='" . db_input($app_user['id']) . "'");
            if ($users_info = db_fetch_array($users_info_query)) {
                $app_user['email'] = $users_info['field_9'];
                $app_user['name'] = $app_users_cache[$users_info['id']]['name'];
            }

            $sql_data = [];

            $choices_values = new choices_values($entities_id);

            $fields_query = db_query(
                "select f.* from app_fields f where f.type not in (" . fields_types::get_reserverd_types_list(
                ) . ") and  f.entities_id='" . db_input($entities_id) . "'"
            );
            while ($field = db_fetch_array($fields_query)) {
                $value = $item_info['field_' . $field['id']];

                //copy attachmetns
                if (in_array(
                    $field['type'],
                    ['fieldtype_input_file', 'fieldtype_image', 'fieldtype_image_ajax', 'fieldtype_attachments']
                )) {
                    $value = attachments::copy($value);
                }

                $tasks_field_query = db_query(
                    "select tf.value, f.type from app_ext_recurring_tasks_fields tf, app_fields f where tf.fields_id='" . $field['id'] . "' and tf.fields_id=f.id and tf.tasks_id='" . $tasks_id . "'"
                );
                if ($tasks_field = db_fetch_array($tasks_field_query)) {
                    //handle dates
                    if ($tasks_field['type'] == 'fieldtype_input_date') {
                        $value = (strlen($tasks_field['value']) < 5 ? get_date_timestamp(
                            date('Y-m-d', strtotime($tasks_field['value'] . ' day'))
                        ) : $tasks_field['value']);
                    } elseif ($tasks_field['type'] == 'fieldtype_input_datetime') {
                        $value = (strlen($tasks_field['value']) < 5 ? strtotime(
                            $tasks_field['value'] . ' day'
                        ) : $tasks_field['value']);
                    } else {
                        $value = $tasks_field['value'];

                        if (strlen($value)) {
                            $process_options = [
                                'class' => $tasks_field['type'],
                                'field' => ['id' => $field['id']],
                                'value' => explode(',', $value)
                            ];

                            $choices_values->prepare($process_options);
                        }
                    }
                }

                $sql_data['field_' . $field['id']] = $value;
            }

            $sql_data['date_added'] = time();
            $sql_data['created_by'] = $item_info['created_by'];
            $sql_data['parent_item_id'] = $item_info['parent_item_id'];

            db_perform('app_entity_' . $entities_id, $sql_data);

            $item_id = db_insert_id();

            //copy choices values
            $sql_data = [];
            $choices_values_query = db_query(
                "select * from app_entity_" . $entities_id . "_values where items_id = " . db_input($items_id)
            );
            while ($values = db_fetch_array($choices_values_query)) {
                $sql_data[] = [
                    'items_id' => $item_id,
                    'fields_id' => $values['fields_id'],
                    'value' => $values['value'],
                ];
            }

            db_batch_insert("app_entity_" . $entities_id . "_values", $sql_data);

            //insert choices values for fields with multiple values
            $choices_values->process($item_id);

            //send nofitication
            items::send_new_item_nofitication($entities_id, $item_id);

            //log changeds
            $log = new track_changes($entities_id, $item_id);
            $log->log_insert();

            //autoupdate all field types
            fields_types::update_items_fields($entities_id, $item_id);

            //email rules
            $email_rules = new email_rules($entities_id, $item_id);
            $email_rules->send_edit_msg($item_info);

            //sending sms
            $modules = new modules('sms');
            $sms = new sms($entities_id, $item_id);
            $sms->send_to = items::get_send_to($entities_id, $item_id);
            $sms->send_insert_msg();
        }
    }

    static function delete($entities_id, $items_id)
    {
        $tasks_query = db_query(
            "select id from app_ext_recurring_tasks where entities_id='" . $entities_id . "' and items_id='" . $items_id . "'"
        );
        while ($tasks = db_fetch_array($tasks_query)) {
            db_query("delete from app_ext_recurring_tasks where id='" . $tasks['id'] . "'");
            db_query("delete from app_ext_recurring_tasks_fields where tasks_id='" . $tasks['id'] . "'");
        }
    }

    static function get_repeat_time_choices()
    {
        $choices = [];

        for ($i = 0; $i < 24; $i++) {
            $choices[$i] = ($i < 10 ? '0' . $i : $i) . ':00';
        }

        return $choices;
    }

    static function get_repeat_types()
    {
        $list = [
            'daily' => TEXT_EXT_EVENT_REPEAT_DAILY,
            'weekly' => TEXT_EXT_EVENT_REPEAT_WEEKLY,
            'monthly' => TEXT_EXT_EVENT_REPEAT_MONTHLY,
            'yearly' => TEXT_EXT_EVENT_REPEAT_YEARLY,
        ];
        return $list;
    }

    public static function get_actions_fields_choices($entity_id)
    {
        $available_types = [
            'fieldtype_checkboxes',
            'fieldtype_radioboxes',
            'fieldtype_boolean',
            'fieldtype_dropdown',
            'fieldtype_dropdown_multiple',
            'fieldtype_input_date',
            'fieldtype_input_datetime',
            'fieldtype_input_numeric',
            'fieldtype_input',
            'fieldtype_input_email',
            'fieldtype_input_url',
            'fieldtype_input_masked',
            'fieldtype_textarea',
            'fieldtype_textarea_wysiwyg',
            'fieldtype_input_masked',
            'fieldtype_entity',
            'fieldtype_users',
            'fieldtype_users_ajax',
            'fieldtype_grouped_users',
            'fieldtype_progress',
            'fieldtype_todo_list',
            'fieldtype_time',
            'fieldtype_stages',
            'fieldtype_entity_ajax',
            'fieldtype_entity_multilevel',
            'fieldtype_iframe',
            'fieldtype_user_accessgroups',
        ];
        $choices = [];
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in (\"" . implode(
                '","',
                $available_types
            ) . "\")  and f.entities_id='" . db_input(
                $entity_id
            ) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($v = db_fetch_array($fields_query)) {
            $choices[$v['id']] = $v['name'];
        }

        return $choices;
    }

    public static function output_action_field_value($actions_fields)
    {
        $field = db_find('app_fields', $actions_fields['fields_id']);

        $output_options = [
            'class' => $field['type'],
            'value' => $actions_fields['value'],
            'field' => $field,
            'is_listing' => true,
        ];

        if (in_array($actions_fields['field_type'], ['fieldtype_users', 'fieldtype_users_ajax', 'fieldtype_dropdown']
        )) {
            if (strstr($actions_fields['value'], '[')) {
                return $actions_fields['value'];
            } else {
                return fields_types::output($output_options);
            }
        } elseif (in_array($actions_fields['field_type'], ['fieldtype_input_date', 'fieldtype_input_datetime'])) {
            if (strlen($actions_fields['value']) < 10) {
                return $actions_fields['value'];
            } else {
                return fields_types::output($output_options);
            }
        } elseif (in_array($actions_fields['field_type'], ['fieldtype_input_numeric']) and strstr(
                $actions_fields['value'],
                '['
            )) {
            return $actions_fields['value'];
        } else {
            return fields_types::output($output_options);
        }
    }
}