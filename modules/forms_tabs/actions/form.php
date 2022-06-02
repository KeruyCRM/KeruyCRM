<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_forms_tabs', $_GET['id']);
} else {
    $obj = db_show_columns('app_forms_tabs');
    $obj['sort_order'] = forms_tabs::get_last_sort_number(_GET('entities_id')) + 1;
}