<?php
if (in_array(
    $app_module_path,
    [
        'ext/resource_timeline/view',
        'ext/pivot_calendars/view',
        'ext/calendar/personal',
        'ext/calendar/public',
        'ext/calendar/report',
        'dashboard/dashboard',
        'dashboard/reports',
        'dashboard/reports_groups'
    ]
)): ?>
    <script type="text/javascript" src="js/fullcalendar-3.10.0/lib/moment.min.js"></script>
    <script type="text/javascript" src="js/fullcalendar-3.10.0/fullcalendar.min.js"></script>
    <script type="text/javascript" src="js/fullcalendar-scheduler-1.9.4/scheduler.min.js"></script>
    <?php
    if (is_file($language_file_path = 'js/fullcalendar-3.10.0/locale/' . TEXT_APP_LANGUAGE_SHORT_CODE . '.js')) {
        echo '<script type="text/javascript" src="' . $language_file_path . '"></script>';
    }
    ?>
<?php
endif ?>

<?php
if (in_array($app_module_path, ['ext/pivotreports/view', 'dashboard/dashboard', 'dashboard/reports'])): ?>
    <script type="text/javascript" src="js/PapaParse-master/papaparse.min.js"></script>
    <script type="text/javascript" src="js/pivottable-master/dist/pivot.js"></script>
    <script type="text/javascript" src="js/pivottable-master/dist/c3.min.js"></script>
    <script type="text/javascript" src="js/pivottable-master/dist/d3.min.js"></script>
    <script type="text/javascript" src="js/pivottable-master/dist/c3_renderers.js"></script>
    <script type="text/javascript" src="js/pivottable-master/dist/export_renderers.js"></script>
    <script type="text/javascript" src="<?php
    echo url_for(
        'ext/pivotreports/view',
        'id=' . (isset($_GET['id']) ? (int)$_GET['id'] : 0) . '&action=get_localization'
    ) ?>"></script>
<?php
endif ?>


<?php
if ($app_module == 'timeline_reports' and $app_action == 'view'): ?>
    <script type="text/javascript" src="js/timeline-2.9.1/timeline.js"></script>
<?php
endif ?>

<?php
if (in_array(
    $app_module_path,
    [
        'ext/graphicreport/view',
        'ext/funnelchart/view',
        'dashboard/dashboard',
        'dashboard/reports',
        'dashboard/reports_groups',
        'ext/pivot_tables/view'
    ]
)): ?>
    <script src="js/highcharts//4.2.2/highcharts.js"></script>
    <script src="js/highcharts//4.2.2/highcharts-more.js"></script>
    <script type="text/javascript" src="js/highcharts/modules/funnel.js"></script>
    <script type="text/javascript" src="js/highcharts/modules/exporting.js"></script>
<?php
endif ?>

<script type="text/javascript" src="js/templates/templates.js"></script>
<script type="text/javascript" src="js/timer/timer.js?v=2"></script>

<!-- chat -->
<script type="text/javascript" src="js/ion.sound-master/js/ion.sound.min.js"></script>
<script type="text/javascript" src="js/ion.sound-master/js/init.js.php"></script>
<script type="text/javascript" src="js/app-chat/app-chat.js?v=1"></script>
<?php
require(component_path('ext/app_chat/chat_button')) ?>


<!-- pivot table -->
<script src="js/webdatarocks/1.3.3/webdatarocks.toolbar.min.js"></script>
<script src="js/webdatarocks/1.3.3/webdatarocks.js"></script>
<script src="js/webdatarocks/1.3.3/webdatarocks.highcharts.js"></script>

<?php
//force print template
echo export_templates::force_print_template();
?>



