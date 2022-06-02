<?php

require_once 'plugins/ext/sms_modules/smsassistent/lib/sms_assistent.conf.php';
require_once 'plugins/ext/sms_modules/smsassistent/lib/sms_assistent.lib.php';

use SmsAssistentBy\Lib as ass_lib;

class smsassistent
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_SMSASSISTENT_TITLE;
        $this->site = 'http://sms-assistent.by';
        $this->api = 'https://goo.gl/ndRKnn';
        $this->version = '1.0';
        $this->country = 'BY';
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => '',
            'title' => 'API',
            'type' => 'text',
            'default' => TEXT_MODULE_SMSASSISTENT_INFO,
        ];

        $cfg[] = [
            'key' => 'login',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_SMSASSISTENT_LOGIN,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'password',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_SMSASSISTENT_PASSWORD,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'sender',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_SMSASSISTENT_SENDER,
            'description' => TEXT_MODULE_SMSASSISTENT_SENDER_INFO,
            'params' => ['class' => 'form-control input-large'],
        ];

        return $cfg;
    }

    function send($module_id, $destination = [], $text = '')
    {
        global $alerts;

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $phones = [];
        foreach ($destination as $phone) {
            $phone = preg_replace('/\D/', '', $phone);

            $phones[] = $phone;
        }

        $sms_assistent = new ass_lib\sms_assistent($cfg['login'], $cfg['password']);

        $result = $sms_assistent->sendSms($cfg['sender'], $phones, $text);

        //print_r($phones);
        //print_r($result);

        if (isset($result['error'])) {
            if ($result['error'] == 1) {
                $alerts->add($this->title . ' ' . TEXT_ERROR . ' ' . $result['error_messages'][0], 'error');
            }
        }
        //exit();
    }

}