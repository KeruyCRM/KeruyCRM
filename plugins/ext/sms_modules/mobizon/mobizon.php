<?php

require('plugins/ext/sms_modules/mobizon/lib/MobizonApi.php');

class mobizon
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_MOBIZON_TITLE;
        $this->site = 'https://mobizon.com/ru';
        $this->api = 'https://mobizon.ua/help/api-docs/message#SendSmsMessage';
        $this->version = '1.0';
        $this->country = 'RU';
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'api_key',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_EXT_API_KEY,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'api_server',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_MOBIZON_API_SERVER,
            'description' => 'api.mobizon.kz | api.mobizon.ua',
            'params' => ['class' => 'form-control input-medium required'],
        ];

        $cfg[] = [
            'key' => 'from',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_MOBIZON_API_FROM,
            'description' => TEXT_MODULE_MOBIZON_API_DESCRIPTION,
            'params' => ['class' => 'form-control input-medium'],
        ];

        return $cfg;
    }

    function send($module_id, $destination = [], $text = '')
    {
        global $alerts;

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $api = new Mobizon\MobizonApi($cfg['api_key'], $cfg['api_server']);

        foreach ($destination as $phone) {
            $phone = preg_replace('/\D/', '', $phone);

            $params = [
                // Recipient international phone number
                'recipient' => $phone,
                // Message text
                'text' => $text,
            ];

            //Alphaname is optional, if you don't have registered alphaname, just skip this parameter and your message will be sent with our free common alphaname, if it's available for this direction.
            if (strlen($cfg['from'])) {
                $params['from'] = $cfg['from'];
            }

            // API call to send a message
            if ($api->call('message', 'sendSMSMessage', $params)) {
                // Get message ID assigned by our system to request it's delivery report later.
                $messageId = $api->getData('messageId');

                if (!$messageId) {
                    // Message is not accepted, see error code and data for details.
                }
                // Message has been accepted by API.
            } else {
                // An error occurred while sending message
                $alerts->add(
                    $this->title . ' ' . TEXT_ERROR . ' ' . '[' . $api->getCode() . '] ' . $api->getMessage(),
                    'error'
                );
            }
        }
    }

}