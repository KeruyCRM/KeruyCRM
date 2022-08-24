<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Item_page_configuration extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        require(component_path('entities/check_entities_id'));

        $cfg = new entities_cfg($_GET['entities_id']);

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'item_page_configuration.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        $cfg = new entities_cfg($_GET['entities_id']);

        if (!isset($_POST['cfg']['item_page_hidden_fields'])) {
            $_POST['cfg']['item_page_hidden_fields'] = '';
        }

        foreach ($_POST['cfg'] as $k => $v) {
            $cfg->set($k, $v);
        }

        $alerts->add(TEXT_CONFIGURATION_UPDATED, 'success');

        redirect_to('entities/item_page_configuration', 'entities_id=' . $_GET['entities_id']);
    }
}