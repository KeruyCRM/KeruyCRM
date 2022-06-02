<?php


switch ($app_module_action) {
    case 'change':
        $group_id = _GET('id');
        if ($group_id > 0) {
            $group_query = db_query("select id from app_access_groups where id={$group_id}");
            if ($group = db_fetch_array($group_query) and //check if group exist
                CFG_ENABLE_MULTIPLE_ACCESS_GROUPS == 1 and //check if multiple groups allowed
                in_array($group_id, explode(',', $app_user['multiple_access_groups'])) //check assigned gorup to user
            ) {
                db_query("update app_entity_1 set field_6={$group_id} where id={$app_user['id']}"); //update user group
            }
        } elseif ($group_id == 0) {
            if (strlen(
                    $app_user['multiple_access_groups']
                ) and CFG_ENABLE_MULTIPLE_ACCESS_GROUPS == 1 and //check if multiple groups allowed
                in_array($group_id, explode(',', $app_user['multiple_access_groups']))) {
                db_query("update app_entity_1 set field_6={$group_id} where id={$app_user['id']}"); //update user group
            }
        }

        redirect_to('dashboard/');

        break;
}