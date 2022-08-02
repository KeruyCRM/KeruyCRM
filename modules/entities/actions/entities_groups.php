<?php

switch ($app_module_action) {
    case 'save':
        $sql_data = [
            'name' => $_POST['name'],
            'sort_order' => $_POST['sort_order']
        ];

        if (isset($_GET['id'])) {
            db_perform('app_entities_groups', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_entities_groups', $sql_data);
            $id = db_insert_id();
        }

        redirect_to('entities/entities_groups');
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            $name = entities_groups::get_name_by_id(_GET('id'));

            entities_groups::delete(_GET('id'));

            $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $name), 'success');

            redirect_to('entities/entities_groups');
        }
        break;
    case 'sort':
        $choices_sorted = $_POST['choices_sorted'];

        if (strlen($choices_sorted) > 0) {
            $choices_sorted = json_decode(stripslashes($choices_sorted), true);

            $sort_order = 0;
            foreach ($choices_sorted as $v) {
                db_query("update app_entities_groups set sort_order={$sort_order} where id={$v['id']}");
                $sort_order++;
            }
        }

        redirect_to('entities/entities_groups');
        break;
}
