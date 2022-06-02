<?php

$reports_query = db_query("select * from app_ext_map_reports where is_public_access=1 and id='" . _GET('id') . "'");
if (!$reports = db_fetch_array($reports_query)) {
    exit();
}

$app_user = [];
$app_user['id'] = 0;
$app_user['group_id'] = 0;

$app_title = $reports['name'];
$app_layout = 'public_map_layout.php';
