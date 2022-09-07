<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Infopage_entityfield_filters extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();

        \K::$fw->entities_info = \K::model()->db_find('app_entities', \K::$fw->GET['entities_id']);

        $reports_type = 'field' . (int)\K::$fw->GET['fields_id'] . '_entity_item_info_page';

        /*$reports_info_query = db_query(
            "select * from app_reports where entities_id='" . db_input(
                \K::$fw->GET['entities_id']
            ) . "' and reports_type='{$reports_type}'"
        );*/

        $reports_info = \K::model()->db_fetch_one('app_reports', [
            'entities_id = ? and reports_type = ?',
            \K::$fw->GET['entities_id'],
            $reports_type
        ]);

        if (!$reports_info) {
            $sql_data = [
                'name' => '',
                'entities_id' => \K::$fw->GET['entities_id'],
                'reports_type' => $reports_type,
                'in_menu' => 0,
                'in_dashboard' => 0,
                'created_by' => 0,
            ];

            $mapper = \K::model()->db_perform('app_reports', $sql_data);
            $id = \K::model()->db_insert_id($mapper);

            \K::$fw->reports_info = \K::model()->db_find('app_reports', $id);
        }
    }

    public function index()
    {
        \K::$fw->fields_info = \K::model()->db_find('app_fields', \K::$fw->GET['fields_id']);

        \K::$fw->filters_query = \K::model()->db_query_exec(
            "select rf.*, f.name, f.type from app_reports_filters rf left join app_fields f on rf.fields_id = f.id where rf.reports_id = ? order by rf.id",
            \K::$fw->reports_info['id'],
            'app_reports_filters,app_fields'
        );

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'infopage_entityfield_filters.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['reports_id']) and isset(\K::$fw->GET['entities_id']) and isset(\K::$fw->GET['related_entities_id']) and isset(\K::$fw->GET['fields_id'])) {
            $this->_saveReportsFilters();

            \Helpers\Urls::redirect_to(
                'main/entities/infopage_entityfield_filters',
                'reports_id=' . \K::$fw->GET['reports_id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&related_entities_id=' . \K::$fw->GET['related_entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
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
                'main/entities/infopage_entityfield_filters',
                'reports_id=' . \K::$fw->GET['reports_id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&related_entities_id=' . \K::$fw->GET['related_entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}