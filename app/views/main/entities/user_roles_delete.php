<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_HEADING_DELETE) ?>

<?= \Helpers\Html::form_tag(
    'login',
    \Helpers\Urls::url_for(
        'main/entities/user_roles/delete',
        'id=' . \K::$fw->GET['id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
    )
) ?>

<div class="modal-body">
    <?= sprintf(
        \K::$fw->TEXT_DEFAULT_DELETE_CONFIRMATION,
        \Models\Main\Users\User_roles::get_name_by_id(\K::$fw->GET['id'])
    ) ?>
</div>

<?= \Helpers\App::ajax_modal_template_footer(\K::$fw->TEXT_BUTTON_DELETE) ?>

</form>