<?php

//check access
if ($app_user['group_id'] > 0 and !in_array(
        $app_module_path,
        ['ext/resource_timeline/view', 'ext/resource_timeline/add_item']
    )) {
    redirect_to('dashboard/access_forbidden');
}