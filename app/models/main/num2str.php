<?php

namespace Models\Main;

class Num2str extends \Prefab
{
    public $data;

    function __construct()
    {
        $glob = glob('app/languages/num2str/*.php');

        foreach ($glob as $file) {
            if (is_file($file) and substr($file, -4) == '.php') {
                $entry = substr(substr($file, (strrpos($file, "/") + 1)), 0, -4);
                $data = require 'includes/languages/num2str/' . $entry;

                $this->data[$entry] = $data;
            }
        }
    }

    function prepare($text, $item)
    {
        foreach ($this->data as $code => $data) {
            if (preg_match_all(
                '/num2str_' . $code . '\({#(\w+):[^}]*}\)|num2str_' . $code . '\(\[(\d+)\]\)/',
                $text,
                $matches
            )) {
                foreach ($matches[1] as $matches_key => $filed_id) {
                    $number = '';

                    $filed_id = (strlen($matches[2][$matches_key]) ? $matches[2][$matches_key] : $filed_id);

                    //$field_query = db_query("select * from app_fields where id='" . (int)$filed_id . "'");

                    $field = \K::model()->db_fetch_one('app_fields', [
                        'id = ?',
                        $filed_id
                    ]);

                    if ($field) {
                        $value = $item['field_' . $field['id']] ?? '';

                        $output_options = [
                            'class' => $field['type'],
                            'value' => $value,
                            'field' => $field,
                            'item' => $item,
                            'is_export' => true,
                            'path' => $field['entities_id']
                        ];

                        $number = trim(\K::fw()->clean(\Models\Main\Fields_types::output($output_options)));
                    }

                    if (!strlen($number)) {
                        $number = 0;
                    }

                    $text = str_replace($matches[0][$matches_key], $this->convert($code, $number), $text);
                }
            }
        }

        return $text;
    }

    function convert($code, $num, $add_currency = true)
    {
        $nul = $this->data[$code]['nul'];
        $ten = $this->data[$code]['ten'];
        $a20 = $this->data[$code]['a20'];
        $tens = $this->data[$code]['tens'];
        $hundred = $this->data[$code]['hundred'];
        $unit = $this->data[$code]['unit'];

        [$banknote, $coin] = explode('.', sprintf("%015.2f", floatval($num)));

        $out = [];

        if (intval($banknote) > 0) {
            foreach (str_split($banknote, 3) as $uk => $v) { // by 3 symbols
                if (!intval($v)) {
                    continue;
                }

                $uk = sizeof($unit) - $uk - 1; // unit key
                $gender = $unit[$uk][3];

                [$i1, $i2, $i3] = array_map('intval', str_split($v, 1));

                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2 > 1) {
                    $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3];
                }# 20-99
                else {
                    $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3];
                }# 10-19 | 1-9
                // units without rub & kop
                if ($uk > 1) {
                    $out[] = $this->morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
                }
            } //foreach
        } else {
            $out[] = $nul;
        }

        if ($add_currency) {
            $out[] = $this->morph(intval($banknote), $unit[1][0], $unit[1][1], $unit[1][2]); // rub

            $out[] = $coin . ' ' . $this->morph($coin, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
        }

        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

    function morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;
        if ($n > 10 && $n < 20) {
            return $f5;
        }
        $n = $n % 10;
        if ($n > 1 && $n < 5) {
            return $f2;
        }
        if ($n == 1) {
            return $f1;
        }
        return $f5;
    }
}