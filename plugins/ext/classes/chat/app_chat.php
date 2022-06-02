<?php

class app_chat
{

    public $has_access;
    public $access_schema;
    public $render_messages_limit;
    public $messages_delay;
    public $count_all_unrad;

    function __construct()
    {
        global $app_user;

        if (CFG_ENABLE_CHAT != 1 or !isset($app_user['group_id'])) {
            $this->has_access = false;
        } else {
            $this->access_schema = self::get_access($app_user['group_id']);

            $this->has_access = ($this->access_schema != '-1' ? true : false);
        }

        $this->render_messages_limit = 30;

        $this->messages_delay = 2000;
    }

    function get_msg_number_of_pages($assigned_to)
    {
        global $app_user;

        $messages_query = db_query(
            "select count(*) as total from app_ext_chat_messages where (users_id='" . $app_user['id'] . "' and assigned_to='" . $assigned_to . "') or (assigned_to='" . $app_user['id'] . "' and users_id='" . $assigned_to . "')"
        );
        $messages = db_fetch_array($messages_query);

        return ceil($messages['total'] / $this->render_messages_limit);
    }

    function get_conversations_msg_number_of_pages($assigned_to)
    {
        global $app_user;

        $messages_query = db_query(
            "select count(*) as total from app_ext_chat_conversations_messages where conversations_id='" . $assigned_to . "'"
        );
        $messages = db_fetch_array($messages_query);

        return ceil($messages['total'] / $this->render_messages_limit);
    }

    function get_access($group_id)
    {
        $access_query = db_query(
            "select * from app_ext_chat_access where access_groups_id='" . db_input($group_id) . "'"
        );
        if ($access = db_fetch_array($access_query)) {
            return $access['access_schema'];
        } else {
            return '-1';
        }
    }

    function has_access_by_group($group_id, $multiple_access_groups = '')
    {
        if (strlen($multiple_access_groups)) {
            $has_access = false;

            foreach (explode(',', $multiple_access_groups) as $id) {
                if (in_array($id, explode(',', $this->access_schema))) {
                    $has_access = true;
                }
            }

            return $has_access;
        } else {
            return in_array($group_id, explode(',', $this->access_schema));
        }
    }

    function get_users_choices()
    {
        global $app_user, $app_users_cache;

        $choices = [];

        //get users
        $order_by_sql = (CFG_APP_DISPLAY_USER_NAME_ORDER == 'firstname_lastname' ? 'u.field_7, u.field_8' : 'u.field_8, u.field_7');
        $users_query = db_query(
            "select u.*,a.name as group_name,u.field_6 as group_id from app_entity_1 u left join app_access_groups a on a.id=u.field_6 where u.field_5=1 and u.id!='" . $app_user['id'] . "' order by " . $order_by_sql
        );
        while ($users = db_fetch_array($users_query)) {
            //check access
            if (!$this->has_access_by_group($users['group_id'], $users['multiple_access_groups'])) {
                continue;
            }

            $group_name = ($users['group_id'] > 0 ? $users['group_name'] : TEXT_ADMINISTRATOR);

            $choices[$group_name][$users['id']] = $app_users_cache[$users['id']]['name'];
        }

        return $choices;
    }

    function get_conversations_users_choices($users_list = [])
    {
        global $app_user, $app_users_cache;

        $choices = [];

        if (count($users_list)) {
            $order_by_sql = (CFG_APP_DISPLAY_USER_NAME_ORDER == 'firstname_lastname' ? 'u.field_7, u.field_8' : 'u.field_8, u.field_7');
            $users_query = db_query(
                "select u.*,a.name as group_name,a.id as group_id from app_entity_1 u left join app_access_groups a on a.id=u.field_6 where u.field_5=1 and u.id in (" . implode(
                    ',',
                    $users_list
                ) . ") order by " . $order_by_sql
            );
            while ($users = db_fetch_array($users_query)) {
                $choices[$users['id']] = $app_users_cache[$users['id']]['name'];
            }
        }

        return $choices;
    }

    function get_conversations_users_dropdown($users_list, $users_id)
    {
        $choices = $this->get_conversations_users_choices($users_list);

        $html = '';
        foreach ($choices as $k => $v) {
            $html .= '
					<li>
						<a href="#">' . $v . ($k == $users_id ? ' <small>(' . TEXT_EXT_CHAT_CONVERSATION_OWNER . ')</small>' : '') . '</a>
					</li>';
        }

        return $html;
    }

    function get_conversations_info($conversations)
    {
        $assigned_to = [];

        if (strlen($conversations['assigned_to'])) {
            $assigned_to = explode(',', $conversations['assigned_to']);
        }

        $assigned_to[] = $conversations['users_id'];

        $info = [
            'assigned_to' => $assigned_to,
            'count_users' => count($assigned_to),
            'menu_icon' => $this->get_conversations_icon($conversations),
        ];

        return $info;
    }

    function get_conversations_icon($conversations)
    {
        $menu_icon_color = (strlen(
            $conversations['menu_icon_color']
        ) ? 'style="color: ' . $conversations['menu_icon_color'] . '"' : '');
        $mnue_icon = (strlen(
            $conversations['menu_icon']
        ) ? '<i ' . $menu_icon_color . ' class="fa ' . $conversations['menu_icon'] . '" aria-hidden="true"></i>' : '<i ' . $menu_icon_color . ' class="fa fa-comments-o" aria-hidden="true"></i>');

        return $mnue_icon;
    }

    function set_online_status()
    {
        global $app_user;

        $users_query = db_query("select * from app_ext_chat_users_online where users_id='" . $app_user['id'] . "'");
        if ($users = db_fetch_array($users_query)) {
            db_query(
                "update app_ext_chat_users_online set date_check=" . time(
                ) . " where users_id='" . $app_user['id'] . "'"
            );
        } else {
            $sql_data = [
                'users_id' => db_prepare_input($app_user['id']),
                'date_check' => time(),
            ];

            db_perform('app_ext_chat_users_online', $sql_data);
        }
    }

    function is_user_online($users_id)
    {
        $users_query = db_query(
            "select * from app_ext_chat_users_online where users_id='" . $users_id . "' and date_check>" . (time() - 15)
        );
        if ($users = db_fetch_array($users_query)) {
            return true;
        } else {
            return false;
        }
    }

    function render_online_status($is_online)
    {
        if ($is_online) {
            return '<div class="chat-user-online-status"><i class="fa fa-circle online" aria-hidden="true"></i> ' . TEXT_EXT_USER_ONLINE . '</div>';
        } else {
            return '<div class="chat-user-online-status"><i class="fa fa-circle-o" aria-hidden="true"></i> ' . TEXT_EXT_USER_OFFLINE . '</div>';
        }
    }

    function render_users_list()
    {
        global $app_user, $app_users_cache, $app_users_cfg;

        $choices = [];

        //get users
        $order_by_sql = (CFG_APP_DISPLAY_USER_NAME_ORDER == 'firstname_lastname' ? 'u.field_7, u.field_8' : 'u.field_8, u.field_7');
        $users_query = db_query(
            "select u.*,a.name as group_name,u.field_6 as group_id from app_entity_1 u left join app_access_groups a on a.id=u.field_6 where u.field_5=1 and u.id!='" . $app_user['id'] . "' order by " . $order_by_sql
        );
        while ($users = db_fetch_array($users_query)) {
            //check access			
            if (!$this->has_access_by_group($users['group_id'], $users['multiple_access_groups'])) {
                continue;
            }

            //get last message
            $messages_query = db_query(
                "select * from app_ext_chat_messages where ((users_id='" . $app_user['id'] . "' and assigned_to='" . $users['id'] . "') or (assigned_to='" . $app_user['id'] . "' and users_id='" . $users['id'] . "')) order by id desc limit 1"
            );
            if ($messages = db_fetch_array($messages_query)) {
                $choices_key = $messages['date_added'];
            } else {
                $choices_key = $users['date_added'];
            }

            //check duplicates dates
            while (isset($choices[$choices_key])) {
                $choices_key++;
            }

            //count new messages
            $count_query = db_query(
                "select count(*) as total from app_ext_chat_unread_messages where assigned_to='" . $app_user['id'] . "' and users_id='" . $users['id'] . "' and conversations_id=0"
            );
            $count = db_fetch_array($count_query);
            $count_new = ($count['total'] > 0 ? ($count['total'] > 9 ? '9+' : $count['total']) : '');

            $choices[$choices_key] = [
                'type' => 'user',
                'id' => $users['id'],
                'name' => $app_users_cache[$users['id']]['name'],
                'photo' => render_user_photo($app_users_cache[$users['id']]['photo']),
                'description' => ($users['group_id'] > 0 ? $users['group_name'] : TEXT_ADMINISTRATOR),
                'count_new' => $count_new,
                'online_status' => $this->render_online_status($this->is_user_online($users['id'])),
            ];
        }

        //get conversations
        $conversations_query = db_query(
            "select * from app_ext_chat_conversations where users_id='" . $app_user['id'] . "' or find_in_set('" . $app_user['id'] . "',assigned_to)"
        );
        while ($conversations = db_fetch_array($conversations_query)) {
            //get last message
            $messages_query = db_query(
                "select * from app_ext_chat_conversations_messages where conversations_id='" . $conversations['id'] . "' order by id desc limit 1"
            );
            if ($messages = db_fetch_array($messages_query)) {
                $choices_key = $messages['date_added'];
            } else {
                $choices_key = $conversations['date_added'];
            }

            //check duplicates dates
            while (isset($choices[$choices_key])) {
                $choices_key++;
            }

            //count new messages
            $count_query = db_query(
                "select count(*) as total from app_ext_chat_unread_messages where assigned_to='" . $app_user['id'] . "'  and conversations_id='" . $conversations['id'] . "'"
            );
            $count = db_fetch_array($count_query);
            $count_new = ($count['total'] > 0 ? ($count['total'] > 9 ? '9+' : $count['total']) : '');

            $choices[$choices_key] = [
                'type' => 'conversation',
                'id' => $conversations['id'],
                'name' => $conversations['name'],
                'photo' => $this->get_conversations_icon($conversations),
                'description' => $conversations['description'],
                'count_new' => $count_new,
                'online_status' => ''
            ];
        }

        //sort choices by last msg
        krsort($choices);

        $html = '
					<ul class="chat-users-list">
				';

        foreach ($choices as $item) {
            $is_active = false;

            if ($app_users_cfg->get('app_chat_active_dialog') == $item['type'] . ':' . $item['id']) {
                $is_active = true;
            }

            $html .= '
					<li class="chat-user chat-to-' . $item['type'] . ($is_active ? ' selected' : '') . '" data-assigned-to="' . $item['id'] . '" data-user-name="' . addslashes(
                    $item['name']
                ) . '">
						<div class="chat-user-photo">' . $item['photo'] . '</div>
					  <div class="badge badge-warning chat-user-count-new-msg ' . (strlen(
                    $item['count_new']
                ) ? '' : 'hidden') . '">' . $item['count_new'] . '</div>
						<div class="chat-user-name">' . $item['name'] . '</div>
						<div class="chat-user-group-name">' . $item['description'] . '</div>
						' . $item['online_status'] . '		
					</li>
					';
        }

        $html .= '
					</ul>
				';

        return $html;
    }

    function reset_unread_messages($users_id)
    {
        global $app_user;

        db_query(
            "delete from app_ext_chat_unread_messages where assigned_to='" . $app_user['id'] . "' and users_id='" . $users_id . "' and conversations_id=0"
        );
    }

    function reset_unread_conversations_messages($conversations_id)
    {
        global $app_user;

        db_query(
            "delete from app_ext_chat_unread_messages where assigned_to='" . $app_user['id'] . "'  and conversations_id='" . $conversations_id . "'"
        );
    }

    function render_count_all_unrad()
    {
        global $app_user;

        $count_query = db_query(
            "select count(*) as total from app_ext_chat_unread_messages where assigned_to='" . $app_user['id'] . "'"
        );
        $count = db_fetch_array($count_query);
        $count_new = ($count['total'] > 0 ? ($count['total'] > 9 ? '9+' : $count['total']) : '');

        $this->count_all_unrad = $count['total'];

        return (strlen($count_new) ? '<span class="badge badge-warning">' . $count_new . '</span>' : '');
    }

    function render_messages_list($assigned_to)
    {
        global $app_user, $app_users_cfg;

        //reset unread messages
        $this->reset_unread_messages($assigned_to);

        //reset last msg ID
        $app_users_cfg->set('app_chat_last_msg_id', 0);

        $html = '';

        $count = 0;
        $chat_msg_pager_skip_id = 0;
        $messages_query = db_query(
            "select * from app_ext_chat_messages where (users_id='" . $app_user['id'] . "' and assigned_to='" . $assigned_to . "') or (assigned_to='" . $app_user['id'] . "' and users_id='" . $assigned_to . "') order by id desc limit " . $this->render_messages_limit
        );
        while ($messages = db_fetch_array($messages_query)) {
            $html = $this->render_message_template($messages) . $html;

            if ($count == 0) {
                $app_users_cfg->set('app_chat_last_msg_id', $messages['id']);
                $count++;
            }

            $chat_msg_pager_skip_id = $messages['id'];
        }

        $html .= input_hidden_tag('chat_msg_pager_skip_id', $chat_msg_pager_skip_id);

        return $html;
    }

    function render_conversations_messages_list($assigned_to)
    {
        global $app_user, $app_users_cfg;

        //reset unread messages
        $this->reset_unread_conversations_messages($assigned_to);

        //reset last msg ID
        $app_users_cfg->set('app_chat_last_msg_id', 0);

        $html = '';

        $count = 0;
        $chat_msg_pager_skip_id = 0;
        $messages_query = db_query(
            "select * from app_ext_chat_conversations_messages where conversations_id='" . $assigned_to . "' order by id desc limit " . $this->render_messages_limit
        );
        while ($messages = db_fetch_array($messages_query)) {
            $html = $this->render_message_template($messages) . $html;

            if ($count == 0) {
                $app_users_cfg->set('app_chat_last_msg_id', $messages['id']);
                $count++;
            }

            $chat_msg_pager_skip_id = $messages['id'];
        }

        $html .= input_hidden_tag('chat_msg_pager_skip_id', $chat_msg_pager_skip_id);

        return $html;
    }

    function render_message_template($messages)
    {
        global $app_user, $app_users_cache;

        $html = '
				<div class="chat-msg-item">
					<div class="chat-msg-item-user-photo">' . render_user_photo(
                $app_users_cache[$messages['users_id']]['photo']
            ) . '</div>			
					<div class="chat-msg-item-user">
						' . $app_users_cache[$messages['users_id']]['name'] . '
						<span class="chat-msg-item-time">' . format_date_time($messages['date_added']) . '</span>		
					</div>			
					<div class="chat-msg-item-text">' . auto_link_text($messages['message']) . '</div>
					' . (strlen(
                $messages['attachments']
            ) ? '<div class="chat-msg-item-text">' . $this->render_message_attachments_template(
                    $messages['attachments']
                ) . '</div>' : '') . '		
				</div>
				';

        return $html;
    }

    function render_message_attachments_template($attachments)
    {
        $html = '<table class="chat-attachments-table">';

        foreach (explode(',', $attachments) as $v) {
            $file = attachments::parse_filename($v);

            $html .= '
						<tr>
							<td><img src="' . url_for_file($file['icon']) . '"></td>
							<td><a target="_blank" href="' . url_for(
                    'ext/app_chat/chat',
                    'action=attachment_download&file=' . urlencode(base64_encode($file['file']))
                ) . '">' . $file['name'] . '</a>&nbsp;<small>(' . $file['size'] . ')</small></a>
							</td>
						</tr>';
        }

        $html .= '</table>';

        return $html;
    }

    function render_attachments_preview($form_token)
    {
        $attachments_list = [];
        $attachments_id_list = [];

        $attachments_query = db_query(
            "select * from app_attachments where form_token='" . db_input($form_token) . "' and container=0"
        );
        while ($attachments = db_fetch_array($attachments_query)) {
            $attachments_list[$attachments['id']] = $attachments['filename'];
            $attachments_id_list[] = $attachments['id'];
        }

        $html = '';

        if (count($attachments_list) > 0) {
            $html = '<table class="chat-attachments-table">';
            foreach ($attachments_list as $attachments_id => $v) {
                $file = attachments::parse_filename($v);

                $html .= '
						<tr class="attachment-row-' . $attachments_id . '">
							<td><img src="' . url_for_file($file['icon']) . '"></td>						
							<td>
									<a target="_blank" href="' . url_for(
                        'ext/app_chat/chat',
                        'action=attachment_download&file=' . urlencode(base64_encode($file['file']))
                    ) . '">' . $file['name'] . '</a>
									&nbsp;<small>(' . $file['size'] . ')</small>
									&nbsp;<a href="javascript: chat_attachment_remove(\'' . $attachments_id . '\')" class="chat-attachment-remove"><i class="fa fa-times" aria-hidden="true"></i></a>
							</td>									
						</tr>';
            }
            $html .= '</table>';

            $html .= input_hidden_tag('chat_message_attachments', implode(',', $attachments_list));
            $html .= input_hidden_tag('chat_message_attachments_ids', implode(',', $attachments_id_list));
        } else {
            $html .= input_hidden_tag('chat_message_attachments', '');
        }

        return $html;
    }

    public static function send_notification()
    {
        if (CFG_CHAT_SEND_ALERTS != 1) {
            return false;
        }

        $messages_query = db_query(
            "select count(*) as count_new, assigned_to from app_ext_chat_unread_messages where notification_status=0 group by assigned_to"
        );
        while ($messages = db_fetch_array($messages_query)) {
            $users_query = db_query(
                "select e.* from app_entity_1 e where e.field_5=1 and id='" . $messages['assigned_to'] . "'"
            );
            if ($user = db_fetch_array($users_query)) {
                $subject = (strlen(CFG_CHAT_ALERTS_SUBJECT) ? CFG_CHAT_ALERTS_SUBJECT : TEXT_EXT_CHAT_ALERTS_SUBJECT);

                $html = TEXT_EXT_CHAT_NOTIFICATION_EMAIL . '<p>' . TEXT_EXT_CHAT_NOTIFICATION_EMAIL_NEW_MESSAGES . ': ' . $messages['count_new'] . '</p>';

                $count_query = db_query(
                    "select count(*) as total from app_ext_chat_unread_messages where notification_status=1 and assigned_to='" . $messages['assigned_to'] . "'"
                );
                $count = db_fetch_array($count_query);

                if ($count['total'] > 0) {
                    $html .= '<p>' . TEXT_EXT_CHAT_NOTIFICATION_EMAIL_ALL_MESSAGES . ': ' . $count['total'] . '</p>';
                }

                $html .= '<p><a href="' . CRON_HTTP_SERVER_HOST . 'index.php?module=ext/app_chat/chat_window' . '">' . TEXT_EXT_CHAT_NOTIFICATION_EMAIL_GOTO_MESSAGES . '</a></p>';

                $options = [
                    'to' => $user['field_9'],
                    'to_name' => users::output_heading_from_item($user),
                    'subject' => sprintf($subject, $messages['count_new']),
                    'body' => $html,
                    'from' => CFG_EMAIL_ADDRESS_FROM,
                    'from_name' => CFG_EMAIL_NAME_FROM
                ];

                //echo '<pre>';
                //print_r($options);

                users::send_email($options);
            }
        }

        //update notification status
        db_query("update app_ext_chat_unread_messages set notification_status=1");
    }

}
