<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Forms_rows extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        $obj = \K::model()->db_find('app_forms_rows', \K::$fw->GET['id']);

        if (!isset(\K::$fw->GET['id'])) {
            $obj['columns'] = 2;
            $obj['column1_width'] = 6;
            $obj['column2_width'] = 6;
            $obj['field_name_new_row'] = 1;
        }

        \K::$fw->obj = $obj;

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'forms_rows.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['entities_id'])) {
            $sql_data = [
                'entities_id' => \K::$fw->GET['entities_id'],
                'forms_tabs_id' => \K::$fw->GET['forms_tabs_id'],
                'columns' => \K::$fw->POST['columns'],
                'field_name_new_row' => (isset(\K::$fw->POST['field_name_new_row']) ? 1 : 0),
                'column1_width' => \K::$fw->POST['column1_width'],
                'column2_width' => \K::$fw->POST['column2_width'],
                'column3_width' => \K::$fw->POST['column3_width'],
                'column4_width' => \K::$fw->POST['column4_width'],
                'column5_width' => \K::$fw->POST['column5_width'],
                'column6_width' => \K::$fw->POST['column6_width'],
            ];

            \K::model()->begin();

            if (isset(\K::$fw->GET['id'])) {
                \K::model()->db_perform('app_forms_rows', $sql_data, ['id = ?', \K::$fw->GET['id']]);

                //reset forms_rows_position
                for ($i = (\K::$fw->POST['columns'] + 1); $i <= 6; $i++) {
                    /*db_query(
                        "update app_fields set forms_rows_position='' where forms_rows_position='" . \K::$fw->GET['id'] . ":" . $i . "' and entities_id='" . \K::$fw->GET['entities_id'] . "'"
                    );*/

                    \K::model()->db_update('app_fields', ['forms_rows_position' => ''], [
                        'forms_rows_position = ? and entities_id = ?',
                        \K::$fw->GET['id'] . ':' . $i,
                        \K::$fw->GET['entities_id']
                    ]);
                }
            } else {
                /*$check_query = db_query(
                    "select (max(sort_order)+1) as total from app_forms_rows where entities_id='" . \K::$fw->GET['entities_id'] . "' and forms_tabs_id='" . \K::$fw->GET['forms_tabs_id'] . "'"
                );
                $check = db_fetch_array($check_query);*/

                $check = \K::model()->db_fetch_one('app_forms_rows', [
                    'entities_id = ? and forms_tabs_id = ?',
                    \K::$fw->GET['entities_id'],
                    \K::$fw->GET['forms_tabs_id']
                ], [], 'total', ['total' => '(max(sort_order)+1)']);

                $sort_order = $check['total'];

                $sql_data['sort_order'] = $sort_order;

                \K::model()->db_perform('app_forms_rows', $sql_data);
            }

            \K::model()->commit();

            \Helpers\Urls::redirect_to('main/entities/forms', 'entities_id=' . \K::$fw->GET['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function sort_rows()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['entities_id'])) {
            /*$tabs_query = db_fetch_all(
                'app_forms_tabs',
                "entities_id='" . db_input(\K::$fw->GET['entities_id']) . "' order by  sort_order, name"
            );*/

            $tabs_query = \K::model()->db_fetch('app_forms_tabs', [
                'entities_id = ?',
                \K::$fw->GET['entities_id']
            ], ['order' => 'sort_order,name']);

            \K::model()->begin();

            //while ($tabs = db_fetch_array($tabs_query)) {
            foreach ($tabs_query as $tabs) {
                $tabs = $tabs->cast();

                if (isset(\K::$fw->POST['forms_rows_' . $tabs['id']])) {
                    $sort_order = 0;
                    $exp = explode(',', str_replace('forms_rows_', '', \K::$fw->POST['forms_rows_' . $tabs['id']]));

                    foreach ($exp as $v) {
                        \K::model()->db_perform(
                            'app_forms_rows',
                            ['sort_order' => $sort_order, 'forms_tabs_id' => $tabs['id']],
                            ['id = ?', $v]
                        );
                        $sort_order++;

                        /*db_query(
                            "update app_fields set forms_tabs_id='" . $tabs['id'] . "' where entities_id='" . _GET(
                                'entities_id'
                            ) . "' and forms_rows_position in ('" . $v . ":1','" . $v . ":2','" . $v . ":3','" . $v . ":4','" . $v . ":5','" . $v . ":6')"
                        );*/

                        $forms_rows_positionIn = \K::model()->quoteToString([
                            $v . ':1',
                            $v . ':2',
                            $v . ':3',
                            $v . ':4',
                            $v . ':5',
                            $v . ':6'
                        ]);

                        \K::model()->db_update('app_fields', ['forms_tabs_id' => $tabs['id']], [
                            'entities_id = ? and forms_rows_position in (' . $forms_rows_positionIn . ')',
                            \K::$fw->GET['entities_id']
                        ]);
                    }
                }
            }

            \K::model()->commit();
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST') {
            if (isset($_GET['id'])) {
                \K::model()->begin();

                \K::model()->db_delete_row('app_forms_rows', \K::$fw->GET['id']);

                $v = \K::$fw->GET['id'];

                $forms_rows_positionIn = \K::model()->quoteToString([
                    $v . ':1',
                    $v . ':2',
                    $v . ':3',
                    $v . ':4',
                    $v . ':5',
                    $v . ':6'
                ]);

                /*\K::model()->db_query(
                    "update app_fields set forms_rows_position='' where entities_id='" . \K::$fw->GET['entities_id'] . "' and forms_rows_position in ('" . $v . ":1','" . $v . ":2','" . $v . ":3','" . $v . ":4','" . $v . ":5','" . $v . ":6')"
                );*/

                \K::model()->db_update('app_fields', ['forms_rows_position' => ''], [
                    'entities_id = ? and forms_rows_position in (' . $forms_rows_positionIn . ')',
                    \K::$fw->GET['entities_id']
                ]);

                \K::model()->commit();
            }

            \Helpers\Urls::redirect_to('main/entities/forms', 'entities_id=' . \K::$fw->GET['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}