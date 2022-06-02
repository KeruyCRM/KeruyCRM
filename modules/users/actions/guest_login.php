<?php

if (guest_login::is_enabled()) {
    app_session_register('app_logged_users_id', CFG_GUEST_LOGIN_USER);

    redirect_to('dashboard/dashboard');
} else {
    redirect_to('users/login');
}