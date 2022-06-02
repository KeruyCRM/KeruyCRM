<?php

require('plugins/ext/sms_modules/iqsms/lib/iqsms_JsonGateV2.php');

use iqsms_JsonGateV2\iqsms_JsonGate;

class iqsms
{

    public $title;
    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_IQSMS_TITLE;
        $this->site = 'https://iqsms.ru';
        $this->api = 'https://iqsms.ru/api/api_json-php/';
        $this->version = '1.0';
        $this->country = 'RU';
    }

    public function configuration()
    {
        $cfg = [];


        $cfg[] = [
            'key' => 'login',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_IQSMS_LOGIN,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'password',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_IQSMS_PASSWORD,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'sign',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_IQSMS_SIGN,
            'description' => TEXT_MODULE_IQSMS_SIGN_INFO,
            'params' => ['class' => 'form-control input-large'],
        ];

        return $cfg;
    }

    function send($module_id, $destination = [], $text = '')
    {
        global $alerts;

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $sms = new iqsms_JsonGate($cfg['login'], $cfg['password']);

        $i = 1;

        foreach ($destination as $phone) {
            $phone = preg_replace('/\D/', '', $phone);

            $messages = [];
            $messages[] = [
                'clientId' => $i++,
                'phone' => $phone,
                'text' => $text,
                'sender' => $cfg['sign']
            ];

            $response = $sms->send($messages);

            //print_rr($response);
            //exit();

            if ($response['status'] != 'ok') {
                $alerts->add(
                    $this->title . ' ' . TEXT_ERROR . ' ' . $response['description'] . ' ' . $response['message']['0']['status'],
                    'error'
                );
            }
        }
    }

}
