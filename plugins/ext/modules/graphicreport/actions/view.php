<?php

//check if report exist
$reports_query = db_query("select * from app_ext_graphicreport where id='" . db_input((int)$_GET['id']) . "'");
if (!$reports = db_fetch_array($reports_query)) {
    redirect_to('dashboard/page_not_found');
}

if (!in_array($app_user['group_id'], explode(',', $reports['allowed_groups'])) and $app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}
  