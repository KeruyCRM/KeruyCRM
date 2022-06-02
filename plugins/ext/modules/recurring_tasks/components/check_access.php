<?php

if (!isset($_GET['path'])) {
    redirect_to('dashboard/');
}

$path_info = items::parse_path($_GET['path']);

$current_path = $_GET['path'];
$current_entity_id = $path_info['entity_id'];
$current_item_id = $path_info['item_id'];
$current_path_array = $path_info['path_array'];

$access_schema = users::get_entities_access_schema($current_entity_id, $app_user['group_id']);

if (!users::has_access('repeat', $access_schema)) {
    redirect_to('dashboard/access_forbidden');
}

//checking view access
if (!users::has_access('view', $access_schema) and !users::has_access('view_assigned', $access_schema)) {
    redirect_to('dashboard/access_forbidden');
}

//check assigned access
if (users::has_access('view_assigned', $access_schema) and $app_user['group_id'] > 0 and $current_item_id > 0) {
    if (!users::has_access_to_assigned_item($current_entity_id, $current_item_id)) {
        redirect_to('dashboard/access_forbidden');
    }
}