<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php
$obj = db_find('app_forms_tabs', $_GET['id']); ?>

<?php
echo form_tag(
    'vars',
    url_for('forms_tabs/groups', 'action=delete&entities_id=' . _GET('entities_id') . '&id=' . $_GET['id'])
) ?>

<div class="modal-body">
    <?php
    echo sprintf(TEXT_DEFAULT_DELETE_CONFIRMATION, $obj['name']) ?>
</div>

<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>    