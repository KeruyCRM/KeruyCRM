<?php

class tcheckout
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_2CHECKOUT_TITLE;
        $this->site = 'https://www.2checkout.com';
        $this->api = 'https://www.2checkout.com/documentation/checkout/parameters';
        $this->version = '1.0';
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'sid',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_2CHECKOUT_SID,
            'description' => TEXT_MODULE_2CHECKOUT_SID_INFO,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'secret',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_2CHECKOUT_SECRET_WORD,
            'info' => TEXT_MODULE_2CHECKOUT_SECRET_WORD_INFO,
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
            'key' => 'lang',
            'type' => 'input',
            'default' => 'en',
            'title' => TEXT_LANGUAGE,
            'description' => '<a href="https://www.2checkout.com/documentation/checkout/parameters" target="_blank">lang</a>',
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
            $amount = (isset($item_info['field_' . $cfg['amount']]) ? $item_info['field_' . $cfg['amount']] : 0);
            $amount = (strlen($amount) ? $amount : 0);

            $fieldtype_text_pattern = new fieldtype_text_pattern();

            $item_name = $fieldtype_text_pattern->output_singe_text($cfg['item_name'], $current_entity_id, $item_info);

            $parameters = [];

            $parameters['mode'] = '2CO';
            $parameters['sid'] = $cfg['sid'];
            $parameters['merchant_order_id'] = $current_item_id;
            $parameters['lang'] = $cfg['lang'];
            $parameters['currency_code'] = $cfg['currency'];

            $parameters['li_0_type'] = 'product';
            $parameters['li_0_product_id'] = $process_id;
            $parameters['li_0_quantity'] = 1;
            $parameters['li_0_name'] = $item_name;
            $parameters['li_0_price'] = number_format($amount, 2, '.', '');

            if ($cfg['gateway_server'] == 'live') {
                $form_action_url = 'https://www.2checkout.com/checkout/purchase';
            } else {
                $form_action_url = 'https://sandbox.2checkout.com/checkout/purchase';
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

        //echo '<pre>';
        //print_r($_GET);

        if (isset($_REQUEST['order_number'])) {
            $compare_string = $cfg['secret'] . $cfg['sid'] . $_REQUEST['merchant_order_id'] . $_REQUEST['total'];
            // make it md5
            $compare_hash1 = md5($compare_string);
            // make all upper
            $compare_hash1 = strtoupper($compare_hash1);
            $compare_hash2 = $_REQUEST['key'];
            if ($compare_hash1 != $compare_hash2) {
                if (isset($_GET['li_0_product_id'])) {
                    $process_id = _get::int('li_0_product_id');

                    $process_info_query = db_query("select * from app_ext_processes where id='" . $process_id . "'");
                    if ($app_process_info = db_fetch_array($process_info_query)) {
                        $current_entity_id = $app_process_info['entities_id'];
                        $current_item_id = $_REQUEST['merchant_order_id'];

                        $item_info_query = db_query(
                            "select e.* from app_entity_" . $current_entity_id . " e  where e.id='" . $current_item_id . "'"
                        );
                        if ($item_info = db_fetch_array($item_info_query)) {
                            $comment = '<b>' . TEXT_EXT_PAYMENT_NOTIFICATION . '</b><br>' .
                                TEXT_EXT_PAYMENT_MODULE . ': ' . $this->title . '<br>' .
                                TEXT_MODULE_PAYMENT_TOTAL . ': ' . number_format(
                                    $_REQUEST['total'],
                                    2,
                                    '.',
                                    ''
                                ) . '<br>' .
                                TEXT_MODULE_TRANSACTION_ID . ': ' . $_REQUEST['order_number'] . '<br>' .
                                TEXT_MODULE_PAYMENT_STATUS . ': ' . modules::status_label(
                                    $_REQUEST['credit_card_processed'],
                                    'Y'
                                );


                            $sql_data = [
                                'description' => $comment,
                                'entities_id' => $current_entity_id,
                                'items_id' => $current_item_id,
                                'date_added' => time(),
                                'created_by' => 0,

                            ];

                            db_perform('app_comments', $sql_data);

                            if ($_REQUEST['credit_card_processed'] == 'Y') {
                                $processes = new processes($current_entity_id);
                                $processes->items_id = $current_item_id;
                                $processes->run($app_process_info, false, true);
                            }

                            $redirect_url = url_for(
                                'items/info',
                                'path=' . $current_entity_id . '-' . $current_item_id
                            );
                            $redirect_url = str_replace('/api/', '/', $redirect_url);
                            header('Location: ' . $redirect_url);
                            exit();
                        }
                    }
                }
            }
        }
    }

}