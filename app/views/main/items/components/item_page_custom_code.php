<?php

if (!defined('KERUY_CRM')) {
    exit;
}

$javascript_code = trim($entity_cfg->get('javascript_in_item_page'));
$php_code = trim($entity_cfg->get('php_in_item_page'));

if (strlen($javascript_code) or strlen($php_code)) {
    $fields_values = $current_item_info;

    //valuse for parent item
    if ($parent_entity_item_id > 0) {
        $parent_item_query = db_query("select * from app_entity_{$parent_entity_id} where id={$parent_entity_item_id}");
        if ($parent_item = db_fetch_array($parent_item_query)) {
            foreach ($parent_item as $fields_id => $fields_value) {
                if (strstr($fields_id, 'field_')) {
                    $fields_values[$fields_id] = $fields_value;
                }
            }
        }
    }

    //prepare values to replace
    foreach ($fields_values as $fiels_id => $fields_value) {
        $fiels_id = str_replace('field_', '', $fiels_id);

        if (!strlen($fields_value)) {
            $fields_value = 0;
        } elseif (is_string($fields_value)) {
            $fields_value = "'" . $fields_value . "'";
        }

        $php_code = str_replace('[' . $fiels_id . ']', $fields_value, $php_code);
        $javascript_code = str_replace('[' . $fiels_id . ']', $fields_value, $javascript_code);
    }

    $php_code = str_replace('[current_user_id]', $app_user['id'], $php_code);
    $javascript_code = str_replace('[current_user_id]', $app_user['id'], $javascript_code);

    //insert custom javascript code
    if (strlen($javascript_code)) {
        echo '
      			<script>
      				' . $javascript_code . '
      			</script>
      			';
    }

    if ($entity_cfg->get('php_in_item_page_debug_mode') == 1 and strlen($php_code)) {
        print_rr($fields_values);
        print_rr(htmlspecialchars($php_code));
    }

    if (strlen($php_code)) {
        try {
            eval($php_code);
        } catch (Error $e) {
            echo alert_error(TEXT_ERROR . ' ' . $e->getMessage() . ' on line ' . $e->getLine());
        }
    }
}
