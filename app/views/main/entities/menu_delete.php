<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_HEADING_DELETE) ?>

<?= \Helpers\Html::form_tag(
    'delete',
    \Helpers\Urls::url_for('main/entities/menu/delete', 'id=' . \K::$fw->GET['id']),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body">
        <?= sprintf(\K::$fw->TEXT_DEFAULT_DELETE_CONFIRMATION, \K::$fw->obj['name']); ?>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer(\K::$fw->TEXT_BUTTON_DELETE) ?>

</form>