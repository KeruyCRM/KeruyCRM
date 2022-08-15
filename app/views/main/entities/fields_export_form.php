<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_FIELDS_EXPORT) ?>

<?= \Helpers\Html::form_tag(
    'form-copy-to',
    \Helpers\Urls::url_for('main/entities/fields/export', 'entities_id=' . \K::$fw->GET['entities_id']),
    ['class' => 'form-horizontal']
) ?>
<?= \Helpers\Html::input_hidden_tag('selected_fields') ?>
<div class="modal-body">
    <div id="modal-body-content">
        <p><?= \K::$fw->TEXT_FIELDS_EXPORT_INFO ?></p>
        <div class="form-group">
            <label class="col-md-4 control-label" for="type"><?= \K::$fw->TEXT_FILENAME ?></label>
            <div class="col-md-8">
                <?= \Helpers\Html::input_tag(
                    'filename',
                    \K::$fw->TEXT_FIELDS . '_' . \K::$fw->app_entities_cache[\K::$fw->GET['entities_id']]['name'],
                    ['class' => 'form-control input-large required']
                ) ?>
            </div>
        </div>
    </div>
</div>
<?= \Helpers\App::ajax_modal_template_footer(\K::$fw->TEXT_EXPORT) ?>

</form>

<script>
    $(function () {
        if ($('.fields_checkbox:checked').length == 0) {
            $('#modal-body-content').html('<?= \K::$fw->TEXT_PLEASE_SELECT_FIELDS ?>')
            $('.btn-primary-modal-action').hide()
        } else {
            selected_fields_list = $('.fields_checkbox:checked').serialize().replace(/fields%5B%5D=/g, '').replace(/&/g, ',');
            $('#selected_fields').val(selected_fields_list);
        }
    })
</script>