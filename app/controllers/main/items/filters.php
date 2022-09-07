<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Filters extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();

        $reports_info_query = db_query("select * from app_reports where id='" . db_input($_GET['reports_id']) . "'");
        if (!$reports_info = db_fetch_array($reports_info_query)) {
            $alerts->add(TEXT_REPORT_NOT_FOUND, 'error');
            redirect_to('items/', 'path=' . $_GET['path']);
        }
    }

    public function index()
    {
        $entity_info = db_find('app_entities', $current_entity_id);
        $entity_cfg = entities::get_cfg($current_entity_id);

        $entity_listing_heading = (strlen(
            $entity_cfg['listing_heading']
        ) > 0 ? $entity_cfg['listing_heading'] : $entity_info['name']);

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'filters.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        \Controllers\Main\Items\_Module::save();

        redirect_to('items/filters', 'reports_id=' . $_GET['reports_id'] . '&path=' . $_GET['path']);
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            db_query("delete from app_reports_filters where id='" . db_input($_GET['id']) . "'");

            $alerts->add(TEXT_WARN_DELETE_FILTER_SUCCESS, 'success');

            redirect_to('items/filters', 'reports_id=' . $_GET['reports_id'] . '&path=' . $_GET['path']);
        }
    }
}