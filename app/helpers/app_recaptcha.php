<?php

namespace Helpers;

class App_recaptcha
{
    static function is_enabled()
    {
        if (strlen(\K::$fw->CFG_RECAPTCHA_KEY) and strlen(
                \K::$fw->CFG_RECAPTCHA_SECRET_KEY
            ) and \K::$fw->CFG_RECAPTCHA_ENABLE == true) {
            if (!defined('CFG_RECAPTCHA_TRUSTED_IP')) {
                define('CFG_RECAPTCHA_TRUSTED_IP', '');
            }

            if (!\K::fw()->exists('CFG_RECAPTCHA_TRUSTED_IP')) {
                \K::$fw->CFG_RECAPTCHA_TRUSTED_IP = '';
            }

            if (strlen(\K::$fw->CFG_RECAPTCHA_TRUSTED_IP) and in_array(
                    \K::$fw->IP,
                    array_map('trim', explode(',', \K::$fw->CFG_RECAPTCHA_TRUSTED_IP))
                )) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    static function render_js()
    {
        if (self::is_enabled()) {
            return '<script src="https://www.google.com/recaptcha/api.js?hl=' . \K::$fw->TEXT_APP_LANGUAGE_SHORT_CODE . '"></script>';
        } else {
            return '';
        }
    }

    static function render()
    {
        return '<div class="g-recaptcha" data-sitekey="' . \K::$fw->CFG_RECAPTCHA_KEY . '"></div>';
    }

    static function verify()
    {
        require('app/libs/ReCaptcha/ReCaptcha.php');
        require('app/libs/ReCaptcha/RequestMethod.php');
        require('app/libs/ReCaptcha/RequestParameters.php');
        require('app/libs/ReCaptcha/Response.php');
        require('app/libs/ReCaptcha/RequestMethod/Curl.php');
        require('app/libs/ReCaptcha/RequestMethod/CurlPost.php');

        $recaptcha = new \ReCaptcha\ReCaptcha(\K::$fw->CFG_RECAPTCHA_SECRET_KEY);
        $resp = $recaptcha->verify(\K::fw()->get('POST.g-recaptcha-response'), \K::$fw->IP);

        return $resp->isSuccess();
    }
}