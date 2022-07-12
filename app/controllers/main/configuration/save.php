<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Configuration;

class Save extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Configuration\_Module::top();
    }

    public function index()
    {
        if (\K::fw()->exists('POST.delete_logo')) {
            if (is_file(\K::$fw->DIR_FS_UPLOADS . \K::$fw->CFG_APP_LOGO)) {
                unlink(\K::$fw->DIR_FS_UPLOADS . \K::$fw->CFG_APP_LOGO);
            }

            $_POST['CFG']['APP_LOGO'] = '';
        }

        if (\K::fw()->exists('POST.delete_favicon')) {
            if (is_file(\K::$fw->DIR_FS_UPLOADS . \K::$fw->CFG_APP_FAVICON)) {
                unlink(\K::$fw->DIR_FS_UPLOADS . \K::$fw->CFG_APP_FAVICON);
            }

            $_POST['CFG']['APP_FAVICON'] = '';
        }

        if (\K::fw()->exists('POST.delete_login_page_background')) {
            if (is_file(\K::$fw->DIR_FS_UPLOADS . \K::$fw->CFG_APP_LOGIN_PAGE_BACKGROUND)) {
                unlink(\K::$fw->DIR_FS_UPLOADS . \K::$fw->CFG_APP_LOGIN_PAGE_BACKGROUND);
            }

            $_POST['CFG']['APP_LOGIN_PAGE_BACKGROUND'] = '';
        }

        if (\K::fw()->exists('POST.delete_login_maintenance_background')) {
            if (is_file(\K::$fw->DIR_FS_UPLOADS . \K::$fw->CFG_APP_LOGIN_MAINTENANCE_BACKGROUND)) {
                unlink(\K::$fw->DIR_FS_UPLOADS . \K::$fw->CFG_APP_LOGIN_MAINTENANCE_BACKGROUND);
            }

            $_POST['CFG']['APP_LOGIN_MAINTENANCE_BACKGROUND'] = '';
        }

        if (\K::$fw->VERB == 'POST' and \K::fw()->exists('POST.CFG')) {
            foreach ($_POST['CFG'] as $k => $v) {
                $k = 'CFG_' . $k;

                //logo
                if ($k == 'CFG_APP_LOGO') {
                    if (strlen($_FILES['APP_LOGO']['name']) > 0) {
                        if (\Helpers\App::is_image($_FILES['APP_LOGO']['tmp_name'])) {
                            $pathinfo = pathinfo($_FILES['APP_LOGO']['name']);
                            $filename = 'app_logo_' . time() . '.' . $pathinfo['extension'];

                            move_uploaded_file($_FILES['APP_LOGO']['tmp_name'], \K::$fw->DIR_FS_UPLOADS . $filename);
                            $v = $filename;
                        }
                    }
                }

                //favicon
                if ($k == 'CFG_APP_FAVICON') {
                    if (strlen($_FILES['APP_FAVICON']['name']) > 0) {
                        if (\Helpers\App::is_image($_FILES['APP_FAVICON']['tmp_name'])) {
                            $pathinfo = pathinfo($_FILES['APP_FAVICON']['name']);
                            $filename = 'app_favicon_' . time() . '.' . $pathinfo['extension'];

                            move_uploaded_file($_FILES['APP_FAVICON']['tmp_name'], \K::$fw->DIR_FS_UPLOADS . $filename);
                            $v = $filename;
                        }
                    }
                }

                if ($k == 'CFG_APP_LOGIN_PAGE_BACKGROUND') {
                    if (strlen($_FILES['APP_LOGIN_PAGE_BACKGROUND']['name']) > 0) {
                        if (\Helpers\App::is_image($_FILES['APP_LOGIN_PAGE_BACKGROUND']['tmp_name'])) {
                            $pathinfo = pathinfo($_FILES['APP_LOGIN_PAGE_BACKGROUND']['name']);
                            $filename = 'app_bg_' . time() . '.' . $pathinfo['extension'];

                            move_uploaded_file(
                                $_FILES['APP_LOGIN_PAGE_BACKGROUND']['tmp_name'],
                                \K::$fw->DIR_FS_UPLOADS . $filename
                            );
                            $v = $filename;
                        }
                    }
                }

                if ($k == 'CFG_APP_LOGIN_MAINTENANCE_BACKGROUND') {
                    if (strlen($_FILES['APP_LOGIN_MAINTENANCE_BACKGROUND']['name']) > 0) {
                        if (\Helpers\App::is_image($_FILES['APP_LOGIN_MAINTENANCE_BACKGROUND']['tmp_name'])) {
                            $pathinfo = pathinfo($_FILES['APP_LOGIN_MAINTENANCE_BACKGROUND']['name']);
                            $filename = 'app_bg_' . time() . '.' . $pathinfo['extension'];

                            move_uploaded_file(
                                $_FILES['APP_LOGIN_MAINTENANCE_BACKGROUND']['tmp_name'],
                                \K::$fw->DIR_FS_UPLOADS . $filename
                            );
                            $v = $filename;
                        }
                    }
                }

                //handle arrays
                if (is_array($v)) {
                    $v = implode(',', $v);
                }

                switch ($k) {
                    case 'CFG_APP_NUMBER_FORMAT':
                        $value = $v;
                        break;
                    default:
                        $value = trim($v);
                        break;
                }

                \Models\Main\Configuration::set($k, $value);

                /*$cfq_query = db_query("select * from app_configuration where configuration_name='" . $k . "'");
                if (!$cfq = db_fetch_array($cfq_query)) {
                    db_perform('app_configuration', ['configuration_value' => $value, 'configuration_name' => $k]);
                } else {
                    db_perform(
                        'app_configuration',
                        ['configuration_value' => $value],
                        'update',
                        "configuration_name='" . $k . "'"
                    );
                }*/
            }

            \K::flash()->addMessage(\K::$fw->TEXT_CONFIGURATION_UPDATED, 'success');

            \Helpers\Urls::redirect_to(\K::$fw->app_redirect_to);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}