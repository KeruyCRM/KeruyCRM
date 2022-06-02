<h3 class="page-title"><?php
    echo TEXT_EXT_MY_RECURRING_TASKS ?></h3>

<?php

$tasks_query = db_query(
    "select * from app_ext_recurring_tasks where created_by ='" . $app_user['id'] . "' order by id"
);
$redirect_to = '&redirect_to=my_recurring_tasks';

require(component_path('ext/recurring_tasks/listing'));