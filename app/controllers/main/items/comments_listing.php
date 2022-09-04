<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Comments_listing extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();
    }

    public function index()
    {
        \K::$fw->access_rules = new \Models\Main\Access_rules(\K::$fw->current_entity_id, \K::$fw->current_item_id);

        if (\Models\Main\Users\Users::has_comments_access(
            'view',
            \K::$fw->access_rules->get_comments_access_schema()
        )) {
            \K::$fw->entity_cfg = new \Models\Main\Entities_cfg(\K::$fw->current_entity_id);

            \K::$fw->user_has_comments_access = (\Models\Main\Users\Users::has_comments_access(
                    'update',
                    \K::$fw->access_rules->get_comments_access_schema()
                ) or \Models\Main\Users\Users::has_comments_access(
                    'delete',
                    \K::$fw->access_rules->get_comments_access_schema()
                ) or \Models\Main\Users\Users::has_comments_access(
                    'create',
                    \K::$fw->access_rules->get_comments_access_schema()
                ));

            \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'comments_listing.php';

            echo \K::view()->render(\K::$fw->app_layout);
        }
    }
}