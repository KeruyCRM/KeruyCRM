<?php

class nbu
{
    public $title;

    function __construct()
    {
        $this->title = TEXT_EXT_CURRENCY_MODULE_NBU_TITLE;
    }

    static function rate($from, $to)
    {
        $ch = curl_init('https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);
        $currencies = json_decode($data, true);

        $from_value = false;
        $to_value = false;

        if ($from == 'UAH') {
            $from_value = 1;
        }
        if ($to == 'UAH') {
            $to_value = 1;
        }

        foreach ($currencies as $currency) {
            if ($currency['cc'] == $to) {
                $to_value = $currency['rate'];
            }
            if ($currency['cc'] == $from) {
                $from_value = $currency['rate'];
            }
        }

        if ($from_value and $to_value) {
            return ($from_value / $to_value);
        } else {
            return false;
        }
    }
}