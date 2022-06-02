<?php

if (!users::has_access('import') or !strlen($app_path)) {
    redirect_to('dashboard/access_forbidden');
}

if (!app_session_is_registered('import_fields')) {
    $import_fields = [];
    app_session_register('import_fields');
}

switch ($app_module_action) {
    case 'import':
        $worksheet = json_decode(stripslashes($_POST['worksheet']), true);
        $entities_id = $current_entity_id;
        $parent_item_id = $parent_entity_item_id;

        $entity_info = db_find('app_entities', $entities_id);

        if ($entity_info['parent_id'] > 0) {
            $parent_item_query = db_query(
                "select * from app_entity_" . $entity_info['parent_id'] . " where id='" . db_input(
                    $parent_item_id
                ) . "'"
            );

            if ($parent_item = db_fetch_array($parent_item_query)) {
                $path_info = items::get_path_info($entity_info['parent_id'], $parent_item['id']);

                $redirect_path = $path_info['full_path'] . '/' . $entities_id;
            }
        } else {
            $redirect_path = $entities_id;
        }

        //check if any fields are binded
        if (count($import_fields) == 0) {
            $alerts->add(TEXT_IMPORT_BIND_FIELDS_ERROR, 'error');
            redirect_to('items/items', 'path=' . $redirect_path);
        }

        //check required fields for users entity
        if ($entities_id == 1) {
            if (!in_array(7, $import_fields) or !in_array(8, $import_fields) or !in_array(9, $import_fields)) {
                $alerts->add(TEXT_IMPORT_BIND_USERS_FIELDS_ERROR, 'error');
                redirect_to('items/items', 'path=' . $redirect_path);
            }

            $hasher = new PasswordHash(11, false);
        }

        //multilevel import
        $multilevel_import = _get::int('multilevel_import');

        $import_entities_list = [];
        $import_entities_list[] = $current_entity_id;

        if ($multilevel_import > 0) {
            $import_entities_list = [];
            $import_entities_list[] = $multilevel_import;

            foreach (entities::get_parents($multilevel_import) as $entity_id) {
                $import_entities_list[] = $entity_id;

                if ($entity_id == $current_entity_id) {
                    break;
                }
            }

            $import_entities_list = array_reverse($import_entities_list);

            //print_rr($import_entities_list);
            //exit();

            //check heading
            foreach ($import_entities_list as $id) {
                $check = false;
                $heading_field_id = fields::get_heading_id($id);
                foreach ($import_fields as $c => $v) {
                    if ($v == $heading_field_id) {
                        $check = true;
                    }
                }

                if (!$check) {
                    $alerts->add(
                        sprintf(TEXT_MULTI_LEVEL_IMPORT_HEADING_ERROR, entities::get_name_by_id($id)),
                        'error'
                    );
                    redirect_to('items/items', 'path=' . $app_path);
                }
            }
        }

        //check if import first row
        $first_row = (isset($_POST['import_first_row']) ? 0 : 1);

        //use when import users
        $already_exist_username = [];

        $count_items_added = 0;
        $count_items_updated = 0;

        //create chocies cahce to reduce sql queries
        $choices_names_to_id = [];
        $global_choices_names_to_id = [];
        $choices_parents_to_id = [];
        $global_choices_parents_to_id = [];

        $unique_fields = fields::get_unique_fields_list($entities_id);

        //start import
        for ($row = $first_row; $row < count($worksheet); ++$row) {
            $import_entity_parent_item_id = $parent_item_id;

            if ($multilevel_import > 0) {
                foreach ($import_entities_list as $import_entity_level => $import_entity_id) {
                    $entities_id = $import_entity_id;
                    require(component_path('items/_import.process.inc'));
                }
            } else {
                $entities_id = $current_entity_id;
                require(component_path('items/_import.process.inc'));
            }
        }


        //exit();

        if (count($already_exist_username) > 0) {
            $alerts->add(TEXT_USERS_IMPORT_ERROR . ' ' . implode(', ', $already_exist_username), 'warning');
        }

        switch ($_POST['import_action']) {
            case 'import':
                $alerts->add(TEXT_COUNT_ITEMS_ADDED . ' ' . $count_items_added, 'success');
                break;
            case 'update':
                $alerts->add(TEXT_COUNT_ITEMS_UPDATED . ' ' . $count_items_updated, 'success');
                break;
            case 'update_import':
                $alerts->add(
                    TEXT_COUNT_ITEMS_UPDATED . ' ' . $count_items_updated . '. ' . TEXT_COUNT_ITEMS_ADDED . ' ' . $count_items_added,
                    'success'
                );
                break;
        }

        //reset import fields session
        $import_fields = [];

        redirect_to('items/items', 'path=' . $redirect_path);

        exit();
        break;
    case 'bind_field':
        $col = $_POST['col'];
        $filed_id = $_POST['filed_id'];

        $multilevel_import = _get::int('multilevel_import');

        if ($filed_id > 0) {
            $import_fields[$col] = $filed_id;

            $v = db_find('app_fields', $filed_id);

            if ($multilevel_import > 0) {
                echo '<small style="font-weight: normal">' . entities::get_name_by_id(
                        $v['entities_id']
                    ) . ':</small><br>';
            }

            echo fields_types::get_option($v['type'], 'name', $v['name']);
        } elseif (isset($import_fields[$col])) {
            unset($import_fields[$col]);
            echo '';
        }

        exit();
        break;
}