<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_public_forms', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_public_forms');
}