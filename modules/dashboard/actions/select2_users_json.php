<?php

if ((!IS_AJAX or !isset($_GET['form_type'])) and $app_module_action != 'preview_image') {
    exit();
}

//check if field exist
$field_query = db_query(
    "select * from app_fields where id='" . _get::int('field_id') . "' and type='fieldtype_users_ajax'"
);
if (!$field = db_fetch_array($field_query)) {
    exit();
}

$cfg = new fields_types_cfg($field['configuration']);


//echo $app_session_token . '=' .base64_decode(urldecode($_GET['token']));


//check access
switch ($_GET['form_type']) {
    case 'ext/public/form':
        $check_query = db_query(
            "select id from app_ext_public_forms where entities_id='" . $field['entities_id'] . "' and not find_in_set('" . $field['id'] . "',hidden_fields)"
        );
        if (!$check = db_fetch_array($check_query)) {
            exit();
        }
        break;
    case 'users/registration':
        if ($field['entities_id'] != 1) {
            exit();
        }
        break;
    default:
        if (!app_session_is_registered('app_logged_users_id')) {
            exit();
        }
        break;
}

switch ($app_module_action) {
    case 'select_items':

        $parent_entity_item_id = _GET('parent_entity_item_id');

        $entity_info = db_find('app_entities', 1);
        $field_entity_info = db_find('app_entities', $field['entities_id']);


        $listing_sql_query = 'e.id>0 ';
        $listing_sql_query_order = '';
        $listing_sql_query_join = '';
        $listing_sql_query_having = '';
        $listing_sql_select = '';


        //check if parent item has users fields and if users are assigned        
        $parent_users_list = [];

        if (isset($_GET['parent_entity_item_id']) and $_GET['parent_entity_item_id'] > 0 and $cfg->get(
                'disable_dependency'
            ) != 1) {
            if ($parent_users_list = items::get_paretn_users_list(
                $field['entities_id'],
                $_GET['parent_entity_item_id']
            )) {
                $listing_sql_query .= " and e.id in (" . implode(',', $parent_users_list) . ")";
            }
        }

        if (isset($_POST['search'])) {
            $items_search = new items_search($entity_info['id']);
            $items_search->set_search_keywords($_POST['search']);

            if (is_array($cfg->get('fields_for_search'))) {
                $search_fields = [];
                foreach ($cfg->get('fields_for_search') as $id) {
                    $search_fields[] = ['id' => $id];
                }
                $items_search->search_fields = $search_fields;
            }

            $listing_sql_query .= $items_search->build_search_sql_query('and');
        }


        //add visibility access query
        $listing_sql_query .= records_visibility::add_access_query(1);


        $listing_sql_query .= fieldtype_entity_ajax::mysql_query_where($cfg, $field, $parent_entity_item_id);


        //select all active users or already assigned users
        $listing_sql_query .= " and e.field_5=1";


        $listing_sql_query_order = " order by " . (CFG_APP_DISPLAY_USER_NAME_ORDER == 'firstname_lastname' ? ' e.field_7, e.field_8' : ' e.field_8, e.field_7');

        //prepare formula query
        if (strlen($heading_template = $cfg->get('heading_template'))) {
            if (preg_match_all('/\[(\d+)\]/', $heading_template, $matches)) {
                $listing_sql_select = fieldtype_formula::prepare_query_select(
                    1,
                    '',
                    false,
                    ['fields_in_listing' => implode(',', $matches[1])]
                );
            }
        }

        //join access
        $listing_sql_query_join = ''; // " left join app_entities_access ea on (ea.access_groups_id=e.field_6 and ea.entities_id=" . $field['entities_id'] . ")";
        $listing_sql_query .= ''; // " and (find_in_set('view',ea.access_schema) or find_in_set('view_assigned',ea.access_schema) " . ($cfg->get('hide_admin')!=1 ? " or e.field_6=0":"") . ")";

        $access_schema = users::get_entities_access_schema_by_groups($field['entities_id']);

        $results = [];
        $listing_sql = "select  e.* " . $listing_sql_select . " from app_entity_1 e " . $listing_sql_query_join . " where " . $listing_sql_query . $listing_sql_query_having . $listing_sql_query_order;

        $listing_split = new split_page($listing_sql, '', 'query_num_rows', 30);
        $items_query = db_query($listing_split->sql_query, false);
        while ($item = db_fetch_array($items_query)) {
            $multiple_access_groups = strlen($item['multiple_access_groups']) ? explode(
                ',',
                $item['multiple_access_groups']
            ) : [$item['field_6']];

            foreach ($multiple_access_groups as $access_group_id) {
                //hide administrators
                if ($cfg->get('hide_admin') == 1 and $access_group_id == 0) {
                    continue;
                }

                //display users from selected users groups only
                if (is_array($cfg->get('use_groups')) and count($cfg->get('use_groups')) and !in_array(
                        $access_group_id,
                        $cfg->get('use_groups')
                    )) {
                    continue;
                }

                $access_schema[$access_group_id] = $access_schema[$access_group_id] ?? [];

                if ($access_group_id == 0 or in_array('view', $access_schema[$access_group_id]) or in_array(
                        'view_assigned',
                        $access_schema[$access_group_id]
                    )) {
                    $template = fieldtype_users_ajax::render_heading_template($item, $cfg->get('heading_template'));

                    $results[] = ['id' => $item['id'], 'text' => $template['text'], 'html' => $template['html']];

                    //break from foreach to add only one user in list
                    break;
                }
            }
        }

        $response = ['results' => $results];

        if ($listing_split->number_of_pages != $_POST['page'] and $listing_split->number_of_pages > 0) {
            $response['pagination']['more'] = 'true';
        }

        echo json_encode($response);

        exit();

        break;

    case 'preview_image':
        $file = attachments::parse_filename(base64_decode($_GET['file']));

        $filepath = DIR_WS_USERS . $file['file_sha1'];
        if (!is_file($filepath)) {
            $filepath = 'images/no_photo.png';
        }

        $size = getimagesize($filepath);
        header("Content-type: " . $size['mime']);
        header('Content-Disposition: filename="' . $file['name'] . '"');

        flush();

        readfile($filepath);

        break;
}