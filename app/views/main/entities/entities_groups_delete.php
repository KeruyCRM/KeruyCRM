<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_HEADING_DELETE) ?>

<?= \Helpers\Html::form_tag('login', \Helpers\Urls::url_for('entities/entities_groups/delete', \K::$fw->GET['id'])) ?>
<div class="modal-body">
    <?= \K::$fw->TEXT_ARE_YOU_SURE ?>
</div>
<?= \Helpers\App::ajax_modal_template_footer(\K::$fw->TEXT_DELETE) ?>

</form>