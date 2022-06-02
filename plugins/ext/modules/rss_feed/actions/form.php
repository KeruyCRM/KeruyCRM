<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_rss_feeds', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_rss_feeds');

    $obj['type'] = 'entity';
}