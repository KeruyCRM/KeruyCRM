<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Call_history extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();
    }

    public function index()
    {
        /*$history_query = db_query(
            "select * from app_ext_call_history where phone='" . preg_replace(
                '/\D/',
                '',
                $_GET['phone']
            ) . "' order by date_added desc"
        );*/

        \K::$fw->history_query = \K::model()->db_fetch('app_ext_call_history', [
            'phone = ?',
            preg_replace('/\D/', '', $_GET['phone'])
        ], ['order' => 'date_added desc']);

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'call_history.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}