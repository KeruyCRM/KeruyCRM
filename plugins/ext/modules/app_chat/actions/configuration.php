<?php

//check access
if ($app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}


switch ($app_module_action) {
    case 'save':
        //reset access
        db_query("delete from app_ext_chat_access");

        //insert access
        if (isset($_POST['access'])) {
            $sql_data = [];

            foreach ($_POST['access'] as $group_id => $access_schema) {
                $sql_data[] = ['access_groups_id' => $group_id, 'access_schema' => implode(',', $access_schema)];
            }

            db_batch_insert('app_ext_chat_access', $sql_data);
        }

        require(component_path('ext/ext/save_configuration'));

        $alerts->add(TEXT_CONFIGURATION_UPDATED, 'success');

        redirect_to('ext/app_chat/configuration');
        break;
}

