<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>


<?php
echo form_tag(
    'login',
    url_for(
        'ext/recurring_tasks/' . (strlen($app_redirect_to) ? $app_redirect_to : 'repeat'),
        'action=delete&id=' . $_GET['id'] . (strlen($app_path) ? '&path=' . $app_path : '')
    )
) ?>

<div class="modal-body">
    <?php
    echo sprintf(TEXT_DEFAULT_DELETE_CONFIRMATION, $_GET['id']) ?>
</div>

<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>    
    