<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Listing_sections_sort extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        /*$filters_query = db_query(
            "select * from app_listing_sections where listing_types_id='" . db_input(
                _get::int('listing_types_id')
            ) . "' order by sort_order, name"
        );*/

        \K::$fw->filters_query = \K::model()->db_fetch('app_listing_sections', [
            'listing_types_id = ?',
            \K::$fw->GET['listing_types_id']
        ], ['order' => 'sort_order,name']);

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'listing_sections_sort.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }
}