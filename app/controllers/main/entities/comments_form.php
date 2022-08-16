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
        if (isset($_POST['fields_in_comments'])) {
            $sort_order = 0;
            foreach (explode(',', $_POST['fields_in_comments']) as $v) {
                $sql_data = [
                    'comments_status' => 1,
                    'comments_forms_tabs_id' => 0,
                    'comments_sort_order' => $sort_order
                ];
                db_perform('app_fields', $sql_data, 'update', "id='" . db_input(str_replace('fields_', '', $v)) . "'");
                $sort_order++;
            }
        }

        $tabs_query = db_fetch_all(
            'app_comments_forms_tabs',
            "entities_id='" . db_input($_GET['entities_id']) . "' order by  sort_order, name"
        );
        while ($tabs = db_fetch_array($tabs_query)) {
            if (isset($_POST['forms_tabs_' . $tabs['id']])) {
                echo $_POST['forms_tabs_' . $tabs['id']];
                $sort_order = 0;
                foreach (explode(',', $_POST['forms_tabs_' . $tabs['id']]) as $v) {
                    db_perform(
                        'app_fields',
                        [
                            'comments_forms_tabs_id' => $tabs['id'],
                            'comments_status' => 1,
                            'comments_sort_order' => $sort_order
                        ],
                        'update',
                        "id='" . db_input(str_replace('fields_', '', $v)) . "'"
                    );
                    $sort_order++;
                }
            }
        }

        if (isset($_POST['available_fields'])) {
            foreach (explode(',', $_POST['available_fields']) as $v) {
                $sql_data = ['comments_status' => 0, 'comments_sort_order' => 0, 'comments_forms_tabs_id' => 0];
                db_perform('app_fields', $sql_data, 'update', "id='" . db_input(str_replace('fields_', '', $v)) . "'");
            }
        }
    }

    public function sort_tabs()
    {
        if (isset($_POST['forms_tabs_ol'])) {
            $sort_order = 0;
            foreach (explode(',', str_replace('forms_tabs_', '', $_POST['forms_tabs_ol'])) as $v) {
                db_perform(
                    'app_comments_forms_tabs',
                    ['sort_order' => $sort_order],
                    'update',
                    "id='" . db_input($v) . "'"
                );
                $sort_order++;
            }
        }
    }

    public function save_tab()
    {
        $sql_data = [
            'name' => $_POST['name'],
            'entities_id' => $_POST['entities_id'],
            'sort_order' => (forms_tabs::get_last_sort_number($_POST['entities_id']) + 1),
        ];

        if (isset($_GET['id'])) {
            db_perform('app_comments_forms_tabs', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_comments_forms_tabs', $sql_data);
        }

        redirect_to('entities/comments_form', 'entities_id=' . $_POST['entities_id']);
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            $msg = comments_forms_tabs::check_before_delete($_GET['id']);

            if (strlen($msg) > 0) {
                $alerts->add($msg, 'error');
            } else {
                $name = comments_forms_tabs::get_name_by_id($_GET['id']);

                db_delete_row('app_comments_forms_tabs', $_GET['id']);

                $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $name), 'success');
            }

            redirect_to('entities/comments_form', 'entities_id=' . $_GET['entities_id']);
        }
    }
}