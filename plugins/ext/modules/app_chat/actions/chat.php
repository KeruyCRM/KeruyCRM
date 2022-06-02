<?php


switch ($app_module_action) {
    case 'render_users_list':
        echo $app_chat->render_users_list();

        require(component_path('ext/app_chat/chat.js'));

        exit();

        break;

    case 'get_count_unrad_messages':

        $app_chat->set_online_status();

        echo $count = $app_chat->render_count_all_unrad();

        $chat_notification = new app_chat_notification();
        echo $chat_notification->send($app_chat->count_all_unrad);

        exit();

        break;

    case 'attachments_upload':

        if (strlen($_FILES['Filedata']['tmp_name'])) {
            $file = attachments::prepare_filename($_FILES['Filedata']['name']);

            if (move_uploaded_file(
                $_FILES['Filedata']['tmp_name'],
                DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file']
            )) {
                //autoresize images if enabled
                attachments::resize(DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file']);

                //add attachments to tmp table
                $sql_data = [
                    'form_token' => $_POST['token'],
                    'filename' => $file['name'],
                    'date_added' => date('Y-m-d'),
                    'container' => 0
                ];
                db_perform('app_attachments', $sql_data);
            }
        }
        exit();
        break;

    case 'attachments_preview':

        echo $app_chat->render_attachments_preview($_GET['token']);

        exit();
        break;
    case 'attachment_delete':
        $attachments_query = db_query(
            "select * from app_attachments where id='" . db_input($_POST['id']) . "' and container=0"
        );
        if ($attachments = db_fetch_array($attachments_query)) {
            $file = attachments::parse_filename($attachments['filename']);

            if (is_file(DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file_sha1'])) {
                unlink(DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file_sha1']);
            }

            db_delete_row('app_attachments', $attachments['id']);
        }
        break;
    case 'attachment_download':
        $file = attachments::parse_filename(base64_decode($_GET['file']));

        if (is_file($file['file_path'])) {
            if ($file['is_image']) {
                $size = getimagesize($file['file_path']);
                header("Content-type: " . $size['mime']);
                header('Content-Disposition: filename="' . $file['name'] . '"');
                ob_clean();
                flush();

                readfile($file['file_path']);
            } elseif ($file['is_pdf']) {
                header("Content-type: application/pdf");
                header('Content-Disposition: filename="' . $file['name'] . '"');
                ob_clean();
                flush();

                readfile($file['file_path']);
            } else {
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
            }
        } else {
            echo 'File is not found!';
        }

        exit();
        break;
}