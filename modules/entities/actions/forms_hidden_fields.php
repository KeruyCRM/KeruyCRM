<?php

switch ($app_module_action) {
    case 'save':
        $cfg = new entities_cfg($_GET['entities_id']);

        if (isset($_POST['hidden_form_fields'])) {
            $cfg->set('hidden_form_fields', implode(',', $_POST['hidden_form_fields']));
        } else {
            $cfg->set('hidden_form_fields', '');
        }

        redirect_to('entities/forms', 'entities_id=' . $_GET['entities_id']);
        break;
}

