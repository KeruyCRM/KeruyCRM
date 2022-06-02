<?php
echo ajax_modal_template_header('Просмотр данных перед подписанием') ?>

<?php
echo form_tag(
    'signature_form',
    url_for('items/digital_signature', 'action=sign&fields_id=' . _GET('fields_id') . '&path=' . $app_path)
) ?>

<?php
echo input_hidden_tag('redirect_to', $app_redirect_to) ?>
<?php
if (isset($_GET['gotopage'])) echo input_hidden_tag(
    'gotopage[' . key($_GET['gotopage']) . ']',
    current($_GET['gotopage'])
) ?>

<div class="modal-body ajax-modal-width-790">

    <p>На данной форме отображается содержание информации, подписание которой производится (в соответствии с п. 2 статьи
        12 Федерального закона № 63-ФЗ от 6 апреля 2011г.).</p>

    <?php

    $item_query = db_query(
        "select e.* " . fieldtype_formula::prepare_query_select(
            $current_entity_id,
            ''
        ) . " from app_entity_" . $current_entity_id . " e where id='" . $current_item_id . "'",
        false
    );
    $item = db_fetch_array($item_query);

    $fields_access_schema = users::get_fields_access_schema($current_entity_id, $app_user['group_id']);

    //sign item
    $where_sql = " and f.id in ('" . implode(
            "','",
            $cfg->get('signature_fields')
        ) . "') and f.type not in (" . fields_types::get_attachments_types_list() . ")";
    $fields_query = fields::get_query($current_entity_id, $where_sql);
    if (db_num_rows($fields_query)) {
        $sign_source = '';
        $html = '<table class="table table-bordered table-hover">';

        while ($fields = db_fetch_array($fields_query)) {
            //check field access
            if (isset($fields_access_schema[$fields['id']]) and $fields_access_schema[$fields['id']] == 'hide') {
                continue;
            }

            //prepare field value
            $value = items::prepare_field_value_by_type($fields, $item);

            $output_options = [
                'class' => $fields['type'],
                'value' => $value,
                'field' => $fields,
                'item' => $item,
                'is_export' => true,
                'is_print' => true,
            ];

            $field_name = fields_types::get_option($fields['type'], 'name', $fields['name']);
            $output = fields_types::output($output_options);

            $html .= '
            <tr>
                <td width="35%">' . $field_name . '</td>
                <td>' . $output . '</td>
            </tr>
        ';

            $sign_source .= $field_name . ': ' . strip_tags($output) . "\n";
        }

        $html .= '</table>'
        ?>


        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_sign_data" data-toggle="tab">Подписываемые данные</a></li>
            <li><a href="#tab_sign_data_source" data-toggle="tab">TXT</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="tab_sign_data">
                <?php
                echo $html ?>
            </div>
            <div class="tab-pane fade" id="tab_sign_data_source">
                <?php
                echo textarea_tag(
                    'sign_source',
                    $sign_source,
                    ['class' => 'form-control', 'style' => 'height:250px;', 'readonly' => 'readonly']
                ) ?>
                <?php
                echo input_hidden_tag(
                    'sign_source_base64',
                    base64_encode($sign_source),
                    ['class' => 'data-to-sign', 'signature_container' => 'sign_source_signature']
                ) ?>
                <?php
                echo input_hidden_tag('sign_source_signature', '', ['class' => 'form-control', 'placeholder' => 'ЭЦП']
                ) ?>
                <br>
            </div>
        </div>

        <?php
    }

    //sign attachments
    $where_sql = " and f.id in ('" . implode(
            "','",
            $cfg->get('signature_fields')
        ) . "') and f.type in (" . fields_types::get_attachments_types_list() . ")";
    $fields_query = fields::get_query($current_entity_id, $where_sql);
    if (db_num_rows($fields_query)) {
        $attachments_list = [];
        $html = '<table class="table table-bordered table-hover">';

        while ($fields = db_fetch_array($fields_query)) {
            //check field access
            if (isset($fields_access_schema[$fields['id']]) and $fields_access_schema[$fields['id']] == 'hide') {
                continue;
            }

            $value = items::prepare_field_value_by_type($fields, $item);

            if (strlen($value)) {
                foreach (explode(',', $value) as $key => $filename) {
                    $attachments_list[] = $filename;

                    $file = attachments::parse_filename($filename);

                    $link = link_to(
                        $file['name'],
                        url_for(
                            'items/info',
                            'path=' . $app_path . '&action=download_attachment&file=' . urlencode(
                                base64_encode($filename)
                            )
                        ) . '&field=' . $fields['id']
                    );

                    $link .= input_hidden_tag('sign_file_name[' . $key . ']', $filename);
                    $link .= '<div class="hidden">' . input_tag(
                            'sign_file_base64[' . $key . ']',
                            base64_encode(file_get_contents($file['file_path'])),
                            [
                                'class' => 'data-to-sign',
                                'signature_container' => 'sign_file_signature_' . $key,
                                'disabled' => 'disabled'
                            ]
                        ) . '</div>';
                    $link .= input_hidden_tag(
                        'sign_file_signature[' . $key . ']',
                        '',
                        ['class' => 'form-control', 'placeholder' => 'ЭЦП']
                    );


                    $html .= '
                    <tr>
                        <td>' . $link . '</td>
                    </tr>';
                }
            }
        }

        $html .= '</table>';

        if (count($attachments_list)) {
            ?>


            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_sign_attachments" data-toggle="tab">Подписываемые файлы</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade active in" id="tab_sign_attachments">
                    <?php
                    echo $html ?>
                </div>
            </div>

            <?php
        }
    }

    $user_certificate = '';
    $cryptopro_certificates_query = db_query(
        "select thumbprint from app_ext_cryptopro_certificates where users_id='" . $app_user['id'] . "'"
    );
    if ($cryptopro_certificates = db_fetch_array($cryptopro_certificates_query)) {
        $user_certificate = $cryptopro_certificates['thumbprint'];
    }

    echo input_hidden_tag('user_certificate', $user_certificate);
    ?>

    <hr>

    <script language="javascript" src="js/cryptopro/cades/es6-promise.min.js"></script>
    <script language="javascript" src="js/cryptopro/cades/ie_eventlistner_polyfill.js"></script>
    <script language="javascript">window.allow_firefox_cadesplugin_async = 1</script>
    <script language="javascript" src="js/cryptopro/cades/cadesplugin_api.js"></script>
    <script language="javascript" src="js/cryptopro/cades/Code.js"></script>


    <div class="mainContent">
        <div id="left-col1">
            <div id="info">
                <div id="info_msg" style="text-align:center;">
                    <span id="PlugInEnabledTxt">Плагин не загружен</span>
                    <img src="images/red_dot.png" width="10" height="10" alt="Плагин не загружен"
                         id="PluginEnabledImg"/>
                    <span id="PlugInVersionTxt" lang="ru"> </span>
                    <span id="CSPVersionTxt" lang="ru">  </span>
                    <span id="CSPNameTxt" lang="ru">   </span>
                </div>
                <div id="boxdiv" style="display:none">
           <span id="errorarea">
           У вас отсутствуют личные сертификаты 
           </span>
                </div>
            </div>
            <p id="info_msg1" name="CertificateTitle"></p>


            <p>
                <select name="CertListBox" id="CertListBox" class="form-control"
                        placeholder="Выберите сертификат"> </select>
            </p>

            <div id="cert_info" style="display:none">

                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_sign_attachments" data-toggle="tab">Информация о сертификате</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade active in" id="tab_sign_attachments">

                        <table class="table table-bordered table-hover">
                            <tr>
                                <td width="50%">
                                    <span id="status"></span>
                                </td>
                                <td>
                                    <span id="location"></span>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%">
                                    <span id="from"></span>
                                </td>
                                <td>
                                    <span id="till"></span>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%">
                                    <span id="subject"></span>
                                </td>
                                <td>
                                    <span id="issuer"></span>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%">
                                    <span id="provname"></span>
                                </td>
                                <td>
                                    <span id="algorithm"></span>
                                </td>
                            </tr>
                        </table>
                        <span id="thumbprint"></span>

                    </div>
                </div>


            </div>


            <div style="padding-bottom: 10px;">
                <button type="button" class="btn btn-primary" style="display:none" id="sign_process_btn"
                        onClick="sign_process()"><i class="fa fa-pencil"></i> Подписать
                </button>
                <div class="fa fa-spinner fa-spin primary-modal-action-loading" id="sign_process_btn_loading"></div>
            </div>


            <div id="sign_process_error"></div>


            <div style="display:none">
                <input id="UseDetached" name="UseDetached" type="checkbox" checked="checked"><!--Отсоединеная подпись-->
                <input id="DataInBase64" name="DataInBase64" type="checkbox" checked="checked">
                <!-- Данные уже закодированы в base64 -->
            </div>

            <textarea id="DataToSignTxtBox" name="DataToSignTxtBox" class="form-control hidden"
                      placeholder="текст для подписания"></textarea>
            <textarea id="SignatureTxtBox" readonly class="form-control hidden" placeholder="Результат"></textarea>


            <!-- button id="SignBtn" type="button" class="btn" value="Подписать текст" name="SignData" onclick="Common_SignCadesBES('CertListBox');" style="width:200px;" />Подписать текст</button-->


            <script>

                function sign_process() {
                    Common_SignCadesBES('CertListBox');
                }

                var canPromise = !!window.Promise;
                if (isEdge()) {
                    ShowEdgeNotSupported();
                } else {
                    if (canPromise) {
                        cadesplugin.then(function () {
                                Common_CheckForPlugIn();
                            },
                            function (error) {
                                document.getElementById('PluginEnabledImg').setAttribute("src", "images/red_dot.png");
                                document.getElementById('PlugInEnabledTxt').innerHTML = error;
                            }
                        );
                    } else {
                        window.addEventListener("message", function (event) {
                                if (event.data == "cadesplugin_loaded") {
                                    CheckForPlugIn_NPAPI();
                                } else if (event.data == "cadesplugin_load_error") {
                                    document.getElementById('PluginEnabledImg').setAttribute("src", "images/red_dot.png");
                                    document.getElementById('PlugInEnabledTxt').innerHTML = "Плагин не загружен";
                                }
                            },
                            false);
                        window.postMessage("cadesplugin_echo_request", "*");
                    }
                }
            </script>

        </div>
        <span id="TimeTitle" name="TimeTitle"/>
    </div>
</div>


</div>

<?php
echo ajax_modal_template_footer('hide-save-button') ?>

</form>  