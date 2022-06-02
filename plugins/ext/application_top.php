<?php

require('plugins/ext/application_core.php');

define('PLUGIN_EXT_VERSION', '1.0.1');
define('PLUGIN_EXT_REQUIRED_KERUYCRM_VERSION', '1.0.1');

//check required KeruyCRM version
if (PROJECT_VERSION != PLUGIN_EXT_REQUIRED_KERUYCRM_VERSION and !in_array($app_module_path, ['tools/check_version']
    )) {
    $alerts->add(
        sprintf(TEXT_EXT_REQUIRED_KERUYCRM_VERSION, PLUGIN_EXT_REQUIRED_KERUYCRM_VERSION, PLUGIN_EXT_VERSION),
        'warning'
    );

    redirect_to('tools/check_version');
}

if (!app_session_is_registered('plugin_ext_current_version')) {
    $plugin_ext_current_version = '';
    app_session_register('plugin_ext_current_version');
}

if (CFG_DISABLE_CHECK_FOR_UPDATES == 1) {
    $plugin_ext_current_version = '';
}

$app_chat = new app_chat();