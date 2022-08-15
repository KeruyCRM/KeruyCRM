<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Users_alerts;

class Users_alerts extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Users_alerts\_Module::top();
    }

    public function index()
    {
        //$alerts_query = db_query("select * from app_users_alerts order by id desc");

        \K::$fw->alerts_query = \K::model()->db_fetch('app_users_alerts', [], ['order' => 'id desc']);

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'users_alerts.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $sql_data = [
                'is_active' => (isset(\K::$fw->POST['is_active']) ? 1 : 0),
                'type' => \K::$fw->POST['type'],
                'title' => \K::$fw->POST['title'],
                'description' => \K::$fw->POST['description'],
                'location' => \K::$fw->POST['location'],
                'start_date' => (int)\Helpers\App::get_date_timestamp(\K::$fw->POST['start_date']),
                'end_date' => (int)\Helpers\App::get_date_timestamp(\K::$fw->POST['end_date']),
                'users_groups' => (isset(\K::$fw->POST['users_groups']) ? implode(
                    ',',
                    \K::$fw->POST['users_groups']
                ) : ''),
                'assigned_to' => (isset(\K::$fw->POST['assigned_to']) ? implode(
                    ',',
                    \K::$fw->POST['assigned_to']
                ) : ''),
                'created_by' => \K::$fw->app_user['id'],
            ];

            \K::model()->db_perform('app_users_alerts', $sql_data, ['id = ?', \K::$fw->GET['id']]);

            \Helpers\Urls::redirect_to('main/users_alerts');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['id'])) {
            //db_query("delete from app_users_alerts where id='" . \K::$fw->GET['id'] . "'");
            //db_query("delete from app_users_alerts_viewed where alerts_id='" . \K::$fw->GET['id'] . "'");

            \K::model()->begin();

            \K::model()->db_delete('app_users_alerts', [
                'id = ?',
                \K::$fw->GET['id']
            ]);
            \K::model()->db_delete('app_users_alerts_viewed', [
                'alerts_id = ?',
                \K::$fw->GET['id']
            ]);

            \K::model()->commit();

            \Helpers\Urls::redirect_to('main/users_alerts');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}