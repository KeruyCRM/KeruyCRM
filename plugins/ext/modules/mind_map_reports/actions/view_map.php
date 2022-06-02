<?php

//check if report exist
$reports_query = db_query("select * from app_ext_mind_map where id='" . db_input(_get::int('id')) . "'");
if (!$reports = db_fetch_array($reports_query)) {
    exit();
}

//check access
if (!mind_map_reports::has_access($reports['users_groups'])) {
    exit();
}

$app_path = (isset($_GET['path']) ? '&path=' . $_GET['path'] : $reports['entities_id']);

//get access schema for current entity
$current_access_schema = users::get_entities_access_schema($reports['entities_id'], $app_user['group_id']);

$app_layout = 'mind_map_layout.php';

$mind_map = new  mind_map_reports($reports, $app_path);

switch ($app_module_action) {
    case 'save':

        //echo '<pre>';
        //print_r($_POST);

        if (isset($_POST['root'])) {
            $mind_map->save($_POST['root']);
        }

        exit();
        break;
    case 'prepare_new_item':

        if (isset($_POST['mm_id'])) {
            $mm_query = db_query(
                "select id,mm_items_id from app_mind_map where mm_id='" . $_POST['mm_id'] . "' and entities_id='" . $mind_map->entities_id . "' and reports_id='" . $mind_map->reports_id . "' and parent_entity_item_id = '" . $mind_map->parent_entity_item_id . "'"
            );
            if (!$mm = db_fetch_array($mm_query)) {
                $sql_data = [
                    'mm_id' => $_POST['mm_id'],
                    'mm_parent_id' => $_POST['parent_id'],
                    'mm_text' => $_POST['text'],
                ];

                $sql_data['entities_id'] = $mind_map->entities_id;
                $sql_data['reports_id'] = $mind_map->reports_id;
                $sql_data['parent_entity_item_id'] = $mind_map->parent_entity_item_id;

                db_perform('app_mind_map', $sql_data);
                $mind_map_id = db_insert_id();

                $item_id = $mind_map->save_item($mind_map_id, $_POST['text']);
            } else {
                $item_id = $mm['mm_items_id'];
            }

            $item_info = db_find('app_entity_' . $reports['entities_id'], $item_id);

            $response = [
                'item_id' => $item_id,
                'color' => $mind_map->set_color($item_info),
                'icon' => $mind_map->set_icon($item_info),
                'popup' => $mind_map->get_popup($item_info),
            ];

            echo app_json_encode($response);
        }

        exit();
        break;
}

