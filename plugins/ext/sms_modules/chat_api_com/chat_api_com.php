<?php

class chat_api_com
{
    public $title;
    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_CHAT_API_COM_TITLE;
        $this->site = 'https://chat-api.com';
        $this->api = 'https://chat-api.com/en/docs.html#post_message';
        $this->version = '1.0';
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'token',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_CHAT_API_COM_TOKEN,
            'description' => TEXT_MODULE_CHAT_API_COM_TOKEN_DESCRIPTION,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'instance_id',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_CHAT_API_COM_INSTANCE_ID,
            'params' => ['class' => 'form-control input-large required'],
        ];


        return $cfg;
    }

    function send($module_id, $destination = [], $text = '')
    {
        global $alerts;


        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $token = $cfg['token'];
        $instanceId = $cfg['instance_id'];
        $url = 'https://api.chat-api.com/instance' . $instanceId . '/message?token=' . $token;

        foreach ($destination as $phone) {
            if (strstr($phone, '@')) {
                $params = [
                    'chatId' => $phone,
                    'body' => $text,
                ];
            } else {
                $phone = preg_replace('/\D/', '', $phone);
                $params = [
                    'phone' => $phone,
                    'body' => $text,
                ];
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $result = curl_exec($ch);
            curl_close($ch);

            if ($result) {
                $result = json_decode($result, true);

                if (isset($result['sent']) and $result['sent'] != true) {
                    $alerts->add($this->title . ' ' . TEXT_ERROR . ' ' . $result['message'], 'error');
                }

                if (isset($result['error'])) {
                    $alerts->add($this->title . ' ' . TEXT_ERROR . ' ' . $result['error'], 'error');
                }
            }
        }
    }

}
