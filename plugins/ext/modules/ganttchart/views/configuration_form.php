<?php
echo ajax_modal_template_header(TEXT_EXT_GANTTCHART_REPORT) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for('ext/ganttchart/configuration', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">


        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#settings" data-toggle="tab"><?php
                    echo TEXT_SETTINGS ?></a></li>
            <li><a href="#access" data-toggle="tab"><?php
                    echo TEXT_ACCESS ?></a></li>
            <li><a href="#dhtmlxGanttPRO" data-toggle="tab">dhtmlxGantt PRO</a></li>

        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_info">


                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_NAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-xlarge required']) ?>
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

            <div class="tab-pane fade" id="settings">

                <?php
                $format_list = [
                    'MM/DD/YYYY' => 'MM/DD/YY',
                    'MM/DD/YYYY H:i' => 'MM/DD/YY H:i',
                    'DD/MM/YYYY' => 'DD/MM/YY',
                    'DD/MM/YYYY H:i' => 'DD/MM/YY H:i',
                ];
                ?>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="gantt_date_format"><?php
                        echo TEXT_EXT_GANTT_DATE_FORMAT ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'gantt_date_format',
                            $format_list,
                            $obj['gantt_date_format'],
                            ['class' => 'form-control input-medium required']
                        ) ?>
                        <?php
                        echo tooltip_text(TEXT_EXT_GANTT_DATE_FORMAT_INFO) ?>
                    </div>
                </div>

                <?php
                $choices = [
                    'hour' => TEXT_EXT_HOUR,
                    'day' => TEXT_EXT_DAY,
                    'week' => TEXT_EXT_WEEK,
                    'month' => TEXT_EXT_MONTH,
                    'year' => TEXT_EXT_YEAR
                ];
                ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="type"><?php
                        echo TEXT_EXT_DEFAULT_VIEW ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'default_view',
                            $choices,
                            $obj['default_view'],
                            ['class' => 'form-control input-medium ']
                        ) ?>
                    </div>
                </div>


                <?php
                $choices = [
                    'terrace' => 'Terrace',
                    'skyblue' => 'Skyblue',
                    'meadow' => 'Meadow',
                    'broadway' => 'Broadway',
                    'material' => 'Material',
                    'contrast_white' => 'High contrast light'
                ];
                ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="skin"><?php
                        echo TEXT_SKIN ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag('skin', $choices, $obj['skin'], ['class' => 'form-control input-medium']) ?>
                    </div>
                </div>

                <?php
                $choices = [];
                $choices['start_date'] = TEXT_EXT_GANTT_START_DATE_SHORT;
                $choices['end_date'] = TEXT_EXT_GANTT_END_DATE_SHORT;
                $choices['duration'] = TEXT_EXT_GANTT_DURATION_SHORT;
                ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="type"><?php
                        echo TEXT_DISPLAY_FIELDS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'default_fields_in_listing[]',
                            $choices,
                            $obj['default_fields_in_listing'],
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                        ) ?>
                    </div>
                </div>

                <div id="entity_listing_fields"></div>


                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_EXT_GRID_WIDTH ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('grid_width', $obj['grid_width'], ['class' => 'form-control input-small']) ?>
                    </div>
                </div>

                <?php
                //$days_array = explode(',',str_replace('"','',TEXT_DATEPICKER_DAYS));
                //$days_list = array('6'=>$days_array[6],'0'=>$days_array[0]);
                ?>

                <!--div class="form-group">
    	<label class="col-md-3 control-label" for="allowed_groups"><?php
                //echo TEXT_EXT_GANTT_WEEKENDS ?></label>
      <div class="col-md-9">	
    	   <?php
                //echo select_checkboxes_tag('weekends',$days_list,$obj['weekends'],array('class'=>'form-control'))?>
         <?php
                //echo tooltip_text(TEXT_EXT_GANTT_WEEKENDS_INFO) ?>
      </div>			
    </div-->

            </div>
            <div class="tab-pane fade" id="access">
                <p><?php
                    echo TEXT_EXT_USERS_GROUPS_INFO ?></p>

                <?php
                foreach (access_groups::get_choices(false) as $group_id => $group_name): ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="allowed_groups"><?php
                            echo $group_name ?></label>
                        <div class="col-md-9">
                            <?php
                            echo select_tag(
                                'access[' . $group_id . ']',
                                ['' => '', 'view' => TEXT_VIEW_ONLY_ACCESS, 'full' => TEXT_FULL_ACCESS],
                                ganttchart::get_access_by_report($obj['id'], $group_id),
                                ['class' => 'form-control input-medium']
                            ) ?>
                        </div>
                    </div>
                <?php
                endforeach ?>

            </div>

            <div class="tab-pane fade" id="dhtmlxGanttPRO">
                <p><?php
                    echo TEXT_EXT_DHTMLXGANTT_PRO_INFO ?></p>

                <div class="form-group">
                    <label class="col-md-4 control-label">dhtmlxGantt PRO</label>
                    <div class="col-md-8">
                        <p class="form-control-static"><?php
                            echo app_render_status_label(
                                is_file('js/dhtmlxGantt/Pro/codebase/dhtmlxgantt.js') ? true : false
                            ) ?></p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="auto_scheduling"><?php
                        echo TEXT_EXT_AUTO_SCHEDULING ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'auto_scheduling',
                            ['0' => TEXT_NO, '1' => TEXT_YES],
                            $obj['auto_scheduling'],
                            ['class' => 'form-control input-small']
                        ) ?>
                        <?php
                        echo tooltip_text(TEXT_EXT_AUTO_SCHEDULING_INFO) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="highlight_critical_path"><?php
                        echo TEXT_EXT_HIGHLIGHT_CRITICAL_PATH ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'highlight_critical_path',
                            ['0' => TEXT_NO, '1' => TEXT_YES],
                            $obj['highlight_critical_path'],
                            ['class' => 'form-control input-small']
                        ) ?>
                        <?php
                        echo tooltip_text(TEXT_EXT_HIGHLIGHT_CRITICAL_PATH_INFO) ?>
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
            "ext/ganttchart/configuration",
            "action=get_entities_fields"
        )?>', {entities_id: entities_id, id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                entity_listing_fields()
            }
        });
    }

    function entity_listing_fields() {
        $('#entity_listing_fields').load('<?php echo url_for(
            "ext/ganttchart/configuration",
            "action=get_entity_listing_fields"
        )?>', {entities_id: $('#entities_id').val(), id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
            }
        });
    }


</script>   