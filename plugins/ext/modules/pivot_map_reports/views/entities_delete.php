<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php
echo form_tag(
    'login',
    url_for(
        'ext/pivot_map_reports/entities',
        'action=delete&id=' . _get::int('id') . '&reports_id=' . _get::int('reports_id')
    )
) ?>

<div class="modal-body">
    <?php
    echo TEXT_ARE_YOU_SURE ?>
</div>

<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>  
    