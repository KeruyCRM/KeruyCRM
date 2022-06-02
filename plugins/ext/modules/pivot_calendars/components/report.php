<?php
echo pivot_calendars::render_legend($reports) ?>

<div id="calendar_loading<?php
echo $reports['id'] ?>" class="loading_data"></div>
<div id="calendar<?php
echo $reports['id'] ?>"></div>

<?php
//highlighting_weekends
echo calendar::render_highlighting_weekends($reports['highlighting_weekends']);

if (pivot_calendars::has_access($reports['users_groups'], 'full')):

    $count_entities_with_access = 0;
    $reports_entities_query = db_query(
        "select ce.*, e.name from app_ext_pivot_calendars_entities ce, app_entities e where e.id=ce.entities_id and ce.calendars_id='" . $reports['id'] . "' order by e.name"
    );
    while ($reports_entities_info = db_fetch_array($reports_entities_query)) {
        if (users::has_users_access_name_to_entity('create', $reports_entities_info['entities_id'])) {
            $reports_entities = $reports_entities_info;
            $count_entities_with_access++;
        }

        echo pivot_calendars::get_css($reports_entities_info);
    }

    if ($count_entities_with_access == 1) {
//create default entity report for logged user
        $reports_info_query = db_query(
            "select * from app_reports where entities_id='" . db_input(
                $reports_entities['entities_id']
            ) . "' and reports_type='pivot_calendars" . $reports_entities['id'] . "'"
        );
        $reports_info = db_fetch_array($reports_info_query);

        $entity_info = db_find('app_entities', $reports_entities['entities_id']);

        if ($entity_info['parent_id'] > 0) {
            $add_url = url_for(
                "reports/prepare_add_item",
                "redirect_to=pivot_calendars" . $reports_entities['id'] . "&reports_id=" . $reports_info['id']
            );
        } else {
            $add_url = url_for(
                "items/form",
                "redirect_to=pivot_calendars" . $reports_entities['id'] . "&path=" . $reports_entities['entities_id']
            );
        }
    } else {
        $add_url = url_for('ext/pivot_calendars/add_item', 'calendars_id=' . $reports['id']);
    }
    ?>

    <script>

        <?php echo holidays::render_js_holidays() ?>

        var allow_add_item = 1;

        $(document).ready(function () {

            $('#calendar<?php echo $reports['id'] ?>').fullCalendar({
                minTime: '<?php echo(strlen($reports['min_time']) ? $reports['min_time'] : "00:00") ?>',
                maxTime: '<?php echo(strlen($reports['max_time']) ? $reports['max_time'] : "24:00") ?>',
                slotDuration: '<?php echo(strlen(
                    $reports['time_slot_duration']
                ) ? $reports['time_slot_duration'] : '00:30:00') ?>',
                customButtons: {
                    printButton: {
                        icon: 'fa fa-print',
                        click: function () {
                            window.print();
                        }
                    },
                    calendarButton: {
                        icon: 'fa fa-calendar',
                        click: function () {

                        }
                    }
                },
                header: {
                    left: 'prev,next today calendarButton',
                    center: 'title',
                    right: '<?php echo calendar::get_view_modes($reports) ?>'
                },

                views: {
                    year: {
                        buttonText: '<?php echo TEXT_EXT_YEAR ?>',
                        type: 'timeline',
                        duration: {year: 1},
                        slotDuration: {months: 1},
                        slotLabelFormat: 'MMMM',
                    },
                    agenda: {
                        eventLimit: <?php echo($reports['event_limit'] > 0 ? $reports['event_limit'] : 6) ?> // adjust to 6 only for agendaWeek/agendaDay
                    }
                },

                schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',

                defaultDate: '<?php echo date("Y-m-d") ?>',
                firstDay: '<?php echo CFG_APP_FIRST_DAY_OF_WEEK ?>',
                defaultView: '<?php echo(strlen($reports['default_view']) ? $reports['default_view'] : 'month') ?>',
                timezone: false,
                selectable: true,
                selectHelper: true,
                editable: true,
                eventLimit: true, // allow "more" link when too many events
                timeFormat: 'H:mm',
                slotLabelFormat: 'H:mm',

                select: function (start, end, jsEvent, view) {
                    if (allow_add_item == 1) {
                        open_dialog('<?php echo $add_url ?>' + '&start=' + start + '&end=' + end + '&view_name=' + view.name)
                    }
                },
                eventClick: function (calEvent, jsEvent, view) {
                    $(this).attr('target', '_new')
                },
                eventResize: function (event, delta, revertFunc) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo url_for('ext/pivot_calendars/view', 'action=resize&id=' . $reports['id']) ?>",
                        data: {id: event.id, entities_id: event.entities_id, end: event.end.format()}
                    });
                },
                eventDrop: function (event, delta, revertFunc) {
                    if (event.end) {
                        $.ajax({
                            type: "POST",
                            url: "<?php echo url_for(
                                'ext/pivot_calendars/view',
                                'action=drop&id=' . $reports['id']
                            ) ?>",
                            data: {
                                id: event.id,
                                entities_id: event.entities_id,
                                start: event.start.format(),
                                end: event.end.format()
                            }
                        });
                    } else {
                        $.ajax({
                            type: "POST",
                            url: "<?php echo url_for(
                                'ext/pivot_calendars/view',
                                'action=drop&id=' . $reports['id']
                            ) ?>",
                            data: {id: event.id, entities_id: event.entities_id, start: event.start.format()}
                        });
                    }

                    $('.popover').remove();
                },
                eventMouseover: function (calEvent, jsEvent, view) {
                    if (calEvent.title.length > 23 || calEvent.description.length > 0 && $('.popover').length == 0)
                        $(this).popover({
                            html: true,
                            title: calEvent.title,
                            content: calEvent.description,
                            placement: 'top',
                            container: 'body'
                        }).popover('show');
                },
                eventMouseout: function (calEvent, jsEvent, view) {
                    $(this).popover('hide');
                },
                events: {
                    url: '<?php echo url_for(
                        "ext/pivot_calendars/view",
                        "action=get_events&mode=full&id=" . $reports["id"]
                    ) ?>',
                    error: function () {
                        alert('<?php echo TEXT_ERROR_LOADING_DATA ?>')
                    }
                },
                loading: function (bool) {
                    $('#calendar_loading<?php echo $reports['id'] ?>').toggle(bool);


                    if (!bool) {
                        fc_calendar_button($(this).attr('id'))
                    }

                },

                //handle holidays
                eventAfterAllRender: function (view) {
                    for (var key in holidays) {
                        if (view.name == 'month') {
                            $("td[data-date=" + key + "]").each(function () {
                                $(this).addClass('holiday');
                                $('span', this).attr('title', holidays[key])
                            });
                        } else if (view.name == 'listMonth') {
                            $("tr[data-date=" + key + "]").each(function () {
                                $(this).addClass('holiday');
                                $('span', this).attr('title', holidays[key])
                            });
                        } else {
                            $("th[data-date=" + key + "]").each(function () {
                                $(this).addClass('holiday');
                                $('span', this).attr('title', holidays[key])
                            });
                        }
                    }
                }

            });

        });

    </script>

<?php
else: ?>


    <?php
    $reports_entities_query = db_query(
        "select ce.*, e.name from app_ext_pivot_calendars_entities ce, app_entities e where e.id=ce.entities_id and ce.calendars_id='" . $reports['id'] . "' order by e.name"
    );
    while ($reports_entities_info = db_fetch_array($reports_entities_query)) {
        echo pivot_calendars::get_css($reports_entities_info);
    }
    ?>

    <script>

        $(document).ready(function () {

            $('#calendar<?php echo $reports['id'] ?>').fullCalendar({
                minTime: '<?php echo(strlen($reports['min_time']) ? $reports['min_time'] : "00:00") ?>',
                maxTime: '<?php echo(strlen($reports['max_time']) ? $reports['max_time'] : "24:00") ?>',
                slotDuration: '<?php echo(strlen(
                    $reports['time_slot_duration']
                ) ? $reports['time_slot_duration'] : '00:30:00') ?>',
                customButtons: {
                    printButton: {
                        text: '',
                        icon: 'fa fa-print',
                        click: function () {
                            window.print();
                        }
                    },
                    calendarButton: {
                        icon: 'fa fa-calendar',
                        click: function () {

                        }
                    }
                },
                header: {
                    left: 'prev,next today calendarButton',
                    center: 'title',
                    right: '<?php echo calendar::get_view_modes($reports) ?>'
                },

                views: {
                    year: {
                        buttonText: '<?php echo TEXT_EXT_YEAR ?>',
                        type: 'timeline',
                        duration: {year: 1},
                        slotDuration: {months: 1},
                        slotLabelFormat: 'MMMM',
                    },
                    agenda: {
                        eventLimit: <?php echo($reports['event_limit'] > 0 ? $reports['event_limit'] : 6) ?> // adjust to 6 only for agendaWeek/agendaDay
                    }
                },

                schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',

                defaultDate: '<?php echo date("Y-m-d") ?>',
                firstDay: '<?php echo CFG_APP_FIRST_DAY_OF_WEEK ?>',
                defaultView: '<?php echo(strlen($reports['default_view']) ? $reports['default_view'] : 'month') ?>',
                timezone: false,
                selectable: false,
                selectHelper: false,
                editable: false,
                eventLimit: true, // allow "more" link when too many events
                timeFormat: 'H:mm',
                slotLabelFormat: 'H:mm',

                eventMouseover: function (calEvent, jsEvent, view) {
                    if (calEvent.title.length > 23)
                        $(this).popover({
                            html: true,
                            title: calEvent.title,
                            content: calEvent.description,
                            placement: 'top',
                            container: 'body'
                        }).popover('show');
                },
                eventMouseout: function (calEvent, jsEvent, view) {
                    $(this).popover('hide');
                },
                events: {
                    url: '<?php echo url_for(
                        "ext/pivot_calendars/view",
                        "action=get_events&mode=view&id=" . $reports["id"]
                    ) ?>',
                    error: function () {
                        alert('<?php echo TEXT_ERROR_LOADING_DATA ?>')
                    }
                },
                loading: function (bool) {
                    $('#calendar_loading<?php echo $reports['id'] ?>').toggle(bool);

                    if (!bool) {
                        fc_calendar_button($(this).attr('id'))
                    }

                }

            });

        });

    </script>

<?php
endif ?>

<br>