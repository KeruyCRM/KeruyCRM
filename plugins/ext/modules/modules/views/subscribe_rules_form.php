<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for('ext/modules/subscribe_rules', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">

        <?php
        $modules = new modules('mailing');
        $modules_choices = $modules->get_active_modules();
        ?>
        <div class="form-group">
            <label class="col-md-3 control-label" for="type"><?php
                echo TEXT_EXT_MODULE ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'modules_id',
                    $modules_choices,
                    $obj['modules_id'],
                    ['class' => 'form-control input-large required', 'onChange' => 'ext_get_list_of_contacts()']
                ) ?>
            </div>
        </div>

        <div id="list_of_contacts"></div>

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

        <div id="rules_entities_fields"></div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="cfg_sms_send_to_number_text"><?php
                echo TEXT_EXT_EXTRA_FIELDS; ?></label>
            <div class="col-md-9">
                <?php
                echo textarea_tag(
                    'contact_fields',
                    $obj['contact_fields'],
                    ['class' => 'form-control input-xlarge textarea-small']
                ); ?>
                <?php
                echo tooltip_text(TEXT_EXT_EXTRA_FIELDS_SUBSCRIBE_INFO) ?>
            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>

    $(function () {
        $('#configuration_form').validate();

        ext_get_entities_fields();

        ext_get_list_of_contacts();

    });

    function ext_get_list_of_contacts() {
        var modules_id = $('#modules_id').val();

        $('#list_of_contacts').html('<div class="ajax-loading"></div>');

        $('#list_of_contacts').load('<?php echo url_for(
            "ext/modules/subscribe_rules",
            "action=get_list_of_contacts"
        )?>', {modules_id: modules_id, id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();
            }
        });
    }

    function ext_get_entities_fields() {
        var entities_id = $('#entities_id').val();
        var action_type = $('#action_type').val();

        $('#rules_entities_fields').html('<div class="ajax-loading"></div>');

        $('#rules_entities_fields').load('<?php echo url_for(
            "ext/modules/subscribe_rules",
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