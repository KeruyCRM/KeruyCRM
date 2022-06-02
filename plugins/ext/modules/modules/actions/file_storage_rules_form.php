<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_file_storage_rules', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_file_storage_rules');
}