<?php

namespace Tools;

class Maintenance_mode
{
    static function login_message()
    {
        $html = '';

        if (\K::f3()->CFG_MAINTENANCE_MODE == 1) {
            $html = '
					<div class="alert alert-block alert-warning fade in">
						<h4>' . (strlen(
                    \K::f3()->CFG_MAINTENANCE_MESSAGE_HEADING
                ) > 0 ? \K::f3()->CFG_MAINTENANCE_MESSAGE_HEADING : \K::f3()->TEXT_MAINTENANCE_MESSAGE_HEADING) . '</h4>
						<p>' . (strlen(
                    \K::f3()->CFG_MAINTENANCE_MESSAGE_CONTENT
                ) > 0 ? \K::f3()->CFG_MAINTENANCE_MESSAGE_CONTENT : \K::f3()->TEXT_MAINTENANCE_MESSAGE_CONTENT) . '</p>
					</div>
					';
        }

        return $html;
    }

    static function header_message()
    {
        $html = '';

        if (\K::f3()->CFG_MAINTENANCE_MODE == 1) {
            $html = '
					<span class="label label-warning">' . \K::f3()->TEXT_MAINTENANCE_MODE . '</span>
					';
        }

        return $html;
    }

    static function check()
    {
        if (\K::sessionExists('app_logged_users_id') and \K::f3()->app_module_path != 'users/login') {
            if (\K::f3()->CFG_MAINTENANCE_MODE == 1 and \K::f3()->app_user['group_id'] != 0) {
                if (!in_array(
                    \K::f3()->app_user['id'],
                    explode(',', \K::f3()->CFG_MAINTENANCE_ALLOW_LOGIN_FOR_USERS)
                )) {
                    \K::flash()->addMessage(\K::f3()->TEXT_ACCESS_FORBIDDEN, 'error');
                    \K::f3()->reroute('/users/login/logoff');
                }
            }
        }
    }
}