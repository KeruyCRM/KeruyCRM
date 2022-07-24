<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_BUTTON_DB_RESTORE_FROM_FILE) ?>

<?= \Helpers\Html::form_tag(
    'restore_file_form',
    \Helpers\Urls::url_for('main/tools/db_restore_process/restore_from_file'),
    ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']
) ?>
<div class="modal-body">
    <div class="form-body">

        <div class="form-group">
            <label class="col-md-3 control-label" for="sort_order"><?= \K::$fw->TEXT_FILE ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_file_tag('filename', ['class' => 'form-control']) ?>
                <?= \Helpers\App::tooltip_text(
                    '(*.sql | *.zip) ' . sprintf(\K::$fw->TEXT_MAX_FILE_SIZE, \K::$fw->CFG_SERVER_UPLOAD_MAX_FILESIZE)
                ) ?>
            </div>
        </div>

    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer(\K::$fw->TEXT_BUTTON_RESTORE) ?>

</form>

<script>
    $(function () {
        $('#restore_file_form').validate({
            rules: {
                filename: {
                    required: true,
                    extension: "zip|sql"
                }
            }
        });
    });
</script>