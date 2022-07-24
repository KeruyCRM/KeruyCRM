<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Tools;

class Db_restore_process extends \Controller
{
    private $app_layout = 'public_layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Tools\_Module::top();
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'db_restore_process.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }

    public function restore()
    {
        //public_layout
        $info_query = db_query("select * from app_backups where id='" . db_input($_GET['id']) . "'");
        if ($info = db_fetch_array($info_query)) {
            $filename = $info['filename'];

            $backup_dir = $info['is_auto'] ? DIR_FS_BACKUPS_AUTO : DIR_FS_BACKUPS;

            if (is_file($backup_dir . $filename)) {
                //check if file is ZIP archive and unzip it
                $is_zip_archive = false;
                if (substr($filename, -4) == '.zip') {
                    $zip = new \ZipArchive();
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
    }

    public function restore_file()
    {
        //public_layout
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
    }

    public function restore_by_id()
    {
        $html .= '
			<script>
				$(function(){
					$("#db_restore_process").load("' . url_for(
                'tools/db_restore_process',
                'action=restore&id=' . $_GET['id']
            ) . '")
				})				
			</script>
		';
    }

    public function restore_from_file()
    {
        $is_file = false;
        if (strlen($filename = $_FILES['filename']['name']) > 0) {
            if (substr($filename, -4) == '.sql' or substr($filename, -4) == '.zip') {
                if (move_uploaded_file($_FILES['filename']['tmp_name'], DIR_FS_BACKUPS . $filename)) {
                    $is_file = true;

                    $html .= '
					<script>
						$(function(){
							$("#db_restore_process").load("' . url_for(
                            'tools/db_restore_process',
                            'action=restore_file'
                        ) . '",{filename:"' . $filename . '"})
						})
					</script>
				';
                }
            }
        }

        if (!$is_file) {
            $html = '<div class="alert alert-danger">' . TEXT_FILE_NOT_FOUND . '</div>';
        }
    }
}