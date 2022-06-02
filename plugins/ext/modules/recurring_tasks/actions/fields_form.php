<?php

require(component_path('ext/recurring_tasks/check_access'));

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_recurring_tasks_fields', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_recurring_tasks_fields');
}