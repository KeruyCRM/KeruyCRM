<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_forms_fields_rules', $_GET['id']);
} else {
    $obj = db_show_columns('app_forms_fields_rules');
    $obj['is_active'] = 1;

    $check_query = db_query(
        "select max(sort_order) as max_sort_order from app_forms_fields_rules where entities_id='" . _GET(
            'entities_id'
        ) . "'"
    );
    $check = db_fetch($check_query);
    $obj['sort_order'] = $check->max_sort_order;
}