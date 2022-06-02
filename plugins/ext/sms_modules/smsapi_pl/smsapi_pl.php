<?php

class smsapi_pl
{

    public $title;
    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_SMSAPI_PL_TITLE;
        $this->site = 'https://smsapi.pl';
        $this->api = 'https://www.smsapi.pl/docs/?php--curl#2-pojedynczy-sms';
        $this->version = '1.0';
        $this->country = 'PL';
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'token',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_SMSAPI_PL_TOKEN,
            'description' => TEXT_MODULE_SMSAPI_PL_TOKEN_INFO,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'sender',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_SMSAPI_PL_SENDER,
            'description' => TEXT_MODULE_SMSAPI_PL_SENDER_INFO,
            'params' => ['class' => 'form-control input-large required'],
        ];


        return $cfg;
    }

    function send($module_id, $destination = [], $text = '')
    {
        global $alerts;

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $token = $cfg['token'];

        foreach ($destination as $phone) {
            $phone = preg_replace('/\D/', '', $phone);

            $params = [
                'to' => $phone, //numery odbiorców rozdzielone przecinkami
                'from' => $cfg['sender'], //pole nadawcy stworzone w https://ssl.smsapi.pl/sms_settings/sendernames
                'message' => strip_tags($text), //treść wiadomości
                'format' => 'json'
            ];

            //print_rr($params);


            $result = $this->sms_send($params, $token);

            if ($result) {
                $result = json_decode($result, true);

                if (isset($result['error'])) {
                    $alerts->add(
                        $this->title . ' ' . TEXT_ERROR . ' ' . $result['error'] . ' ' . $result['message'],
                        'error'
                    );
                }
            }

            //print_rr($result);
            //exit();
        }
    }

    function sms_send($params, $token, $backup = false)
    {
        static $content;

        if ($backup == true) {
            $url = 'https://api2.smsapi.pl/sms.do';
        } else {
            $url = 'https://api.smsapi.pl/sms.do';
        }

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $params);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token"
        ]);

        $content = curl_exec($c);
        $http_status = curl_getinfo($c, CURLINFO_HTTP_CODE);

        if ($http_status != 200 && $backup == false) {
            $backup = true;
            $this->sms_send($params, $token, $backup);
        }

        curl_close($c);
        return $content;
    }


}
