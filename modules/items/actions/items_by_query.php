<?php

$field_query = db_query(
    "select name, configuration from app_fields where id=" . _GET('fields_id') . " and type='fieldtype_items_by_query'"
);
if (!$field = db_fetch_array($field_query)) {
    redirect_to('dashboard/page_not_found');
}

$item_query = db_query(
    "select e.* " . fieldtype_formula::prepare_query_select(
        $current_entity_id,
        ''
    ) . " from app_entity_" . $current_entity_id . " e where e.id='" . $current_item_id . "'"
);
if (!$item = db_fetch_array($item_query)) {
    redirect_to('dashboard/page_not_found');
}