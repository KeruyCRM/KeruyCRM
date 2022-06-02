<?php

//check if process exist
$app_process_info_query = db_query(
    "select * from app_ext_processes where id='" . _get::int('id') . "' and is_active=1"
);
if (!$app_process_info = db_fetch_array($app_process_info_query)) {
    redirect_to('dashboard/page_not_found');
}