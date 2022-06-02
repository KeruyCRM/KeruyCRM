<h3 class="page-title"><?php
    echo TEXT_EXT_СALENDAR_PERSONAL ?></h3>

<?php
echo form_tag(
    'configuration_form',
    url_for('ext/calendar/configuration', 'action=save_personal'),
    ['class' => 'form-horizontal']
) ?>
<div class="form-body">

    <div class="tabbable tabbable-custom">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#alerts_settings" data-toggle="tab"><?php
                    echo TEXT_EXT_ALERTS_SETTINGS ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_info">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_PERSONAL_CALENDAR_DEFAULT_VIEW"><?php
                        echo TEXT_EXT_DEFAULT_VIEW ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[PERSONAL_CALENDAR_DEFAULT_VIEW]',
                            calendar::get_default_view_choices(),
                            CFG_PERSONAL_CALENDAR_DEFAULT_VIEW,
                            ['class' => 'form-control input-medium']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_PERSONAL_CALENDAR_VIEW_MODES"><?php
                        echo tooltip_icon(TEXT_EXT_CALENDAR_USE_VIEW_INFO) . TEXT_EXT_CALENDAR_USE_VIEW ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[PERSONAL_CALENDAR_VIEW_MODES][]',
                            calendar::get_view_modes_choices(),
                            CFG_PERSONAL_CALENDAR_VIEW_MODES,
                            [
                                'class' => 'form-control input-xlarge chosen-select chosen-sortable',
                                'multiple' => 'multiple',
                                'chosen_order' => CFG_PERSONAL_CALENDAR_VIEW_MODES
                            ]
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_PERSONAL_CALENDAR_HIGHLIGHTING_WEEKENDS"><?php
                        echo TEXT_EXT_HIGHLIGHTING_WEEKENDS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[PERSONAL_CALENDAR_HIGHLIGHTING_WEEKENDS][]',
                            calendar::get_highlighting_weekends_choices(),
                            CFG_PERSONAL_CALENDAR_HIGHLIGHTING_WEEKENDS,
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
                                'CFG[PERSONAL_CALENDAR_MIN_TIME]',
                                CFG_PERSONAL_CALENDAR_MIN_TIME,
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
                                'CFG[PERSONAL_CALENDAR_MAX_TIME]',
                                CFG_PERSONAL_CALENDAR_MAX_TIME,
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
                    $(function () {
                        $(".datetimepicker-timeonly").datetimepicker({
                            autoclose: true,
                            isRTL: App.isRTL(),
                            format: 'hh:ii',
                            startView: 1,
                            minuteStep: 30,
                            clearBtn: true,
                        });
                    })
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
                            'CFG[PERSONAL_CALENDAR_TIME_SLOT_DURATION]',
                            $choices,
                            CFG_PERSONAL_CALENDAR_TIME_SLOT_DURATION,
                            ['class' => 'form-control input-small']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="allowed_groups"><?php
                        echo TEXT_EXT_USERS_GROUPS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'allowed_groups[]',
                            access_groups::get_choices(false),
                            implode(',', calendar::get_personal_access()),
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                        ) ?>
                        <?php
                        echo tooltip_text(TEXT_EXT_СALENDAR_PERSONAL_ACCESS) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="enable_ical"><?php
                        echo tooltip_icon(TEXT_EXT_ENALBE_ICAL_URL_TIP) . TEXT_EXT_ENALBE_ICAL_URL ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[PERSONAL_CALENDAR_ICAL]',
                            ['0' => TEXT_NO, '1' => TEXT_YES],
                            CFG_PERSONAL_CALENDAR_ICAL,
                            ['class' => 'form-control input-small']
                        ) ?>
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" id="alerts_settings">

                <?php
                $default_selector = ['1' => TEXT_YES, '0' => TEXT_NO]; ?>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_PERSONAL_CALENDAR_SEND_ALERTS"><?php
                        echo TEXT_EXT_SEND_ALERTS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[PERSONAL_CALENDAR_SEND_ALERTS]',
                            $default_selector,
                            CFG_PERSONAL_CALENDAR_SEND_ALERTS,
                            ['class' => 'form-control input-small']
                        ); ?>
                        <?php
                        echo tooltip_text(
                            TEXT_NOTIFICATIONS_SCHEDULE_TIP . '<br>' . DIR_FS_CATALOG . 'cron/calendar.php'
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_PERSONAL_CALENDAR_ALERTS_TIME"><?php
                        echo TEXT_EXT_ALERTS_TIME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[PERSONAL_CALENDAR_ALERTS_TIME]',
                            app_get_hours_choices(),
                            CFG_PERSONAL_CALENDAR_ALERTS_TIME,
                            ['class' => 'form-control input-small']
                        ); ?>
                        <?php
                        echo tooltip_text(TEXT_EXT_CALENDAR_ALERTS_TIME_INFO) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_PERSONAL_CALENDAR_ALERTS_SUBJECT"><?php
                        echo TEXT_EXT_ALERTS_EMAIL_SUBJECT ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'CFG[PERSONAL_CALENDAR_ALERTS_SUBJECT]',
                            CFG_PERSONAL_CALENDAR_ALERTS_SUBJECT,
                            ['class' => 'form-control input-large']
                        ); ?>
                        <?php
                        echo tooltip_text(TEXT_DEFAULT . ': ' . TEXT_EXT_PERSONAL_CALENDAR_ALERTS_SUBJECT) ?>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

<?php
echo submit_tag(TEXT_BUTTON_SAVE) ?>
</form> 