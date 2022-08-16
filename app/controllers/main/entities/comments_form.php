<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Comments_form extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        /*\K::$fw->fields_query = \K::model()->db_query_exec(
            "select f.* from app_fields f where comments_status = 1 and  f.entities_id = ? and f.comments_forms_tabs_id = 0 order by f.comments_sort_order",
            \K::$fw->GET['entities_id']
        );*/

        \K::$fw->fields_query = \K::model()->db_fetch('app_fields', [
            'comments_status = 1 and entities_id = ? and comments_forms_tabs_id = 0',
            \K::$fw->GET['entities_id']
        ], ['order' => 'comments_sort_order'], 'id,type,name');

        \K::$fw->tabs_query = \K::model()->db_fetch('app_comments_forms_tabs', [
            'entities_id = ?',
            \K::$fw->GET['entities_id']
        ], ['order' => 'sort_order,name'], 'id,name');

        \K::$fw->fields_query2 = \K::model()->db_query_exec(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in (" . \K::model(
            )->quoteToString(
                \Models\Main\Comments::get_available_filedtypes_in_comments()
            ) . ") and comments_status = 0 and f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
            \K::$fw->GET['entities_id'],
            'app_fields,app_forms_tabs'
        );

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'comments_form.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function set_fields()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['entities_id'])) {
            \K::model()->begin();

            if (isset(\K::$fw->POST['fields_in_comments'])) {
                $sort_order = 0;
                $exp = explode(',', \K::$fw->POST['fields_in_comments']);

                foreach ($exp as $v) {
                    $sql_data = [
                        'comments_status' => 1,
                        'comments_forms_tabs_id' => 0,
                        'comments_sort_order' => $sort_order
                    ];

                    \K::model()->db_update('app_fields', $sql_data, [
                            'id = ?',
                            str_replace('fields_', '', $v)
                        ]
                    );
                    $sort_order++;
                }
            }

            $tabs_query = \K::model()->db_fetch('app_comments_forms_tabs', [
                'entities_id = ?',
                \K::$fw->GET['entities_id']
            ], ['order' => 'sort_order,name']
            );

            //while ($tabs = db_fetch_array($tabs_query)) {
            foreach ($tabs_query as $tabs) {
                $tabs = $tabs->cast();

                if (isset(\K::$fw->POST['forms_tabs_' . $tabs['id']])) {
                    //echo \K::$fw->POST['forms_tabs_' . $tabs['id']];//LOST?
                    $sort_order = 0;
                    $exp = explode(',', \K::$fw->POST['forms_tabs_' . $tabs['id']]);

                    foreach ($exp as $v) {
                        $sql_data = [
                            'comments_forms_tabs_id' => $tabs['id'],
                            'comments_status' => 1,
                            'comments_sort_order' => $sort_order
                        ];

                        \K::model()->db_update('app_fields', $sql_data, [
                                'id = ?',
                                str_replace('fields_', '', $v)
                            ]
                        );
                        $sort_order++;
                    }
                }
            }

            if (isset(\K::$fw->POST['available_fields'])) {
                $sql_data = [
                    'comments_status' => 0,
                    'comments_sort_order' => 0,
                    'comments_forms_tabs_id' => 0
                ];
                $exp = explode(',', \K::$fw->POST['available_fields']);

                foreach ($exp as $v) {
                    \K::model()->db_update('app_fields', $sql_data, [
                            'id = ?',
                            str_replace('fields_', '', $v)
                        ]
                    );
                }
            }

            \K::model()->commit();
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function sort_tabs()
    {
        if (isset(\K::$fw->POST['forms_tabs_ol'])) {
            $sort_order = 0;
            $exp = explode(',', str_replace('forms_tabs_', '', \K::$fw->POST['forms_tabs_ol']));

            \K::model()->begin();

            foreach ($exp as $v) {
                \K::model()->db_update(
                    'app_comments_forms_tabs',
                    ['sort_order' => $sort_order],
                    ['id = ?', $v]
                );
                $sort_order++;
            }

            \K::model()->commit();
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function save_tab()
    {
        if (isset(\K::$fw->POST['entities_id']) and isset(\K::$fw->POST['name'])) {
            $sql_data = [
                'name' => \K::$fw->POST['name'],
                'entities_id' => \K::$fw->POST['entities_id'],
                'sort_order' => (\Models\Main\Forms_tabs::get_last_sort_number(\K::$fw->POST['entities_id']) + 1),
            ];

            /*if (isset($_GET['id'])) {
                db_perform('app_comments_forms_tabs', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
            } else {
                db_perform('app_comments_forms_tabs', $sql_data);
            }*/

            \K::model()->db_perform('app_comments_forms_tabs', $sql_data, [
                'id = ?',
                \K::$fw->GET['id']
            ]);

            \Helpers\Urls::redirect_to('main/entities/comments_form', 'entities_id=' . \K::$fw->POST['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST') {
            if (isset(\K::$fw->GET['id']) and isset(\K::$fw->GET['entities_id'])) {
                $msg = \Models\Main\Comments_forms_tabs::check_before_delete(\K::$fw->GET['id']);

                if (strlen($msg) > 0) {
                    \K::flash()->addMessage($msg, 'error');
                } else {
                    $name = \Models\Main\Comments_forms_tabs::get_name_by_id(\K::$fw->GET['id']);

                    \K::model()->db_delete_row('app_comments_forms_tabs', \K::$fw->GET['id']);

                    \K::flash()->addMessage(sprintf(\K::$fw->TEXT_WARN_DELETE_SUCCESS, $name), 'success');
                }

                \Helpers\Urls::redirect_to('main/entities/comments_form', 'entities_id=' . \K::$fw->GET['entities_id']);
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}