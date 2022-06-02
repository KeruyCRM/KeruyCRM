<?php

class ecb
{
    public $title;

    function __construct()
    {
        $this->title = TEXT_EXT_CURRENCY_MODULE_ECB_TITLE;
    }

    static function rate($from, $to)
    {
        $ch = curl_init('https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);

        $xml = simplexml_load_string($data);
        $json = json_encode($xml);
        $currencies = json_decode($json, true);

        $from_value = false;
        $to_value = false;

        if ($from == 'EUR') {
            $from_value = 1;
        }
        if ($to == 'EUR') {
            $to_value = 1;
        }

        foreach ($currencies['Cube']['Cube']['Cube'] as $currency) {
            if ($currency['@attributes']['currency'] == $to) {
                $to_value = $currency['@attributes']['rate'];
            }
            if ($currency['@attributes']['currency'] == $from) {
                $from_value = $currency['@attributes']['rate'];
            }
        }

        if ($from_value and $to_value) {
            return ($to_value / $from_value);
        } else {
            return false;
        }
    }
}