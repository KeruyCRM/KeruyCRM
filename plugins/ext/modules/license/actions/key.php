<?php

//check access
if ($app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}

switch ($app_module_action) {
    case 'save':
        if (!defined('CFG_PLUGIN_EXT_LICENSE_KEY')) {
            db_perform(
                'app_configuration',
                ['configuration_value' => $_POST['product_key'], 'configuration_name' => 'CFG_PLUGIN_EXT_LICENSE_KEY']
            );
        }

        redirect_to('ext/license/key');
        break;
    case 'update':
        db_perform(
            'app_configuration',
            ['configuration_value' => $_POST['product_key']],
            'update',
            "configuration_name = 'CFG_PLUGIN_EXT_LICENSE_KEY'"
        );

        redirect_to('ext/license/key');
        break;
}

