<?php

if ($app_user['group_id'] > 0 and $app_module_path != 'ext/track_changes/view') {
    redirect_to('dashboard/access_forbidden');
}