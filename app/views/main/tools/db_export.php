<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_DATABASE_EXPORT_APPLICATION) ?>

    <div class="modal-body">
        <p><?= \K::$fw->TEXT_DATABASE_EXPORT_EXPLANATION ?></p>

        <p><?= \Helpers\Html::button_tag(
                \K::$fw->TEXT_BUTTON_EXPORT_DATABASE,
                \Helpers\Urls::url_for('main/tools/db_backup/export_template'),
                false
            ) ?></p>

        <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_DATABASE_EXPORT_TOOLTIP) ?>
    </div>

<?= \Helpers\App::ajax_modal_template_footer('hide-save-button') ?>