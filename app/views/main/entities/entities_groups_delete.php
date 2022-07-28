<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php
echo form_tag('login', url_for('entities/entities_groups', 'action=delete&id=' . $_GET['id'])) ?>
<div class="modal-body">
    <?php
    echo TEXT_ARE_YOU_SURE ?>
</div>
<?php
echo ajax_modal_template_footer(TEXT_DELETE) ?>

</form>  