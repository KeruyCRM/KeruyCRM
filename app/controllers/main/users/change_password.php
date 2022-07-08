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

        if ((in_array($app_user['group_id'], explode(',', CFG_APP_DISABLE_CHANGE_PWD)) and strlen(
                    CFG_APP_DISABLE_CHANGE_PWD
                ) > 0) or CFG_USE_LDAP_LOGIN_ONLY == true) {
            redirect_to('users/account');
        }
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'change_password.php';

        echo \K::view()->render($this->app_layout);
    }

    public function change()
    {
        $password = $_POST['password_new'];
        $password_confirm = $_POST['password_confirmation'];

        $error = false;

        if ($password != $password_confirm) {
            $error = true;
            $alerts->add(TEXT_ERROR_PASSWORD_CONFIRMATION, 'error');
        }

        if (strlen($password) < CFG_PASSWORD_MIN_LENGTH) {
            $error = true;
            $alerts->add(TEXT_ERROR_PASSWORD_LENGTH, 'error');
        }

        if (CFG_IS_STRONG_PASSWORD) {
            if (!preg_match('/[A-Z]/', $password) or !preg_match('/[0-9]/', $password) or !preg_match(
                    '/[^\w]/',
                    $password
                )) {
                $error = true;
                $alerts->add(TEXT_STRONG_PASSWORD_TIP, 'error');
            }
        }

        if (!$error) {
            $hasher = new PasswordHash(11, false);

            $sql_data = [];
            $sql_data['password'] = $hasher->HashPassword($password);

            db_perform('app_entity_1', $sql_data, 'update', "id='" . db_input($app_logged_users_id) . "'");

            $alerts->add(TEXT_PASSWORD_UPDATED, 'success');
        }

        redirect_to('users/change_password');
    }
}