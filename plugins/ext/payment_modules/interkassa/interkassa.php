<?php

class interkassa
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_INTERKASSA_TITLE;
        $this->site = 'https://www.interkassa.com';
        $this->api = 'https://www.interkassa.com/documentation-sci/';
        $this->version = '1.0';
        $this->country = 'UA';
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'id',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_INTERKASSA_ID,
            'info' => TEXT_MODULE_INTERKASSA_ID_INFO,
            'description' => TEXT_MODULE_INTERKASSA_ID_DESCRIPTION,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'secret_key',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_INTERKASSA_SECRET_KEY,
            'info' => TEXT_MODULE_INTERKASSA_SECRET_KEY_INFO,
            'params' => ['class' => 'form-control input-large'],
        ];

        $cfg[] = [
            'key' => 'currency',
            'type' => 'input',
            'default' => 'USD',
            'title' => TEXT_EXT_MODULE_TRANSACTION_CURRENCY,
            'description' => TEXT_EXT_MODULE_TRANSACTION_CURRENCY_INFO,
            'params' => ['class' => 'form-control input-small required'],
        ];

        $cfg[] = [
            'key' => 'custom_title',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_CUSTOM_TITLE,
            'description' => TEXT_DEFAULT . ' "' . $this->title . '".',
            'params' => ['class' => 'form-control input-large']
        ];

        $cfg[] = [
            'key' => 'item_name',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_PURPOSE_OF_PAYMENT,
            'description' => TEXT_ENTER_TEXT_PATTERN_INFO,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'amount',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_PAYMENT_TOTAL,
            'description' => TEXT_MODULE_PAYMENT_TOTAL_INFO,
            'params' => ['class' => 'form-control input-small required'],
        ];

        return $cfg;
    }

    function confirmation($module_id, $process_id)
    {
        global $app_path, $current_item_id, $current_entity_id, $app_redirect_to;

        $html = '';

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $item_info_query = db_query(
            "select e.* " . fieldtype_formula::prepare_query_select(
                $current_entity_id,
                ''
            ) . " from app_entity_" . $current_entity_id . " e  where e.id='" . $current_item_id . "'"
        );
        if ($item_info = db_fetch_array($item_info_query)) {
            $amount = $item_info['field_' . $cfg['amount']];

            $fieldtype_text_pattern = new fieldtype_text_pattern();

            $item_name = $fieldtype_text_pattern->output_singe_text($cfg['item_name'], $current_entity_id, $item_info);

            $parameters = [];

            $parameters['ik_desc'] = $item_name;
            $parameters['ik_co_id'] = $cfg['id'];
            $parameters['ik_am'] = number_format($amount, 2, '.', '');
            $parameters['ik_cur'] = $cfg['currency'];
            $parameters['ik_pm_no'] = $current_item_id;

            $parameters['ik_x_process_id'] = $process_id;
            $parameters['ik_ia_u'] = url_for_file('api/ipn.php?module_id=' . $module_id);
            $parameters['ik_suc_u'] = url_for('items/info', 'path=' . $app_path);
            $parameters['ik_pnd_u'] = url_for('items/info', 'path=' . $app_path);
            $parameters['ik_fal_u'] = url_for('items/info', 'path=' . $app_path);

            $parameters['ik_sign'] = $this->signature($parameters, $cfg);

            $form_action_url = 'https://sci.interkassa.com/';

            $html .= '<form name="payment_confirmation" id="payment_confirmation" action="' . $form_action_url . '" method="post">';

            foreach ($parameters as $k => $v) {
                $html .= input_hidden_tag($k, $v) . "\n";
            }

            $html .= '<p class="to-pay">' . TEXT_EXT_TO_PAY . ': ' . $amount . ' ' . $cfg['currency'] . '</p>';
            $html .= submit_tag(TEXT_EXT_BUTTON_PAY, ['class' => 'btn btn-primary btn-pay']);
            $html .= '</form>';
        }

        return $html;
    }

    function signature($parameters, $cfg)
    {
        ksort($parameters, SORT_STRING);
        $parameters['secret'] = $cfg['secret_key'];
        $signString = implode(':', $parameters);
        $sign = base64_encode(md5($signString, true));

        return $sign;
    }

    function check_ip()
    {
        $ip_stack = [
            'ip_begin' => '151.80.190.97',
            'ip_end' => '151.80.190.104'
        ];

        if (!ip2long($_SERVER['REMOTE_ADDR']) >= ip2long($ip_stack['ip_begin']) && !ip2long(
                $_SERVER['REMOTE_ADDR']
            ) <= ip2long($ip_stack['ip_end'])) {
            return false;
        }

        return true;
    }

    function ipn($module_id)
    {
        $cfg = modules::get_configuration($this->configuration(), $module_id);


        if (isset($_POST['ik_pm_no']) && is_numeric(
                $_POST['ik_pm_no']
            ) && ($_POST['ik_pm_no'] > 0) && isset($_POST['ik_x_process_id']) && is_numeric(
                $_POST['ik_x_process_id']
            ) && ($_POST['ik_x_process_id'] > 0) and $this->check_ip()) {
            $process_info_query = db_query(
                "select * from app_ext_processes where id='" . _post::int('ik_x_process_id') . "'"
            );
            if ($app_process_info = db_fetch_array($process_info_query)) {
                $current_entity_id = $app_process_info['entities_id'];
                $current_item_id = _post::int('ik_pm_no');

                $item_info_query = db_query(
                    "select e.* from app_entity_" . $current_entity_id . " e  where e.id='" . $current_item_id . "'"
                );
                if ($item_info = db_fetch_array($item_info_query)) {
                    $comment = '<b>' . TEXT_EXT_PAYMENT_NOTIFICATION . '</b><br>' .
                        TEXT_EXT_PAYMENT_MODULE . ': ' . $this->title . '<br>' .
                        TEXT_MODULE_PAYMENT_TOTAL . ': ' . number_format(
                            $_POST['ik_am'],
                            2,
                            '.',
                            ''
                        ) . ' ' . $_POST['ik_cur'] . '<br>' .
                        TEXT_EXT_PAYMENT_METHOD . ': ' . $_POST['ik_pw_via'] . '<br>' .
                        TEXT_MODULE_PAYMENT_STATUS . ': ' . modules::status_label($_POST['ik_inv_st'], 'success');

                    $sql_data = [
                        'description' => $comment,
                        'entities_id' => $current_entity_id,
                        'items_id' => $current_item_id,
                        'date_added' => time(),
                        'created_by' => 0,
                    ];

                    db_perform('app_comments', $sql_data);

                    if ($_POST['ik_inv_st'] == 'success') {
                        $processes = new processes($current_entity_id);
                        $processes->items_id = $current_item_id;
                        $processes->run($app_process_info, false, true);
                    }
                }
            }
        }
    }


}