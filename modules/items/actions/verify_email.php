<?php

$access_rules = new access_rules($current_entity_id, $current_item_id);

if (!users::has_access('update', $access_rules->get_access_schema()) or $current_entity_id != 1) {
    redirect_to('dashboard/access_forbidden');
}

switch ($app_module_action) {
    case 'verify':
        db_query("update app_entity_1 set is_email_verified=1 where id='" . db_input($current_item_id) . "'");
        exit();
        break;
}