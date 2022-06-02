<?php

$report_info_query = db_query(
    "select * from app_ext_report_page where id=" . _GET(
        'report_id'
    ) . " and (find_in_set(" . $app_user['group_id'] . ",users_groups) or find_in_set(" . $app_user['id'] . ",assigned_to))"
);
if (!$report_info = db_fetch_array($report_info_query)) {
    redirect_to('dashboard/page_not_found');
}
