<?php

require('plugins/ext/sms_modules/sms_ru/lib/sms.ru.php');

class sms_ru
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_SMSRU_TITLE;
        $this->site = 'http://sms.ru';
        $this->api = 'http://sms.ru/api';
        $this->version = '1.0';
        $this->country = 'RU';
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'api_id',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_SMSRU_API_KEY,
            'description' => TEXT_MODULE_SMSRU_API_KEY_INFO,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'sign',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_SMSRU_SIGN,
            'description' => TEXT_MODULE_SMSRU_SIGN_INFO,
            'params' => ['class' => 'form-control input-large'],
        ];

        return $cfg;
    }

    function send($module_id, $destination = [], $text = '')
    {
        global $alerts;

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $sms = new SMSRU($cfg['api_id']);

        $data = new stdClass();
        //$data->test = 1; //Test mode
        $data->text = $text;
        if (strlen($cfg['sign']) > 0) {
            $data->from = $cfg['sign'];
        }
        $data->to = '';

        foreach ($destination as $phone) {
            $phone = preg_replace('/\D/', '', $phone);

            $data->to .= $phone . ',';
        }

        $data->to = substr($data->to, 0, -1);

        $request = $sms->send($data);

        if ($request->status == "OK") {
            foreach ($request->sms as $phone => $sms) {
                if ($sms->status != "OK") {
                    $alerts->add(
                        $this->title . ' ' . TEXT_MODULE_SMSRU_PHONE_ERROR . ' ' . $phone . ' ' . TEXT_ERROR . ' ' . $request->status_code . ' ' . $request->status_text,
                        'error'
                    );
                }
            }
        } else {
            $alerts->add(
                $this->title . ' ' . TEXT_ERROR . ' ' . $request->status_code . ' ' . $request->status_text,
                'error'
            );
        }
    }

}