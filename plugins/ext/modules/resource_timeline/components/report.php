<?php
echo resource_timeline::render_legend($reports) ?>

<div id="resource_timeline<?php
echo $reports['id'] ?>"></div>

<script>

    $(function () {

        $('#resource_timeline<?php echo $reports['id'] ?>').fullCalendar({
            header: {
                left: 'today prev,next',
                center: 'title',
                right: '<?php echo resource_timeline::get_view_modes($reports) ?>'
            },
            views: {
                timelineYear: {
                    buttonText: '<?php echo TEXT_EXT_YEAR ?>',
                    type: 'timeline',
                    duration: {year: 1},
                    slotDuration: {months: 1}
                },
                timelineYear2: {
                    buttonText: '<?php echo TEXT_EXT_YEAR . '(2)' ?>',
                    type: 'timeline',
                    duration: {year: 2},
                    slotDuration: {months: 1}
                },
                timelineMonth2: {
                    buttonText: '<?php echo TEXT_EXT_MONTH . ' (2)' ?>',
                    type: 'timeline',
                    duration: {months: 2},
                    slotDuration: {day: 1}
                },
                timelineMonth3: {
                    buttonText: '<?php echo TEXT_EXT_MONTH . ' (3)' ?>',
                    type: 'timeline',
                    duration: {months: 3},
                    slotDuration: {day: 1}
                },
            },
            slotDuration: '<?php echo resource_timeline::get_slot_duration($reports) ?>',
            height: <?php echo resource_timeline::get_height() ?>,
            defaultView: '<?php echo resource_timeline::get_default_view($reports) ?>',
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            resources: '<?php echo url_for(
                'ext/resource_timeline/view',
                'action=get_resources&id=' . $reports['id']
            ) ?>',
            events: '<?php echo url_for('ext/resource_timeline/view', 'action=get_events&id=' . $reports['id']) ?>',
            resourceColumns: <?php echo resource_timeline::get_resources_columns($reports) ?>,
            resourceAreaWidth: "<?php echo resource_timeline::get_area_width($reports) ?>",
            editable: <?php echo (int)resource_timeline::has_access($reports['users_groups'], 'full') ?>,
            eventResourceEditable: false,
            selectable: <?php echo resource_timeline::is_selectable($reports) ?>,
            selectHelper: true,
            timeFormat: 'H:mm',
            resourceRender: function (resourceObj, $td) {
                if (resourceObj.popup.length > 0) {
                    $td.eq(0).find('.fc-cell-content')
                        .prepend(
                            $('<i class="fa fa-info-circle" aria-hidden="true"></i>').popover({
                                content: resourceObj.popup,
                                trigger: 'hover',
                                placement: 'bottom',
                                container: 'body',
                                html: true,
                            })
                        );
                }
            },
            eventClick: function (calEvent, jsEvent, view) {
                $(this).attr('target', '_new')
            },
            eventMouseover: function (calEvent, jsEvent, view) {
                if ((['timelineMonth', 'timelineMonth2', 'timelineMonth3', 'timelineYear', 'timelineYear2'].includes(view.name))
                    && (calEvent.title.length > 10 || calEvent.description.length > 0) && $('.popover').length == 0)
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
            eventResize: function (event, delta, revertFunc) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo url_for('ext/resource_timeline/view', 'action=resize&id=' . $reports['id'])?>",
                    data: {id: event.item_id, reports_entities_id: event.reports_entities_id, end: event.end.format()}
                });
            },
            eventDrop: function (event, delta, revertFunc) {
                if (event.end) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo url_for('ext/resource_timeline/view', 'action=drop&id=' . $reports['id'])?>",
                        data: {
                            id: event.item_id,
                            reports_entities_id: event.reports_entities_id,
                            start: event.start.format(),
                            end: event.end.format()
                        }
                    });
                } else {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo url_for('ext/resource_timeline/view', 'action=drop&id=' . $reports['id'])?>",
                        data: {
                            id: event.item_id,
                            reports_entities_id: event.reports_entities_id,
                            start: event.start.format()
                        }
                    });
                }

                $('.popover').remove();
            },
            select: function (start, end, jsEvent, view, resource) {
                open_dialog('<?php echo resource_timeline::get_add_url(
                    $reports
                ) ?>' + '&start=' + start + '&end=' + end + '&view_name=' + view.name + '&resource_id=' + resource.id)
            },

        });

    });

</script>
