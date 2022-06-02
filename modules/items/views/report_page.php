<?php
echo ajax_modal_template_header($report_info['name']) ?>

<?php
echo form_tag(
        'export-form',
        url_for('items/report_page', 'path=' . $app_action . '&report_id=' . $report_info['id']),
        ['class' => 'form-horizontal']
    ) . input_hidden_tag('action', 'print');

if (strlen($report_info['save_filename'])) {
    $item = items::get_info($current_entity_id, $current_item_id);

    $pattern = new fieldtype_text_pattern;
    $filename = $pattern->output_singe_text($report_info['save_filename'], $current_entity_id, $item);
} else {
    $filename = $report_info['name'] . '_' . $current_item_id;
}

?>

<div class="modal-body ajax-modal-width-1100">

    <div id="export_templates_preview">

        <?php
        $page = new report_page\report($report_info);
        $page->set_item($current_entity_id, $current_item_id);
        echo $page->get_html();
        ?>

    </div>

    <p>
        <?php
        echo TEXT_FILENAME . '<br>' . input_tag('filename', $filename, ['class' => 'form-control input-xlarge']);
        ?>
    </p>

    <div><?php
        echo TEXT_EXT_PRINT_BUTTON_PDF_NOTE ?></div>
</div>


<?php
$buttons_html = '';


if (strstr($report_info['save_as'], 'pdf')) {
    $buttons_html .= '<button type="button" class="btn btn-primary btn-template-export-pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> ' . TEXT_SAVE . '</button> ';
}

if (strstr($report_info['save_as'], 'print')) {
    $buttons_html .= '<button type="button" class="btn btn-primary btn-template-print"><i class="fa fa-print" aria-hidden="true"></i> ' . TEXT_PRINT . '</button>';
}

echo ajax_modal_template_footer('hide-save-button', $buttons_html);
?>


</form>

<script>
    $('.btn-template-export-pdf').click(function () {
        $('#action').val('export_pdf');
        $('#export-form').attr('target', '_self')
        $('#export-form').submit();
    })

    $('.btn-template-print').click(function () {
        $('#action').val('print');
        $('#export-form').attr('target', '_new')
        $('#export-form').submit();
    })
</script>
