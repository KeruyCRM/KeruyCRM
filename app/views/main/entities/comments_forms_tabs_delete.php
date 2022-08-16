<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->heading) ?>

<?= \Helpers\Html::form_tag(
    'login',
    \Helpers\Urls::url_for('main/entities/comments_form/delete', 'id=' . \K::$fw->GET['id'] . '&entities_id=' . \K::$fw->GET['entities_id'])
) ?>

<div class="modal-body">
    <?= \K::$fw->content ?>
</div>

<?= \Helpers\App::ajax_modal_template_footer(\K::$fw->button_title) ?>

</form>