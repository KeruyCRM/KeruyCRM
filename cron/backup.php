<?php

chdir(substr(__DIR__, 0, -5));

define('IS_CRON', true);

//load core
require('includes/application_core.php');

//load app lng
if (is_file($v = 'includes/languages/' . CFG_APP_LANGUAGE)) {
    require($v);
}

$app_users_cache = users::get_cache();

$backup = new backup(true);

$backup->create();
