<?php
echo ajax_modal_template_header($template_info['name']) ?>

<?php
if (!isset($app_selected_items[$_GET['reports_id']])) {
    $app_selected_items[$_GET['reports_id']] = [];
}

if (count($app_selected_items[$_GET['reports_id']]) == 0) {
    echo '
    <div class="modal-body">    
      <div>' . TEXT_PLEASE_SELECT_ITEMS . '</div>
    </div>    
  ' . ajax_modal_template_footer('hide-save-button');
} else {
    ?>


    <?php
    echo form_tag(
        'export-form',
        url_for('items/print_template', 'path=' . $_GET['path'] . '&templates_id=' . $_GET['templates_id']),
        ['target' => '_blank', 'class' => 'form-horizontal']
    ) ?>
    <?php
    echo input_hidden_tag('action', 'print') ?>
    <?php
    echo input_hidden_tag('reports_id', $_GET['reports_id']) ?>

    <?php
    if ($template_info['type'] == 'docx') {
        echo '
            <div class="modal-body ajax-modal-width-790">
                <div class="form-group">
                    <label class="col-md-3 control-label">' . TEXT_FILENAME . '</label>
                                <div class="col-md-9">
                        <div class="input-group input-xlarge">
                                ' . input_tag(
                'filename',
                $app_entities_cache[$template_info['entities_id']]['name'],
                ['class' => 'form-control required']
            ) . '
                                <span class="input-group-addon">
                                        .zip
                                </span>                        
                        </div>                                    
                        <label id="filename-error" class="error" for="filename"></label><br>

                        ' . (strstr($template_info['save_as'], 'zip') ? '<label>' . input_checkbox_tag(
                    'with_attachments'
                ) . ' ' . TEXT_ATTACHMENTS . '</label>' : '') . '
                    </div>
                </div>  
            </div>
    ';

        $count_selected_text = sprintf(TEXT_SELECTED_RECORDS, count($app_selected_items[$_GET['reports_id']]));
        echo ajax_modal_template_footer(
            '<i class="fa fa-download" aria-hidden="true"></i> ' . TEXT_DOWNLOAD,
            '',
            $count_selected_text
        );
    } else {
        ?>

        <div class="modal-body ajax-modal-width-790">
            <div><?php
                echo TEXT_EXT_PRINT_BUTTON_PDF_NOTE ?></div>
        </div>

        <?php

        $buttons_html = '';

        if (export_templates::has_button('docx', $template_info)) {
            $buttons_html .= '<button type="button" class="btn btn-primary btn-template-export-word"><i class="fa fa-file-word-o" aria-hidden="true"></i></button>';
        }

        if (export_templates::has_button('zip', $template_info)) {
            $buttons_html .= '<button type="button" class="btn btn-primary btn-template-export-zip"><i class="fa fa fa-file-archive-o" aria-hidden="true"></i></button> ';
        }

        $buttons_html .= ' <button type="button" class="btn btn-primary btn-template-print"><i class="fa fa-print" aria-hidden="true"></i> ' . TEXT_PRINT . '</button>';

        $count_selected_text = sprintf(TEXT_SELECTED_RECORDS, count($app_selected_items[$_GET['reports_id']]));
        echo ajax_modal_template_footer('hide-save-button', $buttons_html, $count_selected_text);
    }
    ?>

    </form>

    <script>

        $(function () {
            $('#export-form').validate({
                submitHandler: function (form) {
                    return true;
                }
            });
        });

        $('.btn-template-export-zip').click(function () {
            $('#action').val('export_zip');
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


<?php
} ?>