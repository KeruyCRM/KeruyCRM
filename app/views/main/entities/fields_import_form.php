<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_IMPORT_FIELDS) ?>

<?= \Helpers\Html::form_tag(
    'import_form',
    \Helpers\Urls::url_for('main/entities/fields/import', 'entities_id=' . \K::$fw->GET['entities_id']),
    ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']
) ?>
<div class="modal-body">
    <div id="modal-body-content">
        <p><?= \K::$fw->TEXT_IMPORT_FIELDS_INFO ?></p>
        <div class="form-group">
            <label class="col-md-4 control-label" for="type"><?= \K::$fw->TEXT_FILE ?></label>
            <div class="col-md-8">
                <?= \Helpers\Html::input_file_tag(
                    'filename',
                    [
                        'class' => 'form-control required',
                        'accept' => \Tools\FieldsTypes\Fieldtype_attachments::get_accept_types_by_extensions('xml')
                    ]
                ) ?>
                <span class="help-block">*.xml</span>
            </div>
        </div>
    </div>
</div>
<?= \Helpers\App::ajax_modal_template_footer(\K::$fw->TEXT_CONTINUE) ?>

</form>

<script>
    $(function () {
        $('#import_form').validate();
    });
</script>