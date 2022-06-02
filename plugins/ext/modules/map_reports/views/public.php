<?php

$field_info = db_find('app_fields', $reports['fields_id']);

if (in_array($field_info['type'], ['fieldtype_google_map', 'fieldtype_google_map_directions'])) {
    require(component_path('ext/map_reports/view_google'));
} elseif (in_array($field_info['type'], ['fieldtype_yandex_map'])) {
    require(component_path('ext/map_reports/view_yandex'));
} else {
    require(component_path('ext/map_reports/view'));
}