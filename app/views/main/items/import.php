<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_IMPORT) ?>

<?php
$import_fields = []; ?>

<?= \Helpers\Html::form_tag(
    'import_data',
    \Helpers\Urls::url_for('main/items/import_preview', 'path=' . \K::$fw->app_path),
    ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']
) ?>

<div class="modal-body">
    <div class="form-body">
        <p><?= \K::$fw->TEXT_IMPORT_DATA_INFO ?></p>

        <div class="alert alert-info"><?= \K::$fw->TEXT_IMPORT_DATA_TOOLTIP ?></div>

        <?php
        $choices = [
            'import' => \K::$fw->TEXT_ACTION_IMPORT_DATA,
            'update' => \K::$fw->TEXT_ACTION_UPDATE_DATA,
            'update_import' => \K::$fw->TEXT_ACTION_UPDATE_AND_IMPORT_DATA,
        ];

        ?>
        <div class="form-group">
            <label class="col-md-3 control-label" for="entities_id"><?= \K::$fw->TEXT_ACTION ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'import_action',
                    $choices,
                    '',
                    ['class' => 'form-control input-large required']
                ) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?= \K::$fw->TEXT_FILENAME ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_file_tag(
                    'filename',
                    [
                        'class' => 'form-control required input-xlarge',
                        'accept' => \Tools\FieldsTypes\Fieldtype_attachments::get_accept_types_by_extensions('xls,xlsx')
                    ]
                ) ?>
                <span class="help-block">*.xls, *.xlsx</span>
            </div>
        </div>

        <?php
        if (\Models\Main\Entities::has_subentities(\K::$fw->current_entity_id)) {
            $choices = [];
            $choices[] = '';
            foreach (\Models\Main\Entities::get_tree(\K::$fw->current_entity_id) as $entity) {
                $choices[$entity['id']] = str_repeat(' - ', ($entity['level'] + 1)) . $entity['name'];
            }
            ?>

            <div class="form-group">
                <label class="col-md-3 control-label" for="entities_id"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_MULTI_LEVEL_IMPORT_INFO
                    ) . \K::$fw->TEXT_MULTI_LEVEL_IMPORT ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'multilevel_import',
                        $choices,
                        '',
                        ['class' => 'form-control input-xlarge']
                    ) ?>
                </div>
            </div>

            <?php
        }
        if (\Helpers\App::is_ext_installed()) {
            $choices = import_templates::get_choices(\K::$fw->current_entity_id);

            if (count($choices) > 1) {
                ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="entities_id"><?= \K::$fw->TEXT_EXT_TEMPLATE ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'import_template',
                            $choices,
                            '',
                            ['class' => 'form-control input-large']
                        ) ?>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer(\K::$fw->TEXT_BUTTON_CONTINUE) ?>

</form>

<script>
    $(function () {
        $('#import_data').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });
    });
</script>