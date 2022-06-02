<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php
$obj = db_find('app_ext_processes_actions_fields', $_GET['id']);
$field = db_find('app_fields', $obj['fields_id']);
?>

<?php
echo form_tag(
    'login',
    url_for(
        'ext/processes/fields',
        'process_id=' . _get::int('process_id') . '&actions_id=' . _get::int(
            'actions_id'
        ) . '&action=delete&id=' . $_GET['id']
    )
) ?>

<div class="modal-body">
    <?php
    echo sprintf(TEXT_DEFAULT_DELETE_CONFIRMATION, $field['name']) ?>
</div>

<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>    
    
 
