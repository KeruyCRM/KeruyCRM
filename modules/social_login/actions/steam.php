<?php

require('includes/libs/social_login/Steam/SteamAuthentication/4.0/steamauth/openid.php');

try {
    $openid = new LightOpenID($_SERVER['SERVER_NAME']);

    if (!$openid->mode) {
        $openid->identity = 'https://steamcommunity.com/openid';
        header('Location: ' . $openid->authUrl());
    } elseif ($openid->mode == 'cancel') {
        echo 'User has canceled authentication!';
    } else {
        if ($openid->validate()) {
            $id = $openid->identity;
            $ptn = "/^https?:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
            if (preg_match($ptn, $id, $matches)) {
                $steamid = $matches[1];

                $url = "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" . CFG_STEAM_API_KEY . "&steamids=" . $steamid;

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                $result = curl_exec($ch);
                curl_close($ch);

                //var_dump($result);
                //exit();

                if ($result) {
                    if ($content = json_decode($result, true)) {
                        $personaname = $content['response']['players'][0]['personaname'];
                        $realname = $content['response']['players'][0]['realname'] ?? '';

                        if (strlen($realname) and strstr($realname, ' ')) {
                            $name = explode(' ', $realname);
                            $personaname = trim($name[0]);
                            $realname = trim(substr($realname, strlen($personaname)));
                        }

                        //print_rr($content);
                        //exit();

                        $social_login->set_user([
                            'first_name' => $personaname,
                            'last_name' => $realname,
                            'photo' => $content['response']['players'][0]['avatarfull'] ?? '',
                            'email' => $steamid . '@' . $_SERVER['SERVER_NAME'],
                            'username' => $steamid,
                            'social' => 'steam',
                        ]);

                        $social_login->login();
                    }
                }
            } else {
                echo "Steam ID not found!";
            }
        } else {
            echo "User is not logged in!";
        }
    }
} catch (ErrorException $e) {
    echo $e->getMessage();
}

exit();


