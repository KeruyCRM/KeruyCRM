<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php
echo ajax_modal_template_header($template_info['name']) ?>

<?php
echo form_tag(
        'export-form',
        url_for('items/export_template', 'path=' . $_GET['path'] . '&templates_id=' . $_GET['templates_id']),
        ['class' => 'form-horizontal']
    ) . input_hidden_tag('action', 'export');

if (strlen($template_info['template_filename'])) {
    $item = items::get_info($current_entity_id, $current_item_id);

    $pattern = new fieldtype_text_pattern;
    $filename = $pattern->output_singe_text($template_info['template_filename'], $current_entity_id, $item);
} else {
    $filename = $template_info['name'] . '_' . $current_item_id;
}

if ($template_info['type'] == 'docx') {
    echo '
    <div class="modal-body ajax-modal-width-790">
        <div class="form-group">
            <label class="col-md-3 control-label">' . TEXT_FILENAME . '</label>
			<div class="col-md-9">
                <div class="input-group input-xlarge">
            		' . input_tag('filename', $filename, ['class' => 'form-control required']) . '
            		<span class="input-group-addon">
            			.docx
            		</span>
            	</div>
                <label id="filename-error" class="error" for="filename"></label>
            </div>
        </div>  
    </div>
    ';

    $buttons_html = '';
    if (export_templates::has_button('pdf', $template_info)) {
        $buttons_html .= '<button type="button" class="btn btn-info btn-template-export-pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>';
    }

    if (export_templates::has_button('print', $template_info)) {
        $buttons_html .= '<button type="button" class="btn btn-info btn-template-print"><i class="fa fa-print" aria-hidden="true"></i></button>';
    }

    if (export_templates::has_button('zip', $template_info)) {
        $buttons_html .= '<button type="button" class="btn btn-primary btn-template-export-zip"><i class="fa fa fa-file-archive-o" aria-hidden="true"></i></button> ';
    }

    if (export_templates::has_button('docx', $template_info)) {
        $buttons_html .= '<button type="button" class="btn btn-primary btn-template-export"><i class="fa fa-download" aria-hidden="true"></i> ' . TEXT_DOWNLOAD . '</button>';
    }

    echo ajax_modal_template_footer('hide-save-button', $buttons_html);
} else {
    ?>


    <div class="modal-body ajax-modal-width-790">

        <div id="export_templates_preview">
            <style>
                <?php echo $template_info['template_css'] ?>
            </style>

            <?php
            echo $template_info['template_header'] . export_templates::get_html(
                    $current_entity_id,
                    $current_item_id,
                    $_GET['templates_id']
                ) . $template_info['template_footer']; ?>

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


    if (export_templates::has_button('pdf', $template_info)) {
        $buttons_html .= '<button type="button" class="btn btn-primary btn-template-export"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button> ';
    }

    if (export_templates::has_button('docx', $template_info)) {
        $buttons_html .= '<button type="button" class="btn btn-primary btn-template-export-word"><i class="fa fa-file-word-o" aria-hidden="true"></i></button> ';
    }

    if (export_templates::has_button('zip', $template_info)) {
        $buttons_html .= '<button type="button" class="btn btn-primary btn-template-export-zip"><i class="fa fa fa-file-archive-o" aria-hidden="true"></i></button> ';
    }

    if (export_templates::has_button('print', $template_info)) {
        $buttons_html .= '<button type="button" class="btn btn-primary btn-template-print"><i class="fa fa-print" aria-hidden="true"></i> ' . TEXT_PRINT . '</button>';
    }

    echo ajax_modal_template_footer('hide-save-button', $buttons_html);
}
?>

</form>

<script>
    $('.btn-template-export-pdf').click(function () {
        $('#action').val('export_pdf');
        $('#export-form').attr('target', '_self')
        $('#export-form').submit();
    })

    $('.btn-template-export-zip').click(function () {
        $('#action').val('export_zip');
        $('#export-form').attr('target', '_self')
        $('#export-form').submit();
    })

    $('.btn-template-export').click(function () {
        $('#action').val('export');
        $('#export-form').attr('target', '_self')
        $('#export-form').submit();
    })

    $('.btn-template-export-word').click(function () {
        $('#action').val('export_word');
        $('#export-form').attr('target', '_self')
        $('#export-form').submit();
    })

    $('.btn-template-print').click(function () {
        $('#action').val('print');
        $('#export-form').attr('target', '_new')
        $('#export-form').submit();
    })
</script>