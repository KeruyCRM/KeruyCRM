<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php
echo form_tag('login', url_for('ext/processes/buttons_groups', 'action=delete&id=' . $_GET['id'])) ?>
<div class="modal-body">
    <?php
    $info = db_find('app_ext_processes_buttons_groups', _get::int('id'));
    echo sprintf(TEXT_DEFAULT_DELETE_CONFIRMATION, $info['name'])
    ?>
</div>
<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form> 