<?php

switch ($app_module_action) {
    case 'save':
        if (isset($_POST['CFG'])) {
            foreach ($_POST['CFG'] as $k => $v) {
                $k = 'CFG_' . $k;

                //handle arrays
                if (is_array($v)) {
                    $v = implode(',', $v);
                }

                $value = trim($v);

                $cfq_query = db_query("select * from app_configuration where configuration_name='" . $k . "'");
                if (!$cfq = db_fetch_array($cfq_query)) {
                    db_perform('app_configuration', ['configuration_value' => $value, 'configuration_name' => $k]);
                } else {
                    db_perform(
                        'app_configuration',
                        ['configuration_value' => $value],
                        'update',
                        "configuration_name='" . $k . "'"
                    );
                }
            }

            $alerts->add(TEXT_CONFIGURATION_UPDATED, 'success');

            redirect_to('ext/ext/api');
        }
        break;
}	