<?php
echo ajax_modal_template_header(TEXT_EXT_EMPTY_TRASH) ?>


<?php
echo form_tag('empty_trash', url_for('ext/mail/accounts', 'action=empty_trash')) ?>

<div class="modal-body">
    <?php
    echo TEXT_ARE_YOU_SURE ?>
</div>

<?php
echo ajax_modal_template_footer(TEXT_BUTTON_EMPTY) ?>

</form>

<script>
    $(function () {
        $('#empty_trash').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });
    });
</script>     
     