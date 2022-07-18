<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Users;

class Validate_form extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();
    }

    public function index()
    {
        if (\K::$fw->VERB == 'POST') {
            $msg = [];

            if (\K::$fw->CFG_ALLOW_REGISTRATION_WITH_THE_SAME_EMAIL == 0 and isset(\K::$fw->POST['useremail'])) {
                /*$check_query = db_query(
                    "select count(*) as total from app_entity_1 where field_9='" . db_input(
                        $_POST['useremail']
                    ) . "' " . (isset(\K::$fw->GET['id']) ? " and id!='" . db_input($_GET['id']) . "'" : '')
                );
                $check = db_fetch_array($check_query);*/

                $check = \K::model()->db_fetch_count(
                    'app_entity_1',
                    [
                        'field_9 = :useremail' . (isset(\K::$fw->GET['id']) ? ' and id != :id' : ''),
                        ':useremail' => \K::$fw->POST['useremail']
                    ] + (isset(\K::$fw->GET['id']) ? [':id' => \K::$fw->GET['id']] : [])
                );

                if ($check > 0) {
                    $msg[] = \K::$fw->TEXT_ERROR_USEREMAIL_EXIST;
                }
            }

            if (isset(\K::$fw->POST['username'])) {
                /*$check_query = db_query(
                    "select count(*) as total from app_entity_1 where field_12='" . db_input(
                        \K::$fw->POST['username']
                    ) . "' " . (isset(\K::$fw->GET['id']) ? " and id!='" . db_input(\K::$fw->GET['id']) . "'" : '')
                );
                $check = db_fetch_array($check_query);*/

                $check = \K::model()->db_fetch_count(
                    'app_entity_1',
                    [
                        'field_12 = :username' . (isset(\K::$fw->GET['id']) ? ' and id != :id' : ''),
                        ':username' => \K::$fw->POST['username']
                    ] + (isset(\K::$fw->GET['id']) ? [':id' => \K::$fw->GET['id']] : [])
                );

                if ($check > 0) {
                    $msg[] = \K::$fw->TEXT_ERROR_USERNAME_EXIST;
                }
            }

            if (isset(\K::$fw->POST['password']) and strlen(\K::$fw->POST['password'])) {
                if (strlen(\K::$fw->POST['password']) < \K::$fw->CFG_PASSWORD_MIN_LENGTH) {
                    $msg[] = sprintf(\K::$fw->TEXT_ERROR_PASSWORD_LENGTH, \K::$fw->CFG_PASSWORD_MIN_LENGTH);
                }

                if (\K::$fw->CFG_IS_STRONG_PASSWORD) {
                    if (!preg_match('/[A-Z]/', \K::$fw->POST['password']) or !preg_match(
                            '/\d/',
                            \K::$fw->POST['password']
                        ) or !preg_match(
                            '/\W/',
                            \K::$fw->POST['password']
                        )) {
                        $msg[] = \K::$fw->TEXT_STRONG_PASSWORD_TIP;
                    }
                }
            }

            if (count($msg) == 0) {
                echo 'success';
            } else {
                echo implode('<br>', $msg);
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}