<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php
echo form_tag(
    'login',
    url_for('records_visibility/rules', 'action=delete&id=' . $_GET['id'] . '&entities_id=' . _get::int('entities_id'))
) ?>
<div class="modal-body">
    <?php
    echo TEXT_ARE_YOU_SURE ?>
</div>
<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>   
    