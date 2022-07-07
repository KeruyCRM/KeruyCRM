<?php

namespace Helpers;

class Security extends \Prefab
{
    private $_DELIMITER = '::';

    public function checkCsrfToken()
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
                \Helpers\Urls::redirect_to(\K::$fw->URI);
            } else {
                return true;
            }
        }
    }

    private function validateToken($postToken)
    {
        try {
            $explode = explode(':', $postToken);
            $saltSend = $explode[0];
            $timeSend = $explode[1];
            $tokenSend = $explode[2];

            $timeDecode = $this->decrypt36($timeSend, $saltSend . $this->_DELIMITER . \K::$fw->app_token);

            if ($timeDecode + \K::$fw->CFG_TOKEN_LIFE < time()) {
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

    public function getAppToken()
    {
        $app_token = '';
        try {
            $bytesWithMargin = random_bytes(\K::$fw->CFG_TOKEN_LENGTH * 3);

            $base64 = base64_encode($bytesWithMargin);
            $purified = $this->purified($base64);
            $app_token = substr($purified, 0, \K::$fw->CFG_TOKEN_LENGTH);
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
            $salt = $this->getAppToken();

            $time = $this->encrypt36(time(), $salt . $this->_DELIMITER . \K::$fw->app_token);

            $hash = hash_hmac('sha256', $salt . $this->_DELIMITER . $time, \K::$fw->app_token, true);
            $base64 = base64_encode($hash);
            $purified = $this->purified($base64);
            $token = substr($purified, 0, \K::$fw->CFG_TOKEN_LENGTH);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $salt . ':' . $time . ':' . $token;
    }

    private function purified($string)
    {
        return str_replace(['+', '/', '='], ['_', '-', ''], $string);
    }

    private function encrypt36($n, $key)
    {
        $n ^= crc32($key);
        $encrypt = ((0x000000FF & $n) << 24) + (((0xFFFFFF00 & $n) >> 8) & 0x00FFFFFF);
        //return $this->purified(base64_encode($encrypt));
        return base_convert($encrypt, 10, 36);
    }

    private function decrypt36($n, $key)
    {
        $n = base_convert($n, 36, 10);
        $decrypt = ((0x00FFFFFF & $n) << 8) + (((0xFF000000 & $n) >> 24) & 0x000000FF);
        return $decrypt ^ crc32($key);
    }
}