<?php

$app_layout = 'public_layout.php';

$public_form_query = db_query("select * from app_ext_public_forms where id='" . db_input(_get::int('id')) . "'");
if (!$public_form = db_fetch_array($public_form_query)) {
    die(TEXT_PAGE_NOT_FOUND_CONTENT);
}

