<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for('ext/image_map/reports', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">


        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#access" data-toggle="tab"><?php
                    echo TEXT_ACCESS ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_info">

                <div class="form-group">
                    <label class="col-md-4 control-label" for="name"><?php
                        echo TEXT_NAME ?></label>
                    <div class="col-md-8">
                        <?php
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="in_menu"><?php
                        echo TEXT_IN_MENU ?></label>
                    <div class="col-md-8">
                        <div class="checkbox-list"><label class="checkbox-inline"><?php
                                echo input_checkbox_tag('in_menu', '1', ['checked' => $obj['in_menu']]) ?></label></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="type"><?php
                        echo TEXT_REPORT_ENTITY ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'entities_id',
                            entities::get_choices(),
                            $obj['entities_id'],
                            [
                                'class' => 'form-control input-large required',
                                'onChange' => 'ext_get_entities_fields(this.value)'
                            ]
                        ) ?>
                    </div>
                </div>

                <div id="reports_entities_fields"></div>

                <?php
                $choices = [
                    '6' => '1%',
                    '5' => '3%',
                    '4' => '6%',
                    '3' => '12%',
                    '2' => '25%',
                    '1' => '50%',
                    '0' => '100%',
                ];

                ?>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="type"><?php
                        echo TEXT_SCALE ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag('scale', $choices, $obj['scale'], ['class' => 'form-control input-small']) ?>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="access">
                <p><?php
                    echo TEXT_EXT_USERS_GROUPS_INFO ?></p>

                <?php
                $users_groups = strlen($obj['users_groups']) ? json_decode($obj['users_groups'], true) : [];
                foreach (access_groups::get_choices(false) as $group_id => $group_name): ?>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="allowed_groups"><?php
                            echo $group_name ?></label>
                        <div class="col-md-8">
                            <?php
                            echo select_tag(
                                'access[' . $group_id . ']',
                                ['' => '', 'view' => TEXT_VIEW_ONLY_ACCESS, 'full' => TEXT_FULL_ACCESS],
                                (isset($users_groups[$group_id]) ? $users_groups[$group_id] : ''),
                                ['class' => 'form-control input-medium']
                            ) ?>
                        </div>
                    </div>
                <?php
                endforeach ?>

            </div>
        </div>


    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {

        $('#configuration_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });

        ext_get_entities_fields($('#entities_id').val());

    });

    function ext_get_entities_fields(entities_id) {
        $('#reports_entities_fields').html('<div class="ajax-loading"></div>');

        $('#reports_entities_fields').load('<?php echo url_for(
            "ext/image_map/reports",
            "action=get_entities_fields"
        )?>', {entities_id: entities_id, id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();
            }
        });
    }


</script>   