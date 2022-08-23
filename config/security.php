<?php

\K::fw()->mset([
    /**
     * Google reCAPTCHA
     * If keys entered then reCAPTCHA will be display on login screen
     * You can get keys here https://www.google.com/recaptcha/admin
     */

    'CFG_RECAPTCHA_ENABLE' => false,
    'CFG_RECAPTCHA_KEY' => '',
    'CFG_RECAPTCHA_SECRET_KEY' => '',
    'CFG_RECAPTCHA_TRUSTED_IP' => '',

    /**
     * Restricted countries
     * Enter allowed countries list by comma, for example: UA,US
     */

    'CFG_RESTRICTED_COUNTRIES_ENABLE' => false,
    'CFG_ALLOWED_COUNTRIES_LIST' => '',

    /**
     * Restriction by IP
     * Enter allowed IP list by comma, for example: 192.168.2.1,192.168.2.2
     */

    'CFG_RESTRICTED_BY_IP_ENABLE' => false,
    'CFG_ALLOWED_IP_LIST' => '',

    'CFG_VERIFICATION_CODE_LENGTH' => 6,
    'CFG_TOKEN_LIFE' => 1200,
    'CFG_TOKEN_LENGTH' => 32,
    'CFG_SESSION_CHECK_IP' => false,
    'CFG_SESSION_CHECK_BROWSER' => true,
    'CFG_COOKIE_TIME_REMEMBER_ME' => 60 * 60 * 24 * 30 //1 month in sec
]);