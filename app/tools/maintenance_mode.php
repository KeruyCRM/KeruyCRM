<?php

namespace Tools;

class Maintenance_mode
{
    static function login_message()
    {
        $html = '';

        if (\K::$fw->CFG_MAINTENANCE_MODE == 1) {
            $html = '
					<div class="alert alert-block alert-warning fade in">
						<h4>' . (strlen(
                    \K::$fw->CFG_MAINTENANCE_MESSAGE_HEADING
                ) > 0 ? \K::$fw->CFG_MAINTENANCE_MESSAGE_HEADING : \K::$fw->TEXT_MAINTENANCE_MESSAGE_HEADING) . '</h4>
						<p>' . (strlen(
                    \K::$fw->CFG_MAINTENANCE_MESSAGE_CONTENT
                ) > 0 ? \K::$fw->CFG_MAINTENANCE_MESSAGE_CONTENT : \K::$fw->TEXT_MAINTENANCE_MESSAGE_CONTENT) . '</p>
					</div>
					';
        }

        return $html;
    }

    static function header_message()
    {
        $html = '';

        if (\K::$fw->CFG_MAINTENANCE_MODE == 1) {
            $html = '
					<span class="label label-warning">' . \K::$fw->TEXT_MAINTENANCE_MODE . '</span>
					';
        }

        return $html;
    }

    static function check()
    {
        if (\K::app_session_is_registered('app_logged_users_id') and \K::$fw->app_module_path != 'users/login') {
            if (\K::$fw->CFG_MAINTENANCE_MODE == 1 and \K::$fw->app_user['group_id'] != 0) {
                if (!in_array(
                    \K::$fw->app_user['id'],
                    explode(',', \K::$fw->CFG_MAINTENANCE_ALLOW_LOGIN_FOR_USERS)
                )) {
                    \K::flash()->addMessage(\K::$fw->TEXT_ACCESS_FORBIDDEN, 'error');
                    \Helpers\Urls::redirect_to('main/users/login/logoff');
                }
            }
        }
    }
}