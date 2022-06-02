<?php

class bnr
{
    public $title;

    function __construct()
    {
        $this->title = 'BNR - Romanian National Bank';
    }

    static function rate($from, $to)
    {
        $ch = curl_init('https://bnr.ro/nbrfxrates.xml');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);

        $xml = simplexml_load_string($data);
        $json = json_encode($xml);
        $currencies = json_decode($json, true);

        $from_value = false;
        $to_value = false;

        if ($from == 'RON') {
            $from_value = 1;
        }
        if ($to == 'RON') {
            $to_value = 1;
        }

        $nIndex = 0;
        foreach ($xml->Body->Cube->Rate as $currency) {
            $multiplication = $xml->Body->Cube->Rate[$nIndex]->attributes(
            )[1] != null ? $xml->Body->Cube->Rate[$nIndex]->attributes()[1] : 1;

            if ($currency->attributes()[0] == $to) {
                $to_value = (float)$xml->Body->Cube->Rate[$nIndex] / $multiplication;
            }
            if ($currency->attributes()[0] == $from) {
                $from_value = (float)$xml->Body->Cube->Rate[$nIndex] / $multiplication;
            }
            $nIndex++;
        }

        if ($from_value and $to_value) {
            return ($from_value / $to_value);
        } else {
            return false;
        }
    }
}