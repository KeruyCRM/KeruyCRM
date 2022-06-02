<?php

switch ($app_module_action) {
    case 'save':

        if (!isset($_POST['CFG']['GLOBAL_SEARCH_ALLOWED_GROUPS'])) {
            $_POST['CFG']['GLOBAL_SEARCH_ALLOWED_GROUPS'] = '';
        }

        require(component_path('ext/ext/save_configuration'));

        $alerts->add(TEXT_CONFIGURATION_UPDATED, 'success');

        redirect_to('ext/global_search/settings');
        break;
}