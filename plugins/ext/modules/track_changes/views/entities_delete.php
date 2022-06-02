<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php
echo form_tag(
    'login',
    url_for('ext/track_changes/entities', 'action=delete&reports_id=' . _get::int('reports_id') . '&id=' . $_GET['id'])
) ?>
<div class="modal-body">
    <?php
    echo TEXT_ARE_YOU_SURE ?>
</div>
<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>   
    
 
