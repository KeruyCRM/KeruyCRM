<?php

$field_query = db_query(
    "select id, name, configuration from app_fields where id='" . _GET('field_id') . "' and type='fieldtype_video'"
);
if (!$field = db_fetch_array($field_query)) {
    exit();
}

$cfg = new fields_types_cfg($field['configuration']);

$fields_access_schema = users::get_fields_access_schema($current_entity_id, $app_user['group_id']);

//check field access
if (isset($fields_access_schema[$field['id']])) {
    if ($fields_access_schema[$field['id']] == 'hide') {
        exit();
    }
}

$item_info = db_find('app_entity_' . $current_entity_id, $current_item_id);

if (!isset($item_info['field_' . $field['id']])) {
    exit();
}

echo ajax_modal_template_header(
    items::get_heading_field($current_entity_id, $current_item_id, $item_info) . ': ' . $field['name']
)
?>

    <div class="modal-body ajax-modal-width-790">
        <center><?php
            echo fieldtype_video::render_video($item_info['field_' . $field['id']], $cfg) ?></center>
    </div>

<?php
echo ajax_modal_template_footer('hide-save-button') ?>