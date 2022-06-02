<?php

class email_rules
{

    public $entity_id;
    public $item_id;
    public $item_info;
    public $parent_item_info;
    public $is_debug;
    public $module;
    public $module_id;
    public $send_to;
    public $path;
    public $attach_template_files;

    function __construct($entity_id, $item_id)
    {
        $this->is_debug = false;

        $this->entity_id = $entity_id;

        $this->item_id = $item_id;

        $path_info = items::get_path_info($entity_id, $item_id);

        $this->path = $path_info['full_path'];

        $this->send_to = [];

        $this->attach_template_files = [];

        $this->parent_item_info = false;
    }

    function set_current_item_info()
    {
        global $app_entities_cache;

        $item_query = db_query(
            "select e.* " . fieldtype_formula::prepare_query_select(
                $this->entity_id,
                ''
            ) . " from app_entity_" . $this->entity_id . " e where id='" . $this->item_id . "'",
            false
        );
        if ($item = db_fetch_array($item_query)) {
            $this->item_info = $item;
        }

        if (($parent_entity_id = $app_entities_cache[$this->entity_id]['parent_id']) > 0 and $this->item_info['parent_item_id'] > 0) {
            $item_query = db_query(
                "select e.* " . fieldtype_formula::prepare_query_select(
                    $parent_entity_id,
                    ''
                ) . " from app_entity_" . $parent_entity_id . " e where id='" . $this->item_info['parent_item_id'] . "'",
                false
            );
            if ($item = db_fetch_array($item_query)) {
                $this->parent_item_info = $item;
            }
        }
    }

    static function get_action_type_choices()
    {
        $choices = [];
        $choices[TEXT_EXT_ADDING_NEW_RECORD]['insert_send_to_users'] = TEXT_EXT_SEND_TO_USERS;
        $choices[TEXT_EXT_ADDING_NEW_RECORD]['insert_send_to_assigned_users'] = TEXT_EXT_SEND_TO_ASSIGNED_USERS;
        $choices[TEXT_EXT_ADDING_NEW_RECORD]['insert_send_to_email'] = TEXT_EXT_SEND_TO_EMAIL;
        $choices[TEXT_EXT_ADDING_NEW_RECORD]['insert_send_to_assigned_email'] = TEXT_EXT_SEND_TO_ASSIGNED_EMAIL;
        $choices[TEXT_EXT_ADDING_NEW_RECORD]['insert_send_by_visibility_rules'] = TEXT_EXT_SEND_BY_VISIBILITY_RULES;

        $choices[TEXT_EXT_ADDITING_RECORD]['edit_send_to_users'] = TEXT_EXT_SEND_TO_USERS;
        $choices[TEXT_EXT_ADDITING_RECORD]['edit_send_to_assigned_users'] = TEXT_EXT_SEND_TO_ASSIGNED_USERS;
        $choices[TEXT_EXT_ADDITING_RECORD]['edit_send_to_email'] = TEXT_EXT_SEND_TO_EMAIL;
        $choices[TEXT_EXT_ADDITING_RECORD]['edit_send_to_assigned_email'] = TEXT_EXT_SEND_TO_ASSIGNED_EMAIL;
        $choices[TEXT_EXT_ADDITING_RECORD]['edit_send_by_visibility_rules'] = TEXT_EXT_SEND_BY_VISIBILITY_RULES;

        $choices[TEXT_EXT_NEW_COMMENT]['comment_send_to_users'] = TEXT_EXT_SEND_TO_USERS;
        $choices[TEXT_EXT_NEW_COMMENT]['comment_send_to_assigned_users'] = TEXT_EXT_SEND_TO_ASSIGNED_USERS;
        $choices[TEXT_EXT_NEW_COMMENT]['comment_send_to_email'] = TEXT_EXT_SEND_TO_EMAIL;
        $choices[TEXT_EXT_NEW_COMMENT]['comment_send_to_assigned_email'] = TEXT_EXT_SEND_TO_ASSIGNED_EMAIL;
        $choices[TEXT_EXT_NEW_COMMENT]['comment_send_by_visibility_rules'] = TEXT_EXT_SEND_BY_VISIBILITY_RULES;

        $choices[TEXT_EXT_SEND_BY_DATE]['schedule_send_to_users'] = TEXT_EXT_SEND_TO_USERS;
        $choices[TEXT_EXT_SEND_BY_DATE]['schedule_send_to_assigned_users'] = TEXT_EXT_SEND_TO_ASSIGNED_USERS;
        $choices[TEXT_EXT_SEND_BY_DATE]['schedule_send_to_email'] = TEXT_EXT_SEND_TO_EMAIL;
        $choices[TEXT_EXT_SEND_BY_DATE]['schedule_send_to_assigned_email'] = TEXT_EXT_SEND_TO_ASSIGNED_EMAIL;
        $choices[TEXT_EXT_SEND_BY_DATE]['schedule_send_by_visibility_rules'] = TEXT_EXT_SEND_BY_VISIBILITY_RULES;


        return $choices;
    }

    static function get_action_type($type)
    {
        $text = '';

        switch ($type) {
            case 'insert_send_to_users':
            case 'insert_send_to_assigned_users':
            case 'insert_send_to_email':
            case 'insert_send_to_assigned_email':
            case 'insert_send_by_visibility_rules':
                $text .= '<span class="label label-success">' . TEXT_EXT_ADDING_NEW_RECORD . '</span>';
                break;
            case 'edit_send_to_users':
            case 'edit_send_to_assigned_users':
            case 'edit_send_to_email':
            case 'edit_send_to_assigned_email':
            case 'edit_send_by_visibility_rules':
                $text .= '<span class="label label-info">' . TEXT_EXT_ADDITING_RECORD . '</span>';
                break;
            case 'comment_send_to_users':
            case 'comment_send_to_assigned_users':
            case 'comment_send_to_email':
            case 'comment_send_to_assigned_email':
            case 'comment_send_by_visibility_rules':
                $text .= '<span class="label label-warning">' . TEXT_EXT_NEW_COMMENT . '</span>';
                break;
            case 'schedule_send_to_users':
            case 'schedule_send_to_assigned_users':
            case 'schedule_send_to_email':
            case 'schedule_send_to_assigned_email':
            case 'schedule_send_by_visibility_rules':
                $text .= '<span class="label label-default">' . TEXT_EXT_SEND_BY_DATE . '</span>';
                break;
        }

        return $text;
    }

    static function get_action_type_name($type)
    {
        $text = '';

        switch ($type) {
            case 'insert_send_to_users':
            case 'edit_send_to_users':
            case 'comment_send_to_users':
            case 'schedule_send_to_users':
                $text .= TEXT_EXT_SEND_TO_USERS;
                break;
            case 'insert_send_to_assigned_users':
            case 'edit_send_to_assigned_users':
            case 'comment_send_to_assigned_users':
            case 'schedule_send_to_assigned_users':
                $text .= TEXT_EXT_SEND_TO_ASSIGNED_USERS;
                break;
            case 'insert_send_to_email':
            case 'edit_send_to_email':
            case 'comment_send_to_email':
            case 'schedule_send_to_email':
                $text .= TEXT_EXT_SEND_TO_EMAIL;
                break;
            case 'insert_send_to_assigned_email':
            case 'edit_send_to_assigned_email':
            case 'comment_send_to_assigned_email':
            case 'schedule_send_to_assigned_email':
                $text .= TEXT_EXT_SEND_TO_ASSIGNED_EMAIL;
                break;
            case 'insert_send_by_visibility_rules':
            case 'edit_send_by_visibility_rules':
            case 'comment_send_by_visibility_rules':
            case 'schedule_send_by_visibility_rules':
                $text .= TEXT_EXT_SEND_BY_VISIBILITY_RULES;
                break;
        }

        return $text;
    }

    function prepare_subitems_list($entities_id, $text)
    {
        global $app_entities_cache;

        if (!isset($app_entities_cache[$entities_id]) or $app_entities_cache[$entities_id]['parent_id'] != $this->entity_id) {
            return false;
        }

        $text_pattern = new fieldtype_text_pattern;

        $html = '<ul>';
        $items_query = db_query(
            "select e.* " . fieldtype_formula::prepare_query_select(
                $entities_id,
                ''
            ) . " from app_entity_" . $entities_id . " e where e.parent_item_id='" . $this->item_info['id'] . "' order by e.id"
        );
        while ($items = db_fetch_array($items_query)) {
            $html .= '<li>' . $text_pattern->output_singe_text(
                    $text,
                    $entities_id,
                    $items,
                    ['is_email' => true, 'is_export' => true]
                ) . '</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    function prepare_subitems_table($entities_id, $fields_row)
    {
        global $app_entities_cache, $app_fields_cache;

        if (!isset($app_entities_cache[$entities_id]) or $app_entities_cache[$entities_id]['parent_id'] != $this->entity_id) {
            return false;
        }

        $fields_list = [];
        foreach (explode(',', str_replace(['<', '>'], '', $fields_row)) as $field_id) {
            $field_id = trim($field_id);

            if (isset($app_fields_cache[$entities_id][$field_id])) {
                $fields_list[] = [
                    'id' => $field_id,
                    'name' => fields_types::get_option(
                        $app_fields_cache[$entities_id][$field_id]['type'],
                        'name',
                        $app_fields_cache[$entities_id][$field_id]['name']
                    ),
                    'type' => $app_fields_cache[$entities_id][$field_id]['type'],
                    'configuration' => $app_fields_cache[$entities_id][$field_id]['configuration'],
                ];
            }
        }


        //print_rr($fields_list);

        $html = '
                <table border="1" cellpadding="2" cellspacing="0">
                    <tr>';

        foreach ($fields_list as $field) {
            $html .= '<th>' . $field['name'] . '</th>';
        }

        $html .= '</tr>';

        $items_query = db_query(
            "select e.* " . fieldtype_formula::prepare_query_select(
                $entities_id,
                ''
            ) . " from app_entity_" . $entities_id . " e where e.parent_item_id='" . $this->item_info['id'] . "' order by e.id"
        );
        while ($items = db_fetch_array($items_query)) {
            $html .= '<tr>';

            foreach ($fields_list as $field) {
                //prepare field value
                $value = items::prepare_field_value_by_type($field, $items);

                $output_options = [
                    'class' => $field['type'],
                    'value' => $value,
                    'field' => $field,
                    'item' => $items,
                    'is_export' => true,
                    'is_print' => true,
                    'path' => ''
                ];

                $html .= '<td>' . trim(strip_tags(fields_types::output($output_options))) . '</td>';
            }

            $html .= '</tr>';
        }
        $html .= '</table>';

        return $html;
    }

    function prepare_subitems($text)
    {
        global $app_entities_cach;

        if (preg_match_all('/{#(\w+):([^}]*)}/', $text, $matches)) {
            //print_rr($matches);
            foreach ($matches[1] as $matches_key => $matches_text) {
                $entities_id = str_replace('entity', '', $matches_text);

                $pattern = str_replace(['&lt;', '&gt;'], ['<', '>'], $matches[2][$matches_key]);
                if (preg_match("/<(.+)>/", $pattern)) {
                    if ($html = $this->prepare_subitems_table($entities_id, $pattern)) {
                        $text = str_replace($matches[0][$matches_key], $html, $text);
                    }
                } else {
                    if ($html = $this->prepare_subitems_list($entities_id, $pattern)) {
                        $text = str_replace($matches[0][$matches_key], $html, $text);
                    }
                }
            }
        }

        //prepare html blocks
        $blocks_query = db_query("select * from app_ext_email_rules_blocks  order by name");
        while ($blocks = db_fetch_array($blocks_query)) {
            $text = str_replace('[block_' . $blocks['id'] . ']', $blocks['description'], $text);
        }

        //prepare parent item fields
        $text = $this->prepare_parent_items_fields($text);

        //echo $text;
        //exit();

        return $text;
    }

    function prepare_parent_items_fields($text)
    {
        global $app_entities_cache;

        if ($this->parent_item_info) {
            $parent_entity_id = $app_entities_cache[$this->entity_id]['parent_id'];

            $text_pattern = new fieldtype_text_pattern;

            $text = $text_pattern->output_singe_text(
                $text,
                $parent_entity_id,
                $this->parent_item_info,
                [
                    'is_email' => true,
                    'hide_attachments_url' => 1,
                    'path' => $parent_entity_id . '-' . $this->parent_item_info['id']
                ]
            );
        }

        return $text;
    }

    function send_insert_msg()
    {
        //get current item info
        $this->set_current_item_info();

        $text_pattern = new fieldtype_text_pattern;

        $rules_query = db_query(
            "select r.* from app_ext_email_rules r  where r.entities_id='" . $this->entity_id . "' and action_type in ('insert_send_to_users','insert_send_to_assigned_users','insert_send_to_email','insert_send_to_assigned_email','insert_send_by_visibility_rules') and length(subject)>0 and length(description)>0 and (length(r.send_to_users)>0 or length(r.send_to_assigned_users)>0 or length(r.send_to_email)>0 or length(r.send_to_assigned_email)>0 or action_type='insert_send_by_visibility_rules') and r.is_active=1"
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

            $subject = $text_pattern->output_singe_text($rules['subject'], $this->entity_id, $this->item_info);
            $subject = $this->prepare_parent_items_fields($subject);
            $text = $this->prepare_subitems($rules['description']);
            $text = $text_pattern->output_singe_text(
                $text,
                $this->entity_id,
                $this->item_info,
                ['is_email' => true, 'hide_attachments_url' => $rules['attach_attachments'], 'path' => $this->path]
            );

            $attachments = $this->prepare_attachments($rules, $this->item_info);

            $send_to = $this->prepare_send_to($rules);

            //print_rr($send_to);
            //exit();

            if (count($send_to)) {
                $this->send(array_unique($send_to), $subject, $text, $attachments, 'new_item');
            }
        }
    }

    function prepare_send_to($rules)
    {
        global $app_entities_cache;

        $send_to = [];

        switch ($rules['action_type']) {
            case 'insert_send_by_visibility_rules':
            case 'edit_send_by_visibility_rules':
            case 'comment_send_by_visibility_rules':
            case 'schedule_send_by_visibility_rules':
                $send_to = records_visibility::users_by_visibility_rules($this->entity_id, $this->item_id);
                break;
            case 'insert_send_to_users':
            case 'edit_send_to_users':
            case 'comment_send_to_users':
            case 'schedule_send_to_users':
                if (strlen($rules['send_to_users'])) {
                    $send_to = explode(',', $rules['send_to_users']);
                }
                break;
            case 'insert_send_to_assigned_users':
            case 'edit_send_to_assigned_users':
            case 'comment_send_to_assigned_users':
            case 'schedule_send_to_assigned_users':
                if (strlen($rules['send_to_assigned_users'])) {
                    foreach (explode(',', $rules['send_to_assigned_users']) as $fields_id) {
                        $fields_query = db_query(
                            "select id, entities_id, type, configuration from app_fields where id='" . $fields_id . "'"
                        );
                        if ($fields = db_fetch_array($fields_query)) {
                            $cfg = new fields_types_cfg($fields['configuration']);

                            if ($fields['entities_id'] == $this->entity_id) {
                                if ($fields['type'] == 'fieldtype_created_by') {
                                    $send_to = array_merge($send_to, [$this->item_info['created_by']]);
                                } elseif ($fields['type'] == 'fieldtype_grouped_users') {
                                    $send_to = array_merge(
                                        $send_to,
                                        fieldtype_grouped_users::get_send_to(
                                            $this->item_info['field_' . $fields_id],
                                            $cfg
                                        )
                                    );
                                } elseif ($fields['type'] == 'fieldtype_access_group') {
                                    $send_to = array_merge(
                                        $send_to,
                                        fieldtype_access_group::get_send_to($this->item_info['field_' . $fields_id])
                                    );
                                } elseif (strlen($this->item_info['field_' . $fields_id])) {
                                    $send_to = array_merge(
                                        $send_to,
                                        explode(',', $this->item_info['field_' . $fields_id])
                                    );
                                }
                            } elseif ($app_entities_cache[$this->entity_id]['parent_id'] == $fields['entities_id'] and $this->item_info['parent_item_id'] > 0) {
                                $parent_item_info_query = db_query(
                                    "select * from app_entity_" . $app_entities_cache[$this->entity_id]['parent_id'] . " where id='" . $this->item_info['parent_item_id'] . "'"
                                );
                                if ($parent_item_info = db_fetch_array($parent_item_info_query)) {
                                    if ($fields['type'] == 'fieldtype_created_by') {
                                        $send_to = array_merge($send_to, [$parent_item_info['created_by']]);
                                    } elseif ($fields['type'] == 'fieldtype_grouped_users') {
                                        $send_to = array_merge(
                                            $send_to,
                                            fieldtype_grouped_users::get_send_to(
                                                $parent_item_info['field_' . $fields_id],
                                                $cfg
                                            )
                                        );
                                    } elseif ($fields['type'] == 'fieldtype_access_group') {
                                        $send_to = array_merge(
                                            $send_to,
                                            fieldtype_access_group::get_send_to(
                                                $parent_item_info['field_' . $fields_id]
                                            )
                                        );
                                    } elseif (strlen($parent_item_info['field_' . $fields_id])) {
                                        $send_to = array_merge(
                                            $send_to,
                                            explode(',', $parent_item_info['field_' . $fields_id])
                                        );
                                    }
                                }
                            }
                        }
                    }
                }

                break;

            case 'insert_send_to_email':
            case 'edit_send_to_email':
            case 'comment_send_to_email':
            case 'schedule_send_to_email':
                if (strlen($rules['send_to_email'])) {
                    $send_to = preg_split('/\r\n|\r|\n/', $rules['send_to_email']);
                }
                break;

            case 'insert_send_to_assigned_email':
            case 'edit_send_to_assigned_email':
            case 'comment_send_to_assigned_email':
            case 'schedule_send_to_assigned_email':
                if (strlen($rules['send_to_assigned_email'])) {
                    foreach (explode(',', $rules['send_to_assigned_email']) as $fields_id) {
                        $fields_query = db_query(
                            "select id, entities_id, type, configuration from app_fields where id='" . $fields_id . "'"
                        );
                        if ($fields = db_fetch_array($fields_query)) {
                            $cfg = new fields_types_cfg($fields['configuration']);

                            if ($fields['entities_id'] == $this->entity_id) {
                                if (strlen($this->item_info['field_' . $fields_id])) {
                                    foreach (explode(',', $this->item_info['field_' . $fields_id]) as $v) {
                                        $send_to[] = $v;
                                    }
                                }
                            } elseif ($app_entities_cache[$this->entity_id]['parent_id'] == $fields['entities_id'] and $this->item_info['parent_item_id'] > 0) {
                                $parent_item_info_query = db_query(
                                    "select * from app_entity_" . $app_entities_cache[$this->entity_id]['parent_id'] . " where id='" . $this->item_info['parent_item_id'] . "'"
                                );
                                if ($parent_item_info = db_fetch_array($parent_item_info_query)) {
                                    if (strlen($parent_item_info['field_' . $fields_id])) {
                                        foreach (explode(',', $parent_item_info['field_' . $fields_id]) as $v) {
                                            $send_to[] = $v;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                break;
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
            "select r.* from app_ext_email_rules r  where r.entities_id='" . $this->entity_id . "' and action_type in ('edit_send_to_users','edit_send_to_assigned_users','edit_send_to_email','edit_send_to_assigned_email','edit_send_by_visibility_rules') and monitor_fields_id>0 and length(subject)>0 and length(description)>0 and (length(r.send_to_users)>0 or length(r.send_to_assigned_users)>0 or length(r.send_to_email)>0 or length(r.send_to_assigned_email)>0 or action_type='edit_send_by_visibility_rules') and r.is_active=1"
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

            $subject = $text_pattern->output_singe_text($rules['subject'], $this->entity_id, $this->item_info);
            $subject = $this->prepare_parent_items_fields($subject);
            $text = $this->prepare_subitems($rules['description']);
            $text = $text_pattern->output_singe_text(
                $text,
                $this->entity_id,
                $this->item_info,
                ['is_email' => true, 'hide_attachments_url' => $rules['attach_attachments'], 'path' => $this->path]
            );

            $attachments = $this->prepare_attachments($rules, $this->item_info);

            $send_to = $this->prepare_send_to($rules);

            //print_rr($send_to);
            //exit();

            if (count($send_to)) {
                $this->send($send_to, $subject, $text, $attachments, 'updated_item');
            }
        }
    }

    function send_comments_msg($previous_item_info)
    {
        //get current item info
        $this->set_current_item_info();

        $text_pattern = new fieldtype_text_pattern;

        $rules_query = db_query(
            "select r.* from app_ext_email_rules r  where r.entities_id='" . $this->entity_id . "' and action_type in ('comment_send_to_users','comment_send_to_assigned_users','comment_send_to_email','comment_send_to_assigned_email','comment_send_by_visibility_rules') and length(subject)>0 and length(description)>0 and (length(r.send_to_users)>0 or length(r.send_to_assigned_users)>0 or length(r.send_to_email)>0 or length(r.send_to_assigned_email)>0 or action_type='comment_send_by_visibility_rules') and r.is_active=1"
        );
        while ($rules = db_fetch_array($rules_query)) {
            //check fields choices
            if (strlen($rules['monitor_choices'])) {
                if (!in_array(
                    $this->item_info['field_' . $rules['monitor_fields_id']],
                    explode(',', $rules['monitor_choices'])
                )) {
                    continue;
                }
            }

            $subject = $text_pattern->output_singe_text($rules['subject'], $this->entity_id, $this->item_info);
            $subject = $this->prepare_parent_items_fields($subject);
            $text = $this->prepare_subitems($rules['description']);
            $text = $text_pattern->output_singe_text(
                $text,
                $this->entity_id,
                $this->item_info,
                ['is_email' => true, 'hide_attachments_url' => $rules['attach_attachments'], 'path' => $this->path]
            );

            $attachments = $this->prepare_attachments($rules, $this->item_info);

            $send_to = $this->prepare_send_to($rules);

            //print_rr($send_to);
            //exit();

            if (count($send_to)) {
                $this->send($send_to, $subject, $text, $attachments, 'new_comment');
            }
        }
    }

    function send($send_to, $subject, $text, $attachments = [], $type = '')
    {
        global $app_user;

        $entity_cfg = new entities_cfg($this->entity_id);

        //add internal notification
        if ($entity_cfg->get('disable_internal_notification') != 1 and strlen($type)) {
            $this->add_users_notifications($send_to, $subject, $type);
        }

        //send email notification
        if ($entity_cfg->get('disable_notification') != 1) {
            users::send_to($send_to, $subject, $text, $attachments);
        }

        //delete temp templates files
        $this->reset_template_files();
    }

    function add_users_notifications($send_to, $subject, $type)
    {
        foreach ($send_to as $users_id) {
            if (is_numeric($users_id)) {
                users_notifications::add($subject, $type, $users_id, $this->entity_id, $this->item_id);
            }
        }
    }

    function prepare_attachments($rules, $item)
    {
        global $app_entities_cache;

        $attachments = [];

        if ($rules['attach_attachments'] == 1) {
            $fields_query = db_query(
                "select id, type, configuration from app_fields where entities_id='" . $rules['entities_id'] . "' and type in ('" . implode(
                    "','",
                    fields_types::get_attachments_types()
                ) . "')"
            );
            while ($fields = db_fetch_array($fields_query)) {
                if (strstr($rules['description'], '[' . $fields['id'] . ']')) {
                    if (isset($item['field_' . $fields['id']])) {
                        if (strlen($item['field_' . $fields['id']])) {
                            foreach (explode(',', $item['field_' . $fields['id']]) as $filename) {
                                $file = attachments::parse_filename($filename);

                                $attachments[$file['file_path']] = $file['name'];
                            }
                        }
                    }
                }
            }

            //include comments attachmetns
            if (strstr($rules['description'], '[comment]')) {
                $comment_query = db_query(
                    "select attachments from app_comments where entities_id='" . $rules['entities_id'] . "' and items_id='" . $item['id'] . "' order by id desc"
                );
                if ($comment = db_fetch_array($comment_query)) {
                    if (strlen($comment['attachments'])) {
                        foreach (explode(',', $comment['attachments']) as $filename) {
                            $file = attachments::parse_filename($filename);

                            $attachments[$file['file_path']] = $file['name'];
                        }
                    }
                }
            }

            //attachments for paretn item
            if ($this->parent_item_info) {
                $parent_entity_id = $app_entities_cache[$this->entity_id]['parent_id'];

                $fields_query = db_query(
                    "select id, type, configuration from app_fields where entities_id='" . $parent_entity_id . "' and type in ('" . implode(
                        "','",
                        fields_types::get_attachments_types()
                    ) . "')"
                );
                while ($fields = db_fetch_array($fields_query)) {
                    if (strstr($rules['description'], '[' . $fields['id'] . ']')) {
                        if (isset($this->parent_item_info['field_' . $fields['id']])) {
                            if (strlen($this->parent_item_info['field_' . $fields['id']])) {
                                foreach (explode(',', $this->parent_item_info['field_' . $fields['id']]) as $filename) {
                                    $file = attachments::parse_filename($filename);

                                    $attachments[$file['file_path']] = $file['name'];
                                }
                            }
                        }
                    }
                }
            }
        }

        if (strlen($rules['attach_template'])) {
            //include export libs
            require_once(CFG_PATH_TO_DOMPDF);

            require_once(CFG_PATH_TO_PHPWORD);

            foreach (explode(',', $rules['attach_template']) as $template) {
                $template = explode('_', $template);

                $export_templates_file = new export_templates_file($rules['entities_id'], $item['id']);
                if (strlen($filename = $export_templates_file->save($template[0], $template[1]))) {
                    $file = attachments::parse_filename($filename);
                    $attachments[$file['file_path']] = $file['name'];
                    $this->attach_template_files[$file['file_path']] = $file['name'];
                }
            }
        }

        //print_rr($attachments);
        //print_rr($this->attach_template_files);
        //exit();

        return $attachments;
    }

    function reset_template_files()
    {
        foreach ($this->attach_template_files as $file_path => $name) {
            if (is_file($file_path)) {
                unlink($file_path);
            }
        }
    }

    static function email_by_date()
    {
        global $app_fields_cache;

        $rules_query = db_query(
            "select r.* from app_ext_email_rules r, app_entities e  where r.entities_id=e.id and action_type in ('schedule_send_to_users','schedule_send_to_assigned_users','schedule_send_to_email','schedule_send_to_assigned_email','schedule_send_by_visibility_rules') and length(subject)>0 and length(description)>0 and (length(r.send_to_users)>0 or length(r.send_to_assigned_users)>0 or length(r.send_to_email)>0 or length(r.send_to_assigned_email)>0 or action_type='schedule_send_by_visibility_rules') and r.is_active=1 order by e.id"
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
                $day = (int)str_replace(['+', '-'], '', $day);
                $field_name = 'field_' . $date_fields_id;

                $item_info_query = db_query(
                    "select e.* " . fieldtype_formula::prepare_query_select(
                        $entities_id,
                        '',
                        false,
                        ['fields_in_listing' => $date_fields_id]
                    ) . "  from app_entity_{$entities_id} e where FROM_UNIXTIME({$field_name},'%Y-%m-%d')=date_format({$use_function}(now(),INTERVAL {$day} DAY),'%Y-%m-%d')",
                    false
                );
                while ($item_info = db_fetch_array($item_info_query)) {
                    //print_rr($item_info); 

                    $email_rules = new email_rules($entities_id, $item_info['id']);
                    $email_rules->item_info = $item_info;
                    $email_rules->send_email_by_date_msg($rules);
                }
            }
        }
    }

    function send_email_by_date_msg($rules = false)
    {
        $text_pattern = new fieldtype_text_pattern;

        if ($rules) {
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

            //print_rr($rules);

            $subject = $text_pattern->output_singe_text($rules['subject'], $this->entity_id, $this->item_info);
            $subject = $this->prepare_parent_items_fields($subject);
            $text = $this->prepare_subitems($rules['description']);
            $text = $text_pattern->output_singe_text(
                $text,
                $this->entity_id,
                $this->item_info,
                ['is_email' => true, 'hide_attachments_url' => $rules['attach_attachments'], 'path' => $this->path]
            );

            $attachments = $this->prepare_attachments($rules, $this->item_info);

            $send_to = $this->prepare_send_to($rules);

            //print_rr($send_to);
            //exit();

            if (count($send_to)) {
                $this->send(array_unique($send_to), $subject, $text, $attachments);
            }
        }
    }

    static function send_by_id($entity_id, $item_id, $rule_id)
    {
        $rules_query = db_query(
            "select r.* from app_ext_email_rules r, app_entities e  where r.entities_id=e.id and r.id='{$rule_id}' and length(subject)>0 and length(description)>0 and (length(r.send_to_users)>0 or length(r.send_to_assigned_users)>0 or length(r.send_to_email)>0 or length(r.send_to_assigned_email)>0 or action_type='schedule_send_by_visibility_rules') and r.is_active=1"
        );
        if ($rules = db_fetch_array($rules_query)) {
            $email_rules = new email_rules($entity_id, $item_id);
            $email_rules->send_by_id_msg($rules);
        }
    }

    function send_by_id_msg($rules)
    {
        if (!$rules) {
            return false;
        }

        $this->set_current_item_info();

        $text_pattern = new fieldtype_text_pattern;

        $subject = $text_pattern->output_singe_text($rules['subject'], $this->entity_id, $this->item_info);
        $subject = $this->prepare_parent_items_fields($subject);
        $text = $this->prepare_subitems($rules['description']);
        $text = $text_pattern->output_singe_text(
            $text,
            $this->entity_id,
            $this->item_info,
            ['is_email' => true, 'hide_attachments_url' => $rules['attach_attachments'], 'path' => $this->path]
        );

        $attachments = $this->prepare_attachments($rules, $this->item_info);

        $send_to = $this->prepare_send_to($rules);

        //print_rr($send_to);
        //exit();

        if (count($send_to)) {
            $this->send(array_unique($send_to), $subject, $text, $attachments);
        }
    }

}
