<?php

$template_info_query = db_query(
    "select * from app_ext_xml_import_templates where id='" . _get::int('id') . "' and length(filepath)>0"
);
if (!$template_info = db_fetch_array($template_info_query)) {
    redirect_to('ext/xml_import/templates');
}