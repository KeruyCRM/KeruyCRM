<?php
echo ajax_modal_template_header(TEXT_BUTTON_DB_RESTORE_FROM_FILE) ?>

<?php
echo form_tag(
    'restore_file_form',
    url_for('tools/db_restore_process', 'action=restore_from_file'),
    ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']
) ?>
<div class="modal-body">
    <div class="form-body">

        <div class="form-group">
            <label class="col-md-3 control-label" for="sort_order"><?php
                echo TEXT_FILE ?></label>
            <div class="col-md-9">
                <?php
                echo input_file_tag('filename', ['class' => 'form-control']) ?>
                <?php
                echo tooltip_text('(*.sql | *.zip) ' . sprintf(TEXT_MAX_FILE_SIZE, CFG_SERVER_UPLOAD_MAX_FILESIZE)) ?>
            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer(TEXT_BUTTON_RESTORE) ?>

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