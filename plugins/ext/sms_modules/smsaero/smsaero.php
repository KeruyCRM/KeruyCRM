<?php

require('plugins/ext/sms_modules/smsaero/lib/smsareo_api2.php');

use SmsaeroApiV2\SmsaeroApiV2;

class smsaero
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_SMSAERO_TITLE;
        $this->site = 'https://smsaero.ru';
        $this->api = 'https://smsaero.ru/description/api/';
        $this->version = '2.1';
        $this->country = 'RU';
    }

    public function configuration()
    {
        $cfg = [];


        $cfg[] = [
            'key' => 'login',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_SMSAERO_LOGIN,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'password',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_SMSAERO_PASSWORD,
            'description' => TEXT_MODULE_SMSAERO_PASSWORD_INFO,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'sign',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_SMSAERO_SIGN,
            'description' => TEXT_MODULE_SMSAERO_SIGN_INFO,
            'params' => ['class' => 'form-control input-large'],
        ];

        $cfg[] = [
            'key' => 'channel',
            'type' => 'dorpdown',
            'default' => 'INFO',
            'choices' => [
                'INFO' => 'INFO',
                'DIGITAL' => 'DIGITAL',
                'INTERNATIONAL' => 'INTERNATIONAL',
                'DIRECT' => 'DIRECT',
                'SERVICE' => 'SERVICE',
            ],
            'description' => TEXT_MODULE_SMSAERO_CHANNEL_INFO,
            'title' => TEXT_MODULE_SMSAERO_CHANNEL,
            'params' => ['class' => 'form-control input-medium required'],
        ];


        return $cfg;
    }

    function send($module_id, $destination = [], $text = '')
    {
        global $alerts;

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $channel = (!strlen($cfg['channel']) ? 'INFO' : $cfg['channel']);

        $sms = new SmsaeroApiV2($cfg['login'], $cfg['password'], $cfg['sign']);

        foreach ($destination as $phone) {
            $phone = preg_replace('/\D/', '', $phone);

            $response = $sms->send($phone, $text, $channel);

            if (!$response['success']) {
                $alerts->add(
                    $this->title . ' ' . TEXT_ERROR . ' ' . $response['message'] . '<br>' . print_r(
                        $response['data'],
                        true
                    ),
                    'error'
                );
            }
        }
    }

}