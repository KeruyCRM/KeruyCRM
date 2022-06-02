<?php

if (isset($_GET['path'])) {
    $path_info = items::parse_path($_GET['path']);
    $current_path = $_GET['path'];
    $current_entity_id = $path_info['entity_id'];
    $current_item_id = true; // set to true to set off default title     
    $current_path_array = $path_info['path_array'];
    $app_breadcrumb = items::get_breadcrumb($current_path_array);

    require(component_path('items/navigation'));
}
?>

<h3 class="page-title"><?php
    echo $reports['name'] ?></h3>

<?php

$filters_preivew = new filters_preivew($fiters_reports_id);
$filters_preivew->redirect_to = 'timelinereport' . $_GET['id'];
$filters_preivew->has_listing_configuration = false;

if (isset($_GET['path'])) {
    $filters_preivew->path = $_GET['path'];
    $filters_preivew->include_paretn_filters = false;
}

echo $filters_preivew->render();

echo timeline_reports::get_css($reports);

$json = timeline_reports::get_json($reports, $fiters_reports_id, $app_path);

//echo  $json;

?>

<div id="timeline_report">
    <div class="fa fa-spinner fa-spin"></div>
</div>

<script type="text/javascript">

    if (typeof links === 'undefined') {
        links = {};
        links.locales = {};
    } else if (typeof links.locales === 'undefined') {
        links.locales = {};
    }

    //English ===================================================
    links.locales['custom'] = {
        'MONTHS': [<?php echo TEXT_DATEPICKER_MONTHS ?>],
        'MONTHS_SHORT': [<?php echo TEXT_DATEPICKER_MONTHSSHORT ?>],
        'DAYS': [<?php echo TEXT_DATEPICKER_DAYS ?>],
        'DAYS_SHORT': [<?php echo TEXT_DATEPICKER_DAYSSHORT ?>],
        'ZOOM_IN': "<?php echo addslashes(TEXT_ZOOM_IN) ?>",
        'ZOOM_OUT': "<?php echo addslashes(TEXT_ZOOM_OUT) ?>",
        'MOVE_LEFT': "<?php echo addslashes(TEXT_MOVE_LEFT) ?>",
        'MOVE_RIGHT': "<?php echo addslashes(TEXT_MOVE_RIGHT) ?>",
        'NEW': "New",
        'CREATE_NEW_EVENT': "Create new event"
    };

    var timeline;
    var data;

    // Called when the Visualization API is loaded.
    function timeLineDrawVisualization() {
        // Create a JSON data table

        data = <?php echo $json ?>;

        // specify options
        var options = {
            'width': '100%',
            'height': 'auto',
            'minHeight': '350',
            'editable': false,   // enable dragging and editing events
            'style': 'box',
            'animate': false,
            'showNavigation': false,
            'zoomMin': 86400000,
            'zoomMax': 31104000000,
            'showNavigation': true,
        };

        options.locale = "custom";

        // Instantiate our timeline object.
        timeline = new links.Timeline(document.getElementById('timeline_report'), options);

        function onRangeChanged(properties) {
            //console.log("string",1,properties.start + ' - ' + properties.end);
        }

        // attach an event listener using the links events handler
        links.events.addListener(timeline, 'rangechanged', onRangeChanged);

        // Draw our timeline with the created data and options
        timeline.draw(data);

        //set range
        var newStartDate = new Date(<?php echo date('Y') ?>, <?php echo(date('n') - 1) ?>, 1);
        var newEndDate = new Date(<?php echo date('Y') ?>, <?php echo(date('n') + 1) ?>, 1);
        timeline.setVisibleChartRange(newStartDate, newEndDate);

        timeline.setVisibleChartRangeNow();
    }

    function timeLineItemPopover(obj) {
        $(obj).popover({html: true, placement: 'left', container: 'body'}).popover('show');
    }

    function timeLineItemPopoverHide(obj) {
        $(obj).popover('hide');
    }

    $(function () {
        timeLineDrawVisualization();
    })

</script>