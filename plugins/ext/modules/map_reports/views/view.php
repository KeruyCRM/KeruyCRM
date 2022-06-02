<?php

if (isset($_GET['path'])) {
    $path_info = items::parse_path($_GET['path']);
    $current_path = $_GET['path'];
    $current_entity_id = $path_info['entity_id'];
    $current_item_id = true; // set to true to set off default title
    $current_path_array = $path_info['path_array'];
    $app_breadcrumb = items::get_breadcrumb($current_path_array);

    $app_breadcrumb[] = ['title' => $reports['name']];

    require(component_path('items/navigation'));
}
?>

    <h3 class="page-title"><?php
        echo $reports['name'] ?></h3>

<?php
$field_info = db_find('app_fields', $reports['fields_id']);

if (in_array($field_info['type'], ['fieldtype_google_map', 'fieldtype_google_map_directions'])) {
    require(component_path('ext/map_reports/view_google'));
} elseif (in_array($field_info['type'], ['fieldtype_yandex_map'])) {
    require(component_path('ext/map_reports/view_yandex'));
} else {
    require(component_path('ext/map_reports/view'));
}
