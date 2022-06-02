<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for('ext/modules/sms_rules', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">

        <?php
        $modules = new modules('sms');
        $sms_modules_choices = $modules->get_active_modules();
        ?>
        <div class="form-group">
            <label class="col-md-3 control-label" for="type"><?php
                echo TEXT_EXT_SMS_MODULE ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'modules_id',
                    $sms_modules_choices,
                    $obj['modules_id'],
                    ['class' => 'form-control input-large required']
                ) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="type"><?php
                echo TEXT_REPORT_ENTITY ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'entities_id',
                    entities::get_choices(),
                    $obj['entities_id'],
                    ['class' => 'form-control input-large required', 'onChange' => 'ext_get_entities_fields()']
                ) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="type"><?php
                echo TEXT_EXT_RULE ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'action_type',
                    sms::get_action_type_choices(),
                    $obj['action_type'],
                    [
                        'class' => 'form-control input-xlarge required chosen-select',
                        'onChange' => 'ext_get_entities_fields()'
                    ]
                ) ?>
            </div>
        </div>

        <div id="rules_entities_fields"></div>


    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>

    $(function () {
        $('#configuration_form').validate({ignore: ''});

        ext_get_entities_fields($('#entities_id').val());

    });

    function ext_get_entities_fields() {
        var entities_id = $('#entities_id').val();
        var action_type = $('#action_type').val();

        $('#rules_entities_fields').html('<div class="ajax-loading"></div>');

        $('#rules_entities_fields').load('<?php echo url_for(
            "ext/modules/sms_rules",
            "action=get_entities_fields"
        )?>', {
            action_type: action_type,
            entities_id: entities_id,
            id: '<?php echo $obj["id"] ?>'
        }, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();
            }
        });
    }

    function get_monitor_choices() {
        var entities_id = $('#entities_id').val();
        var action_type = $('#action_type').val();
        var fields_id = $('#monitor_fields_id').val();

        $('#monitor_choices_row').html('<div class="ajax-loading"></div>');

        $('#monitor_choices_row').load('<?php echo url_for(
            "ext/modules/sms_rules",
            "action=get_monitor_choices"
        )?>', {
            action_type: action_type,
            fields_id: fields_id,
            entities_id: entities_id,
            id: '<?php echo $obj["id"] ?>'
        }, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();
            }
        });
    }
</script>   