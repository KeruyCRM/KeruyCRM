<?php

class payanyway
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_PAYANYWAY_TITLE;
        $this->site = 'https://payanyway.ru';
        $this->api = 'https://payanyway.ru/info/w/ru/public/w/partnership/developers/assistant.html';
        $this->version = '1.0';
        $this->country = 'RU';
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'id',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_PAYANYWAY_ID,
            'description' => TEXT_MODULE_PAYANYWAY_ID_INFO,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'code',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_PAYANYWAY_CODE_ID,
            'info' => TEXT_MODULE_PAYANYWAY_CODE_ID_INFO,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'currency',
            'type' => 'input',
            'default' => 'RUB',
            'title' => TEXT_EXT_MODULE_TRANSACTION_CURRENCY,
            'description' => TEXT_EXT_MODULE_TRANSACTION_CURRENCY_INFO,
            'params' => ['class' => 'form-control input-small required'],
        ];

        $cfg[] = [
            'key' => 'locale',
            'type' => 'dorpdown',
            'choices' => [
                'ru' => 'ru',
                'en' => 'en',
            ],
            'default' => 'ru',
            'title' => TEXT_LANGUAGE,
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
            'key' => 'test_mode',
            'type' => 'dorpdown',
            'choices' => [
                '0' => TEXT_MODULE_GATEWAY_SERVER_LIVE,
                '1' => TEXT_MODULE_GATEWAY_SERVER_SANDBOX,
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

            $amount = number_format($amount, 2, '.', '');

            $parameters = [];
            $parameters['MNT_ID'] = $cfg['id'];
            $parameters['MNT_TRANSACTION_ID'] = $current_item_id;
            $parameters['MNT_DESCRIPTION'] = $item_name;
            $parameters['MNT_CURRENCY_CODE'] = $cfg['currency'];
            $parameters['MNT_AMOUNT'] = $amount;
            $parameters['MNT_TEST_MODE'] = $cfg['test_mode'];
            $parameters['MNT_SIGNATURE'] = md5(
                $cfg['id'] . $current_item_id . $amount . $cfg['currency'] . $cfg['test_mode'] . $cfg['code']
            );
            $parameters['MNT_SUCCESS_URL'] = url_for('items/info', 'path=' . $app_path);
            $parameters['MNT_FAIL_URL'] = url_for('items/info', 'path=' . $app_path);
            $parameters['followup'] = 'true';
            $parameters['javascriptEnabled'] = 'true';
            $parameters['MNT_CUSTOM1'] = $process_id;

            $form_action_url = 'https://www.payanyway.ru/assistant.htm"';

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

        // checking and handling
        if (isset($_REQUEST['MNT_ID']) && isset($_REQUEST['MNT_TRANSACTION_ID']) && isset($_REQUEST['MNT_OPERATION_ID'])
            && isset($_REQUEST['MNT_AMOUNT']) && isset($_REQUEST['MNT_CURRENCY_CODE']) && isset($_REQUEST['MNT_TEST_MODE'])
            && isset($_REQUEST['MNT_SIGNATURE'])) {
            if ($_REQUEST['MNT_SIGNATURE'] == md5(
                    $_REQUEST['MNT_ID'] . stripslashes(
                        $_REQUEST['MNT_TRANSACTION_ID']
                    ) . $_REQUEST['MNT_OPERATION_ID'] . $_REQUEST['MNT_AMOUNT'] . $_REQUEST['MNT_CURRENCY_CODE'] . $_REQUEST['MNT_TEST_MODE'] . $cfg['code']
                )) {
                $process_info_query = db_query(
                    "select * from app_ext_processes where id='" . $_REQUEST['MNT_CUSTOM1'] . "'"
                );
                if ($app_process_info = db_fetch_array($process_info_query)) {
                    $current_entity_id = $app_process_info['entities_id'];
                    $current_item_id = $_REQUEST['MNT_TRANSACTION_ID'];

                    $item_info_query = db_query(
                        "select e.* from app_entity_" . $current_entity_id . " e  where e.id='" . $current_item_id . "'"
                    );
                    if ($item_info = db_fetch_array($item_info_query)) {
                        $comment = '<b>' . TEXT_EXT_PAYMENT_NOTIFICATION . '</b><br>' .
                            TEXT_EXT_PAYMENT_MODULE . ': ' . $this->title . '<br>' .
                            TEXT_MODULE_PAYMENT_TOTAL . ': ' . number_format(
                                $_REQUEST['MNT_AMOUNT'],
                                2,
                                '.',
                                ''
                            ) . ' ' . $_REQUEST['MNT_CURRENCY_CODE'] . '<br>' .
                            TEXT_MODULE_TRANSACTION_ID . ': ' . $_REQUEST['MNT_OPERATION_ID'] . '<br>' .
                            TEXT_MODULE_PAYMENT_STATUS . ': ' . modules::status_label(
                                TEXT_MODULE_PAYANYWAY_PAYMENT_COMPLATED,
                                TEXT_MODULE_PAYANYWAY_PAYMENT_COMPLATED
                            );

                        $sql_data = [
                            'description' => $comment,
                            'entities_id' => $current_entity_id,
                            'items_id' => $current_item_id,
                            'date_added' => time(),
                            'created_by' => 0,

                        ];

                        db_perform('app_comments', $sql_data);

                        $processes = new processes($current_entity_id);
                        $processes->items_id = $current_item_id;
                        $processes->run($app_process_info, false, true);
                    }
                }

                die("SUCCESS");
            } else {
                die("FAIL SIGNATURE");
            }
        } else {
            die("FAIL REQUEST");
        }
    }

}