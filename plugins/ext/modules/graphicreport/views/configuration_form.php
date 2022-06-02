<?php
echo ajax_modal_template_header(TEXT_EXT_GRAPHIC_REPORT) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for('ext/graphicreport/configuration', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
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
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_NAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="type"><?php
                        echo TEXT_EXT_CHART_TYPE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'chart_type',
                            ['line' => TEXT_EXT_CHART_TYPE_LINE, 'column' => TEXT_EXT_CHART_TYPE_COLUMN],
                            $obj['chart_type'],
                            ['class' => 'form-control input-large required']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="show_totals"><?php
                        echo TEXT_EXT_SHOW_TOTALS_IN_CHART ?></label>
                    <div class="col-md-9 form-control-static">
                        <?php
                        echo input_checkbox_tag('show_totals', 1, ['checked' => $obj['show_totals']]) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="hide_zero"><?php
                        echo TEXT_EXT_HIDE_ZERO_VALUES ?></label>
                    <div class="col-md-9 form-control-static">
                        <?php
                        echo input_checkbox_tag('hide_zero', 1, ['checked' => $obj['hide_zero']]) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="type"><?php
                        echo TEXT_PERIOD ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'period',
                            [
                                'hourly' => TEXT_HOURLY,
                                'daily' => TEXT_DAILY,
                                'monthly' => TEXT_MONTHLY,
                                'yearly' => TEXT_YEARLY
                            ],
                            $obj['period'],
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
                            [
                                'class' => 'form-control input-large required',
                                'onChange' => 'ext_get_entities_fields(this.value)'
                            ]
                        ) ?>
                    </div>
                </div>


                <div id="reports_entities_fields"></div>

            </div>
            <div class="tab-pane fade" id="access">
                <p><?php
                    echo TEXT_EXT_USERS_GROUPS_INFO ?></p>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="allowed_groups"><?php
                        echo TEXT_EXT_USERS_GROUPS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'allowed_groups[]',
                            access_groups::get_choices(false),
                            $obj['allowed_groups'],
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                        ) ?>
                    </div>
                </div>

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
            ignore: '',
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });

        ext_get_entities_fields($('#entities_id').val());

    });

    function ext_get_entities_fields(entities_id) {
        $('#reports_entities_fields').html('<div class="ajax-loading"></div>');

        $('#reports_entities_fields').load('<?php echo url_for(
            "ext/graphicreport/configuration",
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