<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for('ext/funnelchart/reports', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">


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

                <?php
                $choices = [
                    'funnel' => TEXT_EXT_FUNNEL_CHART,
                    'bars' => TEXT_EXT_BARS_CAHRT,
                    'table' => TEXT_EXT_TABLE
                ];
                ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_TYPE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag('type', $choices, $obj['type'], ['class' => 'form-control input-large']) ?>
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

            <div class="form-group">
                <label class="col-md-3 control-label" for="allowed_groups"><?php
                    echo tooltip_icon(TEXT_EXT_USERS_GROUPS_INFO) . TEXT_EXT_USERS_GROUPS ?></label>
                <div class="col-md-9">
                    <?php
                    echo select_tag(
                        'users_groups[]',
                        access_groups::get_choices(),
                        $obj['users_groups'],
                        ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
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

            $('#configuration_form').validate({
                ignore: '',
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
                "ext/funnelchart/reports",
                "action=get_entities_fields"
            )?>', {entities_id: entities_id, id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
                if (status == "error") {
                    $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
                } else {
                    ext_get_entities_fields_choices();
                    appHandleUniform();
                    jQuery(window).resize();
                }
            });
        }

        function ext_get_entities_fields_choices() {
            var fields_id = $('#group_by_field').val();

            if (fields_id > 0) {
                $('#fields_chocies_list').html('<div class="ajax-loading"></div>');

                $('#fields_chocies_list').load('<?php echo url_for(
                    "ext/funnelchart/reports",
                    "action=get_entities_fields_choices"
                )?>', {fields_id: fields_id, id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
                    if (status == "error") {
                        $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
                    } else {
                        appHandleUniform();
                        jQuery(window).resize();
                    }
                });
            }
        }

    </script>