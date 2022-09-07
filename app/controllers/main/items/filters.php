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

        //$reports_info_query = db_query("select * from app_reports where id='" . db_input(\K::$fw->GET['reports_id']) . "'");

        \K::$fw->reports_info = \K::model()->db_fetch_one('app_reports', [
            'id = ?',
            \K::$fw->GET['reports_id']
        ]);

        if (!\K::$fw->reports_info) {
            \K::flash()->addMessage(\K::$fw->TEXT_REPORT_NOT_FOUND, 'error');
            \Helpers\Urls::redirect_to('main/items/items', 'path=' . \K::$fw->GET['path']);
        }
    }

    public function index()
    {
        $entity_info = \K::model()->db_find('app_entities', \K::$fw->current_entity_id);
        $entity_cfg = \Models\Main\Entities::get_cfg(\K::$fw->current_entity_id);

        \K::$fw->entity_listing_heading = (strlen(
            $entity_cfg['listing_heading']
        ) > 0 ? $entity_cfg['listing_heading'] : $entity_info['name']);

        \K::$fw->filters_query = \K::model()->db_query_exec(
            "select rf.*, f.name from app_reports_filters rf, app_fields f where rf.fields_id = f.id and rf.reports_id = ? order by rf.id",
            \K::$fw->reports_info['id'],
            'app_reports_filters,app_fields'
        );

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'filters.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $this->_saveReportsFilters();

            \Helpers\Urls::redirect_to(
                'main/items/filters',
                'reports_id=' . \K::$fw->GET['reports_id'] . '&path=' . \K::$fw->GET['path']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['id'])) {
            //db_query("delete from app_reports_filters where id='" . db_input(\K::$fw->GET['id']) . "'");
            \K::model()->db_delete_row('app_reports_filters', \K::$fw->GET['id']);

            \K::flash()->addMessage(\K::$fw->TEXT_WARN_DELETE_FILTER_SUCCESS, 'success');

            \Helpers\Urls::redirect_to(
                'main/items/filters',
                'reports_id=' . \K::$fw->GET['reports_id'] . '&path=' . \K::$fw->GET['path']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}