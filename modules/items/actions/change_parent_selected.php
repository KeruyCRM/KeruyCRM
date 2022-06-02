<?php

switch ($app_module_action) {
    case 'change_parent':
        $parent_id = isset($_POST['parent_id']) ? _POST('parent_id') : 0;

        $reports_id = _POST('reports_id');

        if (!isset($app_selected_items[$reports_id])) {
            $app_selected_items[$reports_id] = [];
        }

        if (count($app_selected_items[$reports_id]) and !in_array($parent_id, $app_selected_items[$reports_id])) {
            db_query(
                "update app_entity_{$current_entity_id} set parent_id={$parent_id} where id in (" . implode(
                    ',',
                    $app_selected_items[$reports_id]
                ) . ")"
            );
        }

        redirect_to('items/items', 'path=' . $app_path);
        break;
}

