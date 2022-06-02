<?php

if (!empty($_GET['code'])) {
    // Отправляем код для получения токена (POST-запрос).
    $params = [
        'grant_type' => 'authorization_code',
        'code' => $_GET['code'],
        'client_id' => CFG_YANDEX_APP_ID,
        'client_secret' => CFG_YANDEX_SECRET_KEY,
    ];

    $ch = curl_init('https://oauth.yandex.ru/token');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $data = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($data, true);
    if (!empty($data['access_token'])) {
        // Токен получили, получаем данные пользователя.
        $ch = curl_init('https://login.yandex.ru/info');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['format' => 'json']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: OAuth ' . $data['access_token']]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $info = curl_exec($ch);
        curl_close($ch);

        $info = json_decode($info, true);

        //print_rr($info);
        //echo 'https://avatars.yandex.net/get-yapic/' . $info['default_avatar_id'] . '/islands-200';
        //exit();

        $social_login->set_user([
            'first_name' => $info['first_name'],
            'last_name' => $info['last_name'],
            'photo' => 'https://avatars.yandex.net/get-yapic/' . $info['default_avatar_id'] . '/islands-200',
            'email' => $info['default_email']
        ]);

        $social_login->login();
    }
} else {
    $params = [
        'client_id' => CFG_YANDEX_APP_ID,
        'redirect_uri' => url_for('social_login/yandex'),
        'response_type' => 'code',
    ];

    $url = 'https://oauth.yandex.ru/authorize?' . urldecode(http_build_query($params));

    header('Location: ' . $url);
}

exit();
