<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Export_attachments extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();
    }

    public function index()
    {
        \Helpers\Urls::redirect_to('main/dashboard');
    }

    public function export()
    {
        if (!isset($app_selected_items[$_POST['reports_id']])) {
            $app_selected_items[$_POST['reports_id']] = [];
        }

        if (count($app_selected_items[$_POST['reports_id']]) > 0 and isset($_POST['fields'])) {
            $zip = new ZipArchive();
            $zip_filename = $app_user['id'] . time() . ".zip";
            $zip_filepath = DIR_FS_TMP . $zip_filename;
            $zip->open($zip_filepath, ZipArchive::CREATE);

            $selected_items = implode(',', $app_selected_items[$_POST['reports_id']]);

            $listing_sql = "select e.* from app_entity_" . $current_entity_id . " e where e.id in (" . $selected_items . ") order by field(id," . $selected_items . ")";
            $items_query = db_query($listing_sql);
            while ($item = db_fetch_array($items_query)) {
                $attachments = [];
                foreach ($_POST['fields'] as $field_id) {
                    if (isset($item['field_' . $field_id])) {
                        if (strlen($item['field_' . $field_id])) {
                            $attachments = array_merge(explode(',', $item['field_' . $field_id]), $attachments);
                        }
                    }
                }

                if (count($attachments)) {
                    foreach ($attachments as $filename) {
                        $file = attachments::parse_filename($filename);

                        if (is_file(DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file_sha1'])) {
                            $zip->addFile(
                                DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file_sha1'],
                                '/' . $item['id'] . '/' . $file['name']
                            );
                        }
                    }
                }
                //print_rr($attachments);
            }

            $zip->close();

            $filename = preg_replace('/\W+/u', '_', trim($_POST['filename'])) . '.zip';

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($zip_filepath));

            flush();

            readfile($zip_filepath);

            unlink($zip_filepath);

            exit();
        } else {
            redirect_to('items/items', 'path=' . $app_path);
        }
    }
}