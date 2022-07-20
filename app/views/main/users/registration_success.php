<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
    <h3 class="form-title"><?php
        echo(strlen(
            CFG_REGISTRATION_SUCCESS_PAGE_HEADING
        ) > 0 ? CFG_REGISTRATION_SUCCESS_PAGE_HEADING : TEXT_REGISTRATION_SUCCESS_PAGE_HEADING) ?></h3>

<?php
echo(strlen(
    CFG_REGISTRATION_SUCCESS_PAGE_DESCRIPTION
) > 0 ? '<p>' . CFG_REGISTRATION_SUCCESS_PAGE_DESCRIPTION . '</p>' : TEXT_REGISTRATION_SUCCESS_PAGE_DESCRIPTION) ?>

<?php

$html = '
  <div class="modal-footer">    
    	<a href="' . url_for('users/login') . '" class="btn btn-default">' . TEXT_BUTTON_CONTINUE . '</a>
  </div>';

echo $html;
?>