<?php

require('plugins/ext/sms_modules/twilio/twilio-php-master/src/Twilio/autoload.php');

use Twilio\Rest\Client;

class twilio
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_TWILIO_TITLE;
        $this->site = 'https://www.twilio.com';
        $this->api = 'https://www.twilio.com/docs/usage/api';
        $this->version = '1';
    }

    public function configuration()
    {
        $cfg = [];


        $cfg[] = [
            'key' => 'sid',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_TWILIO_SID,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'token',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_TWILIO_AUTH_TOKEN,
            'description' => TEXT_MODULE_TWILIO_AUTH_TOKEN_INFO,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'phone_number',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_PHONE,
            'description' => TEXT_MODULE_TWILIO_PHONE_INFO,
            'params' => ['class' => 'form-control input-large required'],
        ];


        return $cfg;
    }

    function send($module_id, $destination = [], $text = '')
    {
        global $alerts;

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $client = new Client($cfg['sid'], $cfg['token']);

        foreach ($destination as $phone) {
            $phone = '+' . preg_replace('/\D/', '', $phone);

            try {
                // Use the client to do fun stuff like send text messages!
                $response = $client->messages->create(
                // the number you'd like to send the message to
                    $phone,
                    [
                        // A Twilio phone number you purchased at twilio.com/console
                        'from' => $cfg['phone_number'],
                        // the body of the text message you'd like to send
                        'body' => $text
                    ]
                );
            } catch (Exception $e) {
                $alerts->add($this->title . ' ' . TEXT_ERROR . ' ' . $e->getMessage(), 'error');
            }
        }
    }

}