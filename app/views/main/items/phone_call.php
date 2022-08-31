<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php

$phone = '';
$field_id = _get::int('field_id');

$item_info = db_find('app_entity_' . $current_entity_id, _get::int('item_id'));

if (isset($item_info['field_' . $field_id])) {
    $phone = db_prepare_input($item_info['field_' . $field_id]);
}

?>

<?php
echo ajax_modal_template_header(TEXT_EXT_CALL_TO_NUMBER . ' ' . $phone) ?>

<div class="modal-body">
    <?php

    $module_info_query = db_query(
        "select * from app_ext_modules where id='" . _GET('module_id') . "' and type='telephony' and is_active=1"
    );
    if ($module_info = db_fetch_array($module_info_query)) {
        modules::include_module($module_info, 'telephony');

        $module = new $module_info['module'];
        $module->call_to_number($module_info['id'], $phone);
    }

    ?>
</div>

<?php
echo ajax_modal_template_footer('hide-save-button') ?>

 