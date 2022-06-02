<?php

if (strlen(CFG_API_KEY) and CFG_API_KEY == $_GET['key']) {
    $sql_data = [
        'type' => 'phone',
        'date_added' => db_prepare_input((int)$_GET['date_added']),
        'direction' => db_prepare_input($_GET['direction']),
        'phone' => db_prepare_input(preg_replace('/\D/', '', $_GET['phone'])),
        'duration' => db_prepare_input((int)$_GET['duration']),
    ];

    db_perform('app_ext_call_history', $sql_data);
}

exit();