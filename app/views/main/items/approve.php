<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php

if (\K::$fw->cfg->get('use_signature') == 1) {
    $content = (strlen(\K::$fw->cfg->get('signature_description')) ? '<p>' . \K::$fw->cfg->get(
            'signature_description'
        ) . '</p>' : '');
    $content .= '<iframe width="100%" height="400" scrolling="no" frameborder="no" src="' . \Helpers\Urls::url_for(
            'main/items/signature',
            'fields_id=' . \K::$fw->GET['fields_id'] . '&path=' . \K::$fw->app_path . '&redirect_to=' . \K::$fw->app_redirect_to
        ) . '"></iframe>';

    $heading = (strlen(\K::$fw->cfg->get('button_title')) ? \K::$fw->cfg->get('button_title') : \K::$fw->TEXT_APPROVE);

    $button_title = 'hide-save-button';
} else {
    $heading = $button_title = (strlen(\K::$fw->cfg->get('button_title')) ? \K::$fw->cfg->get(
        'button_title'
    ) : \K::$fw->TEXT_APPROVE);
    $content = (strlen(\K::$fw->cfg->get('confirmation_text')) ? \K::$fw->cfg->get(
        'confirmation_text'
    ) : \K::$fw->TEXT_ARE_YOU_SURE);
}

echo \Helpers\App::ajax_modal_template_header($heading) ?>

<?= \Helpers\Html::form_tag(
    'approve_form',
    \Helpers\Urls::url_for(
        'main/items/approve/approve',
        'fields_id=' . \K::$fw->GET['fields_id'] . '&path=' . \K::$fw->app_path
    )
) ?>
<?= \Helpers\Html::input_hidden_tag('redirect_to', \K::$fw->app_redirect_to) ?>
<?php
if (isset(\K::$fw->GET['gotopage'])) echo \Helpers\Html::input_hidden_tag(
    'gotopage[' . key(\K::$fw->GET['gotopage']) . ']',
    current(\K::$fw->GET['gotopage'])
) ?>

<div class="modal-body">
    <?= $content ?>
</div>

<?= \Helpers\App::ajax_modal_template_footer($button_title) ?>

</form>

<script>
    $('#approve_form').validate({
        submitHandler: function (form) {
            app_prepare_modal_action_loading(form)
            return true;
        }
    });
</script>