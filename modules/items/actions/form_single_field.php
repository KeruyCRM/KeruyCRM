<?php

$field_info_query = db_query("select * from app_fields where id='" . _GET('field_id') . "'");
if (!$field_info = db_fetch_array($field_info_query)) {
    redirect_to('dashboard/page_not_found');
}

$item_info_query = db_query("select * from app_entity_{$current_entity_id} where id='" . $current_item_id . "'");
if (!$obj = db_fetch_array($item_info_query)) {
    redirect_to('dashboard/page_not_found');
}

switch ($app_module_action) {
    case 'save':

        $app_changed_fields = [];

        $item_info = $obj;

        $choices_values = new choices_values($current_entity_id);

        //submited field value
        $value = (isset($_POST['fields'][$field_info['id']]) ? $_POST['fields'][$field_info['id']] : '');

        //current field value
        $current_field_value = (isset($obj['field_' . $field_info['id']]) ? $obj['field_' . $field_info['id']] : '');

        //prepare process options
        $process_options = [
            'class' => $field_info['type'],
            'value' => $value,
            'field' => $field_info,
            'is_new_item' => false,
            'current_field_value' => $current_field_value,
            'item' => $item_info,
        ];

        $sql_data['field_' . $field_info['id']] = fields_types::process($process_options);

        //prepare choices values for fields with multiple values
        $choices_values->prepare($process_options);

        //update item
        $sql_data['date_updated'] = time();
        db_perform('app_entity_' . $current_entity_id, $sql_data, 'update', "id='" . $current_item_id . "'", false);
        $item_id = $current_item_id;

        //insert choices values for fields with multiple values
        $choices_values->process($item_id);

        //autoupdate all field types
        fields_types::update_items_fields($current_entity_id, $item_id);

        //atuocreate comments if fields changed
        if (count($app_changed_fields)) {
            comments::add_comment_notify_when_fields_changed($current_entity_id, $item_id, $app_changed_fields);
        }

        if (is_ext_installed()) {
            //run actions after item update
            $processes = new processes($current_entity_id);
            $processes->run_after_update($item_id);

            //check public form notification
            //using $item_info as item with previous values
            public_forms::send_client_notification($current_entity_id, $item_info);

            //sending sms
            $modules = new modules('sms');
            $sms = new sms($current_entity_id, $item_id);
            $sms->send_edit_msg($item_info);

            //subscribe
            $modules = new modules('mailing');
            $mailing = new mailing($current_entity_id, $item_id);
            $mailing->update($item_info);

            //email rules
            $email_rules = new email_rules($current_entity_id, $item_id);
            $email_rules->send_edit_msg($item_info);
        }

        exit();
        break;
}
