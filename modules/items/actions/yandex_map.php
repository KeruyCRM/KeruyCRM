<?php

switch ($app_module_action) {
    case 'update_latlng':

        $filed_id = _post::int('filed_id');

        $item_info_query = db_query(
            "select field_{$filed_id} from app_entity_{$current_entity_id} where id={$current_item_id}"
        );
        if ($item_info = db_fetch_array($item_info_query)) {
            //get current address
            if (strlen($item_info['field_' . $filed_id])) {
                $value = explode("\t", $item_info['field_' . $filed_id]);

                $cord = $_POST['cord'];

                $value[0] = $cord[0];
                $value[1] = $cord[1];

                db_query(
                    "update app_entity_{$current_entity_id} set field_{$filed_id}='" . db_input(
                        implode("\t", $value)
                    ) . "' where id='" . db_input($current_item_id) . "'"
                );
            }
        }

        break;
}

exit();