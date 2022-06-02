<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'actions_form',
    url_for(
        'ext/processes/actions',
        'action=save&process_id=' . $app_process_info['id'] . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body ajax-modal-width-790">

        <div class="form-group">
            <label class="col-md-3 control-label" for="is_active"><?php
                echo TEXT_IS_ACTIVE ?></label>
            <div class="col-md-9">
                <p class="form-control-static"><?php
                    echo input_checkbox_tag(
                        'is_active',
                        $obj['is_active'],
                        ['checked' => ($obj['is_active'] == 1 ? 'checked' : '')]
                    ) ?></p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="type"><?php
                echo TEXT_TYPE ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'type',
                    processes::get_actions_types_choices($app_process_info['entities_id']),
                    $obj['type'],
                    ['class' => 'form-control required']
                ) ?>
            </div>
        </div>

        <div id="actions_type_settings"></div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="description"><?php
                echo TEXT_NOTE ?></label>
            <div class="col-md-9">
                <?php
                echo textarea_tag('description', $obj['description'], ['class' => 'form-control']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="sort_order"><?php
                echo TEXT_SORT_ORDER ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-small required number']
                ) ?>
            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#actions_form').validate();

        check_action_type();

        $('#type').change(function () {
            check_action_type();
        })
    });

    function check_action_type() {
        $('#actions_type_settings').load('<?php echo url_for(
            "ext/processes/actions",
            "process_id=" . $app_process_info['id'] . "&entities_id=" . $app_process_info['entities_id'] . "&action=actions_type_settings"
        ) ?>', {type: $('#type').val(), id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                $(window).resize();
            }
        });
    }

</script>   


