<?php

class cloudpayments_subscription
{

    function __construct()
    {
        $this->title = 'Cloud Payments Recurrent';
        $this->site = 'https://cloudpayments.ru';
        $this->api = 'https://developers.cloudpayments.ru/#api';
        $this->version = '1.0';
        $this->js = '<script src="https://widget.cloudpayments.ru/bundles/cloudpayments"></script>';
        $this->country = 'RU';
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'id',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_CLOUDPAYMENTS_SUBSCRIPTION_ID,
            'description' => TEXT_MODULE_CLOUDPAYMENTS_SUBSCRIPTION_ID_INFO,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'confirm_url',
            'type' => 'text',
            'default' => '<input type="text" value="' . url_for_file(
                    'api/ipn.php?module_id=' . ($_GET['id'] ?? 0)
                ) . '" class="form-control select-all" readonly>',
            'title' => TEXT_MODULE_CLOUDPAYMENTS_SUBSCRIPTION_CONFIRM_URL,
            'description' => TEXT_MODULE_CLOUDPAYMENTS_SUBSCRIPTION_CONFIRM_URL_INFO,
        ];

        $cfg[] = [
            'key' => 'skin',
            'type' => 'dorpdown',
            'choices' => [
                'classic' => 'classic',
                'modern' => 'modern',
                'mini' => 'mini',
            ],
            'title' => TEXT_MODULE_CLOUDPAYMENTS_SUBSCRIPTION_SKIN,
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
            'key' => 'language',
            'type' => 'dorpdown',
            'choices' => [
                'ru-RU' => 'ru-RU',
                'en-US' => 'en-US',
                'de-DE' => 'de-DE',
                'lv' => 'lv',
                'az' => 'az',
                'kk' => 'kk',
                'kk-KZ' => 'kk-KZ',
                'uk' => 'uk',
                'pl' => 'pl',
                'pt' => 'pt',
                'cs-CZ' => 'cs-CZ',
                'vi-VN' => 'vi-VN',
                'tr-TR' => 'tr-TR',
                'es-ES' => 'es-ES',
                'it' => 'it',
            ],
            'default' => 'ru',
            'title' => TEXT_LANGUAGE,
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

        $cfg[] = [
            'type' => 'section',
            'title' => TEXT_MODULE_CLOUDPAYMENTS_SUBSCRIPTION_SUBSCRIPTION,
        ];

        $cfg[] = [
            'key' => 'interval',
            'type' => 'dorpdown',
            'choices' => [
                'Day' => 'Day',
                'Week' => 'Week',
                'Month' => 'Month',
            ],
            'title' => TEXT_EXT_INTERVAL,
            'params' => ['class' => 'form-control input-medium'],
        ];

        $cfg[] = [
            'key' => 'period',
            'type' => 'input',
            'default' => '1',
            'title' => TEXT_PERIOD,
            'description' => TEXT_MODULE_CLOUDPAYMENTS_SUBSCRIPTION_PERIOD_INFO,
            'params' => ['class' => 'form-control input-small required'],
        ];

        return $cfg;
    }

    function confirmation($module_id, $process_id)
    {
        global $app_path, $current_item_id, $current_entity_id, $app_redirect_to, $alerts, $app_user;

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

            if (!strlen($amount)) {
                return '';
            }

            $fieldtype_text_pattern = new fieldtype_text_pattern();

            $item_name = $fieldtype_text_pattern->output_singe_text($cfg['item_name'], $current_entity_id, $item_info);

            $success_url = url_for('items/info', 'path=' . $app_path);

            $html = '
                <form name="payment_confirmation" id="payment_confirmation"  method="post">
                    <p class="to-pay">' . TEXT_EXT_TO_PAY . ': ' . $amount . ' ' . $cfg['currency'] . '</p>
                    <button type="button" class="btn btn-primary btn-pay">' . TEXT_EXT_BUTTON_PAY . '</button>                        
                </form>';

            $html .=
                <<<JSCODE
                   
<script>                    
    this.pay = function () {
    
    $('#ajax-modal').modal('toggle');
                    
    var data = {
        process_id: {$process_id}
    };
        
    data.CloudPayments = {        
        recurrent: {
         interval: '{$cfg['interval']}',
         period: {$cfg['period']},          
        }
    }; //создание подписки                
        
    var widget = new cp.CloudPayments({language: "{$cfg['language']}"});
       widget.pay('auth', // или 'charge'
           { //options
               publicId: '{$cfg['id']}', //id из личного кабинета
               description: '{$item_name}', //назначение
               amount: {$amount}, //сумма
               currency: 'RUB', //валюта
               accountId: '{$app_user['email']}', //идентификатор плательщика (необязательно)
               invoiceId: '{$item_info['id']}', //номер заказа  (необязательно)
               skin: "{$cfg['skin']}", //дизайн виджета (необязательно)
               data: data
           },
           {
               onSuccess: '{$success_url}',               
           }
       )
    };
    
    $('.btn-pay').click(pay)
</script>               
JSCODE;
        }

        return $html;
    }

    function ipn($module_id)
    {
        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $request = $_POST;

        $process_id = false;

        if (!empty($request['Data'])) {
            $request_data = json_decode(stripslashes($request['Data']), true);
            $process_id = $request_data['process_id'];
        }


        $process_info_query = db_query("select * from app_ext_processes where id='" . $process_id . "'", false);
        if ($app_process_info = db_fetch_array($process_info_query)) {
            $current_entity_id = $app_process_info['entities_id'];
            $current_item_id = $request['InvoiceId'] ?? 0;

            $item_info_query = db_query(
                "select e.* from app_entity_" . $current_entity_id . " e  where e.id='" . $current_item_id . "'",
                false
            );
            if ($item_info = db_fetch_array($item_info_query)) {
                $comment = '<b>' . TEXT_EXT_PAYMENT_NOTIFICATION . '</b><br>' .
                    TEXT_EXT_PAYMENT_MODULE . ': ' . $this->title . '<br>' .
                    TEXT_MODULE_PAYMENT_TOTAL . ': ' . number_format($request['Amount'], 2, '.', '') . ' ' . strtoupper(
                        $request['Currency']
                    ) . '<br>' .
                    TEXT_MODULE_TRANSACTION_ID . ': ' . $request['TransactionId'] . '<br>' .
                    TEXT_MODULE_PAYMENT_STATUS . ': ' . modules::status_label('Completed', 'Completed');

                $sql_data = [
                    'description' => $comment,
                    'entities_id' => $current_entity_id,
                    'items_id' => $current_item_id,
                    'date_added' => time(),
                    'created_by' => 0,
                ];

                db_perform('app_comments', $sql_data);

                //run process
                $processes = new processes($current_entity_id);
                $processes->items_id = $current_item_id;
                $processes->run($app_process_info, false, true);
            }
        }

        die('{"code":0}');
    }

}
