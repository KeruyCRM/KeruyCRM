<?php


$app_breadcrumb = items::get_breadcrumb($current_path_array);
$app_breadcrumb[] = ['title' => TEXT_EXT_REPEAT];

require(component_path('items/navigation'));

echo button_tag(TEXT_EXT_CREATE_REPEAT, url_for('ext/recurring_tasks/form', 'path=' . $app_path));

$tasks_query = db_query(
    "select * from app_ext_recurring_tasks where items_id = '" . $current_item_id . "' order by id"
);
$redirect_to = '';
require(component_path('ext/recurring_tasks/listing'));

echo '<a href="' . url_for(
        'items/info',
        'path=' . $app_path
    ) . '" class="btn btn-default">' . TEXT_BUTTON_BACK . '</a>';