<?php

class mediasendkz
{

    public $title;
    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_MEDIASENDKZ_TITLE;
        $this->site = 'https://u-marketing.org';
        $this->api = 'https://u-marketing.org/api';
        $this->version = '1.0';
        $this->country = 'RU';
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'api_key',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_EXT_API_KEY,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'appid',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_MEDIASENDKZ_APPID,
            'description' => TEXT_MODULE_MEDIASENDKZ_APPID_DESCRIPTION,
            'params' => ['class' => 'form-control input-large required'],
        ];


        return $cfg;
    }

    function send($module_id, $destination = [], $text = '')
    {
        global $alerts;

        $cfg = modules::get_configuration($this->configuration(), $module_id);
        $url = "https://cp.u-marketing.org/api/sms/add?apiKey=" . $cfg['api_key'];

        foreach ($destination as $phone) {
            $phone = preg_replace('/\D/', '', $phone);

            $params = [
                'text' => $text,
                'phones' => [
                    ['name' => '', 'surname' => '', 'phone' => $phone],
                ],
                'appid' => $cfg['appid']
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $result = curl_exec($ch);
            curl_close($ch);

            //var_dump($result);
            //exit();

            if ($result) {
                $result = $this->prepare_result(trim($result));

                $result = json_decode($result, true);

                if ($result['success'] == false) {
                    $alerts->add(
                        $this->title . ' ' . TEXT_ERROR . ' (' . $result['code'] . ') ' . $result['message'],
                        'error'
                    );
                }
            }
        }
    }

    function prepare_result($str)
    {
        // This will remove unwanted characters.
        // Check http://www.php.net/chr for details
        for ($i = 0; $i <= 31; ++$i) {
            $str = str_replace(chr($i), "", $str);
        }

        $str = str_replace(chr(127), "", $str);

        // This is the most common part
        // Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
        // here we detect it and we remove it, basically it's the first 3 characters 
        if (0 === strpos(bin2hex($str), 'efbbbf')) {
            $str = substr($str, 3);
        }

        return $str;
    }

}
