<?php

//check access
if ($app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}

if (!defined('CFG_PERSONAL_CALENDAR_SEND_ALERTS')) {
    define('CFG_PERSONAL_CALENDAR_SEND_ALERTS', 0);
}
if (!defined('CFG_PERSONAL_CALENDAR_ALERTS_TIME')) {
    define('CFG_PERSONAL_CALENDAR_ALERTS_TIME', '');
}
if (!defined('CFG_PERSONAL_CALENDAR_ALERTS_SUBJECT')) {
    define('CFG_PERSONAL_CALENDAR_ALERTS_SUBJECT', '');
}

