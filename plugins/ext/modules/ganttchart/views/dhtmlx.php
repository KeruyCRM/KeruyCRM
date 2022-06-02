<?php

$dhtmlxGanttVersion = '7.0.9';
$dhtmlxGanttType = (is_file('js/dhtmlxGantt/Pro/codebase/dhtmlxgantt.js') ? 'Pro' : 'Standard');
$skin = (strlen($reports['skin']) ? $reports['skin'] : 'meadow');

echo '
        <script src="js/dhtmlxGantt/' . $dhtmlxGanttType . '/codebase/dhtmlxgantt.js?v=' . $dhtmlxGanttVersion . '"></script>
        <link rel="stylesheet" href="js/dhtmlxGantt/' . $dhtmlxGanttType . '/codebase/skins/dhtmlxgantt_' . $skin . '.css?v=' . $dhtmlxGanttVersion . '">
        <script src="https://export.dhtmlx.com/gantt/api.js?v=' . $dhtmlxGanttVersion . '"></script>';
?>

<div class="noprint">
    <?php
    if (isset($_GET['path'])) {
        $path_info = items::parse_path($_GET['path']);
        $current_path = $_GET['path'];
        $current_entity_id = $path_info['entity_id'];
        $current_item_id = true; // set to true to set off default title
        $current_path_array = $path_info['path_array'];
        $app_breadcrumb = items::get_breadcrumb($current_path_array);

        $app_breadcrumb[] = ['title' => $reports['name']];

        require(component_path('items/navigation'));
    } else {
        $app_path = $reports['entities_id'];
    }

    reports::auto_create_parent_reports($fiters_reports_id);

    $include_paretn_filters = (isset($_GET['path']) ? false : true);

    $filters_preivew = new filters_preivew($fiters_reports_id, $include_paretn_filters);
    $filters_preivew->redirect_to = 'ganttreport' . $reports['id'];
    $filters_preivew->has_listing_configuration = false;

    if (isset($_GET['path'])) {
        $filters_preivew->path = $_GET['path'];
    } else {
        echo '<h3 class="page-title">' . $reports['name'] . '</h3>';
    }

    echo $filters_preivew->render();

    $is_read_only = false;

    //set read only if there is dynamic date
    if ($app_fields_cache[$reports['entities_id']][$reports['start_date']]['type'] == 'fieldtype_date_added' or $app_fields_cache[$reports['entities_id']][$reports['start_date']]['type'] == 'fieldtype_dynamic_date' or $app_fields_cache[$reports['entities_id']][$reports['end_date']]['type'] == 'fieldtype_dynamic_date') {
        $is_read_only = true;
    }

    //set read only for all parent records
    if ($app_entities_cache[$reports['entities_id']]['parent_id'] > 0 and !isset($_GET['path'])) {
        $is_read_only = true;
    }
    ?>
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

<div class="dhtmlx-gantt-menu">
    <?php
    $html = '
    	<button type="button" class="gantt-control" id="gantt_fullscreen"><i class="fa fa-arrows-alt"></i> ' . TEXT_EXT_FULL_SCREEN . '</button>
    	<button type="button" class="gantt-control" onclick="grid_dec()"><i class="fa fa-chevron-left"></i></button>
    	<button type="button" class="gantt-control" onclick="grid_inc()"><i class="fa fa-chevron-right"></i></button>
    	' . select_tag('gantt_scale', $choices, $reports['default_view'], ['class' => 'gantt-control']) . '     	
    	<button type="button" class="gantt-control" onclick="gantt.exportToPDF({header:\'<h4>&nbsp;&nbsp;&nbsp;&nbsp;' . addslashes(
            htmlspecialchars($reports['name'])
        ) . ' (' . format_date(time()) . ')</h4>\',name:\'' . app_remove_special_characters(
            $reports['name']
        ) . '.pdf\',locale:\'' . APP_LANGUAGE_SHORT_CODE . '\',skin:\'' . $skin . '\'})">' . TEXT_EXPORT . '  <i class="fa fa-file-pdf-o"></i></button>
    	<button type="button" class="gantt-control" onclick="gantt.exportToPNG({header:\'<h4>&nbsp;&nbsp;&nbsp;&nbsp;' . addslashes(
            htmlspecialchars($reports['name'])
        ) . ' (' . format_date(time()) . ')</h4>\',name:\'' . app_remove_special_characters(
            $reports['name']
        ) . '.png\',locale:\'' . APP_LANGUAGE_SHORT_CODE . '\',skin:\'' . $skin . '\'})"><i class="fa fa-picture-o"></i></button>
      ';

    echo $html;
    ?>
</div>

<!-- gantt container -->
<div id="dhtmlx_gantt" style="width:100%; height:500px; z-index: auto;"></div>

<script type="text/javascript">
    //start gantt cfg
    var gantt_load_url = "<?php echo url_for(
        'ext/ganttchart/dhtmlx',
        'action=load_data&id=' . _get::int('id') . '&path=' . $app_path
    ) ?>";
    var gantt_sd_field_id = "<?php echo $reports['start_date'] ?>"
    var gantt_ed_field_id = "<?php echo $reports['end_date'] ?>"
    var gantt_progress_field_id = "<?php echo $reports['progress'] ?>"
    var gantt_heading_field_id = "<?php echo $heading_field_id ?>"
    var gantt_fields_in_listing = "<?php echo $reports['fields_in_listing'] ?>"

    gantt.plugins({
        marker: true,
        fullscreen: true,
        auto_scheduling: true,
        critical_path: true,
    });

    gantt.i18n.setLocale('<?php echo APP_LANGUAGE_SHORT_CODE ?>');

    gantt.config.date_grid = "<?php echo ganttchart::get_date_grid_format($reports) ?>";
    gantt.config.date_format = "%Y-%m-%d %H:%i:%s"
    gantt.config.drag_progress = false;

    //check read only access
    gantt.config.readonly = <?php echo((ganttchart::users_has_full_access($reports) and !$is_read_only) ? 0 : 1)?>;

    //allows ordering
    gantt.config.order_branch = true;
    gantt.config.order_branch_free = true;

    gantt.config.auto_scheduling = <?php echo $reports['auto_scheduling'] ?>;
    gantt.config.highlight_critical_path = <?php echo $reports['highlight_critical_path'] ?>;

    //set default scale
    setScaleConfig($('#gantt_scale').val());

    gantt.config.duration_unit = '<?php echo ganttchart::get_duration_unit($reports) ?>';


    //add today marker
    var date_to_str = gantt.date.date_to_str(gantt.config.task_date);
    var today = new Date();
    gantt.addMarker({
        start_date: today,
        css: "today",
        text: "<?php echo TEXT_DATEPICKER_TODAY ?>",
        title: "<?php echo TEXT_DATEPICKER_TODAY ?>: <?php echo format_date(time()) ?>"
    });

    //default columns definition
    <?php echo ganttchart::get_columns_config($reports, $is_read_only) ?>

    //init gantt
    gantt.init("dhtmlx_gantt");
    gantt.load(gantt_load_url);

    //handle gantt processor to update any changes in gantt modification
    var dp = new gantt.dataProcessor("<?php echo url_for(
        'ext/ganttchart/dhtmlx',
        'action=save&id=' . _get::int('id') . '&path=' . $app_path
    ) ?>");
    dp.init(gantt);
    dp.setTransactionMode("POST");

    dp.attachEvent("onAfterUpdate", function (id, action, tid, response) {
        gantt.refreshData();
    })

    // when gantt is expended to full screen
    gantt.attachEvent("onExpand", function () {
        $('.header').hide();
    });

    // when gantt exited the full screen mode
    gantt.attachEvent("onCollapse", function () {
        $('.header').show();
    });

    //autocalculate proejct progress if exist projects
    gantt.attachEvent("onParse", function () {
        gantt.eachTask(function (task) {
            task.progress = calculateSummaryProgress(task);
        });
    });

    //build custom window popup
    var taskId = null;

    gantt.showLightbox = function (id) {

        if (gantt.config.readonly == 1) return false

        taskId = id;
        var task = gantt.getTask(id);

        var url_param = '';

        //if exist item then force update form
        if (taskId.length) {
            url_param += '&id=' + taskId;
        } else {
            url_param += '&start=' + task.start_date.getTime() + '&end=' + task.end_date.getTime();
        }

        open_dialog('<?php echo url_for(
            "items/form",
            "redirect_to=ganttreport" . $reports['id'] . "&path=" . $app_path
        ) ?>' + url_param)
    };

    //save item
    function gantt_save(data) {
        var task = gantt.getTask(taskId);

        var item = JSON.parse(data);

        $.each(item, function (field, value) {

            //hnader heading
            if (field == 'field_' + gantt_heading_field_id) {
                task.text = value;
            }

            //hander start date
            if (field == 'field_' + gantt_sd_field_id) {
                date = new Date(value * 1000);
                task.start_date = date;
            }

            //handler end date
            if (field == 'field_' + gantt_ed_field_id) {
                date = new Date(value * 1000);
                task.end_date = date;
            }

            //handler progress
            if (field == 'field_' + gantt_progress_field_id) {
                value = value.replace('%', '');
                task.progress = (value / 100);
            }

            //hander color
            if (field == 'color') {
                task.color = value;
            } else {
                task.color = '#e1ffd4';
            }

            //handler fileds in listing
            if (gantt_fields_in_listing.length) {
                fields_in_listing = gantt_fields_in_listing.split(',')
                fields_in_listing.forEach(function (v) {
                    if (field == 'field_' + v) {
                        eval('task.field_' + v + ' =	value');
                    }
                });
            }
        })

        //add item if new task or update it
        if (task.$new) {
            task.sort_order = item.id
            gantt.addTask(task, task.parent);
        } else {
            gantt.updateTask(task.id);
        }

        //render gant to refresh new data after update task
        gantt.render();
    }

    //cancle adding itme
    function gantt_cancel() {
        var task = gantt.getTask(taskId);

        if (!task.id.length)
            gantt.deleteTask(task.id);
    }

    //delete item
    function gantt_delete() {
        if (confirm('<?php echo TEXT_ARE_YOU_SURE?>')) {
            var task = gantt.getTask(taskId);
            gantt.deleteTask(task.id);

            $('#ajax-modal').modal('hide');
        }
    }

    //set gantt scale
    function setScaleConfig(value) {
        switch (value) {
            case "hour":
                gantt.config.scale_unit = "day";
                gantt.config.step = 1;
                gantt.config.date_scale = "%d %M";
                gantt.config.scale_height = 50;
                gantt.config.min_column_width = 30;
                gantt.templates.date_scale = null;

                gantt.config.subscales = [
                    {unit: "hour", step: 1, date: "%H"}
                ];
                break;
            case "day":
                gantt.config.scale_unit = "day";
                gantt.config.step = 1;
                gantt.config.date_scale = "%d %M";
                gantt.config.subscales = [];
                gantt.config.scale_height = 27;
                gantt.config.min_column_width = 50;
                gantt.templates.date_scale = null;
                break;
            case "week":
                var weekScaleTemplate = function (date) {
                    var dateToStr = gantt.date.date_to_str("%d %M");
                    var endDate = gantt.date.add(gantt.date.add(date, 1, "week"), -1, "day");
                    return dateToStr(date) + " - " + dateToStr(endDate);
                };

                gantt.config.scale_unit = "week";
                gantt.config.step = 1;
                gantt.templates.date_scale = weekScaleTemplate;
                gantt.config.subscales = [
                    {unit: "day", step: 1, date: "%D"}
                ];
                gantt.config.scale_height = 50;
                break;
            case "month":
                gantt.config.scale_unit = "month";
                gantt.config.date_scale = "%F, %Y";
                gantt.config.subscales = [
                    {unit: "day", step: 1, date: "%j, %D"}
                ];
                gantt.config.scale_height = 50;
                gantt.config.min_column_width = 50;
                gantt.templates.date_scale = null;
                break;
            case "year":
                gantt.config.scale_unit = "year";
                gantt.config.step = 1;
                gantt.config.date_scale = "%Y";
                gantt.config.min_column_width = 50;

                gantt.config.scale_height = 50;
                gantt.templates.date_scale = null;


                gantt.config.subscales = [
                    {unit: "month", step: 1, date: "%M"}
                ];
                break;
        }
    }

    $(function () {

        //handle scale change
        $('#gantt_scale').change(function () {
            setScaleConfig($(this).val())
            gantt.render();
        })

        //handle fullscreen click
        $('#gantt_fullscreen').click(function () {
            if (!gantt.getState().fullscreen) {
                gantt.expand();
            } else {
                gantt.collapse();
            }
        })

        //hande gantt height
        $('#dhtmlx_gantt').css('height', $(window).height() - 350);

    })

    function grid_inc() {
        gantt.config.grid_width += 10;
        gantt.render();

        $.ajax({
            method: "POST",
            url: "<?php echo url_for('ext/ganttchart/dhtmlx', 'action=set_grid_width&id=' . $reports['id']) ?>",
            data: {grid_width: gantt.config.grid_width}
        })
    }

    function grid_dec() {
        gantt.config.grid_width -= 10;
        gantt.render();

        $.ajax({
            method: "POST",
            url: "<?php echo url_for('ext/ganttchart/dhtmlx', 'action=set_grid_width&id=' . $reports['id']) ?>",
            data: {grid_width: gantt.config.grid_width}
        })
    }

    function calculateSummaryProgress(task) {
        if (task.type != gantt.config.types.project)
            return task.progress;
        var totalToDo = 0;
        var totalDone = 0;
        gantt.eachTask(function (child) {
            if (child.type != gantt.config.types.project) {
                totalToDo += child.duration;
                totalDone += (child.progress || 0) * child.duration;
            }
        }, task.id);
        if (!totalToDo) return 0;
        else return totalDone / totalToDo;
    }

</script>
