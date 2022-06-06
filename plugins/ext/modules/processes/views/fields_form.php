<?php
echo ajax_modal_template_header(TEXT_HEADING_FIELD_INFO) ?>

<?php
echo form_tag(
    'entities_templates_fields',
    url_for(
        'ext/processes/fields',
        'action=save&actions_id=' . $app_actions_info['id'] . '&process_id=' . $app_process_info['id'] . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body ajax-modal-width-790">
        <div class="form-group">
            <label class="col-md-3 control-label" for="fields_id"><?php
                echo TEXT_SELECT_FIELD ?></label>
            <div class="col-md-9">
                <?php
                $exclude_types = [];
                $action_entity_id = processes::get_entity_id_from_action_type($app_actions_info['type']);

                if ($app_process_info['entities_id'] != $action_entity_id) {
                    $exclude_types[] = 'fieldtype_subentity_form';
                }

                $choices = processes::get_actions_fields_choices($action_entity_id, $exclude_types);


                echo select_tag(
                    'fields_id',
                    $choices,
                    $obj['fields_id'],
                    [
                        'class' => 'form-control input-xlarge required chosen-select',
                        'onChange' => 'render_template_field(this.value)'
                    ]
                )
                ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="fields_id"></label>
            <div class="col-md-9">
                <div id="template_field_container"></div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="fields_id"><?php
                echo tooltip_icon(TEXT_EXT_ENTER_MANUALLY_INFO) . TEXT_EXT_ENTER_MANUALLY ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'enter_manually',
                    ['0' => TEXT_NO, '1' => TEXT_YES, '2' => TEXT_EXT_YES_AND_USE_VALUE],
                    $obj['enter_manually'],
                    ['class' => 'form-control input-large required']
                ) ?>
                <div id="enter_manually_text"></div>
            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>


    $(function () {
        $('#entities_templates_fields').validate();

        render_template_field($('#fields_id').val());

        check_enter_manually();

        $('#enter_manually').change(function () {
            check_enter_manually();
        })
    });

    function check_enter_manually() {
        if ($('#enter_manually').val() == 1) {
            $('#template_field_container').hide();
        } else {
            $('#template_field_container').show();
        }
    }

    function render_template_field(fields_id) {
        $('#template_field_container').html('<div class="ajax-loading"></div>');

        $('#template_field_container').load('<?php echo url_for(
            "ext/processes/fields",
            "process_id=" . $app_process_info['id'] . "&actions_id=" . $app_actions_info['id'] . "&action=render_template_field"
        ) ?>', {fields_id: fields_id, id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
            }
        });
    }

</script>  