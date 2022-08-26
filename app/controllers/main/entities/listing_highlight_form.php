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

        if (!\K::$fw->GET['entities_id']) {
            \Helpers\Urls::redirect_to('main/entities');//FIX
        }
    }

    public function index()
    {
        /*$obj = (isset(\K::$fw->GET['id']) ? db_find('app_listing_highlight_rules', _GET('id')) : db_show_columns(
            'app_listing_highlight_rules'
        ));*/

        \K::$fw->obj = \K::model()->db_find('app_listing_highlight_rules', \K::$fw->GET['id']);

        if (!isset(\K::$fw->GET['id'])) {
            \K::$fw->obj['is_active'] = 1;

            /*$sort_order_query = db_query(
                "select (max(sort_order)+1) as sort_order from app_listing_highlight_rules where entities_id=" . _GET(
                    'entities_id'
                )
            );*/

            $sort_order = \K::model()->db_fetch_one('app_listing_highlight_rules', [
                'entities_id = ?',
                \K::$fw->GET['entities_id']
            ], [], null, ['sort_order' => '(max(sort_order)+1)'], 0);

            if ($sort_order) {
                \K::$fw->obj['sort_order'] = $sort_order['sort_order'];
            }
        }

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'listing_highlight_form.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}