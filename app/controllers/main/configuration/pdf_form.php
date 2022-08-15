<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Configuration;

class Pdf_form extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Configuration\_Module::top();
    }

    public function index()
    {
        if (\K::$fw->AJAX) {
            \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'pdf_form.php';

            echo \K::view()->render(\K::$fw->subTemplate);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}