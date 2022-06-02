<?php

if (isset($_GET['code']) && isset($_GET['state'])) {
    $postData = [
        'grant_type' => 'authorization_code',
        'code' => urldecode(trim($_GET['code'])),
        'redirect_uri' => url_for('social_login/linkedin'),
        'client_id' => CFG_LINKEDIN_APP_ID,
        'client_secret' => CFG_LINKEDIN_SECRET_KEY
    ];

    //print_rr($postData);

    $ch = curl_init("https://www.linkedin.com/oauth/v2/accessToken");
    curl_setopt($ch, CURLOPT_HEADER, ['Content-Type' => 'application/x-www-form-urlencoded']);
    //curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    $response = curl_exec($ch);

    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($response, $header_size);

    curl_close($ch);

    if (!$response) {
        die('Curl error: ' . curl_error($ch));
    }

    //print_rr($response);

    $response = json_decode($body, true);

    //print_rr($response);

    if (isset($response['error'])) {
        $alerts->add(TEXT_ERROR . ' ' . $response['error'] . ' (' . $response['error_description'] . ')', 'error');
        redirect_to('users/login');
    } else {
        $access_token = $response['access_token'];

        $ch = curl_init(
            'https://api.linkedin.com/v2/me?projection=(id,firstName,lastName,emailAddress,profilePicture(displayImage~:playableStreams))&oauth2_access_token=' . $access_token
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            die('Curl error: ' . curl_error($ch));
        }

        //print_rr($response);

        $response = json_decode($response, true);

        //print_rr($response);


        $ch = curl_init(
            'https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))&oauth2_access_token=' . $access_token
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response2 = curl_exec($ch);
        curl_close($ch);

        if (!$response2) {
            die('Curl error: ' . curl_error($ch));
        }

        $response2 = json_decode($response2, true);

        //print_rr($response2);


        if (isset($response2['elements'][0]['handle~']['emailAddress'])) {
            $social_login->set_user([
                'first_name' => current($response['firstName']['localized']),
                'last_name' => current($response['lastName']['localized']),
                'photo' => $response['profilePicture']['displayImage~']['elements'][0]['identifiers'][0]['identifier'],
                'email' => $response2['elements'][0]['handle~']['emailAddress']
            ]);

            //print_rr($social_login);
            //exit();

            $social_login->login();
        }
    }
} else {
    $url = 'https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=' . CFG_LINKEDIN_APP_ID . '&redirect_uri=' . urlencode(
            url_for('social_login/linkedin')
        ) . '&state=' . mt_rand() . '&scope=r_liteprofile,r_emailaddress';
    header('Location: ' . $url);
}

exit();
