<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php
echo form_tag(
    'login',
    url_for(
        'ext/report_page/extra_rows',
        'action=delete&id=' . $_GET['id'] . '&report_id=' . $report_page['id'] . '&block_id=' . $block_info['id'] . '&row_id=' . $row_info['id']
    )
) ?>

<div class="modal-body">
    <?php
    echo TEXT_ARE_YOU_SURE ?>
</div>

<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>    