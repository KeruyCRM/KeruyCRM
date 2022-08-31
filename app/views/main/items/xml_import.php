<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php
echo ajax_modal_template_header($template_info['name']) ?>

<?php
$import_fields = []; ?>

<?php
echo form_tag(
    'import_data',
    url_for('items/xml_import_preview', 'path=' . $app_path . '&templates_id=' . $template_info['id']),
    ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']
) ?>
<?php
echo input_hidden_tag('current_time', time()) ?>
<div class="modal-body">
    <div class="form-body">

        <p><?php
            echo $template_info['description'] ?></p>


        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_FILENAME ?></label>
            <div class="col-md-9">
                <?php
                echo input_file_tag(
                    'filename',
                    [
                        'class' => 'form-control required input-xlarge',
                        'accept' => fieldtype_attachments::get_accept_types_by_extensions('xml')
                    ]
                ) ?>
                <span class="help-block">*.xml</span>
            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer(TEXT_BUTTON_CONTINUE) ?>

</form>

<script>
    $(function () {
        $('#import_data').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });

    });

</script> 

