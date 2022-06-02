<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_resource_timeline_entities', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_resource_timeline_entities');
}