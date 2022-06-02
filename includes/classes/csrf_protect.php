<?php

class csrf_protect
{
    static function add_token_to_url($url)
    {
        global $app_session_token;

        if (strstr($url, '&action=') and app_session_is_registered('app_logged_users_id')
            and !strstr($url, '&action=attachments_preview') and !strstr($url, '&action=download_attachment')) {
            return '&token=' . urlencode($app_session_token);
        } else {
            return '';
        }
    }

    static function check()
    {
        global $app_session_token, $app_module_path;

        if ($app_module_path != 'users/login') {
            if (isset($_GET['action']) and !in_array($_GET['action'], ['attachments_preview', 'download_attachment']
                ) and app_session_is_registered('app_logged_users_id') and (!isset($_GET['token']) or urldecode(
                        $_GET['token']
                    ) != $app_session_token)) {
                redirect_to('dashboard/token_error');
            }
        }
    }
}
