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
        $sql_data = [
            'users_id' => $app_user['id'],
            'entities_id' => $current_entity_id,
            'items_id' => $current_item_id,
        ];

        db_perform('app_favorites', $sql_data);
    }

    public function favorites_remove()
    {
        db_query(
            "delete from app_favorites where users_id={$app_user['id']} and entities_id='{$current_entity_id}' and items_id='{$current_item_id}'"
        );
    }
}