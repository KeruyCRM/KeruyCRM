<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Tools;

class Db_restore extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Tools\_Module::top();
    }

    public function index()
    {
        if (isset(\K::$fw->GET['id'])) {
            \K::$fw->backup_info = \K::model()->db_find('app_backups', \K::$fw->GET['id']);

            \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'db_restore.php';

            echo \K::view()->render(\K::$fw->subTemplate);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}