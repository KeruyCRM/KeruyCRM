<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Tools;

class Db_backup_auto extends \Controller
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
        \Tools\Backup::reset(true);

        //$backups_query = "select * from app_backups where is_auto=1 order by date_added desc";

        $backups_query = \Tools\Split_page::makeQuery(
            'app_backups',
            ['is_auto = 1'],
            ['order' => 'date_added desc']
        );

        \K::$fw->listing_split = $listing_split = new \Tools\Split_page($backups_query, 'records_listing');
        \K::$fw->backups_query = \K::model()->db_fetch_split(
            $listing_split->sql_query()
        );

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'db_backup_auto.php';

        echo \K::view()->render($this->app_layout);
    }
}