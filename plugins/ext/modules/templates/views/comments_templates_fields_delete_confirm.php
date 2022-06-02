<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php
$obj = db_find('app_ext_comments_templates_fields', $_GET['id']);
$field = db_find('app_fields', $obj['fields_id']);
?>

<?php
echo form_tag(
    'login',
    url_for(
        'ext/templates/comments_templates_fields',
        'action=delete&id=' . $_GET['id'] . '&templates_id=' . $obj['templates_id']
    )
) ?>

<div class="modal-body">
    <?php
    echo sprintf(TEXT_DEFAULT_DELETE_CONFIRMATION, $field['name']) ?>
</div>

<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>    
    
 
