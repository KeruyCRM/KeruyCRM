<?php

//check access
if ($app_user['group_id'] > 0) {
    exit();
}

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_kanban', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_kanban');

    $obj['width'] = 300;

    $obj['entities_id'] = $kanban_entity_filter;
}