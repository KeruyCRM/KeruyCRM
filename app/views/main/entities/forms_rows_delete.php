<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_DELETE) ?>

<?= \Helpers\Html::form_tag(
    'login',
    \Helpers\Urls::url_for(
        'main/entities/forms_rows/delete',
        'id=' . \K::$fw->GET['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
    )
) ?>

<div class="modal-body">
    <?= \K::$fw->TEXT_ARE_YOU_SURE ?>
</div>

<?= \Helpers\App::ajax_modal_template_footer(\K::$fw->TEXT_DELETE) ?>

</form>