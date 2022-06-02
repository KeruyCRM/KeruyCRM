<?php
echo ajax_modal_template_header(TEXT_PDF_EXPORT_FONTS) ?>

<?php
echo form_tag(
    'font_form',
    url_for('configuration/pdf', 'action=save'),
    ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'target' => '_new']
) ?>
<div class="modal-body">
    <div class="form-body">

        <?php
        $has_errors = false;

        if (!is_writable(CFG_PATH_TO_DOMPDF_FONTS)) {
            echo alert_error(sprintf(TEXT_ERRRO_FOLDER_NOT_WRITABLE, CFG_PATH_TO_DOMPDF_FONTS));
            $has_errors = true;
        }

        if (!is_writable(CFG_PATH_TO_DOMPDF_FONTS . '/dompdf_font_family_cache.php')) {
            echo alert_error(
                sprintf(TEXT_ERRROR_FILE_NOT_WRITABLE, CFG_PATH_TO_DOMPDF_FONTS . '/dompdf_font_family_cache.php')
            );
            $has_errors = true;
        }

        if (!$has_errors)
        {
        ?>

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><span class="required-label">*</span><?php
                echo TEXT_NAME ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('name', '', ['class' => 'form-control input-large required autofocus']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="file_normal"><span class="required-label">*</span><?php
                echo 'Normal' ?></label>
            <div class="col-md-9">
                <div class="input-group">
                    <?php
                    echo input_file_tag('file_normal', ['class' => 'form-control required']) ?>
                    <span class="input-group-addon">.ttf</span>
                </div>
                <label id="file_normal-error" class="error" for="file_normal"></label>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="file_bold"><?php
                echo 'Bold' ?></label>
            <div class="col-md-9">
                <div class="input-group">
                    <?php
                    echo input_file_tag('file_bold', ['class' => 'form-control']) ?>
                    <span class="input-group-addon">.ttf</span>
                </div>
                <label id="file_bold-error" class="error" for="file_bold"></label>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="file_italic"><?php
                echo 'Italic' ?></label>
            <div class="col-md-9">
                <div class="input-group">
                    <?php
                    echo input_file_tag('file_italic', ['class' => 'form-control']) ?>
                    <span class="input-group-addon">.ttf</span>
                </div>
                <label id="file_italic-error" class="error" for="file_italic"></label>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="file_bold_italic"><?php
                echo 'Bold Italic' ?></label>
            <div class="col-md-9">
                <div class="input-group">
                    <?php
                    echo input_file_tag('file_bold_italic', ['class' => 'form-control']) ?>
                    <span class="input-group-addon">.ttf</span>
                </div>
                <label id="file_bold_italic-error" class="error" for="file_bold_italic"></label>
            </div>
        </div>


    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

<?php
} ?>

</form>

<script>
    $(function () {
        $('#font_form').validate({
            rules: {
                file_normal: {
                    required: true,
                    extension: "ttf"
                },
                file_bold: {
                    extension: "ttf"
                },
                file_italic: {
                    extension: "ttf"
                },
                file_bold_italic: {
                    extension: "ttf"
                }
            }
        });
    });

</script>  