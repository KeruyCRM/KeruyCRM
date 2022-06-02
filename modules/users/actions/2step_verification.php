<?php

//check if user is logged
if (!app_session_is_registered('app_logged_users_id') or CFG_2STEP_VERIFICATION_ENABLED != 1) {
    redirect_to('users/login');
}

//check if is checked
if (isset($two_step_verification_info['is_checked'])) {
    redirect_to('dashboard/');
}

if (!isset($two_step_verification_info['code'])) {
    two_step_verification::send_code();
}

switch ($app_module_action) {
    case 'check':

        //chck form token
        app_check_form_token('users/login');

        if ($two_step_verification_info['code'] == $_POST['code']) {
            two_step_verification::approve();
        } else {
            $alerts->add(TEXT_INCORRECT_CODE, 'error');
            redirect_to('users/2step_verification');
        }

        break;
}

$app_layout = 'public_layout.php';