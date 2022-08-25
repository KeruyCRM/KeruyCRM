<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Listing_highlight_form extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        $obj = [];

        $obj = (isset($_GET['id']) ? db_find('app_listing_highlight_rules', _GET('id')) : db_show_columns(
            'app_listing_highlight_rules'
        ));

        if (!isset($_GET['id'])) {
            $obj['is_active'] = 1;

            $sort_order_query = db_query(
                "select (max(sort_order)+1) as sort_order from app_listing_highlight_rules where entities_id=" . _GET(
                    'entities_id'
                )
            );
            if ($sort_order = db_fetch_array($sort_order_query)) {
                $obj['sort_order'] = $sort_order['sort_order'];
            }
        }

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'listing_highlight_form.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}