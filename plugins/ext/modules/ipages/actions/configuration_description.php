<?php

//check access
if (!in_array($app_user['id'], explode(',', CFG_IPAGES_ACCESS_TO_USERS)) and !in_array(
        $app_user['group_id'],
        explode(',', CFG_IPAGES_ACCESS_TO_USERS_GROUP)
    ) and $app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}


$ipage_info_query = db_query("select * from app_ext_ipages where id='" . $_GET['id'] . "'");
if (!$ipage_info = db_fetch_array($ipage_info_query)) {
    redirect_to('ext/ipages/configuration');
}