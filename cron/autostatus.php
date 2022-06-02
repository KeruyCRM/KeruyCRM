<?php

chdir(substr(__DIR__, 0, -5));

define('IS_CRON', true);

//load core
require('includes/application_core.php');

//include ext plugins
if (is_file('plugins/ext/application_core.php')) {
    require('plugins/ext/application_core.php');
}

//load app lng
if (is_file($v = 'includes/languages/' . CFG_APP_LANGUAGE)) {
    require($v);
}

if (is_file($v = 'plugins/ext/languages/' . CFG_APP_LANGUAGE)) {
    require($v);
}

$app_users_cache = users::get_cache();

if (is_ext_installed()) {
    $modules = new modules('sms');
}

//set user
$app_user = [
    'id' => 0,
    'group_id' => 0,
    'name' => CFG_EMAIL_NAME_FROM,
    'email' => CFG_EMAIL_ADDRESS_FROM,
];

//dynamic fields that can be using in autostatus filters
$dynamic_fields = [
    'fieldtype_input_date',
    'fieldtype_input_datetime',
    'fieldtype_hours_difference',
    'fieldtype_days_difference',
    'fieldtype_formula',
    'fieldtype_mysql_query',
    'fieldtype_dynamic_date',
    'fieldtype_date_added',
    'fieldtype_date_updated',
];

//autostatus fields to update
$autostatus_fields = [];

//all filters fields can be included in formula
$filters_fields = [];

//link choices id to reprts id
$choices_to_reports_id = [];

$fields_query = db_query("select * from app_fields where type='fieldtype_autostatus'");
while ($fields = db_fetch_array($fields_query)) {
    $cfg = new fields_types_cfg($fields['configuration']);

    $has_dynamic_fields = false;

    foreach (fields_choices::get_tree($fields['id'], 0, [], 0, '', '', true) as $choices) {
        $reports_info_query = db_query(
            "select * from app_reports where entities_id='" . $fields['entities_id'] . "' and reports_type='fields_choices" . $choices['id'] . "'"
        );
        if ($reports_info = db_fetch_array($reports_info_query)) {
            $choices_to_reports_id[$choices['id']] = $reports_info['id'];

            $reports_filters_query = db_query(
                "select f.id, f.type from app_reports_filters rf, app_fields f where reports_id='" . $reports_info['id'] . "' and rf.fields_id=f.id"
            );
            if ($reports_filters = db_fetch_array($reports_filters_query)) {
                if (in_array($reports_filters['type'], $dynamic_fields)) {
                    $has_dynamic_fields = true;
                }

                $filters_fields[] = $reports_filters['id'];
            }
        }
    }

    if ($has_dynamic_fields) {
        $autostatus_fields[] = [
            'id' => $fields['id'],
            'entities_id' => $fields['entities_id'],
            'choices' => fields_choices::get_tree($fields['id']),
            'cfg' => $cfg,
        ];
    }
}

//echo '<pre>';
//print_r($choices_to_reports_id);
//print_r($autostatus_fields);
//print_r($filters_fields);

if (count($autostatus_fields)) {
    foreach ($autostatus_fields as $autostatus_field) {
        $exclude_items = [];

        foreach ($autostatus_field['choices'] as $choices) {
            if (isset($choices_to_reports_id[$choices['id']])) {
                $reports_id = $choices_to_reports_id[$choices['id']];
                $entities_id = $autostatus_field['entities_id'];
                $cfg = $autostatus_field['cfg'];

                $sql_query_having = [];

                $listing_sql_query = reports::add_filters_query($reports_id, '');

                //prepare having query for formula fields
                if (isset($sql_query_having[$entities_id])) {
                    $listing_sql_query .= reports::prepare_filters_having_query($sql_query_having[$entities_id]);
                }

                //select items to update for current condition
                $update_items = [];
                $previous_item_info = [];
                $item_info_query = db_query(
                    "select e.* " . fieldtype_formula::prepare_query_select(
                        $entities_id,
                        '',
                        false,
                        ['fields_in_listing' => implode(',', $filters_fields)]
                    ) . fieldtype_related_records::prepare_query_select(
                        $entities_id,
                        ''
                    ) . " from app_entity_" . $entities_id . " e where e.id>0 " . $listing_sql_query . (count(
                        $exclude_items
                    ) ? " and e.id not in (" . implode(',', $exclude_items) . ")" : ''),
                    false
                );
                while ($item_info = db_fetch_array($item_info_query)) {
                    //update item if has different value
                    if ($item_info['field_' . $autostatus_field['id']] != $choices['id']) {
                        $update_items[] = $item_info['id'];

                        //set previous item info
                        $previous_item_info[$item_info['id']] = ['field_' . $autostatus_field['id'] => $item_info['field_' . $autostatus_field['id']]];
                    }

                    //exclude update items for next check
                    $exclude_items[] = $item_info['id'];
                }

                //if has itesm to update
                if (count($update_items)) {
                    $sql_data = [
                        'field_' . $autostatus_field['id'] => $choices['id']
                    ];

                    //update items
                    db_perform(
                        'app_entity_' . $entities_id,
                        $sql_data,
                        'update',
                        "id in (" . implode(',', $update_items) . ")"
                    );

                    if (is_ext_installed()) {
                        //send notification
                        foreach ($previous_item_info as $item_id => $item_info) {
                            //sending sms
                            $sms = new sms($entities_id, $item_id);
                            $sms->send_edit_msg($item_info);

                            //email rules
                            $email_rules = new email_rules($entities_id, $item_id);
                            $email_rules->send_edit_msg($item_info);

                            //run process
                            if (is_ext_installed() and ($process_id = (int)$cfg->get(
                                    'run_process_for_choice_' . $choices['id']
                                )) > 0) {
                                $process_info_query = db_query(
                                    "select * from app_ext_processes where id={$process_id}"
                                );
                                if ($process_info = db_fetch_array($process_info_query)) {
                                    $processes = new processes($entities_id);
                                    $processes->items_id = $item_id;
                                    $processes->run($process_info, false, true);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}