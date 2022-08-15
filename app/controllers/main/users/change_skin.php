<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Users;

class Change_skin extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'change_skin.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }

    public function change_skin()
    {
        if (isset(\K::$fw->GET['set_skin']) and \K::security()->checkCsrfTokenUrl()) {
            $skin = \K::$fw->GET['set_skin'];

            if (is_file('css/skins/' . $skin . '/' . $skin . '.css')) {
                //db_query("update app_entity_1 set field_14='" . db_input($skin) . "' where id='" . \K::$fw->app_logged_users_id . "'");

                \K::model()->db_update('app_entity_1', [
                    'field_14' => $skin
                ], [
                    'id = ?',
                    \K::$fw->app_logged_users_id
                ]);

                //setcookie('user_skin', $skin, time() + (365 * 24 * 3600), $_SERVER['HTTP_HOST'], '', (is_ssl() ? 1 : 0));
                \K::cookieSet('user_skin', $skin, 60 * 60 * 24 * 36);
            }
        }
        \Helpers\Urls::redirect_to('main/dashboard');
    }
}