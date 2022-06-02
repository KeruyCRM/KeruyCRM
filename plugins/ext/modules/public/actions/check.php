<?php

//check security settings if they are enabled
app_restricted_countries::verify();
app_restricted_ip::verify();

$app_layout = 'public_layout.php';

$public_form_query = db_query(
    "select * from app_ext_public_forms where id='" . db_input(_get::int('id')) . "' and check_enquiry=1"
);
if (!$public_form = db_fetch_array($public_form_query)) {
    die(TEXT_PAGE_NOT_FOUND_CONTENT);
}

if ($public_form['is_active'] == 0) {
    redirect_to('ext/public/form_inactive', 'id=' . $public_form['id']);
}

$app_title = (strlen($public_form['check_page_title']) > 0 ? $public_form['check_page_title'] : $public_form['name']);

$current_entity_id = $public_form['entities_id'];
$current_path_array = [$public_form['entities_id']];
$app_user = [];
$app_user['group_id'] = 0;
$app_item_info = false;


switch ($app_module_action) {
    case 'download_attachment':
        $item_query = db_query(
            "select e.*  from app_entity_" . $current_entity_id . " e where id='" . _get::int('item') . "'"
        );
        if ($item = db_fetch_array($item_query)) {
            $filename = base64_decode($_GET['file']);
            $download_filename = '';

            if (isset($_GET['field'])) {
                if (strstr($_GET['field'], 'comment')) {
                    $comment_id = _get::int('field');

                    $comments_query_sql = "select * from app_comments where entities_id='" . db_input(
                            $current_entity_id
                        ) . "' and items_id='" . _get::int('item') . "' and id='" . $comment_id . "'";
                    $comments_query = db_query($comments_query_sql);
                    if ($comments = db_fetch_array($comments_query)) {
                        if (in_array($filename, explode(',', $comments['attachments']))) {
                            $download_filename = $filename;
                        }
                    }
                } else {
                    $field_id = _get::int('field');

                    if (isset($item['field_' . $field_id]) and isset($_GET['file'])) {
                        if (in_array($filename, explode(',', $item['field_' . $field_id]))) {
                            $download_filename = $filename;

                            //check if using file storage for feild
                            file_storage::download_file(_get::int('field'), base64_decode($_GET['file']));
                        }
                    }
                }
            }

            if (strlen($download_filename)) {
                $file = attachments::parse_filename($download_filename);

                if (is_file($file['file_path'])) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename=' . $file['name']);
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file['file_path']));
                    ob_clean();
                    flush();

                    readfile($file['file_path']);
                } else {
                    echo TEXT_FILE_NOT_FOUND;
                }
            } else {
                echo TEXT_FILE_NOT_FOUND;
            }
        }
        exit();
        break;
    case 'check':

        //chck form token
        app_check_form_token('ext/public/check&id=' . $public_form['id']);

        $is_error = false;

        //check reaptcha
        if (app_recaptcha::is_enabled()) {
            if (!app_recaptcha::verify()) {
                $alerts->add(TEXT_RECAPTCHA_VERIFY_ROBOT, 'error');

                $is_error = true;
            }
        }

        if (!$is_error) {
            if (strlen($public_form['check_enquiry_fields'])) {
                $where_sql = [];
                $fields_query = db_query(
                    "select f.* from app_fields f where f.id in (" . $public_form['check_enquiry_fields'] . ") and  f.entities_id='" . db_input(
                        $current_entity_id
                    ) . "' order by f.sort_order, f.name"
                );
                while ($field = db_fetch_array($fields_query)) {
                    $value = (isset($_POST['fields'][$field['id']]) ? $_POST['fields'][$field['id']] : '');

                    switch ($field['type']) {
                        case 'fieldtype_id':
                            $where_sql[] = "e.id=" . (int)$value;
                            break;
                        case 'fieldtype_date_added':
                            $where_sql[] = "FROM_UNIXTIME(e.date_added,'%Y-%m-%d')>='" . db_input($value) . "'";
                            break;
                        default:
                            $where_sql[] = "e.field_" . $field['id'] . "='" . db_input($value) . "'";
                            break;
                    }
                }

                //prepare forumulas query
                $listing_sql_query_select = fieldtype_formula::prepare_query_select($current_entity_id, '');

                $item_query = db_query(
                    "select e.* " . $listing_sql_query_select . " from app_entity_" . $current_entity_id . " e where " . implode(
                        ' and ',
                        $where_sql
                    )
                );
                if ($item = db_fetch_array($item_query)) {
                    $app_item_info = $item;

                    $app_action = 'check_result';
                } else {
                    $alerts->add(TEXT_RECORD_NOT_FOUND, 'error');
                }
            }
        }

        break;
}