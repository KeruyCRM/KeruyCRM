<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Access_rules;

class Parent_filters extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Access_rules\_Module::top();
    }

    public function index()
    {
        \Helpers\Urls::redirect_to('main/dashboard');
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $values = '';

            if (isset(\K::$fw->POST['values'])) {
                if (is_array(\K::$fw->POST['values'])) {
                    $values = implode(',', \K::$fw->POST['values']);
                } else {
                    $values = \K::$fw->POST['values'];
                }
            }

            $sql_data = [
                'reports_id' => \K::$fw->GET['reports_id'],
                'fields_id' => \K::$fw->POST['fields_id'],
                'filters_condition' => \K::$fw->POST['filters_condition'],
                'filters_values' => $values,
            ];

            \K::model()->db_perform('app_reports_filters', $sql_data, ['id = ?', \K::$fw->GET['id']]);

            \Helpers\Urls::redirect_to('main/access_rules/fields', 'entities_id=' . \K::$fw->GET['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['id'])) {
            //db_query("delete from app_reports_filters where id='" . db_input(\K::$fw->GET['id']) . "'");

            \K::model()->db_delete('app_reports_filters', ['id = ?', \K::$fw->GET['id']]);

            \K::flash()->addMessage(\K::$fw->TEXT_WARN_DELETE_FILTER_SUCCESS, 'success');

            \Helpers\Urls::redirect_to('main/access_rules/fields', 'entities_id=' . \K::$fw->GET['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}