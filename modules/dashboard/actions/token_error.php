<?php

if (!app_session_is_registered('app_logged_users_id')) {
    redirect_to('users/login');
}

