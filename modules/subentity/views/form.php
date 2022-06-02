<?php

//prepare public form
if (in_array($app_items_form_name, ['public_form', 'registration_form'])) {
    $app_user = [];
    $app_user['id'] = 0;
    $app_user['group_id'] = 0;
    $app_user['name'] = CFG_EMAIL_NAME_FROM;
    $app_user['email'] = CFG_EMAIL_ADDRESS_FROM;
    $app_user['language'] = CFG_APP_LANGUAGE;

    echo '
        <style>
        #sub-items-form .modal-header .close,
        #sub-items-form .modal-footer .btn-close{
            display:none;
        }               
        </style>        
        ';
}

echo '
    <style>
    #sub-items-form .form-group-fieldtype_subentity_form{
        display:none;
    }
    </style>
    ';

//get data
$subentity_form_params = explode('_', str_replace('subentity_form_', '', $app_redirect_to));

$current_entity_id = _GET('current_entity_id');
$entity_cfg = new entities_cfg($current_entity_id);
$parent_entity_item_id = 0;
$current_path = $current_entity_id;

$obj = db_show_columns('app_entity_' . $current_entity_id);

//prepare exist data
if (isset($subentity_form_params[2])) {
    //declarate item id to set $is_new_item = false
    $_GET['id'] = 0;

    $row = $subentity_form_params[2];
    if (isset($app_subentity_form_items[$fields_id][$row])) {
        foreach ($app_subentity_form_items[$fields_id][$row] as $field_id => $field_value) {
            $obj['field_' . $field_id] = subentity_form::prepare_item_value_by_field_type(
                $app_fields_cache[$current_entity_id][$field_id],
                $field_value
            );
        }

        //set current item id
        if (is_numeric($row)) {
            $current_item_id = $row;
            $path_info = items::get_path_info($current_entity_id, $current_item_id);
            //print_rr($path_info);
            $_GET['path'] = $current_path = $app_path = $path_info['full_path'];
            $_GET['id'] = $row;
        }
    }
}

//include default items form
require('modules/items/views/form.php');
