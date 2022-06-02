<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php
echo form_tag('login', url_for('ext/processes/processes', 'action=delete&id=' . $_GET['id'])) ?>
<div class="modal-body">
    <?php
    $process_info = db_find('app_ext_processes', _get::int('id'));
    echo sprintf(TEXT_DEFAULT_DELETE_CONFIRMATION, $process_info['name'])
    ?>
</div>
<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>   
    
 
