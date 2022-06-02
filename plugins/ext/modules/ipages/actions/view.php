<?php

//check if report exist
$ipage_query = db_query(
    "select * from app_ext_ipages where id='" . db_input(
        (int)$_GET['id']
    ) . "' and (find_in_set(" . $app_user['group_id'] . ",users_groups) or find_in_set(" . $app_user['id'] . ",assigned_to))"
);
if (!$ipage = db_fetch_array($ipage_query)) {
    redirect_to('dashboard/page_not_found');
}

switch ($app_module_action) {
    case 'preview_attachment_image':
        $file = attachments::parse_filename(base64_decode($_GET['file']));

        if (is_file($file['file_path'])) {
            $size = getimagesize($file['file_path']);
            echo '<img width="' . $size[0] . '" height="' . $size[1] . '"  src="' . url_for(
                    'ext/ipages/view',
                    'id=' . _GET('id') . '&action=download_attachment&preview=1&file=' . urlencode($_GET['file'])
                ) . '">';
        }

        exit();
        break;
    case 'download_attachment':
        $file = attachments::parse_filename(base64_decode($_GET['file']));

        //check if using file storage for feild
        if (class_exists('file_storage') and isset($_GET['field'])) {
            file_storage::download_file(_get::int('field'), base64_decode($_GET['file']));
        }

        if (is_file($file['file_path'])) {
            if ($file['is_image'] and isset($_GET['preview'])) {
                $size = getimagesize($file['file_path']);
                header("Content-type: " . $size['mime']);
                header('Content-Disposition: filename="' . $file['name'] . '"');

                flush();

                readfile($file['file_path']);
            } elseif ($file['is_pdf'] and isset($_GET['preview'])) {
                header("Content-type: application/pdf");
                header('Content-Disposition: filename="' . $file['name'] . '"');

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

                flush();

                readfile($file['file_path']);
            }
        } else {
            echo TEXT_FILE_NOT_FOUD;
        }

        exit();
        break;
}

$app_title = app_set_title($ipage['name']);