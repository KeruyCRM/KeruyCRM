<?php

namespace Helpers;

class Security extends \Prefab
{
    private $_DELIMITER = '::';

    public function initCsrfToken()
    {
        echo $this->generateToken() . PHP_EOL;
        \K::f3()->set('csrf_token', $this->generateToken());
    }

    public function checkCsrfToken()
    {
        if (\K::f3()->VERB == 'POST') {
            if (\K::f3()->exists('TOKEN_DISABLED')) {
                return true;
            }

            if (
                !\K::sessionExists('app_session_token', $app_session_token)
                or !\K::f3()->exists('POST.csrf_token', $postToken)
                or !$this->validateToken($postToken)) {
                \K::f3()->error(400, 'Invalid CSRF token');
            } else {
                \K::f3()->error(200, 'TRUE');
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

            if (!\K::sessionExists('app_session_token', $app_session_token)) {
                return false;
            }

            $timeDecode = $this->decrypt36($timeSend, $saltSend . $this->_DELIMITER . $app_session_token);

            if ($timeDecode + \K::f3()->TOKEN_LIFE < time()) {
                return false;
            }

            $hash = hash_hmac('sha256', $saltSend . $this->_DELIMITER . $timeSend, $app_session_token, true);
            $base64 = base64_encode($hash);
            $purified = $this->purified($base64);
            $tokenNew = substr($purified, 0, \K::f3()->TOKEN_LENGTH);

            return hash_equals($tokenNew, $tokenSend);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function genAppSessionToken()
    {
        $app_session_token = '';
        try {
            $bytesWithMargin = random_bytes(\K::f3()->TOKEN_LENGTH * 3);

            $base64 = base64_encode($bytesWithMargin);
            $purified = $this->purified($base64);
            $app_session_token = substr($purified, 0, \K::f3()->TOKEN_LENGTH);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $app_session_token;
    }

    private function generateToken()
    {
        $token = '';
        $salt = '';
        $time = '';
        try {
            if (!\K::sessionExists('app_session_token', $app_session_token)) {
                $app_session_token = \K::sessionSet('app_session_token', $this->genAppSessionToken());
            }

            $salt = $this->genAppSessionToken();

            $time = $this->encrypt36(time(), $salt . $this->_DELIMITER . $app_session_token);

            $hash = hash_hmac('sha256', $salt . $this->_DELIMITER . $time, $app_session_token, true);
            $base64 = base64_encode($hash);
            $purified = $this->purified($base64);
            $token = substr($purified, 0, \K::f3()->TOKEN_LENGTH);
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