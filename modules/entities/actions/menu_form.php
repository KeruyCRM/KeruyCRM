<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_entities_menu', $_GET['id']);
} else {
    $obj = db_show_columns('app_entities_menu');

    $obj['parent_id'] = (isset($_GET['parent_id']) ? _get::int('parent_id') : 0);
}