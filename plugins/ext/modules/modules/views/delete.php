<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php
echo form_tag('delete', url_for('ext/modules/modules', 'action=delete&id=' . $_GET['id'] . '&type=' . $_GET['type'])) ?>
<div class="modal-body">
    <?php
    $modules_info = db_find('app_ext_modules', _get::int('id'));

    $modules = new modules($_GET['type']);

    $module = new $modules_info['module'];

    echo sprintf(TEXT_DEFAULT_DELETE_CONFIRMATION, $module->title);
    ?>
</div>
<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>   
    
 
