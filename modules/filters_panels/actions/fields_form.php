<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_filters_panels_fields', $_GET['id']);
} else {
    $obj = db_show_columns('app_filters_panels_fields');
}