<?php

if (!IS_AJAX or ($_POST['form_session_token'] != $app_session_token)) {
    exit();
}

//check if field exist
$field_query = db_query(
    "select * from app_fields where id='" . _get::int('field_id') . "' and type='fieldtype_ajax_request'"
);
if (!$field = db_fetch_array($field_query)) {
    exit();
}


$cfg = new fields_types_cfg($field['configuration']);

$php_code = $cfg->get('php_code');

$fields_values = [];

//get fields values for current form
$fields_cache = $app_fields_cache[$field['entities_id']];
if ($app_entities_cache[$field['entities_id']]['parent_id'] > 0) {
    $fields_cache = $fields_cache + $app_fields_cache[$app_entities_cache[$field['entities_id']]['parent_id']];
}

foreach ($fields_cache as $fiels_id => $fields_data) {
    if (!in_array($fields_data['type'], fields_types::get_reserved_types())) {
        $fields_values[$fiels_id] = 0;
    }
}

//print_rr($fields_cache);

if (isset($_POST['fields']) and is_array($_POST['fields'])) {
    foreach ($_POST['fields'] as $fiels_id => $fiels_value) {
        if (!in_array($fields_cache[$fiels_id]['type'], fields_types::get_reserved_types())) {
            //prepare date value
            if (in_array($fields_cache[$fiels_id]['type'], ['fieldtype_input_date', 'fieldtype_input_datetime'])) {
                $fields_values[$fiels_id] = (int)get_date_timestamp($fiels_value);
            } else {
                $fields_values[$fiels_id] = $fiels_value;
            }
        }
    }
}

//valuse for parent item
if ($app_entities_cache[$field['entities_id']]['parent_id'] > 0 and isset($_POST['parent_item_id']) and $_POST['parent_item_id'] > 0) {
    $parent_entity_id = $app_entities_cache[$field['entities_id']]['parent_id'];
    $parent_item_id = _POST('parent_item_id');

    $parent_item_query = db_query("select * from app_entity_{$parent_entity_id} where id={$parent_item_id}");
    if ($parent_item = db_fetch_array($parent_item_query)) {
        foreach ($parent_item as $fields_id => $fields_value) {
            if (strstr($fields_id, 'field_')) {
                $fields_values[str_replace('field_', '', $fields_id)] = $fields_value;
            }
        }
    }
}

//print_rr($_POST);
//print_rr($fields_values);

//prepare values to replace
foreach ($fields_values as $fiels_id => $fields_value) {
    if (is_array($fields_value)) {
        $fields_value = "'" . implode(',', $fields_value) . "'";
    } elseif (!strlen($fields_value)) {
        $fields_value = 0;
    } elseif (is_string($fields_value)) {
        $fields_value = "'" . addslashes($fields_value) . "'";
    }

    $php_code = str_replace('[' . $fiels_id . ']', $fields_value, $php_code);
}


if ($cfg->get('debug_mode') == 1) {
    print_rr($fields_values);
    print_rr(htmlspecialchars($php_code));
}

try {
    eval($php_code);
} catch (Error $e) {
    echo alert_error(TEXT_ERROR . ' ' . $e->getMessage() . ' on line ' . $e->getLine());
}

if (isset($form_field_value)) {
    echo input_hidden_tag('fields[' . $field['id'] . ']', $form_field_value);
}