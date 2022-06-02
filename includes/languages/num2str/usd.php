<?php

$data = [
//zero value
    'nul' => 'zero',
    //form 1-9
    'ten' =>
        [
            ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'],
            ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'],
        ],
    //from 10-19
    'a20' =>
        ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'],
    //from 20-90
    'tens' =>
        [2 => 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'],
    //from 100-900
    'hundred' =>
        [
            '',
            'one hundred',
            'two hundred',
            'three hundred',
            'four hundred',
            'five hundred',
            'six hundred',
            'seven hundred',
            'eight hundred',
            'nine hundred'
        ],
    //units
    'unit' =>
        [ // Units
            ['penny', 'penny', 'penny', 1],
            ['dollar', 'dollars', 'dollars', 0],
            ['thousand', 'thousands', 'thousands', 1],
            ['million', 'million', 'million', 0],
            ['billion', 'billion', 'billions', 0],
        ]
];