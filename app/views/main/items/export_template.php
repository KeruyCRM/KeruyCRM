<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->template_info['name']) ?>

<?= \Helpers\Html::form_tag(
    'export-form',
    \Helpers\Urls::url_for(
        'main/items/export_template',
        'path=' . \K::$fw->GET['path'] . '&templates_id=' . \K::$fw->GET['templates_id']
    ),
    ['class' => 'form-horizontal']
) . \Helpers\Html::input_hidden_tag('action', 'export');

if (strlen(\K::$fw->template_info['template_filename'])) {
    $item = \Models\Main\Items\Items::get_info(\K::$fw->current_entity_id, \K::$fw->current_item_id);

    $pattern = new \Tools\FieldsTypes\Fieldtype_text_pattern();
    $filename = $pattern->output_singe_text(
        \K::$fw->template_info['template_filename'],
        \K::$fw->current_entity_id,
        $item
    );
} else {
    $filename = \K::$fw->template_info['name'] . '_' . \K::$fw->current_item_id;
}

if (\K::$fw->template_info['type'] == 'docx') {
    echo '
    <div class="modal-body ajax-modal-width-790">
        <div class="form-group">
            <label class="col-md-3 control-label">' . \K::$fw->TEXT_FILENAME . '</label>
			<div class="col-md-9">
                <div class="input-group input-xlarge">
            		' . \Helpers\Html::input_tag('filename', $filename, ['class' => 'form-control required']) . '
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
    if (\Models\Ext\Templates\Export_templates::has_button('pdf', \K::$fw->template_info)) {
        $buttons_html .= '<button type="button" class="btn btn-info btn-template-export-pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>';
    }

    if (\Models\Ext\Templates\Export_templates::has_button('print', \K::$fw->template_info)) {
        $buttons_html .= '<button type="button" class="btn btn-info btn-template-print"><i class="fa fa-print" aria-hidden="true"></i></button>';
    }

    if (\Models\Ext\Templates\Export_templates::has_button('zip', \K::$fw->template_info)) {
        $buttons_html .= '<button type="button" class="btn btn-primary btn-template-export-zip"><i class="fa fa fa-file-archive-o" aria-hidden="true"></i></button> ';
    }

    if (\Models\Ext\Templates\Export_templates::has_button('docx', \K::$fw->template_info)) {
        $buttons_html .= '<button type="button" class="btn btn-primary btn-template-export"><i class="fa fa-download" aria-hidden="true"></i> ' . \K::$fw->TEXT_DOWNLOAD . '</button>';
    }

    echo \Helpers\App::ajax_modal_template_footer('hide-save-button', $buttons_html);
} else {
    ?>
    <div class="modal-body ajax-modal-width-790">
        <div id="export_templates_preview">
            <style>
                <?php echo \K::$fw->template_info['template_css'] ?>
            </style>

            <?= \K::$fw->template_info['template_header'] . \Models\Ext\Templates\Export_templates::get_html(
                \K::$fw->current_entity_id,
                \K::$fw->current_item_id,
                \K::$fw->GET['templates_id']
            ) . \K::$fw->template_info['template_footer']; ?>

        </div>

        <p>
            <?= \K::$fw->TEXT_FILENAME . '<br>' . \Helpers\Html::input_tag(
                'filename',
                $filename,
                ['class' => 'form-control input-xlarge']
            );
            ?>
        </p>

        <div><?= \K::$fw->TEXT_EXT_PRINT_BUTTON_PDF_NOTE ?></div>
    </div>

    <?php
    $buttons_html = '';

    if (\Models\Ext\Templates\Export_templates::has_button('pdf', \K::$fw->template_info)) {
        $buttons_html .= '<button type="button" class="btn btn-primary btn-template-export"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button> ';
    }

    if (\Models\Ext\Templates\Export_templates::has_button('docx', \K::$fw->template_info)) {
        $buttons_html .= '<button type="button" class="btn btn-primary btn-template-export-word"><i class="fa fa-file-word-o" aria-hidden="true"></i></button> ';
    }

    if (\Models\Ext\Templates\Export_templates::has_button('zip', \K::$fw->template_info)) {
        $buttons_html .= '<button type="button" class="btn btn-primary btn-template-export-zip"><i class="fa fa fa-file-archive-o" aria-hidden="true"></i></button> ';
    }

    if (\Models\Ext\Templates\Export_templates::has_button('print', \K::$fw->template_info)) {
        $buttons_html .= '<button type="button" class="btn btn-primary btn-template-print"><i class="fa fa-print" aria-hidden="true"></i> ' . \K::$fw->TEXT_PRINT . '</button>';
    }

    echo \Helpers\App::ajax_modal_template_footer('hide-save-button', $buttons_html);
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