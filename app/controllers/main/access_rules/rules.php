<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Access_rules;

class Rules extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Access_rules\_Module::top();

        /*$field_info_query = db_query(
            "select * from app_fields where id='" . _get::int('fields_id') . "' and entities_id='" . _get::int(
                'entities_id'
            ) . "'"
        );*/

        \K::$fw->field_info = \K::model()->db_fetch_one('app_fields', [
            'id = ? and entities_id = ?',
            \K::$fw->GET['fields_id'],
            \K::$fw->GET['entities_id']
        ]);

        if (!\K::$fw->field_info) {
            \Helpers\Urls::redirect_to('main/access_rules/fields', 'entities_id=' . \K::$fw->GET['entities_id']);
        }
    }

    public function index()
    {
        \K::$fw->form_fields_query = \K::model()->db_query_exec(
            'select r.*, f.name, f.type, f.id as fields_id, f.configuration from app_access_rules r, app_fields f where r.fields_id = f.id and r.entities_id = ?',
            \K::$fw->GET['entities_id'],
            'app_access_rules,app_fields'
        );

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'rules.php';

        echo \K::view()->render($this->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $sql_data = [
                'entities_id' => \K::$fw->GET['entities_id'],
                'fields_id' => \K::$fw->GET['fields_id'],
                'choices' => (isset(\K::$fw->POST['choices']) ? implode(',', \K::$fw->POST['choices']) : ''),
                'users_groups' => (isset(\K::$fw->POST['users_groups']) ? implode(
                    ',',
                    \K::$fw->POST['users_groups']
                ) : ''),
                'access_schema' => (isset(\K::$fw->POST['access_schema']) ? implode(
                    ',',
                    \K::$fw->POST['access_schema']
                ) : ''),
                'fields_view_only_access' => (isset(\K::$fw->POST['fields_view_only_access']) ? implode(
                    ',',
                    \K::$fw->POST['fields_view_only_access']
                ) : ''),
                'comments_access_schema' => str_replace('_', ',', \K::$fw->POST['comments_access_schema']),
            ];

            \K::model()->db_perform('app_access_rules', $sql_data, ['id = ?', \K::$fw->GET['id']]);

            \Helpers\Urls::redirect_to(
                'main/access_rules/rules',
                'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST') {
            if (isset(\K::$fw->GET['id'])) {
                \K::model()->db_delete_row('app_access_rules', \K::$fw->GET['id']);
            }

            \Helpers\Urls::redirect_to(
                'main/access_rules/rules',
                'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}