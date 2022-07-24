<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= '
	<h3 class="form-title">' . \K::$fw->TEXT_DB_RESTORE_PROCESS . '</h3>
	<p>' . \K::$fw->TEXT_DB_RESTORE_PROCESS_INFO . '</p>			
	<div id="db_restore_process" style="margin: 45px 0;">
		<div class="ajax-loading"></div>	
	</div>		
' . \K::$fw->html;