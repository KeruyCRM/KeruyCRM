<?php

class telegram
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_TELEGRAM_TITLE;
        $this->site = 'https://telegram.org';
        $this->api = 'https://core.telegram.org/bots/api#sendmessage';
        $this->version = '2.0';
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'bot_token',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_TELEGRAM_BOT_TOKEN,
            'description' => TEXT_MODULE_TELEGRAM_BOT_TOKEN_DESCRIPTION,
            'params' => ['class' => 'form-control input-large required'],
        ];


        return $cfg;
    }

    function send($module_id, $destination = [], $text = '')
    {
        global $alerts;


        $cfg = modules::get_configuration($this->configuration(), $module_id);
        $url = "https://api.telegram.org/bot" . $cfg['bot_token'] . "/sendMessage";

        foreach ($destination as $chat_id) {
            $params = [
                'chat_id' => $chat_id,
                'text' => strip_tags($text, '<b><i><a><code><pre>'),
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => 'true',
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $result = curl_exec($ch);
            curl_close($ch);

            if ($result) {
                $result = json_decode($result, true);

                if (isset($result['error_code'])) {
                    $alerts->add(
                        $this->title . ' ' . TEXT_ERROR . ' ' . $result['error_code'] . ' ' . $result['description'] . '. (chat_id: ' . $chat_id . ')',
                        'error'
                    );
                }
            }
        }
    }

}