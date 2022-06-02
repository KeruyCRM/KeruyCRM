<?php

if (strstr($app_redirect_to, 'graphicreport')) {
    $id = str_replace('graphicreport', '', $app_redirect_to);
    redirect_to('ext/graphicreport/view', 'id=' . $id . (strlen($app_path) ? '&path=' . $app_path : ''));
}

if (strstr($app_redirect_to, 'calendarreport')) {
    $id = str_replace('calendarreport', '', $app_redirect_to);
    redirect_to('ext/calendar/report', 'id=' . $id . (strlen($app_path) ? '&path=' . $app_path : ''));
}

if (strstr($app_redirect_to, 'pivotreports')) {
    $id = str_replace('pivotreports', '', $app_redirect_to);
    redirect_to('ext/pivotreports/view', 'id=' . $id);
}

if (strstr($app_redirect_to, 'pivot_table')) {
    $id = str_replace('pivot_table', '', $app_redirect_to);
    redirect_to('ext/pivot_tables/view', 'id=' . $id);
}

if (strstr($app_redirect_to, 'timelinereport')) {
    $id = str_replace('timelinereport', '', $app_redirect_to);
    redirect_to('ext/timeline_reports/view', 'id=' . $id . (strlen($app_path) ? '&path=' . $app_path : ''));
}

if (strstr($app_redirect_to, 'ganttreport')) {
    $id = str_replace('ganttreport', '', $app_redirect_to);
    redirect_to('ext/ganttchart/dhtmlx', 'id=' . $id . (strlen($app_path) ? '&path=' . $app_path : ''));
}

if (strstr($app_redirect_to, 'funnelchart')) {
    $id = str_replace('funnelchart', '', $app_redirect_to);
    redirect_to('ext/funnelchart/view', 'id=' . $id . (strlen($app_path) ? '&path=' . $app_path : ''));
}

if (strstr($app_redirect_to, 'kanban')) {
    $id = str_replace('kanban', '', $app_redirect_to);
    redirect_to('ext/kanban/view', 'id=' . $id . (strlen($app_path) ? '&path=' . $app_path : ''));
}

if (strstr($app_redirect_to, 'image_map')) {
    $id = str_replace('image_map', '', $app_redirect_to);
    redirect_to('ext/image_map/view', 'id=' . $id);
}

if (strstr($app_redirect_to, 'map_reports')) {
    $id = str_replace('map_reports', '', $app_redirect_to);
    redirect_to('ext/map_reports/view', 'id=' . $id . (strlen($app_path) ? '&path=' . $app_path : ''));
}

