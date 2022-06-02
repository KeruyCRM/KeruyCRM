<?php

switch ($app_module_action) {
    case 'change_parent':
        $parent_id = isset($_POST['parent_id']) ? _POST('parent_id') : 0;

        if ($parent_id != $current_item_id) {
            db_query("update app_entity_{$current_entity_id} set parent_id={$parent_id} where id={$current_item_id}");
        }

        redirect_to('items/info', 'path=' . $app_path);
        break;
}
