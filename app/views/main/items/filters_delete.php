<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php
echo form_tag(
    'login',
    url_for(
        'items/filters',
        'action=delete&id=' . $_GET['id'] . '&reports_id=' . $_GET['reports_id'] . '&path=' . $_GET['path']
    )
) ?>
<div class="modal-body">
    <?php
    echo TEXT_ARE_YOU_SURE ?>
</div>
<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>  
     
    
 
