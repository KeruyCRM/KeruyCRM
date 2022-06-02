<?php
echo ajax_modal_template_header(TEXT_EXT_SEND_SMS) ?>

<?php
echo form_tag(
    'sms_form',
    url_for(
        'items/phone_sms',
        'action=send&path=' . $app_path . '&module_id=' . _GET('module_id') . '&field_id=' . _GET(
            'field_id'
        ) . '&item_id=' . _GET('item_id')
    ),
    ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']
) ?>

<div class="modal-body">
    <div class="form-body" id="sms_form_body">

        <div class="form-group">
            <label class="col-md-4 control-label" for="name"><?php
                echo TEXT_PHONE ?></label>
            <div class="col-md-8">
                <p class="form-control-static"><?php
                    echo $phone ?></p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="name"><?php
                echo TEXT_EXT_MESSAGE_TEXT ?></label>
            <div class="col-md-8">
                <?php
                echo textarea_tag('message_text', '', ['class' => 'form-control input-large textarea-small required']
                ) ?>
            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer(TEXT_SEND) ?>

</form>

<script>
    $(function () {
        $('#sms_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)

                $('#sms_form_body').load($('#sms_form').attr('action'), $('#sms_form').serializeArray())
            }
        });
    });
</script> 
