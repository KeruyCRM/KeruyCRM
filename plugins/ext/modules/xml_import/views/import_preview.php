<ul class="page-breadcrumb breadcrumb">
    <?php
    echo '
			<li>' . link_to(TEXT_EXT_XML_IMPORT, url_for('ext/xml_import/templates')) . '<i class="fa fa-angle-right"></i></li>
			<li>' . $template_info['name'] . '<i class="fa fa-angle-right"></i></li>						
			<li>' . TEXT_EXT_XML_IMPORT_PREVIEW . '</li>';
    ?>
</ul>

<p><?php
    echo TEXT_EXT_FILE_PATH . ': ' . $template_info['filepath'] ?></p>

<?php

$xml_import = new xml_import('', $template_info);
$xml_import->set_preview_mode();
$xml_import->get_file_by_path();
$xml_errors = $xml_import->has_xml_errors();

if (strlen($xml_errors)) {
    $html = '
        <a href="' . url_for(
            'ext/xml_import/templates'
        ) . '" class="btn btn-default"><i class="fa fa-angle-left"></i> ' . TEXT_BUTTON_BACK . '</a><hr>
            ' . $xml_errors . '
        <hr><a href="' . url_for(
            'ext/xml_import/templates'
        ) . '" class="btn btn-default"><i class="fa fa-angle-left"></i> ' . TEXT_BUTTON_BACK . '</a>
        ';

    echo $html;
} else {
    $html = form_tag(
            'import_data',
            url_for('ext/xml_import/templates', 'action=import_from_url&id=' . $template_info['id'])
        ) . '
        <a href="' . url_for(
            'ext/xml_import/templates'
        ) . '" class="btn btn-default btn-back"><i class="fa fa-angle-left"></i> ' . TEXT_BUTTON_BACK . '</a>
        <button type="submit" class="btn btn-primary btn-primary-modal-action"><i class="fa fa-upload"></i> ' . TEXT_BUTTON_IMPORT . '</button>
        <div class="fa fa-spinner fa-spin primary-modal-action-loading"></div>
        <hr>
            ' . $xml_import->import_data() . '
        <hr><a href="' . url_for(
            'ext/xml_import/templates'
        ) . '" class="btn btn-default btn-back"><i class="fa fa-angle-left"></i> ' . TEXT_BUTTON_BACK . '</a>
        </form>    
        ';

    echo $html;
}

$xml_import->unlink_import_file();

?>

<script>
    $(function () {
        $('#import_data').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                $('.btn-back').hide()
                return true;
            }
        })
    })
</script>