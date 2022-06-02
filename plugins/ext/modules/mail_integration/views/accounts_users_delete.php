<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>


<?php
echo form_tag(
    'login',
    url_for(
        'ext/mail_integration/accounts_users',
        'action=delete&id=' . $_GET['id'] . '&accounts_id=' . _get::int('accounts_id')
    )
) ?>

<div class="modal-body">
    <?php
    echo TEXT_ARE_YOU_SURE ?>
</div>

<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form> 