<?php

//check access
if ($app_user['group_id'] > 0 and !in_array($app_module_path, ['ext/pivot_tables/view'])) {
    redirect_to('dashboard/access_forbidden');
}