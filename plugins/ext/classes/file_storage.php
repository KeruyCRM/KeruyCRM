<?php

class file_storage
{
    function add_to_queue($fields_id, $filename)
    {
        $rules_query = db_query(
            "select fsr.* from app_ext_file_storage_rules fsr, app_ext_modules m where fsr.modules_id=m.id and m.is_active=1 and find_in_set(" . $fields_id . ",fields)"
        );
        if ($rules = db_fetch_array($rules_query)) {
            $sql_data = [
                'modules_id' => $rules['modules_id'],
                'filename' => $filename,
            ];

            db_perform('app_ext_file_storage_queue', $sql_data);
        }
    }

    static function upload_from_queue()
    {
        $queue_query = db_query(
            "select q.*, m.module from app_ext_file_storage_queue q, app_ext_modules m where m.id=q.modules_id and m.is_active=1 order by q.status limit 1"
        );
        if ($queue = db_fetch_array($queue_query)) {
            $module = new $queue['module'];

            $module->upload($queue['modules_id'], $queue);
        }
    }

    static function remove_from_queue($id)
    {
        db_query("delete from app_ext_file_storage_queue where id='" . db_input($id) . "'");
    }

    static function check($fields_id)
    {
        $rules_query = db_query(
            "select m.module, m.id as modules_id from app_ext_file_storage_rules fsr, app_ext_modules m where fsr.modules_id=m.id and m.is_active=1 and find_in_set(" . $fields_id . ",fields)"
        );
        if ($rules = db_fetch_array($rules_query)) {
            return true;
        } else {
            return false;
        }
    }

    static function download_file($fields_id, $filename)
    {
        $modules = new modules('file_storage');

        $rules_query = db_query(
            "select m.module, m.id as modules_id from app_ext_file_storage_rules fsr, app_ext_modules m where fsr.modules_id=m.id and m.is_active=1 and find_in_set(" . $fields_id . ",fields)"
        );
        if ($rules = db_fetch_array($rules_query)) {
            $module = new $rules['module'];

            $module->download($rules['modules_id'], $filename);
        }
    }

    static function delete_files($fields_id, $files = [])
    {
        $modules = new modules('file_storage');

        $rules_query = db_query(
            "select m.module, m.id as modules_id from app_ext_file_storage_rules fsr, app_ext_modules m where fsr.modules_id=m.id and m.is_active=1 and find_in_set(" . $fields_id . ",fields)"
        );
        if ($rules = db_fetch_array($rules_query)) {
            $module = new $rules['module'];

            $module->delete($rules['modules_id'], $files);
        }
    }

    static function download_files($fields_id, $files)
    {
        $modules = new modules('file_storage');

        $rules_query = db_query(
            "select m.module, m.id as modules_id from app_ext_file_storage_rules fsr, app_ext_modules m where fsr.modules_id=m.id and m.is_active=1 and find_in_set(" . $fields_id . ",fields)"
        );
        if ($rules = db_fetch_array($rules_query)) {
            $module = new $rules['module'];

            $module->download_files($rules['modules_id'], $files);
        }
    }

    static function download_file_content($filename, $filepath)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        ob_clean();
        flush();

        readfile($filepath);

        unlink($filepath);

        exit();
    }
}