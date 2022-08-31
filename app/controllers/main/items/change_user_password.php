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

        if (\K::$fw->current_entity_id != 1) {
            \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
        }

        if (!\Models\Main\Users\Users::has_access('update')) {
            \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
        }
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'change_user_password.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function change()
    {
        if (\K::$fw->VERB == 'POST') {
            $password = \K::$fw->POST['password_new'];
            $password_confirm = \K::$fw->POST['password_confirmation'];

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
                if (!preg_match('/[A-Z]/', $password) or !preg_match('/[0-9]/', $password) or !preg_match(
                        '/\W/',
                        $password
                    )) {
                    $error = true;
                    \K::flash()->addMessage(\K::$fw->TEXT_STRONG_PASSWORD_TIP, 'error');
                }
            }

            if (!$error) {
                //$hasher = new PasswordHash(11, false);

                $sql_data = [];
                $sql_data['password'] = \K::security()->password_hash($password);

                \K::model()->db_perform('app_entity_1', $sql_data, [
                    'id = ?',
                    \K::$fw->current_item_id
                ]);

                $obj = \K::model()->db_find('app_entity_1', \K::$fw->current_item_id);

                $options = [
                    'to' => $obj['field_9'],
                    'to_name' => $obj['field_7'] . ' ' . $obj['field_8'],
                    'subject' => \K::$fw->TEXT_USER_PWD_CHANGED_EMAIL_SUBJECT,
                    'body' => \K::$fw->TEXT_USER_PWD_CHANGED_EMAIL_BODY . '<p><b>' . \K::$fw->TEXT_LOGIN_DETAILS . '</b></p><p>' . \K::$fw->TEXT_USERNAME . ': ' . $obj['field_12'] . '<br>' . \K::$fw->TEXT_PASSWORD . ': ' . $password . '</p><p><a href="' . \Helpers\Urls::url_for(
                            'main/users/login'
                        ) . '">' . \Helpers\Urls::url_for('main/users/login') . '</a></p>',
                    'from' => \K::$fw->app_user['email'],
                    'from_name' => 'noreply'
                ];

                \Models\Main\Users\Users::send_email($options);

                \K::flash()->addMessage(\K::$fw->TEXT_USER_PASSWORD_UPDATED, 'success');
            }

            \Helpers\Urls::redirect_to('main/items/change_user_password', 'path=' . \K::$fw->current_path);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}