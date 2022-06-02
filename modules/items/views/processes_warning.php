<?php
echo ajax_modal_template_header(sprintf(TEXT_EXT_PROCESS_HEADING, $app_process_info['name'])) ?>

<div class="modal-body">
    <?php
    echo app_alert_warning($app_process_info['warning_text']) ?>
</div>

<?php
echo ajax_modal_template_footer('hide-save-button') ?>

