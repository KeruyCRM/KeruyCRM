<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Configuration;

class Emails_send_test extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Configuration\_Module::top();
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'emails_send_test.php';

        echo \K::view()->render($this->app_layout);
    }

    public function send()
    {
        if (\K::$fw->VERB == 'POST') {
            $options = [
                'to' => \K::$fw->POST['send_to'],
                'to_name' => '',
                'subject' => \K::$fw->TEXT_TEST_EMAIL_SUBJECT,
                'body' => \K::$fw->TEXT_TEST_EMAIL_SUBJECT,
                'from' => \K::$fw->app_user['email'],
                'from_name' => \K::$fw->app_user['name'],
                'send_directly' => true,
            ];

            if (\Models\Main\Users\Users::send_email($options)) {
                \K::flash()->addMessage(\K::$fw->TEXT_EMAIL_SENT, 'success');
            }

            \Helpers\Urls::redirect_to('main/configuration/emails_send_test', 'send_to=' . \K::$fw->POST['send_to']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}