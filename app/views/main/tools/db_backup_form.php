<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_HEADING_DB_BACKUP) ?>

<?= \Helpers\Html::form_tag('backup_form', \Helpers\Urls::url_for('main/tools/db_backup/backup'), ['class' => 'form-horizontal']) ?>
<div class="modal-body">
    <div class="form-body">

        <div class="form-group">
            <label class="col-md-3 control-label" for="sort_order"><?= \K::$fw->TEXT_COMMENT ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::textarea_tag('description', '', ['class' => 'form-control']) ?>
                <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_BACKUP_DESCRIPTION_TIP) ?>
            </div>
        </div>

    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer(\K::$fw->TEXT_BUTTON_CREATE_BACKUP) ?>

</form>

<script>
    $(function () {
        $('#backup_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });
    });
</script>