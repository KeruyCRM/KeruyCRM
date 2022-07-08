<?php

namespace Controllers\Main\Users;

class Email_verification extends \Controller
{
    private $app_layout = 'public_layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken('main/users/login');

        //check if user is logged
        if (!\K::app_session_is_registered('app_logged_users_id')
            or \K::$fw->CFG_PUBLIC_REGISTRATION_USER_ACTIVATION != 'email'
            or \K::$fw->CFG_USE_PUBLIC_REGISTRATION != 1) {
            \Helpers\Urls::redirect_to('main/users/login');
        }

        //check if is checked
        if (\K::$fw->app_user['is_email_verified'] == 1) {
            \Helpers\Urls::redirect_to('main/dashboard');
        }

        if (!strlen(\K::$fw->app_email_verification_code)) {
            \Models\Main\Users\Email_verification::send_code();
        }
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'email_verification.php';

        echo \K::view()->render($this->app_layout);
    }

    public function resend()
    {
        \K::flash()->addMessage(\K::$fw->TEXT_RESEND_CODE_TIP);
        \Helpers\Urls::redirect_to('main/users/login');
    }

    public function update_email()
    {
        if (\K::$fw->VERB == 'POST') {
            if (\Helpers\App::app_validate_email(\K::$fw->{'POST.email'})) {
                /*$check_query = db_query(
                    "select count(*) as total from app_entity_1 where field_9='" . db_input(
                        \K::$fw->{'POST.email'}
                    ) . "'  and id!='" . db_input(\K::$fw->app_user['id']) . "'"
                );
                $check = db_fetch_array($check_query);*/

                $check = \K::model()->db_fetch_count('app_entity_1', [
                    'field_9 = ? and id != ?',
                    \K::$fw->{'POST.email'},
                    \K::$fw->app_user['id']
                ]);

                if ($check > 0) {
                    \K::flash()->addMessage(\K::$fw->TEXT_ERROR_USEREMAIL_EXIST, 'warning');
                } elseif (\K::$fw->app_user['email'] != \K::$fw->{'POST.email'}) {
                    //update account
                    /*db_query(
                        "update app_entity_1 set field_9='" . db_input(
                            $_POST['email']
                        ) . "' where id='" . \K::$fw->app_user['id'] . "'"
                    );*/

                    \K::model()->db_perform(
                        'app_entity_1',
                        ['field_9' => \K::$fw->{'POST.email'}],
                        '',
                        ['id = ?', \K::$fw->app_user['id']]
                    );

                    \K::flash()->addMessage(\K::$fw->TEXT_ACCOUNT_UPDATED, 'success');

                    //reset verification code
                    \K::$fw->app_email_verification_code = '';
                }
            }
        }

        \Helpers\Urls::redirect_to('main/users/email_verification');
    }

    public function check()
    {
        if (\K::$fw->VERB == 'POST') {
            if (\K::$fw->app_email_verification_code == \K::$fw->{'POST.code'}) {
                \Models\Main\Users\Email_verification::approve();
            } else {
                \K::flash()->addMessage(\K::$fw->TEXT_INCORRECT_CODE, 'error');
            }
        }
        \Helpers\Urls::redirect_to('main/users/email_verification');
    }
}