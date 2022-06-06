<?php

if ((in_array($app_user['group_id'], explode(',', CFG_APP_DISABLE_CHANGE_PWD)) and strlen(
            CFG_APP_DISABLE_CHANGE_PWD
        ) > 0) or CFG_USE_LDAP_LOGIN_ONLY == true) {
    redirect_to('users/account');
}

switch ($app_module_action) {
    case 'change':

        //chck form token
        app_check_form_token();

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
        break;
}