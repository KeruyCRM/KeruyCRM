<?php

switch ($app_module_action) {
    case 'save':


        $sql_data = [
            'entities_id' => $_GET['entities_id'],
            'forms_tabs_id' => $_GET['forms_tabs_id'],
            'columns' => $_POST['columns'],
            'field_name_new_row' => (isset($_POST['field_name_new_row']) ? 1 : 0),
            'column1_width' => $_POST['column1_width'],
            'column2_width' => $_POST['column2_width'],
            'column3_width' => $_POST['column3_width'],
            'column4_width' => $_POST['column4_width'],
            'column5_width' => $_POST['column5_width'],
            'column6_width' => $_POST['column6_width'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_forms_rows', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");

            //reset forms_rows_position
            for ($i = ($_POST['columns'] + 1); $i <= 6; $i++) {
                db_query(
                    "update app_fields set forms_rows_position='' where forms_rows_position='" . _GET(
                        'id'
                    ) . ":" . $i . "' and entities_id='" . _GET('entities_id') . "'"
                );
            }
        } else {
            $check_query = db_query(
                "select (max(sort_order)+1) as total from app_forms_rows where entities_id='" . _GET(
                    'entities_id'
                ) . "' and forms_tabs_id='" . _GET('forms_tabs_id') . "'"
            );
            $check = db_fetch_array($check_query);
            $sort_order = $check['total'];

            $sql_data['sort_order'] = $sort_order;

            db_perform('app_forms_rows', $sql_data);
        }

        redirect_to('entities/forms', 'entities_id=' . _GET('entities_id'));

        break;

    case 'sort_rows':
        //print_rr($_POST);
        $tabs_query = db_fetch_all(
            'app_forms_tabs',
            "entities_id='" . db_input($_GET['entities_id']) . "' order by  sort_order, name"
        );
        while ($tabs = db_fetch_array($tabs_query)) {
            if (isset($_POST['forms_rows_' . $tabs['id']])) {
                $sort_order = 0;
                foreach (explode(',', str_replace('forms_rows_', '', $_POST['forms_rows_' . $tabs['id']])) as $v) {
                    db_perform(
                        'app_forms_rows',
                        ['sort_order' => $sort_order, 'forms_tabs_id' => $tabs['id']],
                        'update',
                        "id='" . db_input($v) . "'"
                    );
                    $sort_order++;

                    db_query(
                        "update app_fields set forms_tabs_id='" . $tabs['id'] . "' where entities_id='" . _GET(
                            'entities_id'
                        ) . "' and forms_rows_position in ('" . $v . ":1','" . $v . ":2','" . $v . ":3','" . $v . ":4','" . $v . ":5','" . $v . ":6')"
                    );
                }
            }
        }
        exit();
        break;

    case 'delete':

        if (isset($_GET['id'])) {
            db_delete_row('app_forms_rows', _GET('id'));

            $v = _GET('id');
            db_query(
                "update app_fields set forms_rows_position='' where entities_id='" . _GET(
                    'entities_id'
                ) . "' and forms_rows_position in ('" . $v . ":1','" . $v . ":2','" . $v . ":3','" . $v . ":4','" . $v . ":5','" . $v . ":6')"
            );
        }

        redirect_to('entities/forms', 'entities_id=' . _GET('entities_id'));
        break;
}