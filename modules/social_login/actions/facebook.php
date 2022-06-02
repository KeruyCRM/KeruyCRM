<?php

$redirect_uri = str_replace('social_login/facebook', 'social_login%2Ffacebook', url_for('social_login/facebook'));

if (isset($_GET['code']) && isset($_GET['state'])) {
    $postData = [
        'code' => urldecode(trim($_GET['code'])),
        'redirect_uri' => $redirect_uri,
        'client_id' => CFG_FACEBOOK_APP_ID,
        'client_secret' => CFG_FACEBOOK_SECRET_KEY
    ];

    //print_rr($postData);

    $ch = curl_init("https://graph.facebook.com/v10.0/oauth/access_token");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    $response = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($response, true);

    //print_rr($response);
    //exit();


    if (isset($response['error'])) {
        $alerts->add(TEXT_ERROR . ' ' . $response['error']['message'], 'error');
        redirect_to('users/login');
    } else {
        $authorization = "Bearer " . $response['access_token'];

        $ch = curl_init(
            "https://graph.facebook.com/me?fields=id,name,about,link,email,first_name,last_name,picture.width(60).height(60).as(picture_small),picture.width(320).height(320).as(picture_large)&access_token=" . $response['access_token']
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response = curl_exec($ch);
        curl_close($ch);


        $response = json_decode($response, true);

        //print_rr($response);
        //exit();

        if (isset($response['email'])) {
            $social_login->set_user([
                'first_name' => $response['first_name'],
                'last_name' => $response['last_name'],
                'photo' => $response['picture_small']['data']['url'],
                'email' => $response['email']
            ]);

            $social_login->login();
        }
    }
} else {
    $url = "https://www.facebook.com/v10.0/dialog/oauth?scope=email&client_id=" . CFG_FACEBOOK_APP_ID . "&state=" . mt_rand(
        ) . "&redirect_uri=" . $redirect_uri;
    header('Location: ' . $url);
}

exit();
