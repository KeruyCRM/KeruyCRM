<?php
echo ajax_modal_template_header(TEXT_EXT_CLEAR_EMAIL_ACCOUNT) ?>

<?php
$obj = db_find('app_ext_mail_accounts', $_GET['id']); ?>

<?php
echo form_tag('clear_form', url_for('ext/mail_integration/accounts', 'action=clear&id=' . $_GET['id'])) ?>

<div class="modal-body">
    <?php
    echo '<div class="single-checkbox"><label>' . input_checkbox_tag('delete_confirm', 1, ['class' => 'required']
        ) . ' ' . sprintf(TEXT_EXT_CLEAR_EMAIL_ACCOUNT_CONFIRM, $obj['login']) . '</label></div>'; ?>
</div>

<?php
echo ajax_modal_template_footer(TEXT_BUTTON_CONTINUE) ?>

</form>

<script>
    $('#clear_form').validate({
        submitHandler: function (form) {
            app_prepare_modal_action_loading(form)
            form.submit();
        },
        errorPlacement: function (error, element) {
            error.insertAfter(".single-checkbox");
        }
    });
</script> 