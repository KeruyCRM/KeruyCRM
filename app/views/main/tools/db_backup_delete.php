<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_WARNING) ?>

<?= \Helpers\Html::form_tag('backup', \Helpers\Urls::url_for('main/tools/db_backup/delete', 'id=' . \K::$fw->GET['id'])) ?>
<div class="modal-body">
    <?php

    echo sprintf(\K::$fw->TEXT_DEFAULT_DELETE_CONFIRMATION, \K::$fw->backup_info['filename']);
    ?>
</div>
<?= \Helpers\App::ajax_modal_template_footer(\K::$fw->TEXT_DELETE) ?>

</form>  