<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Favorites extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();
    }

    public function index()
    {
        \Helpers\Urls::redirect_to('main/dashboard');
    }

    public function favorites_add()
    {
        if (\K::$fw->VERB == 'POST') {
            $sql_data = [
                'users_id' => \K::$fw->app_user['id'],
                'entities_id' => \K::$fw->current_entity_id,
                'items_id' => \K::$fw->current_item_id,
            ];

            \K::model()->db_perform('app_favorites', $sql_data);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function favorites_remove()
    {
        if (\K::$fw->VERB == 'POST') {
            /*db_query(
                "delete from app_favorites where users_id={\K::$fw->app_user['id']} and entities_id='{\K::$fw->current_entity_id}' and items_id='{\K::$fw->current_item_id}'"
            );*/

            \K::model()->db_delete('app_favorites', [
                'users_id = ? and entities_id = ? and items_id = ?',
                \K::$fw->app_user['id'],
                \K::$fw->current_entity_id,
                \K::$fw->current_item_id
            ]);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}