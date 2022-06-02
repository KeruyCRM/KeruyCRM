<script language="javascript" src="js/cryptopro/js/utils.js"></script>
<script language="javascript" src="js/cryptopro/js/es6-promise.min.js"></script>
<script language="javascript" src="js/cryptopro/js/ie_eventlistner_polyfill.js"></script>
<script language="javascript">
    window.allow_firefox_cadesplugin_async = 1;
</script>
<script language="javascript" src="js/cryptopro/js/cadesplugin_api.js"></script>
<script language="javascript" src="js/cryptopro/js/cryptopro-plugin-api.js?v=2"></script>
<script language="javascript" src="js/cryptopro/js/sha256.js"></script>


<div id="info1">
    <div id="boxdiv" style="display:none">
     <span id="errorarea">
       <div class="alert alert-danger">У вас отсутствуют личные сертификаты.</div>
     </span>
    </div>
</div>


<div class="title_info" name="CertificateTitle">Выберите сертификат:</div>
<div id="item_border" name="CertListBoxToHide">
    <select size="4" name="CertListBox" id="CertListBox" class="form-control" style="height:86px"></select>
</div>

<div id="info_msg" style="text-align:center; padding: 3px;">
    <span id="PlugInEnabledTxt"></span>

    <br>
    <span id="PlugInVersionTxt" lang="ru"> </span>
    <span id="CSPVersionTxt" lang="ru"> </span>
    <br>
    <span id="CSPNameTxt" lang="ru"> </span>
</div>


<div id="cert_info" style="display:none;" align="center">
    <div class="cert_info_box" align="left">


        <?php
        echo form_tag('ecp_login', $form_action_url) ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Сертификат</h3>
            </div>
            <div class="panel-body">

                <table>
                    <tr>
                        <td>Отпечаток сертификата:</td>
                        <td><span class="cert_info_field" id="thumbprint"></span></td>
                    </tr>
                    <tr>
                        <td>Субъект:</td>
                        <td><span class="cert_info_field" id="subject"></span></td>
                    </tr>
                    <tr>
                        <td>ФИО:</td>
                        <td><span class="cert_info_field" id="fio"></span></td>
                    </tr>
                    <tr>
                        <td>Организация:</td>
                        <td><span class="cert_info_field" id="company"></span></td>
                    </tr>
                    <tr>
                        <td>ИНН:</td>
                        <td><span class="cert_info_field" id="inn"></span></td>
                    </tr>
                    <tr>
                        <td>Издатель:</td>
                        <td><span class="cert_info_field" id="issuer"></span></td>
                    </tr>
                    <tr>
                        <td>Действителен с:</td>
                        <td><span class="cert_info_field" id="from"></span> по :<span class="cert_info_field"
                                                                                      id="till"></span></td>
                    </tr>
                    <tr>
                        <td>Состояние:</td>
                        <td><span class="cert_info_field" id="status"></span></td>
                    </tr>
                    <tr>
                        <td>Криптопровайдер:</td>
                        <td><span class="cert_info_field" id="provname"></span></td>
                    </tr>
                    <tr>
                        <td>Алгоритм:</td>
                        <td><span class="cert_info_field" id="algorithm"></span></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                </table>

                <div class="cert_item" style="display:none">In storage: <span class="cert_info_field"
                                                                              id="location"></span></div>
                <input id="status_code" type="hidden" value="">
                <input id="has_private_key" type="hidden" value="">

                <div style="margin-top:20px;">
                    <input class="btn btn-primary btn btn-primary btn-sm btn-process-7" type="button" value="Выбрать"
                           onclick="ChooseCert()">
                </div>

            </div>
        </div>

        <input type="hidden" name="sha256_cert_thumbprint" id="sha256_cert_thumbprint">
        <input type="hidden" name="cert_username" id="cert_username">
        <input type="hidden" name="signed" id="signed">
        </form>

    </div>
</div>


<div style="display:none;">
    <p id="info_msg">Data to sign:</p>
    <div id="item_border">
        <textarea id="DataToSignTxtBox" name="DataToSignTxtBox"
                  style="height:20px;width:100%;resize:none;border:0;"></textarea>
    </div>

    <div class="layout">
        <input id="SignBtn" type="button" value="Sign" name="SignData" onclick="SignDebug()"/>
    </div>
    <p id="info_msg" name="SignatureTitle">Signature:</p>
    <div id="item_border">
        <input type="text" id="SignatureTxtLine" readonly style="font-size:9pt;width:100%;resize:none;border:1;">
        <textarea id="SignatureTxtBox" readonly
                  style="font-size:9pt;height:600px;width:100%;resize:none;border:0;"></textarea>
    </div>

</div>

<script language="javascript">
    var canPromise = !!window.Promise;
    if (isEdge()) {
        ShowEdgeNotSupported();
    } else {
        if (canPromise) {
            cadesplugin.then(function () {
                    Common_CheckForPlugIn();
                },
                function (error) {
                    ElById('PlugInEnabledTxt').innerHTML = error;
                }
            );
        } else {
            window.addEventListener("message", function (event) {
                    if (event.data == "cadesplugin_loaded") {
                        CheckForPlugIn_NPAPI();
                    } else if (event.data == "cadesplugin_load_error") {

                        ElById('PlugInEnabledTxt').innerHTML = "Плагин не загружен";
                    }
                },
                false);
            window.postMessage("cadesplugin_echo_request", "*");
        }
    }

    function ShowError(msg) {
        return alert("Ошибка: " + msg), true;
    }

    window.onerror = ShowError;

    function throwError(msg) {
        throw Error(msg);
    }

    function onSignedData(data) {
        alert("Signed data:\r\n" + data);
        ElById("SignatureTxtBox").value = data;
        ElById("SignatureTxtLine").value = data;
    }

    function SignDebug() {
        Common_SignCadesBESX('CertListBox', ElById("DataToSignTxtBox").value, null);
    }

    function UICancelCert() {
        ElById("cert_info").style.display = "none";
    }

    function ChooseCert() {
        nCryptoPro.ChooseCert();
    }

    nCryptoPro = {
        BadCerificate: function () {
            ShowError("Сертификат некорретный. Выберите другой сертификат.");
        },
        ChooseCert: function () {
            var certObj, code;

            function error() {
                nCryptoPro.BadCerificate();
            }

            var thumbprint = ElById("thumbprint").innerHTML;
            var owner = ElById("subject").innerHTML;

            if (ElById("has_private_key").value != "1") return error();
            if (ElById("status_code").value != CERTIFCATE_STATUS_ACTIVE) return error();

            nECP.LoginTo(thumbprint, ElById(nCryptoPro.getUICertListId()).selectedIndex, owner);
        },

        getUICertListId: function () {
            return "CertListBox";
        },

    };

    function SignCadesBESByThumbprint(thumbprint, data, onSignedData, onError) {
        var canAsync = !!cadesplugin.CreateObjectAsync;

        if (canAsync) {
            include_async_code().then(function () {
                SignCadesBESByThumbprint_Async(thumbprint, data, onSignedData, onError);
            });
        } else {
            SignCadesBES_NPAPIXByThumbprint(thumbprint, data, onSignedData, onError);
        }
    }

    function IsObj(o) {
        return o != null && o != undefined;
    }

    nECP = {
        onSignedCode: function (signed) {
            nECP.thumbprint = nECP.thumbprint.toLowerCase();

            $("#sha256_cert_thumbprint").val(SHA256(nECP.thumbprint + $("#form_session_token").val()));
            $("#cert_username").val(nECP.owner);
            $("#signed").val(signed);
            $('#ecp_login').submit()

        },
        onSignedError: function (msg) {
            alert(msg);
        },
        LoginTo: function (thumbprint, index, owner) {
            nECP.thumbprint = thumbprint;
            nECP.owner = owner;
            SignCadesBESByThumbprint(thumbprint, $("#form_session_token").val(), nECP.onSignedCode, nECP.onSignedError)
        },
    }

</script>



