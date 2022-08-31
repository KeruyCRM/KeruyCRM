<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php
echo ajax_modal_template_header(TEXT_EMAIL_VERIFICATION_EMAIL_SUBJECT) ?>

<?php
echo form_tag(
    'item_form',
    url_for('items/verify_email', 'action=verify&path=' . $app_path),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <?php
    $item_info = db_find('app_entity_' . $current_entity_id, $current_item_id);
    ?>

    <div class="form-group">
        <label class="col-md-4 control-label"><?php
            echo TEXT_FIELDTYPE_USER_EMAIL_TITLE ?></label>
        <div class="col-md-8">
            <p class="form-control-static"><b><?php
                    echo $item_info['field_9'] ?></b></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-4 control-label"><?php
            echo TEXT_FIELDTYPE_USER_FIRSTNAME_TITLE ?></label>
        <div class="col-md-8">
            <p class="form-control-static"><?php
                echo $item_info['field_7'] ?></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-4 control-label"><?php
            echo TEXT_FIELDTYPE_USER_LASTNAME_TITLE ?></label>
        <div class="col-md-8">
            <p class="form-control-static"><?php
                echo $item_info['field_8'] ?></p>
        </div>
    </div>


</div>

<?php
echo ajax_modal_template_footer(TEXT_CONFIRM) ?>

</form>

<script>
    var user_id = <?php echo $current_item_id ?>

        $('#item_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)

                $.ajax({url: form.action}).done(function () {
                    $('#user_email_verify_' + user_id).hide();
                    $('#user_email_' + user_id).css('text-decoration', 'none').attr('title', '');
                    if ($('#user_status_' + user_id).hasClass('label-warning')) $('#user_status_' + user_id).removeClass('label-warning').addClass('label-success').attr('title', '')
                    $("#ajax-modal").modal("hide")
                })

                return false;
            }
        });
</script> 