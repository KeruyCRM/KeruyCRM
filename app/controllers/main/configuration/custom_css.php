<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Configuration;

class Custom_css extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Configuration\_Module::top();
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'custom_css.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $custom_css = \K::$fw->{'POST.custom_css'};
            file_put_contents('css/custom.css', $custom_css);

            \Models\Main\Configuration::set('CFG_CUSTOM_CSS_TIME', time());
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}