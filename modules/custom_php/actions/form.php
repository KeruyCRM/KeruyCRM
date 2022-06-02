<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_custom_php', $_GET['id']);
} else {
    $obj = db_show_columns('app_custom_php');
    $obj['is_active'] = 1;
    $obj['is_folder'] = $_GET['is_folder'] ?? 0;
    $obj['parent_id'] = $_GET['parent_id'] ?? 0;
}
