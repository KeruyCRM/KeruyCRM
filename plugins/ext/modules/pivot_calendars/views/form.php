<?php
echo ajax_modal_template_header(TEXT_EXT_PIVOT_СALENDAR) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for('ext/pivot_calendars/reports', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body ajax-modal-width-790">


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
                    <label class="col-md-3 control-label" for="default_view"><?php
                        echo TEXT_EXT_DEFAULT_VIEW ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'default_view',
                            calendar::get_default_view_choices(),
                            $obj['default_view'],
                            ['class' => 'form-control input-medium']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="default_view"><?php
                        echo tooltip_icon(TEXT_EXT_CALENDAR_USE_VIEW_INFO) . TEXT_EXT_CALENDAR_USE_VIEW ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'view_modes[]',
                            calendar::get_view_modes_choices(),
                            $obj['view_modes'],
                            [
                                'class' => 'form-control chosen-select chosen-sortable',
                                'multiple' => 'multiple',
                                'chosen_order' => $obj['view_modes']
                            ]
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="event_limit"><?php
                        echo tooltip_icon(TEXT_EXT_EVENT_LIMIT_INFO) . TEXT_EXT_EVENT_LIMIT ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('event_limit', $obj['event_limit'], ['class' => 'form-control input-small']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="highlighting_weekends"><?php
                        echo TEXT_EXT_HIGHLIGHTING_WEEKENDS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'highlighting_weekends[]',
                            calendar::get_highlighting_weekends_choices(),
                            $obj['highlighting_weekends'],
                            ['class' => 'form-control input-large chosen-select', 'multiple' => 'multiple']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for=""><?php
                        echo tooltip_icon(TEXT_EXT_CALENDAR_WORK_HOURS_TIP) . TEXT_EXT_WORK_HOURS ?></label>
                    <div class="col-md-9">
                        <div class="input-group input-medium">
					<span class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</span>
                            <?php
                            echo input_tag(
                                'min_time',
                                $obj['min_time'],
                                [
                                    'class' => 'form-control datetimepicker-timeonly daterange-date-field',
                                    'autocomplete' => 'off',
                                    'data-minute-step' => 30,
                                    'placeholder' => TEXT_DATE_FROM
                                ]
                            ) ?>
                            <span class="input-group-addon">
						<i style="cursor:pointer" class="fa fa-refresh" aria-hidden="true" title="<?php
                        echo TEXT_EXT_RESET ?>" onClick="$('.daterange-date-field').val('')"></i>
					</span>
                            <?php
                            echo input_tag(
                                'max_time',
                                $obj['max_time'],
                                [
                                    'class' => 'form-control datetimepicker-timeonly daterange-date-field',
                                    'autocomplete' => 'off',
                                    'placeholder' => TEXT_DATE_TO
                                ]
                            ) ?>
                        </div>
                    </div>
                </div>

                <script>
                    $(".datetimepicker-timeonly").datetimepicker({
                        autoclose: true,
                        isRTL: App.isRTL(),
                        format: 'hh:ii',
                        startView: 1,
                        minuteStep: 30,
                        clearBtn: true,
                    });
                </script>


                <?php
                $choices = [];
                $choices['00:05:00'] = '00:05';
                $choices['00:10:00'] = '00:10';
                $choices['00:15:00'] = '00:15';
                $choices['00:30:00'] = '00:30';
                ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_EXT_TIME_SLOT_DURATION ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'time_slot_duration',
                            $choices,
                            $obj['time_slot_duration'],
                            ['class' => 'form-control input-small']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="display_legend"><?php
                        echo tooltip_icon(
                                TEXT_EXT_PIVOT_CALENDAR_DISPLAY_LEGEND_TIP
                            ) . TEXT_EXT_DISPLAY_LEGEND ?></label>
                    <div class="col-md-9">
                        <div class="checkbox-list"><label class="checkbox-inline"><?php
                                echo input_checkbox_tag('display_legend', '1', ['checked' => $obj['display_legend']]
                                ) ?></label></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="in_menu"><?php
                        echo tooltip_icon(TEXT_EXT_DISPLAY_IN_MAIN_MENU_TIP) . TEXT_EXT_DISPLAY_IN_MAIN_MENU ?></label>
                    <div class="col-md-9">
                        <div class="checkbox-list"><label class="checkbox-inline"><?php
                                echo input_checkbox_tag('in_menu', '1', ['checked' => $obj['in_menu']]) ?></label></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_SORT_ORDER ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-small']) ?>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="access">
                <p><?php
                    echo TEXT_EXT_USERS_GROUPS_INFO ?></p>

                <?php
                $users_groups = strlen($obj['users_groups']) ? json_decode($obj['users_groups'], true) : [];
                foreach (access_groups::get_choices(false) as $group_id => $group_name):
                    ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="allowed_groups"><?php
                            echo $group_name ?></label>
                        <div class="col-md-9">
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

                <p class="form-section"></p>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="enable_ical"><?php
                        echo tooltip_icon(TEXT_EXT_ENABLE_ICAL_URL_TIP) . TEXT_EXT_ENABLE_ICAL_URL ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'enable_ical',
                            ['0' => TEXT_NO, '1' => TEXT_YES],
                            $obj['enable_ical'],
                            ['class' => 'form-control input-small']
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

    });
</script>   