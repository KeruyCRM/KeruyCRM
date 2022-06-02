<?php

switch ($app_module_action) {
    case 'restore':

        $info_query = db_query("select * from app_backups where id='" . db_input($_GET['id']) . "'");
        if ($info = db_fetch_array($info_query)) {
            $filename = $info['filename'];

            $backup_dir = $info['is_auto'] ? DIR_FS_BACKUPS_AUTO : DIR_FS_BACKUPS;

            if (is_file($backup_dir . $filename)) {
                //check if file is ZIP archive and unzip it
                $is_zip_archive = false;
                if (substr($filename, -4) == '.zip') {
                    $zip = new ZipArchive;
                    $res = $zip->open($backup_dir . $filename);
                    if ($res === true) {
                        $zip->extractTo($backup_dir);
                        $zip->close();
                    }

                    $filename = substr($filename, 0, -4);

                    $is_zip_archive = true;
                }

                //restore database
                backup::restore($filename, $info['is_auto']);

                if ($is_zip_archive) {
                    unlink($backup_dir . $filename);
                }
            }
        }

        redirect_to('users/login', 'action=logoff');
        break;

    case 'restore_file':
        $filename = $_POST['filename'];

        if (substr($filename, -4) == '.sql' or substr($filename, -4) == '.zip') {
            if (is_file(DIR_FS_BACKUPS . $filename)) {
                $is_zip_archive = false;
                if (substr($filename, -4) == '.zip') {
                    $zip_filename = $filename;
                    $zip = new ZipArchive;
                    $res = $zip->open(DIR_FS_BACKUPS . $filename);
                    if ($res === true) {
                        $filename = $zip->getNameIndex(0);
                        $zip->extractTo(DIR_FS_BACKUPS);
                        $zip->close();

                        unlink(DIR_FS_BACKUPS . $zip_filename);
                    }

                    //$zip_filename = $filename;
                    //$filename = substr($filename, 0, -4);     
                    //echo $filename;
                    //exit();

                    if (substr($filename, -4) != '.sql') {
                        $filename .= '.sql';
                    }
                }

                //restore database
                backup::restore($filename);

                unlink(DIR_FS_BACKUPS . $filename);
            }
        }

        redirect_to('users/login', 'action=logoff');
        break;
}

$app_layout = 'public_layout.php';
