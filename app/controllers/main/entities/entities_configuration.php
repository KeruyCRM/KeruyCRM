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

        if (!\K::$fw->GET['entities_id']) {
            \Helpers\Urls::redirect_to('main/entities');
        }

        \K::$fw->cfg = new \Models\Main\Entities_cfg(\K::$fw->GET['entities_id']);
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'entities_configuration.php';

        echo \K::view()->render($this->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            \K::model()->begin();

            foreach (\K::$fw->POST['cfg'] as $k => $v) {
                \K::$fw->cfg->set($k, $v);
            }

            \K::model()->commit();

            \K::flash()->addMessage(\K::$fw->TEXT_CONFIGURATION_UPDATED, 'success');

            if (strlen(\K::$fw->app_redirect_to)) {
                \Helpers\Urls::redirect_to(\K::$fw->app_redirect_to, 'entities_id=' . \K::$fw->GET['entities_id']);
            } else {
                \Helpers\Urls::redirect_to(
                    'main/entities/entities_configuration',
                    'entities_id=' . \K::$fw->GET['entities_id']
                );
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}