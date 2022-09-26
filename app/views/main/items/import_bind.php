<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_HEADING_BIND_FIELD) ?>

<?= \Helpers\Html::form_tag(
    'bind_field_form',
    \Helpers\Urls::url_for(
        'main/tools/import_data/bind_filed',
        'multilevel_import=' . \K::$fw->GET['multilevel_import']
    ),
    ['onSubmit' => 'return bind_field(' . \K::$fw->GET['col'] . ')']
) . \Helpers\Html::input_hidden_tag('col', \K::$fw->GET['col']); ?>

<div class="modal-body">
    <?php

    $multilevel_import = \K::$fw->GET['multilevel_import'];

    $entities_list = [];
    $entities_list[\K::$fw->current_entity_id] = \Models\Main\Entities::get_name_by_id(\K::$fw->current_entity_id);

    if ($multilevel_import > 0) {
        foreach (\Models\Main\Entities::get_tree(\K::$fw->current_entity_id) as $entity) {
            $entities_list[$entity['id']] = $entity['name'];

            if ($entity['id'] == $multilevel_import) {
                break;
            }
        }
    }

    $choices = [];
    $choices[] = \K::$fw->TEXT_NONE;

    foreach ($entities_list as $entity_id => $entity_name) {
        $fields_query = \K::model()->db_query_exec(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . \Models\Main\Fields_types::skip_import_field_types(
            ) . ") and f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
            $entity_id
        );//Skip cache

        $fields_access_schema = \Models\Main\Users\Users::get_fields_access_schema(
            \K::$fw->current_entity_id,
            \K::$fw->app_user['group_id']
        );

        //while ($v = db_fetch_array($fields_query)) {
        foreach ($fields_query as $v) {
            if (in_array($v['id'], \K::$fw->import_fields)) {
                continue;
            }

            if (isset($fields_access_schema[$v['id']])) {
                continue;
            }

            $choices[$entity_name][$v['id']] = ($v['is_heading'] == 1 ? '* ' : '') . \Models\Main\Fields_types::get_option(
                    $v['type'],
                    'name',
                    $v['name']
                ) . ($v['is_heading'] == 1 ? ' (' . \K::$fw->TEXT_HEADING . ')' : '');
        }
    }

    echo \Helpers\Html::select_tag('filed_id', $choices, '', ['class' => 'form-control chosen-select']);
    ?>
</div>

<div class="modal-footer">
    <button type="submit" class="btn btn-primary"><?= \K::$fw->TEXT_BUTTON_BIND ?></button>
    <button type="button" class="btn btn-default" data-dismiss="modal"><?= \K::$fw->TEXT_BUTTON_CLOSE ?></button>
</div>

<script>
    jQuery(document).ready(function () {
        appHandleUniform()
    });
</script>

</form>