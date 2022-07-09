<?php

namespace Controllers\Main\Users;

class Change_password extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        //check security settings if they are enabled
        \Helpers\App_restricted_countries::verify();
        \Helpers\App_restricted_ip::verify();

        if ((in_array(\K::$fw->app_user['group_id'], explode(',', \K::$fw->CFG_APP_DISABLE_CHANGE_PWD))
                and strlen(\K::$fw->CFG_APP_DISABLE_CHANGE_PWD) > 0)
            or \K::$fw->CFG_USE_LDAP_LOGIN_ONLY == true) {
            \Helpers\Urls::redirect_to('main/users/account');
        }
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'change_password.php';

        echo \K::view()->render($this->app_layout);
    }

    public function change()
    {
        if (\K::$fw->VERB == 'POST') {
            $password = \K::$fw->{'POST.password_new'};
            $password_confirm = \K::$fw->{'POST.password_confirmation'};

            $error = false;

            if ($password != $password_confirm) {
                $error = true;
                \K::flash()->addMessage(\K::$fw->TEXT_ERROR_PASSWORD_CONFIRMATION, 'error');
            }

            if (strlen($password) < \K::$fw->CFG_PASSWORD_MIN_LENGTH) {
                $error = true;
                \K::flash()->addMessage(\K::$fw->TEXT_ERROR_PASSWORD_LENGTH, 'error');
            }

            if (\K::$fw->CFG_IS_STRONG_PASSWORD) {
                if (!preg_match('/[A-Z]/', $password)
                    or !preg_match('/\d/', $password)
                    or !preg_match('/\W/', $password)) {
                    $error = true;
                    \K::flash()->addMessage(\K::$fw->TEXT_STRONG_PASSWORD_TIP, 'error');
                }
            }

            if (!$error) {
                $hasher = new \Libs\PasswordHash(11, false);

                /*$sql_data = [];
                $sql_data['password'] = $hasher->HashPassword($password);

                db_perform('app_entity_1', $sql_data, 'update', "id='" . db_input(\K::$fw->app_logged_users_id) . "'");*/

                \K::model()->db_perform('app_entity_1', ['password' => $hasher->HashPassword($password)], [
                    'id = ?',
                    \K::$fw->app_logged_users_id
                ]);

                \K::flash()->addMessage(\K::$fw->TEXT_PASSWORD_UPDATED, 'success');
            }
        }
        \Helpers\Urls::redirect_to('main/users/change_password');
    }
}