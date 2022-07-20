<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php
echo ajax_modal_template_header(TEXT_SCANNING_BARCODE) ?>
<?php
$field_id = _GET('field_id');
$is_submodal = isset($_GET['is_submodal']);
?>

<div class="modal-body">
    <div class="alert alert-danger" id="error_content" style="display:none"></div>

    <section id="scanner_content">

        <div id="sourceSelectPanel" style="display:none; margin-bottom: 5px;">
            <div class="input-group">
            <span class="input-group-addon">
                 <?php
                 echo TEXT_CAMERA ?>
            </span>
                <select id="sourceSelect" class="form-control"></select>
            </div>

        </div>

        <div>
            <video id="video" width="100%" height="250" style="border: 1px solid gray; background: lightgrey"></video>
        </div>

        <div class="input-group">
        <span class="input-group-addon">
             <?php
             echo TEXT_BARCODE ?>
        </span>
            <input type="text" class="form-control" id="result" readonly="readonly">
        </div>

    </section>
</div>

<script type="text/javascript" src="js/zxing/0.18.3/zxing.js"></script>

<script type="text/javascript">
    $(function () {

        let selectedDeviceId;

        let is_ssl = <?php echo(is_ssl() ? 1 : 0) ?>

        if (!is_ssl) {
            $("#scanner_content").hide();
            $('#error_content').show().html('<?php echo TEXT_SSL_REQUIRED ?>')
        }

        const codeReader = new ZXing.BrowserMultiFormatReader()

        if (is_ssl)
            codeReader.listVideoInputDevices()
                .then((videoInputDevices) => {
                    const sourceSelect = document.getElementById('sourceSelect')
                    selectedDeviceId = videoInputDevices[0].deviceId
                    if (videoInputDevices.length >= 1) {
                        videoInputDevices.forEach((element) => {
                            const sourceOption = document.createElement('option')
                            sourceOption.text = element.label
                            sourceOption.value = element.deviceId
                            sourceSelect.appendChild(sourceOption)

                            if (element.label.indexOf('back') > 0) {
                                selectedDeviceId = element.deviceId;
                            }

                        })

                        sourceSelect.onchange = () => {
                            selectedDeviceId = sourceSelect.value;
                            codeReaderDecode(codeReader, selectedDeviceId)
                        };

                        const sourceSelectPanel = document.getElementById('sourceSelectPanel')
                        sourceSelectPanel.style.display = 'block'
                        sourceSelect.value = selectedDeviceId
                    }


                    codeReaderDecode(codeReader, selectedDeviceId)

                })
                .catch((err) => {
                    $("#scanner_content").hide();
                    $('#error_content').show().html(err)
                    //console.error(err)
                })


        $('.btn-submodal-back').click(function () {
            codeReaderCloseSubmodal()
        });
    })

    function codeReaderCloseSubmodal() {
        $('#sub-items-form').remove();
        $('.paretn-items-form').show();
    }

    function codeReaderDecode(codeReader, selectedDeviceId) {
        let is_submodal = <?php echo($is_submodal ? 1 : 0) ?>

            $('#error_content').hide()

        codeReader.reset()

        codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
            if (result) {
                codeReader.reset()

                ion.sound.play("bleep");

                document.getElementById('result').value = result.text

                if (is_submodal == 1) {
                    $('#fields_<?php echo $field_id ?>').val(result.text);

                    setTimeout(function () {
                        codeReaderCloseSubmodal()
                    }, 1000)
                } else {
                    $('.filters-panels-input-field-<?php echo $field_id ?>').val(result.text);

                    setTimeout(function () {
                        $('#ajax-modal').modal('hide');
                        let form = $('.filters-panels-input-field-<?php echo $field_id ?>').parents("form:first");
                        form.submit()
                    }, 1000)
                }

            }
            if (err && !(err instanceof ZXing.NotFoundException)) {
                console.error(err)
            }
        })
    }

</script>

<?php
$extra_button = '';
if ($is_submodal) {
    $extra_button = '<button type="button" class="btn btn-default btn-submodal-back"><i class="fa fa-angle-left" aria-hidden="true"></i> ' . TEXT_BUTTON_BACK . '</button>';
}
echo ajax_modal_template_footer('hide-save-button', $extra_button);

?>

