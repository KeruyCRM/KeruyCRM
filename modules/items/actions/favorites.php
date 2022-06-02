<?php

switch ($app_module_action) {
    case  'favorites_add':

        $sql_data = [
            'users_id' => $app_user['id'],
            'entities_id' => $current_entity_id,
            'items_id' => $current_item_id,
        ];

        db_perform('app_favorites', $sql_data);

        exit();
        break;

    case  'favorites_remove':

        db_query(
            "delete from app_favorites where users_id={$app_user['id']} and entities_id='{$current_entity_id}' and items_id='{$current_item_id}'"
        );

        exit();
        break;
}