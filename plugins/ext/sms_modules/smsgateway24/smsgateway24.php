<?php

class smsgateway24
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = 'SmsGateWay24';
        $this->site = 'https://smsgateway24.com';
        $this->api = 'https://smsgateway24.com/ru/docs/apidocumentation';
        $this->version = '1.0';
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'token',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_SMSGATEWAY24_TOKEN,
            'description' => '',
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'device_id',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_SMSGATEWAY24_DEVICE_ID,
            'description' => '',
            'params' => ['class' => 'form-control input-small required'],
        ];

        $cfg[] = [
            'key' => 'sim',
            'type' => 'input',
            'default' => 0,
            'title' => TEXT_MODULE_SMSGATEWAY24_SIM,
            'description' => '',
            'params' => ['class' => 'form-control input-xsmall required'],
        ];


        return $cfg;
    }

    function send($module_id, $destination = [], $text = '')
    {
        global $alerts;

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        foreach ($destination as $phone) {
            if (!strstr($phone, '+')) {
                $phone = '+' . $phone;
            }

            $params = [
                'token' => $cfg['token'],
                'sendto' => $phone,
                'body' => strip_tags($text),
                'device_id' => $cfg['device_id'],
                'sim' => $cfg['sim'],
            ];

            $ch = curl_init('https://smsgateway24.com/getdata/addsms');
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $result = curl_exec($ch);
            curl_close($ch);

            //print_r($result);
            //exit();

            if ($result) {
                $result = json_decode($result, true);

                if ($result['error'] == 1) {
                    $alerts->add($this->title . ' ' . TEXT_ERROR . ' ' . $result['message'], 'error');
                }
            }
        }
    }

}