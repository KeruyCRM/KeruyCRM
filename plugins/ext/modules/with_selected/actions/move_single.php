<?php

if (!isset($_GET['path'])) {
    redirect_to('dashboard/page_not_found');
}

$path_info = items::parse_path($_GET['path']);

$current_entity_id = $path_info['entity_id'];
$current_item_id = $path_info['item_id'];

if ($current_item_id == 0) {
    redirect_to('dashboard/page_not_found');
}

$path_info = items::get_path_info($current_entity_id, $current_item_id);

if ($_GET['path'] != $path_info['full_path']) {
    redirect_to('items/info', 'path=' . $path_info['full_path']);
}

//get access schema for current entity
$current_access_schema = users::get_entities_access_schema($current_entity_id, $app_user['group_id']);

//checking access
if (!users::has_access('move')) {
    redirect_to('dashboard/access_forbidden');
}

switch ($app_module_action) {
    case 'move_single':
        $entities_id = $current_entity_id;
        $entity_info = db_find('app_entities', $entities_id);

        //set default parent id
        $parent_item_id = 0;

        //get parent id for sub-entities                                              
        if ($entity_info['parent_id'] > 0) {
            if (strlen($_POST['move_to']) > 0) {
                $parent_item_id = (int)$_POST['move_to'];
            }

            //parent id is requried for sub-entities
            if ($parent_item_id == 0) {
                echo '<div class="alert alert-danger">' . TEXT_COPY_ERROR_PARENT_RECORD . '</div>';
                exit();
            }
        }

        //move records             
        if ($current_item_id > 0 and $parent_item_id > 0) {
            $item_info_query = db_query(
                "select * from app_entity_" . $entities_id . " where id='" . $current_item_id . "'"
            );
            if ($item_info = db_fetch_array($item_info_query)) {
                $sql_data = [];
                $sql_data['parent_item_id'] = $parent_item_id;
                $sql_data['parent_id'] = 0;
                db_perform('app_entity_' . $entities_id, $sql_data, "update", "id='" . $current_item_id . "'");

                //track changes
                $log = new track_changes($current_entity_id, $current_item_id);
                $log->log_move($parent_item_id);

                $path_info = items::get_path_info($entities_id, $current_item_id);

                //move nested items
                $nested_list = tree_table::get_nested_list($entities_id, $current_item_id);
                foreach ($nested_list as $item) {
                    $sql_data = [];
                    $sql_data['parent_item_id'] = $parent_item_id;
                    db_perform('app_entity_' . $entities_id, $sql_data, "update", "id='" . $item['id'] . "'");
                }

                if ($item_info['parent_id'] > 0) {
                    //tree table recalculated count/sum
                    fieldtype_nested_calculations::update_items_fields(
                        $entities_id,
                        $item_info['parent_id'],
                        $item_info['parent_id']
                    );
                }

                echo '
                    <div class="alert alert-success">' . TEXT_MOVING_COMPLETED . '</div> 
                    <script>
                      location.href="' . url_for('items/info', 'path=' . $path_info['full_path']) . '";
                    </script>         
                  ';
            }
        }


        exit();
        break;
}