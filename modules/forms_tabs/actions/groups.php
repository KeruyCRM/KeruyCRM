<?php

switch ($app_module_action) {
    case 'save':
        $entities_id = _GET('entities_id');

        $sql_data = [
            'entities_id' => $entities_id,
            'parent_id' => 0,
            'is_folder' => 1,
            'name' => $_POST['name'],
            'sort_order' => $_POST['sort_order'],

        ];

        if (isset($_GET['id'])) {
            db_perform('app_forms_tabs', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_forms_tabs', $sql_data);
        }

        redirect_to('forms_tabs/groups', 'entities_id=' . $entities_id);

        break;
    case 'delete':
        $obj = db_find('app_forms_tabs', $_GET['id']);

        db_delete_row('app_forms_tabs', $_GET['id']);

        db_query("update app_forms_tabs set parent_id=0 where parent_id='" . _get::int('id') . "'");

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('forms_tabs/groups', 'entities_id=' . _GET('entities_id'));
        break;

    case 'sort_redirect':
        redirect_to('forms_tabs/groups', 'entities_id=' . _GET('entities_id'));
        break;

    case 'sort_groups':

        if (isset($_POST['groups_list'])) {
            $sort_order = forms_tabs::get_last_sort_number(_GET('entities_id')) + 1;
            foreach (explode(',', str_replace('group_', '', $_POST['groups_list'])) as $v) {
                db_perform('app_forms_tabs', ['sort_order' => $sort_order], 'update', "id='" . db_input($v) . "'");
                $sort_order++;
            }
        }

        exit();
        break;

    case 'sort':

        //print_rr($_POST);

        if (isset($_POST['group_0'])) {
            $sort_order = 0;
            foreach (explode(',', str_replace('entity_', '', $_POST['group_0'])) as $v) {
                db_perform(
                    'app_forms_tabs',
                    ['sort_order' => $sort_order, 'parent_id' => 0],
                    'update',
                    "id='" . db_input($v) . "'"
                );
                $sort_order++;
            }
        }

        $groups_query = db_query(
            "select * from app_forms_tabs where is_folder=1 and entities_id='" . _GET(
                'entities_id'
            ) . "' order by sort_order, name"
        );
        while ($groups = db_fetch_array($groups_query)) {
            if (isset($_POST['group_' . $groups['id']])) {
                $sort_order = 0;
                foreach (explode(',', str_replace('form_tab_', '', $_POST['group_' . $groups['id']])) as $v) {
                    db_perform(
                        'app_forms_tabs',
                        ['sort_order' => $sort_order, 'parent_id' => $groups['id']],
                        'update',
                        "id='" . db_input($v) . "'"
                    );
                    $sort_order++;
                }
            }
        }

        exit();
        break;
}
    