<?php

//check if report exist
$reports_query = db_query("select * from app_ext_pivot_calendars where id='" . db_input($_GET['calendars_id']) . "'");
if (!$reports = db_fetch_array($reports_query)) {
    redirect_to('dashboard/page_not_found');
}

if (!pivot_calendars::has_access($reports['users_groups'])) {
    redirect_to('dashboard/access_forbidden');
}