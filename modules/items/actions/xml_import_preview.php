<?php

if (!xml_import::has_users_access($current_entity_id, _get::int('templates_id'))) {
    redirect_to('dashboard/access_forbidden');
}

$template_info = db_find('app_ext_xml_import_templates', _get::int('templates_id'));

if (strlen($_FILES['filename']['name']) > 0) {
    $xml_import_filename = 'xml_imort_' . _post::int('current_time') . '.xml';
    if (!move_uploaded_file($_FILES['filename']['tmp_name'], DIR_FS_TMP . $xml_import_filename)) {
        exit();
    }
} else {
    $xml_import_filename = 'xml_imort_' . _get::int('current_time') . '.xml';
}