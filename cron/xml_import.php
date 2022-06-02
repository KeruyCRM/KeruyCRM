<?php

chdir(substr(__DIR__, 0, -5));

define('IS_CRON', true);

//load core
require('includes/application_core.php');

//include ext plugins
require('plugins/ext/application_core.php');

//load app lagn
if (is_file($v = 'includes/languages/' . CFG_APP_LANGUAGE)) {
    require($v);
}

if (is_file($v = 'plugins/ext/languages/' . CFG_APP_LANGUAGE)) {
    require($v);
}

$app_users_cache = users::get_cache();

$app_user = ['language' => CFG_APP_LANGUAGE];

$template_info_query = db_query("select * from app_ext_xml_import_templates where length(filepath)>0 and is_active=1");
while ($template_info = db_fetch_array($template_info_query)) {
    $xml_import = new xml_import('', $template_info);

    $xml_import->get_file_by_path();

    $xml_errors = $xml_import->has_xml_errors();

    if (!strlen($xml_errors)) {
        $parent_entity_item_id = $template_info['parent_item_id'];

        $xml_import->import_data();
    }

    $xml_import->unlink_import_file();
}