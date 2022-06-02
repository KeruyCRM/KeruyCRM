<?php

class turbosms
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_TURBOSMS_TITLE;
        $this->site = 'https://turbosms.ua';
        $this->api = 'https://turbosms.ua/soap.html';
        $this->version = '1.0';
        $this->country = 'UA';
    }

    public function configuration()
    {
        $cfg = [];


        $cfg[] = [
            'key' => 'connection_method',
            'type' => 'text',
            'default' => 'SOAP',
            'description' => (!extension_loaded(
                'soap'
            ) ? '<span class="label label-danger">' . TEXT_MODULE_TURBOSMS_SOAP_ERROR . '</span>' : ''),
            'title' => TEXT_MODULE_TURBOSMS_CONNECTION_METHOD,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'login',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_TURBOSMS_LOGIN,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'password',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_TURBOSMS_PASSWORD,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'sender',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_TURBOSMS_SENDER,
            'params' => ['class' => 'form-control input-large required'],
        ];


        return $cfg;
    }

    function send($module_id, $destination = [], $text = '')
    {
        global $alerts;

        if (!extension_loaded('soap')) {
            return false;
        }

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        try {
            $client = new SoapClient('http://turbosms.in.ua/api/wsdl.html');

            $auth = [
                'login' => $cfg['login'],
                'password' => $cfg['password']
            ];

            $result = $client->Auth($auth);

            $sms = [
                'sender' => $cfg['sender'],
                'destination' => implode(',', $destination),
                'text' => $text
            ];

            $result = $client->SendSMS($sms);

            if (strlen($result->SendSMSResult->ResultArray[0]) == 1) {
                $alerts->add($this->title . ' ' . TEXT_ERROR . ' ' . $result->SendSMSResult->ResultArray, 'error');
            } else {
                //$alerts->add($result->SendSMSResult->ResultArray[0],'success');
            }
        } catch (Exception $e) {
            $alerts->add($this->title . ' ' . TEXT_ERROR . ' ' . $e->getMessage(), 'error');
        }
    }


}