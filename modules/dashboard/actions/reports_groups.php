<?php

$app_reports_groups_id = (isset($_GET['id']) ? _get::int('id') : 0);

if ($app_reports_groups_id > 0) {
    $reports_groups_info_query = db_query(
        "select * from app_reports_groups where ((created_by = '" . $app_user['id'] . "' and is_common=0) or (" . ($app_user['group_id'] > 0 ? "(find_in_set(" . $app_user['group_id'] . ",users_groups) or find_in_set(" . $app_user['id'] . ",assigned_to)) and " : "") . " is_common=1)) and id='" . $app_reports_groups_id . "'",
        false
    );
    if (!$reports_groups_info = db_fetch_array($reports_groups_info_query)) {
        redirect_to('dashboard/access_forbidden');
    }
}


switch ($app_module_action) {
    case 'save':
        if (strlen($app_redirect_to)) {
            redirect_to($app_redirect_to);
        } else {
            redirect_to('dashboard/reports_groups', 'id=' . $app_reports_groups_id);
        }
        break;
}