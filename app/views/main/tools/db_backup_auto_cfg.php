<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?><?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_SETTINGS) ?>

<?= \Helpers\Html::form_tag(
    'backup',
    \Helpers\Urls::url_for('main/configuration/save'),
    ['class' => 'form-horizontal']
) ?>
<?= \Helpers\Html::input_hidden_tag('redirect_to', 'main/tools/db_backup_auto') ?>
<div class="modal-body ajax-modal-width-790">
    <?= \K::$fw->TEXT_AUTOMATIC_BACKUP_INFO ?>

    <div class="form-group">
        <label class="col-md-3 control-label"><?= \K::$fw->TEXT_CRON_BACKUP ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'cron_backup',
                \K::$fw->DIR_FS_CATALOG . 'cron/backup',
                ['class' => 'form-control ', 'readonly' => 'readonly']
            ); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label"><?= \K::$fw->TEXT_BACKUP_FOLDER ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'cron_backup_folder',
                \K::$fw->DIR_FS_BACKUPS_AUTO,
                ['class' => 'form-control ', 'readonly' => 'readonly']
            ); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label"><?= \K::$fw->TEXT_KEEP_FIELDS ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[AUTOBACKUP_KEEP_FILES_DAYS]',
                \K::$fw->CFG_AUTOBACKUP_KEEP_FILES_DAYS,
                ['class' => 'form-control input-small', 'type' => 'number']
            ); ?>
            <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_ENTER_NUMBER_OF_DAYS) ?>
        </div>
    </div>
</div>
<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>  

