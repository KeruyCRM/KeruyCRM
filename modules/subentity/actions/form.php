<?php

if (!IS_AJAX) {
    exit();
}

$entities_id = _GET('entities_id');
$fields_id = _GET('fields_id');
$rows_count = (isset($_POST['rows_count']) ? _POST('rows_count') : 0);
//check field
if (!isset($app_fields_cache[$entities_id][$fields_id]) or $app_fields_cache[$entities_id][$fields_id]['type'] != 'fieldtype_subentity_form') {
    exit();
}

$app_items_form_name = $_GET['form_name'] ?? 'items_form';

$subentity_form = new subentity_form($entities_id, 0, $fields_id);

switch ($app_module_action) {
    case 'add':
        echo $subentity_form->render_form($rows_count);
        exit();
        break;
    case 'add_item':
        if (!isset($app_subentity_form_items[$fields_id])) {
            $app_subentity_form_items[$fields_id] = [];
        }

        $subentity_form_params = explode('_', str_replace('subentity_form_', '', $app_redirect_to));

        if (isset($subentity_form_params[2])) {
            $rows_count = count($app_subentity_form_items[$fields_id]);
            $app_subentity_form_items[$fields_id][$subentity_form_params[2]] = $_POST['fields'];
        } else {
            $rows_count = count($app_subentity_form_items[$fields_id]) + 1;
            $app_subentity_form_items[$fields_id]['row' . $rows_count] = $_POST['fields'];
        }
        exit();
        break;
    case 'load_items':
        $response = $subentity_form->render_items_listing_preview();
        echo $response['html'];
        exit();
        break;
    case 'remove_item':
        $row = $_POST['row'];
        if (isset($app_subentity_form_items[$fields_id][$row])) {
            unset($app_subentity_form_items[$fields_id][$row]);
        }

        if (is_numeric($row) and $row > 0) {
            $app_subentity_form_items_deleted[$fields_id][] = $row;
        }

        exit();
        break;
}


