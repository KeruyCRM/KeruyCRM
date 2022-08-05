<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Entities_configuration extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();

        \K::$fw->cfg = new \Models\Main\Entities_cfg(\K::$fw->GET['entities_id']);
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'entities_configuration.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }

    public function save()
    {
        foreach ($_POST['cfg'] as $k => $v) {
            \K::$fw->cfg->set($k, $v);
        }

        $alerts->add(TEXT_CONFIGURATION_UPDATED, 'success');

        if (strlen(\K::$fw->app_redirect_to)) {
            redirect_to(\K::$fw->app_redirect_to, 'entities_id=' . $_GET['entities_id']);
        } else {
            redirect_to('entities/entities_configuration', 'entities_id=' . $_GET['entities_id']);
        }
    }
}