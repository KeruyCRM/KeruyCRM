<?php

require('plugins/ext/payment_modules/liqpay/lib/liqpay_sdk.php');

class liqpay
{
    public $title;

    public $site;

    public $status_codes;

    function __construct()
    {
        $this->title = TEXT_MODULE_LIQPAY_TITLE;
        $this->site = 'https://www.liqpay.ua';
        $this->api = 'https://www.liqpay.ua/documentation/api/aquiring/checkout/doc';
        $this->version = '1.0';
        $this->country = 'UA';

        $this->status_codes = [
            'error' => 'Неуспешный платеж. Некорректно заполнены данные',
            'failure' => 'Неуспешный платеж',
            'reversed' => 'Платеж возвращен',
            'sandbox' => 'Тестовый платеж',
            'subscribed' => 'Подписка успешно оформлена',
            'success' => 'Успешный платеж',
            'unsubscribed' => 'Подписка успешно деактивирована',
            '3ds_verify' => 'Требуется 3DS верификация. Для завершения платежа, требуется выполнить 3ds_verify',
            'captcha_verify' => 'Ожидается подтверждение captcha',
            'cvv_verify' => 'Требуется ввод CVV карты отправителя. Для завершения платежа, требуется выполнить cvv_verify',
            'ivr_verify' => 'Ожидается подтверждение звонком ivr',
            'otp_verify' => 'Требуется OTP подтверждение клиента. OTP пароль отправлен на номер телефона Клиента. Для завершения платежа, требуется выполнить otp_verify',
            'password_verify' => 'Ожидается подтверждение пароля приложения Приват24',
            'phone_verify' => 'Ожидается ввод телефона клиентом. Для завершения платежа, требуется выполнить phone_verify',
            'pin_verify' => 'Ожидается подтверждение pin-code',
            'receiver_verify' => 'Требуется ввод данных получателя. Для завершения платежа, требуется выполнить receiver_verify',
            'sender_verify' => 'Требуется ввод данных отправителя. Для завершения платежа, требуется выполнить sender_verify',
            'senderapp_verify' => 'Ожидается подтверждение в приложении SENDER',
            'wait_qr' => 'Ожидается сканирование QR-кода клиентом',
            'wait_sender' => 'Ожидается подтверждение оплаты клиентом в приложении Privat24/SENDER',
            'cash_wait' => 'Ожидается оплата наличными в ТСО',
            'hold_wait' => 'Сумма успешно заблокирована на счету отправителя',
            'invoice_wait' => 'Инвойс создан успешно, ожидается оплата',
            'prepared' => 'Платеж создан, ожидается его завершение отправителем',
            'processing' => 'Платеж обрабатывается',
            'wait_accept' => 'Деньги с клиента списаны, но магазин еще не прошел проверку. Если магазин не пройдет активацию в течение 90 дней, платежи будут автоматически отменены',
            'wait_card' => 'Не установлен способ возмещения у получателя',
            'wait_compensation' => 'Платеж успешный, будет зачислен в ежесуточной проводке',
            'wait_lc' => 'Аккредитив. Деньги с клиента списаны, ожидается подтверждение доставки товара',
            'wait_reserve' => 'Средства по платежу зарезервированы для проведения возврата по ранее поданной заявке',
            'wait_secure' => 'Платеж на проверке',
        ];
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'public_key',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_LIQPAY_PUBLIC_KEY,
            'info' => TEXT_MODULE_LIQPAY_PUBLIC_KEY_INFO,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'private_key',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_LIQPAY_PRIVATE_KEY,
            'info' => TEXT_MODULE_LIQPAY_PRIVATE_KEY_INFO,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'currency',
            'type' => 'input',
            'default' => 'USD',
            'title' => TEXT_EXT_MODULE_TRANSACTION_CURRENCY,
            'description' => TEXT_EXT_MODULE_TRANSACTION_CURRENCY_INFO . ', EUR, RUB, UAH.',
            'params' => ['class' => 'form-control input-small required'],
        ];

        $cfg[] = [
            'key' => 'language',
            'type' => 'dorpdown',
            'choices' => [
                'ru' => 'ru',
                'en' => 'en',
                'uk' => 'uk',
            ],
            'default' => 'ru',
            'title' => TEXT_LANGUAGE,
            'params' => ['class' => 'form-control input-small']
        ];


        $cfg[] = [
            'key' => 'sandbox',
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

            $parameters = [];
            $parameters['action'] = 'pay';
            $parameters['version'] = '3';
            $parameters['description'] = $item_name;
            $parameters['amount'] = number_format($amount, 2, '.', '');
            $parameters['currency'] = $cfg['currency'];
            $parameters['order_id'] = $current_item_id;
            $parameters['language'] = $cfg['language'];
            $parameters['server_url'] = url_for_file(
                'api/ipn.php?module_id=' . $module_id . '&process_id=' . $process_id
            );
            $parameters['result_url'] = url_for('items/info', 'path=' . $app_path);
            $parameters['sandbox'] = $cfg['sandbox'];


            $html .= '<p class="to-pay">' . TEXT_EXT_TO_PAY . ': ' . $amount . ' ' . $cfg['currency'] . '</p>';
            $html .= submit_tag(TEXT_EXT_BUTTON_PAY, ['class' => 'btn btn-primary btn-pay']);

            $liqpay = new liqpay_sdk($cfg['public_key'], $cfg['private_key']);

            $html = $liqpay->cnb_form($parameters, $html);
        }

        return $html;
    }

    function ipn($module_id)
    {
        $cfg = modules::get_configuration($this->configuration(), $module_id);

        if (isset($_POST['data']) && isset($_POST['signature']) and isset($_REQUEST['process_id'])) {
            $data = $_POST['data'];
            $parsed_data = json_decode(base64_decode($data), true);
            $received_signature = $_POST['signature'];
            $received_public_key = $parsed_data['public_key'];
            $order_id = $parsed_data['order_id'];
            $status = $parsed_data['status'];
            $sender_phone = $parsed_data['sender_phone'];
            $amount = $parsed_data['amount'];
            $currency = $parsed_data['currency'];
            $transaction_id = $parsed_data['transaction_id'];

            $private_key = $cfg['private_key'];
            $public_key = $cfg['public_key'];

            $generated_signature = base64_encode(sha1($private_key . $data . $private_key, 1));

            if ($received_signature != $generated_signature || $public_key != $received_public_key) {
                die("FAIL SIGNATURE");
            }

            if ($order_id > 0) {
                $process_info_query = db_query(
                    "select * from app_ext_processes where id='" . $_REQUEST['process_id'] . "'"
                );
                if ($app_process_info = db_fetch_array($process_info_query)) {
                    $current_entity_id = $app_process_info['entities_id'];
                    $current_item_id = $order_id;

                    $item_info_query = db_query(
                        "select e.* from app_entity_" . $current_entity_id . " e  where e.id='" . $current_item_id . "'"
                    );
                    if ($item_info = db_fetch_array($item_info_query)) {
                        $comment = '<b>' . TEXT_EXT_PAYMENT_NOTIFICATION . '</b><br>' .
                            TEXT_EXT_PAYMENT_MODULE . ': ' . $this->title . '<br>' .
                            TEXT_MODULE_PAYMENT_TOTAL . ': ' . number_format(
                                $amount,
                                2,
                                '.',
                                ''
                            ) . ' ' . $currency . '<br>' .
                            TEXT_MODULE_TRANSACTION_ID . ': ' . $transaction_id . '<br>' .
                            TEXT_MODULE_PAYMENT_STATUS . ': ' . modules::status_label($status, 'success');

                        if (isset($this->status_codes[$status])) {
                            $comment .= ' ' . $this->status_codes[$status];
                        }

                        $sql_data = [
                            'description' => $comment,
                            'entities_id' => $current_entity_id,
                            'items_id' => $current_item_id,
                            'date_added' => time(),
                            'created_by' => 0,

                        ];

                        db_perform('app_comments', $sql_data);

                        if ($status == 'success') {
                            $processes = new processes($current_entity_id);
                            $processes->items_id = $current_item_id;
                            $processes->run($app_process_info, false, true);
                        }
                    }
                }
            }
        } else {
            die("FAIL REQUEST");
        }
    }

}