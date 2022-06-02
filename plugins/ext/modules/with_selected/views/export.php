<?php
echo ajax_modal_template_header($template_info['name']) ?>

<?php

if (!isset($app_selected_items[_GET('reports_id')]) or count($app_selected_items[_GET('reports_id')]) == 0) {
    echo '
    <div class="modal-body">    
      <div>' . TEXT_PLEASE_SELECT_ITEMS . '</div>
    </div>    
  ' . ajax_modal_template_footer('hide-save-button');
} else {
    echo form_tag(
            'export_form',
            url_for(
                'ext/with_selected/export',
                'action=export_' . $template_info['type'] . '&templates_id=' . $template_info['id']
            ),
            ['class' => 'form-horizontal']
        )
        . input_hidden_tag('reports_id', _GET('reports_id'))
        . input_hidden_tag('export_type', '');

    if (strlen($template_info['template_filename'])) {
        $filename = str_replace(['[current_user_name]', '[current_date]', '[current_date_time]'],
            [$app_user['name'], format_date(time()), format_date_time(time())],
            $template_info['template_filename']);
    } else {
        $filename = $template_info['name'];
    }

    echo '
    <div class="modal-body ajax-modal-width-790">
        <div class="form-group">
            <label class="col-md-3 control-label">' . TEXT_FILENAME . '</label>
			<div class="col-md-9">
                <div class="input-group input-xlarge">
            		' . input_tag('filename', $filename, ['class' => 'form-control required']) . '
            		<span class="input-group-addon">
            			.' . $template_info['type'] . '
            		</span>
            	</div>
                <label id="filename-error" class="error" for="filename"></label>
            </div>
        </div>  
    </div>
    ';


    $count_selected_text = sprintf(TEXT_SELECTED_RECORDS, count($app_selected_items[$_GET['reports_id']]));

    if ($template_info['type'] == 'docx') {
        $buttons_html = '
		<button type="submit" name="pdf" class="btn btn-info"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button> 		
                <button type="submit" name="print" class="btn btn-info"><i class="fa fa-print" aria-hidden="true"></i></button>
                <button type="submit" name="docx" class="btn btn-primary">' . TEXT_SAVE . '</button>';

        echo ajax_modal_template_footer('hide-save-button', $buttons_html, $count_selected_text);
    } else {
        echo ajax_modal_template_footer(TEXT_SAVE, '', $count_selected_text);
    }

    echo '</form>';
}

?>

<script>
    $(function () {
        $('#export_form').validate({
            submitHandler: function (form) {
                $('#export_type').val($(this.submitButton).attr("name"));

                if ($('#export_type').val() == 'print') {
                    $(form).attr('target', '_new')
                } else {
                    $(form).attr('target', '_self')
                }

                return true;
            }
        })
    })
</script>


    
