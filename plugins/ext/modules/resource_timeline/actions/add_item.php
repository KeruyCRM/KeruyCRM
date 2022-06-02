<?php

//check if report exist
$reports_query = db_query("select * from app_ext_resource_timeline where id='" . db_input(_GET('id')) . "'");
if (!$reports = db_fetch_array($reports_query)) {
    redirect_to('dashboard/page_not_found');
}

if (!resource_timeline::has_access($reports['users_groups'])) {
    redirect_to('dashboard/access_forbidden');
}