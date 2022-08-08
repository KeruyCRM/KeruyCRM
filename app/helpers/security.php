<?php

namespace Helpers;

class Security extends \Prefab
{
    private $_DELIMITER = '::';

    public function checkCsrfToken($redirect_to = 'main/dashboard/token_error')
    {
        if (\K::$fw->VERB == 'POST') {
            if (\K::fw()->exists('TOKEN_DISABLED')) {
                return true;
            }

            if (
                !\K::app_session_is_registered('app_token')
                or !\K::fw()->exists('POST.form_session_token', $postToken)
                or !$this->validateToken($postToken)) {
                \K::flash()->addMessage(\K::$fw->TEXT_FROM_SESSION_ERROR, 'error');

                \Helpers\Urls::redirect_to($redirect_to);
            } else {
                return true;
            }
        }
    }

    public function checkCsrfTokenUrl($redirect_to = 'main/dashboard/token_error')
    {
        if (\K::fw()->exists('TOKEN_DISABLED')) {
            return true;
        }

        if (
            !\K::app_session_is_registered('app_token')
            or !\K::fw()->exists('GET.url_session_token', $getToken)
            or !$this->validateToken($getToken)) {
            \K::flash()->addMessage(\K::$fw->TEXT_FROM_SESSION_ERROR, 'error');

            \Helpers\Urls::redirect_to($redirect_to);
        } else {
            return true;
        }
    }

    private function validateToken($token)
    {
        try {
            //$explode = explode(':', $token);

            $saltSend = substr($token, 0, \K::$fw->CFG_TOKEN_LENGTH);
            $timeSend = substr($token, \K::$fw->CFG_TOKEN_LENGTH, strlen($token) - \K::$fw->CFG_TOKEN_LENGTH * 2);
            $tokenSend = substr($token, -\K::$fw->CFG_TOKEN_LENGTH);

            $timeDecode = $this->decrypt36($timeSend, $saltSend . $this->_DELIMITER . \K::$fw->app_token);

            if (\K::$fw->CFG_TOKEN_LIFE and ($timeDecode + \K::$fw->CFG_TOKEN_LIFE) < time()) {
                return false;
            }

            $hash = hash_hmac('sha256', $saltSend . $this->_DELIMITER . $timeSend, \K::$fw->app_token, true);
            $base64 = base64_encode($hash);
            $purified = $this->purified($base64);
            $tokenNew = substr($purified, 0, \K::$fw->CFG_TOKEN_LENGTH);

            return hash_equals($tokenNew, $tokenSend);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAppToken($length = 32)
    {
        $app_token = '';
        try {
            $bytesWithMargin = random_bytes($length * 3);

            $base64 = base64_encode($bytesWithMargin);
            $purified = $this->purified($base64);
            $app_token = substr($purified, 0, $length);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $app_token;
    }

    public function getAppSessionToken()
    {
        $token = '';
        $salt = '';
        $time = '';
        try {
            $salt = $this->getAppToken(\K::$fw->CFG_TOKEN_LENGTH);

            $time = $this->encrypt36(time(), $salt . $this->_DELIMITER . \K::$fw->app_token);

            $hash = hash_hmac('sha256', $salt . $this->_DELIMITER . $time, \K::$fw->app_token, true);
            $base64 = base64_encode($hash);
            $purified = $this->purified($base64);
            $token = substr($purified, 0, \K::$fw->CFG_TOKEN_LENGTH);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $salt . $time . $token;
    }

    public function addTokenToUrl()
    {
        return 'url_session_token=' . $this->getAppSessionToken();
    }

    private function purified($string)
    {
        return str_replace(['+', '/', '='], ['_', '-', ''], $string);
    }

    private function encrypt36($n, $key)
    {
        $n ^= crc32($key);
        $encrypt = ((0x000000FF & $n) << 24) + (((0xFFFFFF00 & $n) >> 8) & 0x00FFFFFF);
        return base_convert($encrypt, 10, 36);
    }

    private function decrypt36($n, $key)
    {
        $n = base_convert($n, 36, 10);
        $decrypt = ((0x00FFFFFF & $n) << 8) + (((0xFF000000 & $n) >> 24) & 0x000000FF);
        return $decrypt ^ crc32($key);
    }
}