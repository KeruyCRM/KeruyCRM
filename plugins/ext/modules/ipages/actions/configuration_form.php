<?php

//check access
if (!in_array($app_user['id'], explode(',', CFG_IPAGES_ACCESS_TO_USERS)) and !in_array(
        $app_user['group_id'],
        explode(',', CFG_IPAGES_ACCESS_TO_USERS_GROUP)
    ) and $app_user['group_id'] > 0) {
    exit();
}

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_ipages', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_ipages');

    if (isset($_GET['parent_id'])) {
        $obj['parent_id'] = $_GET['parent_id'];
    }
}