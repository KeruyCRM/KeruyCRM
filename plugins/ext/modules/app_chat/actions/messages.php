<?php

$assigned_to = (int)$_POST['assigned_to'];

$attachments_form_token = md5($app_user['id'] . $assigned_to . 'messages');

$chat_user_query = db_query(
    "select u.*,a.name as group_name,u.field_6 as group_id from app_entity_1 u left join app_access_groups a on a.id=u.field_6 where u.id='" . db_input(
        $assigned_to
    ) . "'"
);
if (!$chat_user = db_fetch_array($chat_user_query)) {
    echo '<div class="alert alert-warning">' . TEXT_USER_IS_NOT_FOUND . '</div>';
    exit();
}

//check access
if (!$app_chat->has_access_by_group($chat_user['group_id'])) {
    echo '<div class="alert alert-warning">' . TEXT_USER_IS_NOT_FOUND . '</div>';
    exit();
}

$app_users_cfg->set('app_chat_active_dialog', 'user:' . $assigned_to);

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
                        "select * from app_ext_chat_messages where ((users_id='" . $app_user['id'] . "' and assigned_to='" . $assigned_to . "') or (assigned_to='" . $app_user['id'] . "' and users_id='" . $assigned_to . "')) and " . $where_str . " order by id"
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

        $sql_data = [
            'users_id' => db_prepare_input($app_user['id']),
            'assigned_to' => db_prepare_input($_POST['assigned_to']),
            'message' => db_prepare_html_input($_POST['chat_message']),
            'attachments' => db_prepare_input($_POST['chat_message_attachments']),
            'date_added' => time(),
        ];

        db_perform('app_ext_chat_messages', $sql_data);
        $messages_id = db_insert_id();

        if (isset($_POST['chat_message_attachments_ids'])) {
            db_query("delete from app_attachments where id in (" . $_POST['chat_message_attachments_ids'] . ")");
        }

        //add unread message
        $sql_data = [
            'users_id' => db_prepare_input($app_user['id']),
            'assigned_to' => db_prepare_input($_POST['assigned_to']),
            'messages_id' => $messages_id,
        ];

        db_perform('app_ext_chat_unread_messages', $sql_data);

        exit();
        break;
    case 'get_messages':

        $html = '';
        $messages_ids = [];
        $count = 0;
        $messages_query = db_query(
            "select * from app_ext_chat_messages where ((users_id='" . $app_user['id'] . "' and assigned_to='" . $assigned_to . "') or (assigned_to='" . $app_user['id'] . "' and users_id='" . $assigned_to . "')) and id>" . (int)$app_users_cfg->get(
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
            $app_chat->reset_unread_messages($assigned_to);
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
            "select * from app_ext_chat_messages where ((users_id='" . $app_user['id'] . "' and assigned_to='" . $assigned_to . "') or (assigned_to='" . $app_user['id'] . "' and users_id='" . $assigned_to . "')) order by id desc " . $sql_query
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
