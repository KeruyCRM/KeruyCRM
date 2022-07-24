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
        \Helpers\Urls::redirect_to('main/dashboard');
    }

    public function restore()
    {
        if (\K::$fw->VERB == 'POST') {
            //$info_query = db_query("select * from app_backups where id='" . db_input(\K::$fw->GET['id']) . "'");

            $info = \K::model()->db_fetch_one('app_backups', [
                'id = ?',
                \K::$fw->POST['id']
            ]);

            if ($info) {
                $filename = $info['filename'];

                $backup_dir = $info['is_auto'] ? \K::$fw->DIR_FS_BACKUPS_AUTO : \K::$fw->DIR_FS_BACKUPS;

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
                    \Tools\Backup::restore($filename, $info['is_auto']);

                    if ($is_zip_archive) {
                        unlink($backup_dir . $filename);
                    }
                }
            }

            \Helpers\Urls::redirect_to('main/users/login/logoff', '', true);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function restore_file()
    {
        if (\K::$fw->VERB == 'POST') {
            $filename = \K::$fw->POST['filename'];

            if (substr($filename, -4) == '.sql' or substr($filename, -4) == '.zip') {
                if (is_file(\K::$fw->DIR_FS_BACKUPS . $filename)) {
                    $is_zip_archive = false;
                    if (substr($filename, -4) == '.zip') {
                        $zip_filename = $filename;
                        $zip = new \ZipArchive();
                        $res = $zip->open(\K::$fw->DIR_FS_BACKUPS . $filename);
                        if ($res === true) {
                            $filename = $zip->getNameIndex(0);
                            $zip->extractTo(\K::$fw->DIR_FS_BACKUPS);

                            for ($x = 1; $x < $zip->numFiles; $x++) {
                                @unlink(\K::$fw->DIR_FS_BACKUPS . $zip->getNameIndex($x));
                            }

                            $zip->close();

                            unlink(\K::$fw->DIR_FS_BACKUPS . $zip_filename);
                        }

                        if (substr($filename, -4) != '.sql') {
                            $filename .= '.sql';
                        }
                    }

                    //restore database
                    \Tools\Backup::restore($filename);

                    unlink(\K::$fw->DIR_FS_BACKUPS . $filename);
                }
            }

            \Helpers\Urls::redirect_to('main/users/login/logoff', '', true);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function restore_by_id()
    {
        \K::$fw->html .= '
			<script>
				$(function(){
					$("#db_restore_process").load("' . \Helpers\Urls::url_for(
                'main/tools/db_restore_process/restore'
            ) . '",{id:"' . \K::$fw->GET['id'] . '"})
				})				
			</script>
		';
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'db_restore_process.php';

        echo \K::view()->render($this->app_layout);
    }

    public function restore_from_file()
    {
        if (\K::$fw->VERB == 'POST') {
            $is_file = false;
            if (strlen($filename = \K::$fw->FILES['filename']['name']) > 0) {
                if (substr($filename, -4) == '.sql' or substr($filename, -4) == '.zip') {
                    if (move_uploaded_file(
                        \K::$fw->FILES['filename']['tmp_name'],
                        \K::$fw->DIR_FS_BACKUPS . $filename
                    )) {
                        $is_file = true;

                        \K::$fw->html .= '
					<script>
						$(function(){
							$("#db_restore_process").load("' . \Helpers\Urls::url_for(
                                'main/tools/db_restore_process/restore_file'
                            ) . '",{filename:"' . $filename . '"})
						})
					</script>
				';
                    }
                }
            }

            if (!$is_file) {
                \K::$fw->html = '<div class="alert alert-danger">' . \K::$fw->TEXT_FILE_NOT_FOUND . '</div>';
            }

            \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'db_restore_process.php';

            echo \K::view()->render($this->app_layout);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}