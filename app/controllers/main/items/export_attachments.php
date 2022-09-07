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
        if (\K::$fw->VERB == 'POST') {
            if (!isset(\K::$fw->app_selected_items[\K::$fw->POST['reports_id']])) {
                \K::$fw->app_selected_items[\K::$fw->POST['reports_id']] = [];
            }

            if (count(
                    \K::$fw->app_selected_items[\K::$fw->POST['reports_id']]
                ) > 0 and isset(\K::$fw->POST['fields'])) {
                $zip = new \ZipArchive();
                $zip_filename = \K::$fw->app_user['id'] . time() . ".zip";
                $zip_filepath = \K::$fw->DIR_FS_TMP . $zip_filename;
                $zip->open($zip_filepath, \ZipArchive::CREATE);

                $selected_items = \K::model()->quoteToString(
                    \K::$fw->app_selected_items[\K::$fw->POST['reports_id']],
                    \PDO::PARAM_INT
                );

                /*$listing_sql = "select e.* from app_entity_" . \K::$fw->current_entity_id . " e where e.id in (" . $selected_items . ") order by field(id," . $selected_items . ")";
                $items_query = db_query($listing_sql);*/

                $items_query = \K::model()->db_fetch('app_entity_' . (int)\K::$fw->current_entity_id, [
                    'id in (' . $selected_items . ')'
                ], ['order' => 'field(id,' . $selected_items . ')']);

                //while ($item = db_fetch_array($items_query)) {
                foreach ($items_query as $item) {
                    $item = $item->cast();

                    $attachments = [];
                    foreach (\K::$fw->POST['fields'] as $field_id) {
                        if (isset($item['field_' . $field_id])) {
                            if (strlen($item['field_' . $field_id])) {
                                $attachments = array_merge(explode(',', $item['field_' . $field_id]), $attachments);
                            }
                        }
                    }

                    if (count($attachments)) {
                        foreach ($attachments as $filename) {
                            $file = \Tools\Attachments::parse_filename($filename);

                            if (is_file(\K::$fw->DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file_sha1'])) {
                                $zip->addFile(
                                    \K::$fw->DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file_sha1'],
                                    '/' . $item['id'] . '/' . $file['name']
                                );
                            }
                        }
                    }
                }

                $zip->close();

                $filename = preg_replace('/\W+/u', '_', trim(\K::$fw->POST['filename'])) . '.zip';

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
            } else {
                \Helpers\Urls::redirect_to('main/items/items', 'path=' . \K::$fw->app_path);
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}