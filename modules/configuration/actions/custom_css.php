<?php

switch ($app_module_action) {
    case 'save':
        $custom_css = $_POST['custom_css'];
        file_put_contents('css/custom.css', $custom_css);

        configuration::set('CFG_CUSTOM_CSS_TIME', time());

        exit();

        break;
}