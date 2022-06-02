<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>


<?php
$filters_id = (isset($_GET['filters_id']) ? _get::int('filters_id') : 0);

echo form_tag(
    'login',
    url_for(
        'ext/mail_integration/entities_fields',
        'account_entities_id=' . _get::int(
            'account_entities_id'
        ) . '&action=delete&id=' . $_GET['id'] . ($filters_id > 0 ? '&filters_id=' . $filters_id : '')
    )
)
?>

<div class="modal-body">
    <?php
    echo TEXT_ARE_YOU_SURE ?>
</div>

<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>    
    