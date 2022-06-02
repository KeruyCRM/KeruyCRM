<?php
echo ajax_modal_template_header('Электронные подписи') ?>

<div class="modal-body ajax-modal-width-790">

    <?php
    $signed_items_query = db_query(
        "select * from  app_ext_signed_items where fields_id='" . _GET(
            'fields_id'
        ) . "' and entities_id='" . $current_entity_id . "' and items_id='" . $current_item_id . "' and id='" . _GET(
            'signed_items_id'
        ) . "'"
    );
    if ($signed_items = db_fetch_array($signed_items_query)) {
        $html = '
        <div class="panel panel-default">
    		<div class="panel-heading">
    			<h3 class="panel-title">' . $signed_items['name'] . '</h3>
    		</div>
    		<div class="panel-body">
                <table class="table" style="margin-bottom: 0;">';

        if (strlen($signed_items['company'])) {
            $html .= '
            <tr>
                <td>Организация:</td>
                <td>' . $signed_items['company'] . '</td>
            </tr>';
        }

        if (strlen($signed_items['position'])) {
            $html .= '
            <tr>
                <td>Должность:</td>
                <td>' . $signed_items['position'] . '</td>
            </tr>';
        }

        if (strlen($signed_items['inn'])) {
            $html .= '
            <tr>
                <td>ИНН:</td>
                <td>' . $signed_items['inn'] . '</td>
            </tr>';
        }

        if (strlen($signed_items['ogrn'])) {
            $html .= '
            <tr>
                <td>ОГРН/ОГРНИП:</td>
                <td>' . $signed_items['ogrn'] . '</td>
            </tr>';
        }

        $html .= ' 
                    <tr>
                        <td>Дата и время подписания:</td>
                        <td>' . format_date_time($signed_items['date_added']) . '</td>
                    </tr>           
                </table>
    		</div>
    	</div>
    ';

        $html .= '
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab_signed_data"  data-toggle="tab">Подписанные данные</a></li>           
        </ul> 
        <div class="tab-content">
          <div class="tab-pane fade active in" id="tab_signed_data"> 
            <table class="table">                    
       ';


        $result_text = false;

        $signatures_query = db_query(
            "select * from app_ext_signed_items_signatures where signed_items_id='" . $signed_items['id'] . "'"
        );
        while ($signatures = db_fetch_array($signatures_query)) {
            if (strlen($signatures['signed_text'])) {
                if (!$result_text) {
                    $result = cryptopro::SignatureCheck($signatures['signed_text'], $signatures['signature'], 1, false);
                    $result_text = ($result ? '<div class="alert alert-success"><i class="fa fa-check"></i> ' : '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle" style="color: #f0ad4e"></i> ') . $SignatureCheckResult . '</div>';
                }

                $html .= '
                <tr>
                    <td><a href="javascript: save_signed_text()"><i class="fa fa-download"></i>  Подписанный текст</a> ' . input_hidden_tag(
                        'signed_text',
                        $signatures['signed_text']
                    ) . '</td>
                    <td style="text-align: right; white-space:nowrap;">
                        <a href="javascript: save_sign_from_input(\'signature_' . $signatures['id'] . '\')" ><i class="la la-certificate"></i> Скачать ЭЦП</a> ' .
                    input_hidden_tag('signature_' . $signatures['id'], $signatures['signature']) .
                    input_hidden_tag('signature_' . $signatures['id'] . '_filename', 'Подписанный_текст') .
                    '</td>
                </tr>
                ';
            } else {
                $filename = $signatures['singed_filename'];
                $file = attachments::parse_filename($filename);

                if (!$result_text) {
                    $result = cryptopro::SignatureCheck(
                        file_get_contents($file['file_path']),
                        $signatures['signature'],
                        1,
                        false
                    );
                    $result_text = ($result ? '<div class="alert alert-success"><i class="fa fa-check"></i> ' : '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle" style="color: #f0ad4e"></i> ') . $SignatureCheckResult . '</div>';
                }


                $link = link_to(
                    '<i class="fa fa-download"></i> ' . $file['name'],
                    url_for(
                        'items/info',
                        'path=' . $app_path . '&action=download_attachment&file=' . urlencode(base64_encode($filename))
                    )
                );

                $html .= '
                <tr>
                    <td>' . $link . '</td>
                    <td style="text-align: right; white-space:nowrap;">
                        <a href="javascript: save_sign_from_input(\'signature_' . $signatures['id'] . '\')" ><i class="la la-certificate"></i> Скачать ЭЦП</a> ' .
                    input_hidden_tag('signature_' . $signatures['id'], $signatures['signature']) .
                    input_hidden_tag('signature_' . $signatures['id'] . '_filename', $file['name']) .
                    '</td>
                </tr>
                ';
            }
        }

        $html .= '
              </table>
            </div>
        </div>
       ' . $result_text;

        echo $html;
    }

    ?>

</div>

<?php
echo ajax_modal_template_footer('hide-save-button') ?>

<a herf="" id="save_date_url" style="display:none;"></a>

<script>

    function save_signed_text() {
        var data = $('#signed_text').val();
        var oMyBlob = new Blob([data], {type: 'application/text/plain'});
        url = window.URL.createObjectURL(oMyBlob);

        var a = document.getElementById("save_date_url");
        a.href = url;
        a.download = 'Подписанный_текст.txt';
        a.click();
        window.URL.revokeObjectURL(url);

    }

    function save_sign_from_input(input_id) {
        var data = $('#' + input_id).val();
        if (data.length < 1000) {
            alert('Ошибка: невозможно скачать ЭЦП');
            return;
        }
        var oMyBlob = new Blob([data], {type: 'application/pkcs7-signature'});
        url = window.URL.createObjectURL(oMyBlob);
        //window.open(URL.createObjectURL(oMyBlob));

        var a = document.getElementById("save_date_url");
        a.href = url;
        a.download = $('#' + input_id + '_filename').val() + '.p7s';
        a.click();
        window.URL.revokeObjectURL(url);
    }
</script>