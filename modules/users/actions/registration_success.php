<?php

if (app_session_is_registered('app_logged_users_id') or CFG_USE_PUBLIC_REGISTRATION == 0) {
    redirect_to('users/login', 'action=logoff');
}

if (CFG_PUBLIC_REGISTRATION_USER_ACTIVATION != 'manually') {
    redirect_to('users/login');
}

$app_layout = 'public_layout.php';