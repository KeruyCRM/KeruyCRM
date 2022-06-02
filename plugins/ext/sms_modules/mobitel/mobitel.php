<?php

class mobitel
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_MOBITEL_TITLE;
        $this->site = 'https://mobitel.lk';
        $this->api = 'https://www.mobitel.lk/contact-us';
        $this->version = '1.0';
        $this->country = 'LK';
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'alias',
            'type' => 'input',
            'default' => 'Mobitel',
            'title' => TEXT_MODULE_MOBITEL_ALIAS,
            'description' => TEXT_MODULE_MOBITEL_ALIAS_INFO,
            'params' => ['class' => 'form-control input-medium required'],
        ];

        $cfg[] = [
            'key' => 'username',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_USERNAME,
            'description' => '',
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'password',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_PASSWORD,
            'description' => '',
            'params' => ['class' => 'form-control input-large required'],
        ];

        return $cfg;
    }

    function send($module_id, $destination = [], $text = '')
    {
        global $alerts;

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $recipients = [];
        foreach ($destination as $phone) {
            $phone = preg_replace('/\D/', '', $phone);

            $params = [
                'm' => strip_tags($text),
                'r' => $phone,
                'a' => $cfg['alias'],
                'u' => $cfg['username'],
                'p' => $cfg['password'],
                't' => 0,
            ];

            $url = 'http://smeapps.mobitel.lk:8585/EnterpriseSMSV3/esmsproxy.php?' . http_build_query($params);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $result = curl_exec($ch);
            curl_close($ch);

            if ($result) {
                $result = substr($result, -3);
            }

            //print_rr($params);
            //print_rr($result);            
            //exit();

            if ($result != 200) {
                $response_codes = [
                    '151' => 'invalid session',
                    '152' => 'session is still in use for previous request',
                    '155' => 'service halted',
                    '156' => 'other network messaging disabled',
                    '157' => 'IDD messages disabled',
                    '159' => 'failed credit check',
                    '160' => 'no message found',
                    '161' => 'message exceeding 160 characters',
                    '162' => 'invalid message type found',
                    '164' => 'invalid group',
                    '165' => 'no recipients found',
                    '166' => 'recipient list exceeding allowed limit',
                    '167' => 'invalid long number',
                    '168' => 'invalid short code',
                    '169' => 'invalid alias',
                    '170' => 'black listed numbers in number list',
                    '171' => 'non-white listed numbers in number list',
                    '175' => 'deprecated method',
                ];

                $alerts->add(
                    $this->title . ' ' . TEXT_ERROR . ' ' . $result . ' - ' . ($response_codes[$result] ?? ''),
                    'error'
                );
            }
        }
    }

}