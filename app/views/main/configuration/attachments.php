<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<h3 class="page-title"><?= \K::$fw->HEADING_ATTACHMENTS_CONFIGURATION ?></h3>

<?= \Helpers\Html::form_tag('cfg', \Helpers\Urls::url_for('main/configuration/save'), ['class' => 'form-horizontal']) ?>
<?= \Helpers\Html::input_hidden_tag('redirect_to', 'main/configuration/attachments') ?>
<div class="form-body">

    <div class="form-group">
        <label class="col-md-3 control-label"><?= \K::$fw->TEXT_MAX_UPLOAD_FILE_SIZE ?></label>
        <div class="col-md-9">
            <p class="form-control-static"><?= \K::$fw->CFG_SERVER_UPLOAD_MAX_FILESIZE; ?> MB</p>
            <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_MAX_UPLOAD_FILE_SIZE_TIP) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES"><?= \K::$fw->TEXT_ENCRYPT_FILE_NAME ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[ENCRYPT_FILE_NAME]',
                \K::$fw->default_selector,
                \K::$fw->CFG_ENCRYPT_FILE_NAME,
                ['class' => 'form-control input-small']
            ); ?>
            <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_ENCRYPT_FILE_NAME_TIP) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"
               for="CFG_PUBLIC_ATTACHMENTS"><?= \K::$fw->TEXT_ALLOW_PUBLIC_ACCESS ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[PUBLIC_ATTACHMENTS][]',
                \K::$fw->choices,
                \K::$fw->CFG_PUBLIC_ATTACHMENTS,
                ['class' => 'form-control input-xlarge chosen-select', 'multiple' => true]
            ); ?>
            <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_PUBLIC_ATTACHMENTS_TIP) ?>
        </div>
    </div>

    <h3 class="form-section"></h3>

    <div class="form-group">
        <label class="col-md-3 control-label"
               for="CFG_CREATE_ATTACHMENTS_PREVIEW"><?= \K::$fw->TEXT_CREATE_ATTACHMENTS_PREVIEW ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[CREATE_ATTACHMENTS_PREVIEW]',
                \K::$fw->default_selector,
                \K::$fw->CFG_CREATE_ATTACHMENTS_PREVIEW,
                ['class' => 'form-control input-small']
            ); ?>
            <?= \Helpers\App::tooltip_text(
                \K::$fw->TEXT_CREATE_ATTACHMENTS_PREVIEW_TIP . '<br>' . \K::$fw->TEXT_FOLDER . ': ' . \K::$fw->DIR_FS_ATTACHMENTS_PREVIEW
            ) ?>
        </div>
    </div>

    <h3 class="form-section"></h3>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES"><?= \K::$fw->TEXT_RESIZE_IMAGES ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[RESIZE_IMAGES]',
                \K::$fw->default_selector,
                \K::$fw->CFG_RESIZE_IMAGES,
                ['class' => 'form-control input-small']
            ); ?>
            <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_RESIZE_IMAGES_TIP) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_MAX_IMAGE_WIDTH"><?= \K::$fw->TEXT_MAX_IMAGE_WIDTH ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[MAX_IMAGE_WIDTH]',
                \K::$fw->CFG_MAX_IMAGE_WIDTH,
                ['class' => 'form-control input-small number', 'type' => 'number']
            ); ?>
            <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_ENTER_VALUES_IN_PIXELS_OR_LEAVE_BLANK) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_MAX_IMAGE_HEIGHT"><?= \K::$fw->TEXT_MAX_IMAGE_HEIGHT ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[MAX_IMAGE_HEIGHT]',
                \K::$fw->CFG_MAX_IMAGE_HEIGHT,
                ['class' => 'form-control input-small number', 'type' => 'number']
            ); ?>
            <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_ENTER_VALUES_IN_PIXELS_OR_LEAVE_BLANK) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES_TYPES"><?= \Helpers\App::tooltip_icon(
                \K::$fw->TEXT_RESIZE_IMAGES_TYPES_TIP
            ) . \K::$fw->TEXT_IMAGES_TYPES ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_checkboxes_tag(
                'CFG[RESIZE_IMAGES_TYPES]',
                ['1' => 'gif', '2' => 'jpeg', '3' => 'png'],
                \K::$fw->CFG_RESIZE_IMAGES_TYPES
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_SKIP_IMAGE_RESIZE"><?= \K::$fw->TEXT_SKIP_IMAGE_RESIZE ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[SKIP_IMAGE_RESIZE]',
                \K::$fw->CFG_SKIP_IMAGE_RESIZE,
                ['class' => 'form-control input-small number', 'type' => 'number']
            ); ?>
            <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_SKIP_IMAGE_RESIZE_TIP) ?>
        </div>
    </div>

    <?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>

</div>
</form>