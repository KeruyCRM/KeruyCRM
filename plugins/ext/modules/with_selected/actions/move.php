<?php

//check report and access
$reports_info_query = db_query("select * from app_reports where id='" . db_input($_GET['reports_id']) . "'");
if ($reports_info = db_fetch_array($reports_info_query)) {
    $access_schema = users::get_entities_access_schema($reports_info['entities_id'], $app_user['group_id']);

    if (!users::has_access('move', $access_schema)) {
        redirect_to('dashboard/access_forbidden');
    }

    $entity_info = db_find('app_entities', $reports_info['entities_id']);

    if ($entity_info['parent_id'] == 0) {
        redirect_to('dashboard/page_not_found');
    }
} else {
    redirect_to('dashboard/page_not_found');
}

switch ($app_module_action) {
    case 'move_selected':
        $entities_id = $reports_info['entities_id'];
        $entity_info = db_find('app_entities', $entities_id);

        //set default parent id
        $parent_item_id = 0;

        //get parent id for sub-entities
        if ($entity_info['parent_id'] > 0) {
            if (strlen($_POST['move_to']) > 0) {
                $path_info = items::get_path_info($entity_info['parent_id'], (int)$_POST['move_to']);

                $go_to_url = url_for('items/items', 'path=' . $path_info['full_path'] . '/' . $entities_id);

                $parent_item_id = (int)$_POST['move_to'];
            }

            //parent id is requried for sub-entities
            if ($parent_item_id == 0) {
                echo '<div class="alert alert-danger">' . TEXT_COPY_ERROR_PARENT_RECORD . '</div>';
                exit();
            }
        }

        //move records
        if (count($app_selected_items[$_GET['reports_id']]) > 0 and $parent_item_id > 0) {
            foreach ($app_selected_items[$_GET['reports_id']] as $item_id) {
                $item_info_query = db_query(
                    "select * from app_entity_" . $entities_id . " where id='" . $item_id . "'"
                );
                if ($item_info = db_fetch_array($item_info_query)) {
                    $sql_data = [];
                    $sql_data['parent_item_id'] = $parent_item_id;
                    $sql_data['parent_id'] = 0;
                    db_perform('app_entity_' . $entities_id, $sql_data, "update", "id='" . $item_id . "'");

                    //reset parent id
                    db_query(
                        "update app_entity_{$entities_id} set parent_id={$item_info['parent_id']} where parent_id={$item_id}"
                    );

                    if ($item_info['parent_id'] > 0) {
                        //tree table recalculated count/sum
                        fieldtype_nested_calculations::update_items_fields(
                            $entities_id,
                            $item_info['parent_id'],
                            $item_info['parent_id']
                        );
                    }

                    //track changes
                    $log = new track_changes($entities_id, $item_id);
                    $log->log_move($parent_item_id);
                }
            }

            echo '
          <div class="alert alert-success">' . TEXT_MOVING_COMPLETED . '</div> 
          <script>
            location.href="' . $go_to_url . '";
          </script>         
        ';
        }


        exit();
        break;
}