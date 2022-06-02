<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php
$obj = db_find('app_ext_recurring_tasks_fields', $_GET['id']);
$field = db_find('app_fields', $obj['fields_id']);
?>

<?php
echo form_tag(
    'login',
    url_for(
        'ext/recurring_tasks/fields',
        'tasks_id=' . _get::int('tasks_id') . '&path=' . $app_path . '&action=delete&id=' . $_GET['id']
    )
) ?>

<div class="modal-body">
    <?php
    echo sprintf(TEXT_DEFAULT_DELETE_CONFIRMATION, $field['name']) ?>
</div>

<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>    
    
 
