<?php

if (isset($_POST['switch_to_entities_id'])) {
    redirect_to('entities/entities_configuration&entities_id=' . $_POST['switch_to_entities_id']);
}

if (!app_session_is_registered('entities_filter')) {
    $entities_filter = 0;
    app_session_register('entities_filter');
}

switch ($app_module_action) {
    case 'set_entities_filter':
        $entities_filter = _POST('entities_filter');

        redirect_to('entities/entities');
        break;
    case 'save':
        $sql_data = [
            'name' => $_POST['name'],
            'display_in_menu' => $_POST['display_in_menu'] ?? 0,
            'notes' => strip_tags($_POST['notes']),
            'group_id' => $_POST['group_id'] ?? 0,
            'sort_order' => $_POST['sort_order']
        ];

        if (isset($_GET['id'])) {
            db_perform('app_entities', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            if (isset($_POST['parent_id'])) {
                $sql_data['parent_id'] = $_POST['parent_id'];
            }else{
                $sql_data['parent_id'] = 0;
            }

            db_perform('app_entities', $sql_data);
            $id = db_insert_id();

            entities::prepare_tables($id);

            $forms_tab_id = entities::insert_default_form_tab($id);

            entities::insert_reserved_fields($id, $forms_tab_id);
        }

        redirect_to('entities/');
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            $msg = entities::check_before_delete($_GET['id']);

            if (strlen($msg) > 0) {
                $alerts->add($msg, 'error');
            } else {
                $name = entities::get_name_by_id($_GET['id']);

                related_records::delete_entities_related_items_table($_GET['id']);

                entities::delete($_GET['id']);

                entities::delete_tables($_GET['id']);

                $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $name), 'success');
            }

            redirect_to('entities/');
        }
        break;

    case 'sort_groups':

        if (isset($_POST['groups_list'])) {
            $sort_order = 0;
            foreach (explode(',', str_replace('group_', '', $_POST['groups_list'])) as $v) {
                db_perform('app_entities_groups', ['sort_order' => $sort_order], 'update', "id='" . db_input($v) . "'");
                $sort_order++;
            }
        }

        exit();
        break;
    case 'sort':

        //print_rr($_POST);

        if (isset($_POST['entities_list_0'])) {
            $sort_order = 0;
            foreach (explode(',', str_replace('entity_', '', $_POST['entities_list_0'])) as $v) {
                db_perform(
                    'app_entities',
                    ['sort_order' => $sort_order, 'group_id' => 0],
                    'update',
                    "id='" . db_input($v) . "'"
                );
                $sort_order++;
            }
        }

        $groups_query = db_query("select * from app_entities_groups order by sort_order, name");
        while ($groups = db_fetch_array($groups_query)) {
            if (isset($_POST['entities_list_' . $groups['id']])) {
                $sort_order = 0;
                foreach (explode(',', str_replace('entity_', '', $_POST['entities_list_' . $groups['id']])) as $v) {
                    db_perform(
                        'app_entities',
                        ['sort_order' => $sort_order, 'group_id' => $groups['id']],
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


$entities_list = entities::get_tree(0, [], 0, [], [], false, $entities_filter);



