<?php


switch ($app_module_action) {
    case 'save':

        require(component_path('ext/ext/save_configuration'));

        $alerts->add(TEXT_CONFIGURATION_UPDATED, 'success');

        redirect_to('ext/mail_integration/settings');
        break;
}