<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_global_vars', $_GET['id']);
} else {
    $obj = db_show_columns('app_global_vars');
    $obj['is_folder'] = $_GET['is_folder'] ?? 0;
    $obj['parent_id'] = $_GET['parent_id'] ?? 0;
}
