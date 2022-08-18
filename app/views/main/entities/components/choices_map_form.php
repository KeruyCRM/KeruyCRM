<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php

if ((int)\K::$fw->obj['parent_id'] == 0 and !isset(\K::$fw->GET['parent_id'])) {
    ?>
    <div class="form-group">
        <label class="col-md-4 control-label" for="sort_order"><?= \Helpers\App::tooltip_icon(
                \K::$fw->TEXT_IMAGE_MAP_FILENAME_INFO
            ) . \K::$fw->TEXT_IMAGE ?></label>
        <div class="col-md-8">
            <?= \Helpers\Html::input_file_tag(
                'filename',
                ['class' => 'form-control input-large ' . (!strlen(\K::$fw->obj['filename']) ? 'required' : '')]
            ) . \K::$fw->obj['filename'] ?>
            <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_IMAGE_MAP_FILENAME_DESCRIPTION) ?>
            <?= \Helpers\App::tooltip_text(
                \K::$fw->TEXT_MAX_UPLOAD_FILE_SIZE . ': ' . \K::$fw->CFG_SERVER_UPLOAD_MAX_FILESIZE . ' MB'
            ) ?>
        </div>
    </div>
    <?php
}
?>