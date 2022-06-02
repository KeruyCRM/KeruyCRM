<?php

switch ($app_module_action) {
    case 'create_timer':
        $timer_query = db_query(
            "select * from app_ext_timer where entities_id='" . db_input(
                $_POST['entities_id']
            ) . "' and items_id='" . db_input($_POST['items_id']) . "' and users_id='" . db_input($app_user['id']) . "'"
        );
        if (!$timer = db_fetch_array($timer_query)) {
            $sql_data = [
                'seconds' => 0,
                'entities_id' => $_POST['entities_id'],
                'items_id' => $_POST['items_id'],
                'users_id' => $app_user['id'],
            ];

            db_perform('app_ext_timer', $sql_data);
        }

        echo timer::render_header_dropdown_menu();

        exit();
        break;
    case 'set_timer':
        $timer_query = db_query(
            "select * from app_ext_timer where entities_id='" . db_input(
                $_POST['entities_id']
            ) . "' and items_id='" . db_input($_POST['items_id']) . "' and users_id='" . db_input($app_user['id']) . "'"
        );
        if (!$timer = db_fetch_array($timer_query)) {
            $sql_data = [
                'seconds' => $_POST['seconds'],
                'entities_id' => $_POST['entities_id'],
                'items_id' => $_POST['items_id'],
                'users_id' => $app_user['id'],
            ];

            db_perform('app_ext_timer', $sql_data);
        } else {
            db_query(
                "update app_ext_timer set seconds='" . db_input($_POST['seconds']) . "' where id='" . db_input(
                    $timer['id']
                ) . "'"
            );
        }

        exit();
        break;
    case 'delete_timer':
        $timer_query = db_query(
            "select * from app_ext_timer where entities_id='" . db_input(
                $_POST['entities_id']
            ) . "' and items_id='" . db_input($_POST['items_id']) . "' and users_id='" . db_input($app_user['id']) . "'"
        );
        if ($timer = db_fetch_array($timer_query)) {
            db_delete_row('app_ext_timer', $timer['id']);
        }

        echo timer::render_header_dropdown_menu();

        exit();
        break;
}