<?php

class paypal
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_PAYPAL_TITLE;
        $this->site = 'https://www.paypal.com';
        $this->api = 'https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/formbasics/';
        $this->version = '1.0';
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'paypal_id',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_PAYPAL_ID,
            'info' => TEXT_MODULE_PAYPAL_ID_INFO,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'currency',
            'type' => 'input',
            'default' => 'USD',
            'title' => TEXT_EXT_MODULE_TRANSACTION_CURRENCY,
            'description' => TEXT_EXT_MODULE_TRANSACTION_CURRENCY_INFO . '. <a href="https://developer.paypal.com/docs/classic/api/currency_codes/#paypal" target="_blank">' . TEXT_MORE_INFO . '</a>.',
            'params' => ['class' => 'form-control input-small required'],
        ];
        $cfg[] = [
            'key' => 'lc',
            'type' => 'input',
            'default' => 'en_US',
            'title' => TEXT_LANGUAGE,
            'description' => '<a href="https://developer.paypal.com/docs/classic/api/locale_codes/" target="_blank">PayPal locale codes</a>',
            'params' => ['class' => 'form-control input-small']
        ];


        $cfg[] = [
            'key' => 'gateway_server',
            'type' => 'dorpdown',
            'choices' => [
                'live' => TEXT_MODULE_GATEWAY_SERVER_LIVE,
                'sandbox' => TEXT_MODULE_GATEWAY_SERVER_SANDBOX,
            ],
            'default' => 'live',
            'title' => TEXT_MODULE_GATEWAY_SERVER,
            'info' => TEXT_MODULE_GATEWAY_SERVER_INFO,
            'params' => ['class' => 'form-control input-small']
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
            $parameters['cmd'] = '_xclick';
            $parameters['item_name'] = $item_name;
            $parameters['business'] = $cfg['paypal_id'];
            $parameters['amount'] = number_format($amount, 2, '.', '');
            $parameters['currency_code'] = $cfg['currency'];
            $parameters['invoice'] = $current_item_id;
            $parameters['no_note'] = '1';
            $parameters['custom'] = $process_id;
            $parameters['notify_url'] = url_for_file('api/ipn.php?module_id=' . $module_id);
            $parameters['return'] = url_for('items/info', 'path=' . $app_path);
            $parameters['cancel_return'] = url_for('items/info', 'path=' . $app_path);
            $parameters['charset'] = 'utf-8';
            $parameters['lc'] = $cfg['lc'];


            if ($cfg['gateway_server'] == 'live') {
                $form_action_url = 'https://www.paypal.com/cgi-bin/webscr';
            } else {
                $form_action_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
            }


            $html .= '<form name="payment_confirmation" id="payment_confirmation" action="' . $form_action_url . '"  method="post">';

            foreach ($parameters as $k => $v) {
                $html .= input_hidden_tag($k, $v) . "\n";
            }

            $html .= '<p class="to-pay">' . TEXT_EXT_TO_PAY . ': ' . $amount . ' ' . $cfg['currency'] . '</p>';
            $html .= submit_tag(TEXT_EXT_BUTTON_PAY, ['class' => 'btn btn-primary btn-pay']);
            $html .= '</form>';
        }

        return $html;
    }

    function ipn($module_id)
    {
        $cfg = modules::get_configuration($this->configuration(), $module_id);

        if (isset($_POST['receiver_email'])) {
            $parameters = 'cmd=_notify-validate';

            foreach ($_POST as $key => $value) {
                $parameters .= '&' . $key . '=' . urlencode(stripslashes($value));
            }

            if ($cfg['gateway_server'] == 'live') {
                $url = 'https://www.paypal.com/cgi-bin/webscr';
            } else {
                $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
            }

            $server = parse_url($url);

            if (!isset($server['port'])) {
                $server['port'] = ($server['scheme'] == 'https') ? 443 : 80;
            }

            if (!isset($server['path'])) {
                $server['path'] = '/';
            }

            $curl = curl_init(
                $server['scheme'] . '://' . $server['host'] . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : '')
            );
            curl_setopt($curl, CURLOPT_PORT, $server['port']);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
            curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $result = curl_exec($curl);

            curl_close($curl);


            if ($result == 'VERIFIED') {
                if (isset($_POST['invoice']) && is_numeric(
                        $_POST['invoice']
                    ) && ($_POST['invoice'] > 0) && isset($_POST['custom']) && is_numeric(
                        $_POST['custom']
                    ) && ($_POST['custom'] > 0)) {
                    $process_info_query = db_query(
                        "select * from app_ext_processes where id='" . _post::int('custom') . "'"
                    );
                    if ($app_process_info = db_fetch_array($process_info_query)) {
                        $current_entity_id = $app_process_info['entities_id'];
                        $current_item_id = _post::int('invoice');

                        $item_info_query = db_query(
                            "select e.* from app_entity_" . $current_entity_id . " e  where e.id='" . $current_item_id . "'"
                        );
                        if ($item_info = db_fetch_array($item_info_query)) {
                            $comment = '<b>' . TEXT_EXT_PAYMENT_NOTIFICATION . '</b><br>' .
                                TEXT_EXT_PAYMENT_MODULE . ': ' . $this->title . '<br>' .
                                TEXT_MODULE_PAYMENT_TOTAL . ': ' . number_format(
                                    $_POST['mc_gross'],
                                    2,
                                    '.',
                                    ''
                                ) . ' ' . $_POST['mc_currency'] . '<br>' .
                                TEXT_MODULE_TRANSACTION_ID . ': ' . $_POST['txn_id'] . '<br>' .
                                TEXT_MODULE_PAYMENT_STATUS . ': ' . modules::status_label(
                                    $_POST['payment_status'],
                                    'Completed'
                                );

                            if ($_POST['payment_status'] == 'Pending') {
                                $comment .= ' ' . $_POST['pending_reason'];
                            } elseif (($_POST['payment_status'] == 'Reversed') || ($_POST['payment_status'] == 'Refunded')) {
                                $comment .= ' ' . $_POST['reason_code'];
                            }


                            $sql_data = [
                                'description' => $comment,
                                'entities_id' => $current_entity_id,
                                'items_id' => $current_item_id,
                                'date_added' => time(),
                                'created_by' => 0,

                            ];

                            db_perform('app_comments', $sql_data);

                            if ($_POST['payment_status'] == 'Completed') {
                                $processes = new processes($current_entity_id);
                                $processes->items_id = $current_item_id;
                                $processes->run($app_process_info, false, true);
                            }
                        }
                    }
                }
            }
        }
    }

}