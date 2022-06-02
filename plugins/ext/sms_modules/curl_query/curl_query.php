<?php

class curl_query
{

    public $title;
    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_CURL_QUERY_TITLE;
        $this->site = '';
        $this->api = '';
        $this->version = '1';
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'url',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_URL,
            'description' => TEXT_MODULE_CURL_QUERY_URL_INFO,
            'params' => ['class' => 'form-control input-xlarge required'],
        ];

        $cfg[] = [
            'key' => 'postfields',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_CURL_QUERY_PARAMS,
            'description' => TEXT_MODULE_CURL_QUERY_PARAMS_INFO,
            'params' => ['class' => 'form-control input-xlarge required'],
        ];

        $cfg[] = [
            'key' => 'userpwd',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_CURL_QUERY_USERPWD,
            'description' => TEXT_MODULE_CURL_QUERY_USERPWD_INFO,
            'params' => ['class' => 'form-control input-xlarge'],
        ];


        return $cfg;
    }

    function send($module_id, $destination = [], $text = '')
    {
        global $alerts;


        $cfg = modules::get_configuration($this->configuration(), $module_id);
        $url = ($cfg['url']);

        foreach ($destination as $phone) {
            $phone = preg_replace('/\D/', '', $phone);
            $text = strip_tags($text);

            $params = str_replace(['[PHONE]', '[TEXT]'], [$phone, str_replace('&', '', $text)], $cfg['postfields']);

            $postfields = [];
            parse_str($params, $postfields);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            if (strlen($cfg['userpwd'])) {
                curl_setopt($ch, CURLOPT_USERPWD, $cfg['userpwd']);
            }

            $result = curl_exec($ch);

            if (curl_errno($ch)) {
                $alerts->add($this->title . ' ' . TEXT_ERROR . ' ' . curl_error($ch), 'error');
            }

            curl_close($ch);
        }
    }

}
