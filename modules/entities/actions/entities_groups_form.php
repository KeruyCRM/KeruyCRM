<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_entities_groups', $_GET['id']);
} else {
    $obj = db_show_columns('app_entities_groups');
    $obj['sort_order'] = 0;
}