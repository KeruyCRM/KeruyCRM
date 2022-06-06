<?php

include("includes/libs/social_login/Vkontakte/Vkontakte.php");

$heateorSsVkontakte = new Vkontakte([
    'client_id' => CFG_VKONTAKTE_APP_ID,
    'client_secret' => CFG_VKONTAKTE_SECRET_KEY,
    'redirect_uri' => url_for('social_login/vkontakte')
]);
$heateorSsVkontakte->setScope(['email']);


if (isset($_GET['code'])) {
    $heateorSsVkontakte->authenticate($_GET['code']);
    $userId = $heateorSsVkontakte->getUserId();
    $email = $heateorSsVkontakte->getUserEmail();

    //check if there is accass to email
    if (!$email) {
        $alerts->add(TEXT_ERROR_USEREMAIL_EMPTY, 'error');
        redirect_to('users/login');
    }

    if ($userId) {
        $users = $heateorSsVkontakte->api('users.get', [
            'user_id' => $userId,
            'fields' => ['first_name', 'last_name', 'nickname', 'screen_name', 'photo_rec', 'photo_big']
        ]);

        if (isset($users[0]) && isset($users[0]["id"]) && $users[0]["id"]) {
            $social_login->set_user([
                'first_name' => $users[0]['first_name'],
                'last_name' => $users[0]['last_name'],
                'photo' => $users[0]['photo_rec'],
                'email' => $email
            ]);

            $social_login->login();
        }
    }
} else {
    header('Location: ' . $heateorSsVkontakte->getLoginUrl());
}

exit();
