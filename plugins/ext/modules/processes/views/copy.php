<?php
echo ajax_modal_template_header(TEXT_COPY) ?>

<?php
echo form_tag('login', url_for('ext/processes/processes', 'action=copy&id=' . $_GET['id'])) ?>
<div class="modal-body">
    <?php
    $process_info = db_find('app_ext_processes', _get::int('id'));
    echo sprintf(TEXT_EXT_COPY_PROCESS_CONFIRMATION, $process_info['name'])
    ?>
</div>
<?php
echo ajax_modal_template_footer(TEXT_BUTTON_COPY) ?>

</form>  