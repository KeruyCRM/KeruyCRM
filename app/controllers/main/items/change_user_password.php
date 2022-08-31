<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Change_user_password extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();

        if ($current_entity_id != 1) {
            redirect_to('dashboard/access_forbidden');
        }

        if (!users::has_access('update')) {
            redirect_to('dashboard/access_forbidden');
        }
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'change_user_password.php';

        echo \K::view()->render(\K::$fw->app_layout);
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

            db_perform('app_entity_1', $sql_data, 'update', "id='" . db_input($current_item_id) . "'");

            $obj = db_find('app_entity_1', $current_item_id);

            $options = [
                'to' => $obj['field_9'],
                'to_name' => $obj['field_7'] . ' ' . $obj['field_8'],
                'subject' => TEXT_USER_PWD_CHANGED_EMAIL_SUBJECT,
                'body' => TEXT_USER_PWD_CHANGED_EMAIL_BODY . '<p><b>' . TEXT_LOGIN_DETAILS . '</b></p><p>' . TEXT_USERNAME . ': ' . $obj['field_12'] . '<br>' . TEXT_PASSWORD . ': ' . $password . '</p><p><a href="' . url_for(
                        'users/login',
                        '',
                        true
                    ) . '">' . url_for('users/login', '', true) . '</a></p>',
                'from' => $app_user['email'],
                'from_name' => 'noreply'
            ];

            users::send_email($options);

            $alerts->add(TEXT_USER_PASSWORD_UPDATED, 'success');
        }

        redirect_to('items/change_user_password', 'path=' . $current_path);
    }
}