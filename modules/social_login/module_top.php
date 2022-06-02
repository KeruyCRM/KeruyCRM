<?php

//check if enabled
if (!CFG_ENABLE_SOCIAL_LOGIN) {
    redirect_to('users/login');
}

include("includes/libs/social_login/social_login.php");

$social_login = new social_login;
