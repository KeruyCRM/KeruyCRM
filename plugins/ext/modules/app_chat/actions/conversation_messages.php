<?php

$assigned_to = _post::int('assigned_to');

$attachments_form_token = md5($app_user['id'] . $assigned_to . 'conversation');

$chat_conversation_query = db_query(
    "select * from app_ext_chat_conversations where id='" . $assigned_to . "' and (users_id='" . $app_user['id'] . "' or find_in_set('" . $app_user['id'] . "',assigned_to))"
);
if (!$chat_conversation = db_fetch_array($chat_conversation_query)) {
    echo "
		<script>
			if(is_app_caht_timer){ clearInterval(app_caht_timer); is_app_caht_timer = false; }
			$('#chat_messages').html('<div class=\"alert alert-warning\">" . TEXT_EXT_CHAT_CONVERSATION_IS_NOT_FOUND . "</div>')
		</script>";

    exit();
}

$app_users_cfg->set('app_chat_active_dialog', 'conversation:' . $assigned_to);

switch ($app_module_action) {
    case 'search':

        if (strlen($_POST['keywords'])) {
            if (app_parse_search_string($_POST['keywords'], $search_keywords)) {
                //print_r($search_keywords);

                $where_str = '';

                if (isset($search_keywords) && (sizeof($search_keywords) > 0)) {
                    $where_str = "(";
                    for ($i = 0, $n = sizeof($search_keywords); $i < $n; $i++) {
                        switch ($search_keywords[$i]) {
                            case '(':
                            case ')':
                            case 'and':
                            case 'or':
                                $where_str .= " " . $search_keywords[$i] . " ";
                                break;
                            default:
                                $keyword = $search_keywords[$i];
                                $where_str .= "message like '%" . db_input($keyword) . "%'";
                                break;
                        }
                    }
                    $where_str .= ")";

                    $messages_query = db_query(
                        "select * from app_ext_chat_conversations_messages where conversations_id='" . $assigned_to . "' and " . $where_str . " order by id"
                    );
                    while ($messages = db_fetch_array($messages_query)) {
                        echo $app_chat->render_message_template($messages);
                    }
                }
            }
        }


        exit();

        break;
    case 'save':
        //if(trim(strip_tags($_POST['message']))>0)
        {
            print_r($_POST);

            $sql_data = [
                'users_id' => db_prepare_input($app_user['id']),
                'conversations_id' => db_prepare_input($_POST['assigned_to']),
                'message' => db_prepare_html_input($_POST['chat_message']),
                'attachments' => db_prepare_input($_POST['chat_message_attachments']),
                'date_added' => time(),
            ];

            db_perform('app_ext_chat_conversations_messages', $sql_data);
            $messages_id = db_insert_id();

            if (isset($_POST['chat_message_attachments_ids'])) {
                db_query("delete from app_attachments where id in (" . $_POST['chat_message_attachments_ids'] . ")");
            }

            $users_list = explode(',', $chat_conversation['assigned_to']);
            $users_list[] = $chat_conversation['users_id'];

            foreach ($users_list as $users_id) {
                if ($users_id != $app_user['id']) {
                    $sql_data = [
                        'assigned_to' => db_prepare_input($users_id),
                        'conversations_id' => db_prepare_input($chat_conversation['id']),
                        'messages_id' => $messages_id,
                    ];

                    db_perform('app_ext_chat_unread_messages', $sql_data);
                }
            }
        }

        exit();
        break;
    case 'get_messages':

        $html = '';
        $messages_ids = [];
        $count = 0;
        $messages_query = db_query(
            "select * from app_ext_chat_conversations_messages where conversations_id='" . $assigned_to . "' and id>" . (int)$app_users_cfg->get(
                'app_chat_last_msg_id'
            ) . " order by id desc"
        );
        while ($messages = db_fetch_array($messages_query)) {
            $html = $app_chat->render_message_template($messages) . $html;


            if ($count == 0) {
                $app_users_cfg->set('app_chat_last_msg_id', $messages['id']);
                $count++;
            }
        }

        if ($count > 0) {
            $app_chat->reset_unread_conversations_messages($assigned_to);
        }

        echo $html;

        exit();
        break;

    case 'get_previous_messages':
        $skip_id = (int)$_GET['skip_id'];
        $offset = ($app_chat->render_messages_limit * ($_GET['page'] - 1));
        $sql_query .= " limit " . max($offset, 0) . ", " . $app_chat->render_messages_limit;

        $html = '';

        $messages_query = db_query(
            "select * from app_ext_chat_conversations_messages where conversations_id='" . $assigned_to . "' order by id desc " . $sql_query
        );
        while ($messages = db_fetch_array($messages_query)) {
            if ($messages['id'] >= $skip_id) {
                continue;
            }

            $html = $app_chat->render_message_template($messages) . $html;
        }

        echo '<div class="chat-msg-page-' . $_GET['page'] . '">' . $html . '</div>';

        exit();
        break;
}