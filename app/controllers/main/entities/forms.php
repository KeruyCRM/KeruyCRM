<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Forms extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        $entity_cfg = new \Models\Main\Entities_cfg(\K::$fw->GET['entities_id']);
        $hidden_form_fields = $entity_cfg->get('hidden_form_fields');
        \K::$fw->count_hidden_form_fields = strlen($hidden_form_fields) ? count(explode(',', $hidden_form_fields)) : 0;

        \K::$fw->count_tabs = \K::model()->db_count('app_forms_tabs', \K::$fw->GET['entities_id'], "entities_id");

        \K::$fw->tabs_tree = \Models\Main\Forms_tabs::get_tree(\K::$fw->GET['entities_id']);

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'forms.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save_javascript()
    {
        if (\K::$fw->VERB == 'POST' and (isset(\K::$fw->GET['entities_id']))) {
            $cfg = new \Models\Main\Entities_cfg(\K::$fw->GET['entities_id']);
            $cfg->set('javascript_in_from', \K::$fw->POST['javascript_in_from']);
            $cfg->set('javascript_onsubmit', \K::$fw->POST['javascript_onsubmit']);

            \K::flash()->addMessage(\K::$fw->TEXT_CONFIGURATION_UPDATED, 'success');

            \Helpers\Urls::redirect_to('main/entities/forms', 'entities_id=' . \K::$fw->GET['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function sort_fields()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['entities_id'])) {
            /*$tabs_query = db_fetch_all(
                'app_forms_tabs',
                "entities_id='" . db_input(\K::$fw->GET['entities_id']) . "' order by  sort_order, name"
            );*/

            $tabs_query = \K::model()->db_fetch('app_forms_tabs', [
                'entities_id = ?',
                \K::$fw->GET['entities_id']
            ], ['order' => 'sort_order, name'], 'id');

            \K::model()->begin();

            //while ($tabs = db_fetch_array($tabs_query)) {
            foreach ($tabs_query as $tabs) {
                $tabs = $tabs->cast();

                if (isset(\K::$fw->POST['forms_tabs_' . (int)$tabs['id']])) {
                    $sort_order = 0;
                    $exp = explode(',', \K::$fw->POST['forms_tabs_' . (int)$tabs['id']]);
                    $sql_data = [
                        'forms_tabs_id' => $tabs['id'],
                        'sort_order' => $sort_order,
                        'forms_rows_position' => ''
                    ];

                    foreach ($exp as $v) {
                        \K::model()->db_perform('app_fields', $sql_data, [
                                'id = ?',
                                str_replace('form_fields_', '', $v)
                            ]
                        );

                        $sort_order++;
                    }
                }

                //handle rows
                /*$rows_query = db_query(
                     "select * from app_forms_rows where entities_id='" . _GET(
                         'entities_id'
                     ) . "' and forms_tabs_id='" . $tabs['id'] . "' order by sort_order"
                 );*/

                $rows_query = \K::model()->db_fetch('app_forms_rows', [
                    'entities_id = ? and forms_tabs_id = ?',
                    \K::$fw->GET['entities_id'],
                    $tabs['id']
                ], ['order' => 'sort_order'], 'id,columns');

                //while ($rows = db_fetch_array($rows_query)) {
                foreach ($rows_query as $rows) {
                    $rows = $rows->cast();

                    for ($i = 1; $i <= $rows['columns']; $i++) {
                        if (isset(\K::$fw->POST['forms_rows_' . (int)$tabs['id'] . '_' . (int)$rows['id'] . '_' . $i])) {
                            $sort_order = 0;
                            $exp = explode(
                                ',',
                                \K::$fw->POST['forms_rows_' . (int)$tabs['id'] . '_' . (int)$rows['id'] . '_' . $i]
                            );
                            $sql_data = [
                                'forms_tabs_id' => $tabs['id'],
                                'sort_order' => $sort_order,
                                'forms_rows_position' => $rows['id'] . ':' . $i
                            ];

                            foreach ($exp as $v) {
                                \K::model()->db_perform('app_fields', $sql_data, [
                                        'id = ?',
                                        str_replace('form_fields_', '', $v)
                                    ]
                                );

                                $sort_order++;
                            }
                        }
                    }
                }
            }

            \K::model()->commit();
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function sort_tabs()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->POST['forms_tabs_ol'])) {
            $sort_order = 0;
            $exp = explode(',', str_replace('forms_tabs_', '', \K::$fw->POST['forms_tabs_ol']));

            \K::model()->begin();

            foreach ($exp as $v) {
                \K::model()->db_perform('app_forms_tabs', ['sort_order' => $sort_order], [
                    'id = ?',
                    $v
                ]);
                $sort_order++;
            }

            \K::model()->commit();
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function save_tab()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->POST['entities_id'])) {
            $sql_data = [
                'name' => \K::$fw->POST['name'],
                'entities_id' => \K::$fw->POST['entities_id'],
                'description' => \K::$fw->POST['description'],
            ];

            if (!isset(\K::$fw->GET['id'])) {
                $sql_data['sort_order'] = (\Models\Main\Forms_tabs::get_last_sort_number(
                        \K::$fw->POST['entities_id']
                    ) + 1);
            }

            \K::model()->db_perform('app_forms_tabs', $sql_data, [
                [
                    'id = ?',
                    \K::$fw->GET['id']
                ]
            ]);

            \Helpers\Urls::redirect_to('main/entities/forms', 'entities_id=' . \K::$fw->POST['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['id']) and isset(\K::$fw->GET['entities_id'])) {
            $msg = \Models\Main\Forms_tabs::check_before_delete(\K::$fw->GET['id']);

            if (strlen($msg) > 0) {
                \K::flash()->addMessage($msg, 'error');
            } else {
                $name = \Models\Main\Forms_tabs::get_name_by_id(\K::$fw->GET['id']);

                \K::model()->begin();

                \K::model()->db_delete_row('app_forms_tabs', \K::$fw->GET['id']);
                //delete rows
                \K::model()->db_delete_row('app_forms_rows', K::$fw->GET['id'], 'forms_tabs_id');

                \K::model()->commit();

                \K::flash()->addMessage(sprintf(\K::$fw->TEXT_WARN_DELETE_SUCCESS, $name), 'success');
            }

            \Helpers\Urls::redirect_to('entities/forms', 'entities_id=' . \K::$fw->GET['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}