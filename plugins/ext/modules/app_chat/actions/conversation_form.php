<?php

$app_users_cfg->set('app_chat_active_dialog', '');

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_chat_conversations', _get::int('id'));
} else {
    $obj = db_show_columns('app_ext_chat_conversations');
}