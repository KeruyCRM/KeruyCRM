<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_PDF_EXPORT_FONTS) ?>

<?= \Helpers\Html::form_tag(
    'font_form',
    \Helpers\Urls::url_for('main/configuration/pdf/save'),
    ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'target' => '_new']
) ?>
<div class="modal-body">
    <div class="form-body">

        <?php
        $has_errors = false;

        if (!is_writable(\K::$fw->CFG_PATH_TO_DOMPDF_FONTS)) {
            echo \Helpers\App::alert_error(
                sprintf(\K::$fw->TEXT_ERROR_FOLDER_NOT_WRITABLE, \K::$fw->CFG_PATH_TO_DOMPDF_FONTS)
            );
            $has_errors = true;
        }

        if (!is_writable(\K::$fw->CFG_PATH_TO_DOMPDF_FONTS . '/dompdf_font_family_cache.php')) {
            echo \Helpers\App::alert_error(
                sprintf(
                    \K::$fw->TEXT_ERROR_FILE_NOT_WRITABLE,
                    \K::$fw->CFG_PATH_TO_DOMPDF_FONTS . '/dompdf_font_family_cache.php'
                )
            );
            $has_errors = true;
        }

        if (!$has_errors)
        {
        ?>

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><span
                        class="required-label">*</span><?= \K::$fw->TEXT_NAME ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag('name', '', ['class' => 'form-control input-large required autofocus']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="file_normal"><span class="required-label">*</span><?php
                echo 'Normal' ?></label>
            <div class="col-md-9">
                <div class="input-group">
                    <?= \Helpers\Html::input_file_tag('file_normal', ['class' => 'form-control required']) ?>
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
                    <?= \Helpers\Html::input_file_tag('file_bold', ['class' => 'form-control']) ?>
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
                    <?= \Helpers\Html::input_file_tag('file_italic', ['class' => 'form-control']) ?>
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
                    <?= \Helpers\Html::input_file_tag('file_bold_italic', ['class' => 'form-control']) ?>
                    <span class="input-group-addon">.ttf</span>
                </div>
                <label id="file_bold_italic-error" class="error" for="file_bold_italic"></label>
            </div>
        </div>


    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

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