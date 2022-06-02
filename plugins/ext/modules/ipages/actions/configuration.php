<?php

//check access
if (!in_array($app_user['id'], explode(',', CFG_IPAGES_ACCESS_TO_USERS)) and !in_array(
        $app_user['group_id'],
        explode(',', CFG_IPAGES_ACCESS_TO_USERS_GROUP)
    ) and $app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}

switch ($app_module_action) {
    case 'save_description':

        $attachments = (isset($_POST['fields']['attachments']) ? $_POST['fields']['attachments'] : '');

        $sql_data = [
            'description' => $_POST['description'],
            'attachments' => fields_types::process(['class' => 'fieldtype_attachments', 'value' => $attachments]),
        ];

        db_perform('app_ext_ipages', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");

        redirect_to('ext/ipages/configuration');
        break;
    case 'save':

        $sql_data = [
            'name' => $_POST['name'],
            'short_name' => (isset($_POST['short_name']) ? $_POST['short_name'] : ''),
            'menu_icon' => $_POST['menu_icon'],
            'icon_color' => db_prepare_input($_POST['icon_color']),
            'bg_color' => db_prepare_input($_POST['bg_color']),
            'is_menu' => $_POST['is_menu'],
            'parent_id' => (isset($_POST['parent_id']) ? $_POST['parent_id'] : 0),
            'users_groups' => (isset($_POST['users_groups']) ? implode(',', $_POST['users_groups']) : ''),
            'assigned_to' => (isset($_POST['assigned_to']) ? implode(',', $_POST['assigned_to']) : ''),
            'sort_order' => $_POST['sort_order'],
            'html_code' => (isset($_POST['html_code']) ? $_POST['html_code'] : ''),
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_ipages', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_ipages', $sql_data);
        }

        redirect_to('ext/ipages/configuration');

        break;
    case 'delete':
        $obj = db_find('app_ext_ipages', $_GET['id']);

        db_delete_row('app_ext_ipages', $_GET['id']);

        db_query("update app_ext_ipages set parent_id=0 where parent_id='" . _get::int('id') . "'");

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/ipages/configuration');
        break;

    case 'attachments_upload':
        $verifyToken = md5($app_user['id'] . $_POST['timestamp']);

        if (strlen($_FILES['Filedata']['tmp_name']) and $_POST['token'] == $verifyToken) {
            $file = attachments::prepare_filename($_FILES['Filedata']['name']);

            if (move_uploaded_file(
                $_FILES['Filedata']['tmp_name'],
                DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file']
            )) {
                //autoresize images if enabled
                attachments::resize(DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file']);

                //add attachments to tmp table
                $sql_data = [
                    'form_token' => $verifyToken,
                    'filename' => $file['name'],
                    'date_added' => date('Y-m-d'),
                    'container' => $_GET['field_id']
                ];
                db_perform('app_attachments', $sql_data);
            }
        }
        exit();
        break;

    case 'attachments_preview':
        $field_id = $_GET['field_id'];

        $attachments_list = $uploadify_attachments[$field_id];

        //get new attachments
        $attachments_query = db_query(
            "select filename from app_attachments where form_token='" . db_input(
                $_GET['token']
            ) . "' and container='" . db_input($_GET['field_id']) . "'"
        );
        while ($attachments = db_fetch_array($attachments_query)) {
            $attachments_list[] = $attachments['filename'];

            if (!in_array($attachments['filename'], $uploadify_attachments_queue[$field_id])) {
                $uploadify_attachments_queue[$field_id][] = $attachments['filename'];
            }
        }

        $delete_file_url = url_for('ext/ipages/configuration', 'action=attachments_delete_in_queue');

        echo attachments::render_preview($field_id, $attachments_list, $delete_file_url);

        exit();
        break;
    case 'attachments_delete_in_queue':
        //chck form token
        app_check_form_token();

        attachments::delete_in_queue($_POST['field_id'], $_POST['filename']);

        exit();
        break;
}