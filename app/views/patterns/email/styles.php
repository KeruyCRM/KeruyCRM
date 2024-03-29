<?php

if (!defined('KERUY_CRM')) {
    exit;
}

$styles = [];

$styles['email_heading_content'] = [
    'div' => '',
    'h3' => 'font-family:  Arial; font-size: 18px; font-weight: normal;'
];

$styles['email_body_content'] = [
    'h4' => 'margin: 0px; font-family:  Arial; font-size: 15px; border-bottom: 1px solid rgb(228,240,252); color: rgb(128,128,128); line-height: 2;',
    'p' => '',
    'a' => 'font-size: 13px; font-family:  Arial;'
];

$styles['email_sidebar_content'] = [
    'h4' => 'margin: 0px; font-family:  Arial; font-size: 15px; border-bottom: 1px solid rgb(228,240,252); color: rgb(128,128,128); line-height: 2;',
    'table' => 'width: 100%',
    'tr' => '',
    'th' => 'width: 40%; text-align: left; vertical-align: top; font-family:  Arial; font-size: 13px; color: black; padding: 2px; border-bottom: 1px solid white;',
    'td' => 'text-align: left; vertical-align: top; font-family:  Arial; font-size: 13px; color: black; padding: 2px; border-bottom: 1px solid white;'
];

$styles['email_single_column'] = [
    'h4' => 'margin: 0px; font-family:  Arial; font-size: 15px; border-bottom: 1px solid #ddd; color: rgb(128,128,128); line-height: 2;',
    'table' => 'width: 100%',
    'tr' => '',
    'th' => 'width: 40%; text-align: left; vertical-align: top; font-family:  Arial; font-size: 13px; color: black; padding: 2px; border: 1px solid #ddd;',
    'td' => 'text-align: left; vertical-align: top; font-family:  Arial; font-size: 13px; color: black; padding: 2px; border: 1px solid #ddd;'
];

return $styles;