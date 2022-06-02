<?php

//check access
if ($app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}

$app_title = app_set_title(TEXT_CONFIGURATION);

$default_selector = ['1' => TEXT_YES, '0' => TEXT_NO];
 