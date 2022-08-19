<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>

<h3 class="page-title"><?= \K::$fw->TEXT_FIELD_SETTINGS . ': ' . \K::$fw->fields_info['name'] ?></h3>

<?= \Helpers\Html::form_tag(
    'fields_form',
    \Helpers\Urls::url_for(
        'main/entities/fields_settings/save',
        'fields_id=' . \K::$fw->GET['fields_id'] . '&entities_id=' . \K::$fw->GET['entities_id']
    )
) ?>

<?php
//get field configuration by type
switch (\K::$fw->fields_info['type']) {
    case 'fieldtype_related_records':
        \K::$fw->exclude_cfg_keys = ['fields_in_listing', 'fields_in_popup'];

        //require(component_path('entities/fieldtype_related_records_settings'));
        \K::view()->render(\Helpers\Urls::components_path('main/entities/fieldtype_related_records_settings'));
        break;

    case 'fieldtype_entity':
        \K::$fw->exclude_cfg_keys = ['fields_in_popup'];

        //require(component_path('entities/fieldtype_entity_settings'));
        \K::view()->render(\Helpers\Urls::components_path('main/entities/fieldtype_entity_settings'));
        break;
}

//prepare other configuration if exist
foreach (\K::$fw->cfg as $k => $v) {
    if (!in_array($k, \K::$fw->exclude_cfg_keys)) {
        if (is_array($v)) {
            foreach ($v as $vv) {
                echo \Helpers\Html::input_hidden_tag('fields_configuration[' . $k . '][]', $vv);
            }
        } else {
            echo \Helpers\Html::input_hidden_tag('fields_configuration[' . $k . ']', $v);
        }
    }
}
?>

<br>
<?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>

</form>