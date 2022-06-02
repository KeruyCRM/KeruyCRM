<?php

$data = [
//zero value
    'nul' => 'нуль',
    //form 1-9
    'ten' =>
        [
            ['', 'одна', 'дві', 'три', 'чотири', 'п\'ять', 'шість', 'сім', 'вісім', 'дев\'ять'],
            ['', 'одна', 'дві', 'три', 'чотири', 'п\'ять', 'шість', 'сім', 'вісім', 'дев\'ять'],
        ],
    //from 10-19
    'a20' =>
        [
            'десять',
            'одинадцять',
            'дванадцять',
            'тринадцять',
            'чотирнадцять',
            'п\'ятнадцять',
            'шістнадцять',
            'сімнадцять',
            'вісімнадцять',
            'дев\'ятнадцять'
        ],
    //from 20-90
    'tens' =>
        [2 => 'двадцять', 'тридцять', 'сорок', 'п\'ятдесят', 'шістдесят', 'сімдесят', 'вісімдесят', 'дев\'яносто'],
    //from 100-900
    'hundred' =>
        ['', 'сто', 'двісті', 'триста', 'чотириста', 'п\'ятсот', 'шістсот', 'сімсот', 'вісімсот', 'дев\'ятсот'],
    //units
    'unit' =>
        [ // Units
            ['копійка', 'копійки', 'копійок', 1],
            ['гривня', 'гривні', 'гривень', 0],
            ['тисяча', 'тисячі', 'тисяч', 1],
            ['мільйон', 'мільйона', 'мільйонів', 0],
            ['мільярд', 'міліарда', 'мільярдів', 0],
        ]
];