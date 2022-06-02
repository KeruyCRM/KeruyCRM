<h3 class="page-title"><?php
    echo TEXT_EXT_RECURRING_TASKS ?></h3>

<p><?php
    echo TEXT_EXT_RECURRING_TASKS_INFO . '<br>' . TEXT_EXT_RECURRING_TASKS_INFO_CRON . '<br>' . DIR_FS_CATALOG . 'cron/recurring_tasks.php' ?></p>

<?php

$tasks_query = db_query("select * from app_ext_recurring_tasks order by id");
$redirect_to = '&redirect_to=recurring_tasks';

require(component_path('ext/recurring_tasks/listing'));