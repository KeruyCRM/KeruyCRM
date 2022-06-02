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
    <link rel=stylesheet href="js/fullcalendar-3.10.0/fullcalendar.min.css" type="text/css">
    <link rel=stylesheet href="js/fullcalendar-3.10.0/fullcalendar.print.min.css" media="print">
    <link rel=stylesheet href="js/fullcalendar-scheduler-1.9.4/scheduler.min.css" type="text/css">
<?php
endif ?>


<?php
if (in_array($app_module_path, ['ext/pivotreports/view', 'dashboard/dashboard', 'dashboard/reports'])): ?>
    <link rel="stylesheet" type="text/css" href="js/pivottable-master/dist/pivot.css">
    <link rel="stylesheet" type="text/css" href="js/pivottable-master/dist/c3.min.css">
<?php
endif ?>


<?php
if ($app_module == 'timeline_reports' and $app_action == 'view'): ?>
    <link rel="stylesheet" type="text/css" href="js/timeline-2.9.1/timeline.css">
<?php
endif ?>

<link rel="stylesheet" type="text/css" href="js/app-chat/app-chat.css?v=1">

<link rel="stylesheet" type="text/css" href="js/app-mail/app-mail.css?v=1">

<!-- pivot table -->
<link href="js/webdatarocks/1.3.3/webdatarocks.min.css" rel="stylesheet"/>