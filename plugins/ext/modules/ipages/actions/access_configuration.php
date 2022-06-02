<?php

//check access
if ($app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}

switch ($app_module_action) {
    case 'save':

        require(component_path('ext/ext/save_configuration'));

        $alerts->add(TEXT_CONFIGURATION_UPDATED, 'success');

        redirect_to('ext/ipages/access_configuration');
        break;
}