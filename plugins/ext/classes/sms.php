<?php

class sms
{

    public $entity_id;
    public $item_id;
    public $item_info;
    public $is_debug;
    public $module;
    public $module_id;
    public $send_to;

    function __construct($entity_id, $item_id)
    {
        $this->is_debug = false;

        $this->entity_id = $entity_id;

        $this->item_id = $item_id;

        $this->send_to = [];
    }

    function set_current_item_info()
    {
        $item_query = db_query(
            "select e.* " . fieldtype_formula::prepare_query_select(
                $this->entity_id
            ) . " from app_entity_" . $this->entity_id . " e where id='" . $this->item_id . "'",
            false
        );
        if ($item = db_fetch_array($item_query)) {
            $this->item_info = $item;
        }
    }

    static function get_action_type_choices()
    {
        $choices = [];
        $choices[TEXT_EXT_ADDING_NEW_RECORD]['insert_send_to_number'] = TEXT_EXT_SEND_TO_NUMBER;
        $choices[TEXT_EXT_ADDING_NEW_RECORD]['insert_send_to_record_number'] = TEXT_EXT_SEND_TO_RECORD_NUMBER;
        $choices[TEXT_EXT_ADDING_NEW_RECORD]['insert_send_to_user_number'] = TEXT_EXT_SEND_TO_USER_NUMBER;
        $choices[TEXT_EXT_ADDING_NEW_RECORD]['insert_send_to_number_in_entity'] = TEXT_EXT_SEND_TO_RELATED_ENTITY;

        $choices[TEXT_EXT_ADDITING_RECORD]['edit_send_to_number'] = TEXT_EXT_SEND_TO_NUMBER;
        $choices[TEXT_EXT_ADDITING_RECORD]['edit_send_to_record_number'] = TEXT_EXT_SEND_TO_RECORD_NUMBER;
        $choices[TEXT_EXT_ADDITING_RECORD]['edit_send_to_user_number'] = TEXT_EXT_SEND_TO_USER_NUMBER;
        $choices[TEXT_EXT_ADDITING_RECORD]['edit_send_to_number_in_entity'] = TEXT_EXT_SEND_TO_RELATED_ENTITY;

        $choices[TEXT_EXT_SEND_BY_DATE]['schedule_send_to_number'] = TEXT_EXT_SEND_TO_NUMBER;
        $choices[TEXT_EXT_SEND_BY_DATE]['schedule_send_to_record_number'] = TEXT_EXT_SEND_TO_RECORD_NUMBER;
        $choices[TEXT_EXT_SEND_BY_DATE]['schedule_send_to_user_number'] = TEXT_EXT_SEND_TO_USER_NUMBER;
        $choices[TEXT_EXT_SEND_BY_DATE]['schedule_send_to_number_in_entity'] = TEXT_EXT_SEND_TO_RELATED_ENTITY;


        return $choices;
    }

    static function get_action_type($type)
    {
        $html = '';
        switch (true) {
            case strstr($type, 'insert'):
                $html = '<span class="label label-success">' . TEXT_EXT_ADDING_NEW_RECORD . '</label>';
                break;
            case strstr($type, 'edit'):
                $html = '<span class="label label-info">' . TEXT_EXT_ADDITING_RECORD . '</span>';
                break;
            case strstr($type, 'schedule'):
                $html = '<span class="label label-default">' . TEXT_EXT_SEND_BY_DATE . '</span>';
                break;
        }

        return $html;
    }

    static function get_action_type_name($type)
    {
        $text = '';

        switch ($type) {
            case 'edit_send_to_number':
            case 'insert_send_to_number':
            case 'schedule_send_to_number':
                $text .= TEXT_EXT_SEND_TO_NUMBER;
                break;
            case 'edit_send_to_record_number':
            case 'insert_send_to_record_number':
            case 'schedule_send_to_record_number':
                $text .= TEXT_EXT_SEND_TO_RECORD_NUMBER;
                break;
            case 'edit_send_to_user_number':
            case 'insert_send_to_user_number':
            case 'schedule_send_to_user_number':
                $text .= TEXT_EXT_SEND_TO_USER_NUMBER;
                break;
            case 'insert_send_to_number_in_entity':
            case 'edit_send_to_number_in_entity':
            case 'schedule_send_to_number_in_entity':
                $text .= TEXT_EXT_SEND_TO_RELATED_ENTITY;
                break;
        }

        return $text;
    }

    function prepare_parent_value_field($entities_id, $fields_id, $value, $item_info)
    {
        global $app_fields_cache;

        if (isset($app_fields_cache[$entities_id][$fields_id])) {
            if ($app_fields_cache[$entities_id][$fields_id]['type'] == 'fieldtype_parent_value') {
                $fieldtype_parent_value = new fieldtype_parent_value;

                $options = [
                    'field' => [
                        'entities_id' => $entities_id,
                        'configuration' => $app_fields_cache[$entities_id][$fields_id]['configuration']
                    ],
                    'item' => $item_info,
                ];

                $value = $fieldtype_parent_value->output($options);

                return $value;
            }
        }

        return $value;
    }

    function send_insert_msg()
    {
        //get current item info
        $this->set_current_item_info();

        $text_pattern = new fieldtype_text_pattern;

        $rules_query = db_query(
            "select r.*, m.module from app_ext_sms_rules r, app_ext_modules m where r.entities_id='" . $this->entity_id . "' and length(description)>0 and (r.fields_id>0 or length(r.phone)>0) and m.id=r.modules_id and m.is_active=1"
        );
        while ($rules = db_fetch_array($rules_query)) {
            //check field
            if ($rules['monitor_fields_id'] > 0) {
                //check fields choices
                if (strlen($rules['monitor_choices'])) {
                    if (!in_array(
                        $this->item_info['field_' . $rules['monitor_fields_id']],
                        explode(',', $rules['monitor_choices'])
                    )) {
                        continue;
                    }
                }
            }

            $this->module = $rules['module'];
            $this->module_id = $rules['modules_id'];

            $text = $text_pattern->output_singe_text($rules['description'], $this->entity_id, $this->item_info);

            $send_to = [];

            switch ($rules['action_type']) {
                case 'insert_send_to_number':
                    if (strlen($rules['phone'])) {
                        $send_to = explode(',', $rules['phone']);
                    }
                    break;
                case 'insert_send_to_record_number':
                    if (isset($this->item_info['field_' . $rules['fields_id']])) {
                        //check if field type 'parent_value' and get value
                        $this->item_info['field_' . $rules['fields_id']] = $this->prepare_parent_value_field(
                            $this->entity_id,
                            $rules['fields_id'],
                            $this->item_info['field_' . $rules['fields_id']],
                            $this->item_info
                        );

                        if (strlen($this->item_info['field_' . $rules['fields_id']])) {
                            $send_to = [$this->item_info['field_' . $rules['fields_id']]];
                        }
                    }

                    break;
                case 'insert_send_to_user_number':

                    if (strlen($rules['send_to_assigned_users'])) {
                        $this->send_to = $this->get_assigned_users($rules);
                    } else {
                        $this->send_to = array_unique($this->send_to);
                    }

                    foreach ($this->send_to as $user_id) {
                        $user_info = db_find('app_entity_1', $user_id);
                        if (isset($user_info['field_' . $rules['fields_id']])) {
                            if (strlen($user_info['field_' . $rules['fields_id']])) {
                                $send_to[] = $user_info['field_' . $rules['fields_id']];
                            }
                        }
                    }
                    break;

                case 'insert_send_to_number_in_entity':
                    $value = explode(':', $rules['phone']);

                    $field_id = $value[0];
                    $send_to_field_id = $value[1];

                    if (isset($this->item_info['field_' . $field_id])) {
                        $fields_query = db_query("select configuration from app_fields where id='" . $field_id . "'");
                        if ($fields = db_fetch_array($fields_query)) {
                            $cfg = new settings($fields['configuration']);
                            $send_to_entity_id = $cfg->get('entity_id');

                            $send_to = $this->get_set_to_from_entity(
                                $send_to_entity_id,
                                $this->item_info['field_' . $field_id],
                                $send_to_field_id
                            );
                        }
                    }
                    break;
            }

            //print_rr($send_to);
            //exit();

            if (count($send_to)) {
                $this->send($send_to, $text);
            }
        }
    }

    function get_assigned_users($rules)
    {
        global $app_fields_cache;

        $send_to = [];
        //print_rr($this->item_info);

        foreach (explode(',', $rules['send_to_assigned_users']) as $field_id) {
            if ($app_fields_cache[$rules['entities_id']][$field_id]['type'] == 'fieldtype_created_by') {
                $send_to[] = $this->item_info['created_by'];
            } elseif (isset($this->item_info['field_' . $field_id]) and strlen(
                    $this->item_info['field_' . $field_id]
                )) {
                $send_to = array_merge($send_to, explode(',', $this->item_info['field_' . $field_id]));
            }
        }

        //print_rr($send_to);        
        //exit();

        return $send_to;
    }

    function send_edit_msg($previous_item_info)
    {
        //get current item info
        $this->set_current_item_info();

        $text_pattern = new fieldtype_text_pattern;

        $rules_query = db_query(
            "select r.*, m.module from app_ext_sms_rules r, app_ext_modules m where r.entities_id='" . $this->entity_id . "' and monitor_fields_id>0 and length(description)>0 and (r.fields_id>0 or length(r.phone)>0) and m.id=r.modules_id and m.is_active=1"
        );
        while ($rules = db_fetch_array($rules_query)) {
            //check if field value changed and skip notification if not changed
            if ($this->item_info['field_' . $rules['monitor_fields_id']] == $previous_item_info['field_' . $rules['monitor_fields_id']]) {
                continue;
            }

            //check fields choices
            if (strlen($rules['monitor_choices'])) {
                if (!in_array(
                    $this->item_info['field_' . $rules['monitor_fields_id']],
                    explode(',', $rules['monitor_choices'])
                )) {
                    continue;
                }
            }

            $this->module = $rules['module'];
            $this->module_id = $rules['modules_id'];

            $text = $text_pattern->output_singe_text($rules['description'], $this->entity_id, $this->item_info);

            $send_to = [];

            switch ($rules['action_type']) {
                case 'edit_send_to_number':
                    if (strlen($rules['phone'])) {
                        $send_to = explode(',', $rules['phone']);
                    }
                    break;
                case 'edit_send_to_record_number':
                    if (isset($this->item_info['field_' . $rules['fields_id']])) {
                        //check if field type 'parent_value' and get value
                        $this->item_info['field_' . $rules['fields_id']] = $this->prepare_parent_value_field(
                            $this->entity_id,
                            $rules['fields_id'],
                            $this->item_info['field_' . $rules['fields_id']],
                            $previous_item_info
                        );

                        if (strlen($this->item_info['field_' . $rules['fields_id']])) {
                            $send_to = [$this->item_info['field_' . $rules['fields_id']]];
                        }
                    }

                    break;
                case 'edit_send_to_user_number':

                    if (strlen($rules['send_to_assigned_users'])) {
                        $this->send_to = $this->get_assigned_users($rules);
                    } elseif (!$this->send_to) {
                        $this->send_to = users::get_assigned_users_by_item($this->entity_id, $this->item_info['id']);
                    }

                    $this->send_to = array_unique($this->send_to);

                    foreach ($this->send_to as $user_id) {
                        $user_info = db_find('app_entity_1', $user_id);
                        if (isset($user_info['field_' . $rules['fields_id']])) {
                            if (strlen($user_info['field_' . $rules['fields_id']])) {
                                $send_to[] = $user_info['field_' . $rules['fields_id']];
                            }
                        }
                    }
                    break;
                case 'edit_send_to_number_in_entity':
                    $value = explode(':', $rules['phone']);

                    $field_id = $value[0];
                    $send_to_field_id = $value[1];

                    if (isset($this->item_info['field_' . $field_id])) {
                        $fields_query = db_query("select configuration from app_fields where id='" . $field_id . "'");
                        if ($fields = db_fetch_array($fields_query)) {
                            $cfg = new settings($fields['configuration']);
                            $send_to_entity_id = $cfg->get('entity_id');

                            $send_to = $this->get_set_to_from_entity(
                                $send_to_entity_id,
                                $this->item_info['field_' . $field_id],
                                $send_to_field_id
                            );
                        }
                    }
                    break;
            }

            if (count($send_to)) {
                $this->send($send_to, $text);
            }
        }
    }

    function send($send_to, $text)
    {
        if ($this->is_debug) {
            $errfile = fopen("log/sms_" . date("M_Y") . ".txt", "a+");
            foreach ($send_to as $phone) {
                fputs(
                    $errfile,
                    $time = date(
                            "d M Y H:i:s"
                        ) . ' ' . $this->module_id . ':' . $this->module . ': ' . $phone . " " . $text . "\n\n"
                );
            }
            fclose($errfile);
        } else {
            $module = new $this->module;
            $module->send($this->module_id, $send_to, $text);
        }
    }

    static function send_by_module($module_id, $send_to, $text)
    {
        $is_debug = false;

        $module_info_query = db_query(
            "select * from app_ext_modules where id='" . (int)$module_id . "' and type='sms' and is_active=1"
        );
        if ($module_info = db_fetch_array($module_info_query)) {
            if ($is_debug) {
                $errfile = fopen("log/sms_" . date("M_Y") . ".txt", "a+");
                fputs(
                    $errfile,
                    $time = date(
                            "d M Y H:i:s"
                        ) . ' ' . $module_info['id'] . ':' . $module_info['module'] . ': ' . $send_to . " " . $text . "\n\n"
                );
                fclose($errfile);
            } else {
                modules::include_module($module_info, 'sms');

                $send_to = [$send_to];

                $module = new $module_info['module'];
                $module->send($module_info['id'], $send_to, $text);
            }
        }
    }

    function get_set_to_from_entity($entities_id, $values, $phone_field_id)
    {
        global $app_fields_cache;

        if (!strlen($values) or !isset($app_fields_cache[$entities_id][$phone_field_id])) {
            return [];
        }

        $send_to = [];
        $items_query = db_query(
            "select field_{$phone_field_id} from app_entity_{$entities_id} where id in ({$values})"
        );
        while ($items = db_fetch_array($items_query)) {
            $send_to[] = $items['field_' . $phone_field_id];
        }

        //print_r($send_to);
        //exit();

        return $send_to;
    }

    static function msg_by_date($date_type = 'day')
    {
        global $app_fields_cache;

        $modules = new modules('sms');

        $rules_query = db_query(
            "select r.*, m.module  from app_ext_sms_rules r, app_entities e, app_ext_modules m  where r.entities_id=e.id and date_type ='{$date_type}' and action_type in ('schedule_send_to_number','schedule_send_to_record_number','schedule_send_to_user_number','schedule_send_to_number_in_entity') and length(description)>0 and (r.fields_id>0 or length(r.phone)>0)  and m.id=r.modules_id and m.is_active=1 order by e.id"
        );
        while ($rules = db_fetch_array($rules_query)) {
            $date_fields_id = $rules['date_fields_id'];
            $entities_id = $rules['entities_id'];
            $number_of_days = $rules['number_of_days'];

            //check if field exist
            if (!isset($app_fields_cache[$entities_id][$date_fields_id])) {
                continue;
            }

            //check if $number_of_days setup
            if (!strlen($number_of_days)) {
                continue;
            }

            //print_rr($rules);

            foreach (explode(',', $number_of_days) as $day) {
                $use_function = (strstr($day[0], '-') ? 'DATE_SUB' : 'DATE_ADD');
                $use_date = $date_type == 'day' ? 'DAY' : 'HOUR';
                $use_format = $date_type == 'day' ? '%Y-%m-%d' : '%Y-%m-%d %H';;
                $day = (int)str_replace(['+', '-'], '', $day);
                $field_name = 'field_' . $date_fields_id;

                $item_info_query = db_query(
                    "select e.* " . fieldtype_formula::prepare_query_select(
                        $entities_id,
                        '',
                        false,
                        ['fields_in_listing' => $date_fields_id]
                    ) . " from app_entity_{$entities_id} e where FROM_UNIXTIME({$field_name},'{$use_format}')=date_format({$use_function}(now(),INTERVAL {$day} {$use_date}),'$use_format')",
                    false
                );
                while ($item_info = db_fetch_array($item_info_query)) {
                    //print_rr($item_info); 
                    //                                      
                    //sending sms

                    $sms = new sms($entities_id, $item_info['id']);
                    $sms->item_info = $item_info;
                    $sms->send_to = items::get_send_to($entities_id, $item_info['id'], $item_info);
                    $sms->send_msg_by_date($rules);
                }
            }
        }
    }


    function send_msg_by_date($rules = false)
    {
        //get current item info

        $text_pattern = new fieldtype_text_pattern;

        if ($rules) {
            //print_rr($rules);
            //print_rr($this->item_info);

            //check field
            if ($rules['monitor_fields_id'] > 0) {
                //check fields choices
                if (strlen($rules['monitor_choices'])) {
                    if (!in_array(
                        $this->item_info['field_' . $rules['monitor_fields_id']],
                        explode(',', $rules['monitor_choices'])
                    )) {
                        return false;
                    }
                }
            }

            $this->module = $rules['module'];
            $this->module_id = $rules['modules_id'];

            $text = $text_pattern->output_singe_text($rules['description'], $this->entity_id, $this->item_info);

            $send_to = [];

            switch ($rules['action_type']) {
                case 'schedule_send_to_number':
                    if (strlen($rules['phone'])) {
                        $send_to = explode(',', $rules['phone']);
                    }
                    break;
                case 'schedule_send_to_record_number':
                    if (isset($this->item_info['field_' . $rules['fields_id']])) {
                        //check if field type 'parent_value' and get value
                        $this->item_info['field_' . $rules['fields_id']] = $this->prepare_parent_value_field(
                            $this->entity_id,
                            $rules['fields_id'],
                            $this->item_info['field_' . $rules['fields_id']],
                            $this->item_info
                        );

                        if (strlen($this->item_info['field_' . $rules['fields_id']])) {
                            $send_to = [$this->item_info['field_' . $rules['fields_id']]];
                        }
                    }

                    break;
                case 'schedule_send_to_user_number':

                    if (strlen($rules['send_to_assigned_users'])) {
                        $this->send_to = $this->get_assigned_users($rules);
                    } else {
                        $this->send_to = array_unique($this->send_to);
                    }

                    foreach ($this->send_to as $user_id) {
                        $user_info = db_find('app_entity_1', $user_id);
                        if (isset($user_info['field_' . $rules['fields_id']])) {
                            if (strlen($user_info['field_' . $rules['fields_id']])) {
                                $send_to[] = $user_info['field_' . $rules['fields_id']];
                            }
                        }
                    }
                    break;

                case 'schedule_send_to_number_in_entity':
                    $value = explode(':', $rules['phone']);

                    $field_id = $value[0];
                    $send_to_field_id = $value[1];

                    if (isset($this->item_info['field_' . $field_id])) {
                        $fields_query = db_query("select configuration from app_fields where id='" . $field_id . "'");
                        if ($fields = db_fetch_array($fields_query)) {
                            $cfg = new settings($fields['configuration']);
                            $send_to_entity_id = $cfg->get('entity_id');

                            $send_to = $this->get_set_to_from_entity(
                                $send_to_entity_id,
                                $this->item_info['field_' . $field_id],
                                $send_to_field_id
                            );
                        }
                    }
                    break;
            }

            //print_rr($send_to);
            //exit();

            if (count($send_to)) {
                $this->send($send_to, $text);
            }
        }
    }

}
