<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php
echo form_tag(
    'login',
    url_for(
        'ext/mail_integration/entities_rules',
        'action=delete&account_entities_id=' . _get::int('account_entities_id') . '&id=' . $_GET['id']
    )
) ?>

<div class="modal-body">
    <?php
    echo TEXT_ARE_YOU_SURE ?>
</div>

<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>  