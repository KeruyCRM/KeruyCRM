<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Tools;

class Db_backup extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Tools\_Module::top();
    }

    public function index()
    {
        $backups_query = \Tools\Split_page::makeQuery(
            'app_backups',
            ['is_auto = 0'],
            ['order' => 'date_added desc']
        );

        \K::$fw->listing_split = $listing_split = new \Tools\Split_page($backups_query, 'records_listing');

        \K::$fw->backups_query = \K::model()->db_fetch_split(
            $listing_split->sql_query()
        );

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'db_backup.php';

        echo \K::view()->render($this->app_layout);
    }

    public function download()
    {
        if (isset(\K::$fw->GET['id'])) {
            //$info_query = db_query("select * from app_backups where id='" . db_input(\K::$fw->GET['id']) . "'");
            $info = \K::model()->db_fetch_one('app_backups', [
                'id = ?',
                \K::$fw->GET['id']
            ]);
            if ($info) {
                $filename = $info['filename'];

                $backup_dir = $info['is_auto'] ? \K::$fw->DIR_FS_BACKUPS_AUTO : \K::$fw->DIR_FS_BACKUPS;

                if (is_file($backup_dir . $filename)) {
                    header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
                    header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
                    header("Cache-Control: no-cache, must-revalidate");
                    header("Pragma: no-cache");
                    header("Content-Type: Application/octet-stream");
                    header("Content-disposition: attachment; filename=" . $filename);

                    readfile($backup_dir . $filename);
                } else {
                    \K::flash()->addMessage(\K::$fw->TEXT_FILE_NOT_FOUND, 'error');

                    \Helpers\Urls::redirect_to('main/tools/db_backup');
                }
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['id'])) {
            //$info_query = db_query("select * from app_backups where id='" . db_input($_GET['id']) . "'");
            $info = \K::model()->db_fetch_one('app_backups', [
                'id = ?',
                \K::$fw->GET['id']
            ]);
            if ($info) {
                $filename = $info['filename'];
                $backup_dir = $info['is_auto'] ? \K::$fw->DIR_FS_BACKUPS_AUTO : \K::$fw->DIR_FS_BACKUPS;

                if (is_file($backup_dir . $filename)) {
                    unlink($backup_dir . $filename);

                    \K::flash()->addMessage(\K::$fw->TEXT_BACKUP_DELETED, 'success');
                } else {
                    \K::flash()->addMessage(\K::$fw->TEXT_FILE_NOT_FOUND, 'error');
                }

                \K::model()->db_delete_row('app_backups', $info['id']);

                if ($info['is_auto']) {
                    \Helpers\Urls::redirect_to('main/tools/db_backup_auto');
                }
            }

            \Helpers\Urls::redirect_to('main/tools/db_backup');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function backup()
    {
        if (\K::$fw->VERB == 'POST') {
            $backup = new \Tools\Backup();
            $backup->set_description(\K::$fw->POST['description']);
            $backup->create();

            \K::flash()->addMessage(\K::$fw->TEXT_BACKUP_CREATED, 'success');

            \Helpers\Urls::redirect_to('main/tools/db_backup');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function export_template()
    {
        $filename = mb_ereg_replace('_+', '_', mb_ereg_replace("[^[:alnum:]]", '_', \K::$fw->CFG_APP_NAME));

        $filename = $filename . '_' . date('Y-m-d_H-i') . '_KeruyCRM_' . \K::$fw->PROJECT_VERSION . '.sql';

        $backup = new \Tools\Backup();
        $backup->set_filename($filename);
        $backup->create();

        header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-Type: Application/octet-stream");
        header("Content-disposition: attachment; filename=" . $filename);

        readfile(\K::$fw->DIR_FS_BACKUPS . $filename);

        unlink(\K::$fw->DIR_FS_BACKUPS . $filename);
    }

    public function __destruct()
    {
        \Tools\Backup::reset();
    }
}