<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->heading) ?>

<?= \Helpers\Html::form_tag(
    'delete_item_form',
    \Helpers\Urls::url_for('main/items/items/delete', 'id=' . \K::$fw->GET['id'] . '&path=' . \K::$fw->GET['path'])
) ?>

<?= \Helpers\Html::input_hidden_tag('redirect_to', \K::$fw->app_redirect_to) ?>
<?php
if (isset(\K::$fw->GET['gotopage'])) echo \Helpers\Html::input_hidden_tag(
    'gotopage[' . key(\K::$fw->GET['gotopage']) . ']',
    current(\K::$fw->GET['gotopage'])
) ?>

<div class="modal-body">
    <?= \K::$fw->content ?>
</div>

<?= \Helpers\App::ajax_modal_template_footer(\K::$fw->button_title) ?>

</form>

<script>
    $('#delete_item_form').validate({
        submitHandler: function (form) {
            app_prepare_modal_action_loading(form)
            form.submit();
        },
        errorPlacement: function (error, element) {
            error.insertAfter(".single-checkbox");
        }
    });
</script>