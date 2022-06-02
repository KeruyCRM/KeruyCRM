<?php

$app_users_cfg->set('app_chat_active_dialog', '');

switch ($app_module_action) {
    case 'save_sending_settings':
        $app_users_cfg->set('chat_sending_settings', db_prepare_input($_POST['chat_sending_settings']));
        $app_users_cfg->set('chat_sound_notification', db_prepare_input($_POST['chat_sound_notification']));
        $app_users_cfg->set('chat_instant_notification', db_prepare_input($_POST['chat_instant_notification']));
        break;
}