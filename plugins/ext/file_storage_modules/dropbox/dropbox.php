<?php

require('plugins/ext/file_storage_modules/dropbox/lib/sdk/Dropbox/Auth.php');
require('plugins/ext/file_storage_modules/dropbox/lib/sdk/Dropbox/Files.php');
require('plugins/ext/file_storage_modules/dropbox/lib/sdk/Dropbox/FileProperties.php');
require('plugins/ext/file_storage_modules/dropbox/lib/sdk/Dropbox/FileRequests.php');
require('plugins/ext/file_storage_modules/dropbox/lib/sdk/Dropbox/Misc.php');
require('plugins/ext/file_storage_modules/dropbox/lib/sdk/Dropbox/Paper.php');
require('plugins/ext/file_storage_modules/dropbox/lib/sdk/Dropbox/Sharing.php');
require('plugins/ext/file_storage_modules/dropbox/lib/sdk/Dropbox/Users.php');
require('plugins/ext/file_storage_modules/dropbox/lib/sdk/Dropbox.php');

class dropbox
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_DROPBOX_TITLE;
        $this->site = 'https://www.dropbox.com';
        $this->api = 'https://github.com/lukeb2014/Dropbox-v2-PHP-SDK';
        $this->version = '1.0';
    }

    public function configuration()
    {
        $cfg = [];


        $cfg[] = [
            'key' => 'access_token',
            'type' => 'input',
            'default' => '',
            'description' => TEXT_MODULE_DROPBOX_ACCESS_TOKEN_INFO,
            'title' => TEXT_MODULE_DROPBOX_ACCESS_TOKEN,
            'params' => ['class' => 'form-control input-large required'],
        ];


        return $cfg;
    }

    function folder_prepare($folder)
    {
        $folder = explode('/', $folder);
        return '/' . $folder[0] . '-' . $folder[1] . '/';
    }

    function upload($module_id, $queue_info)
    {
        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $file = attachments::parse_filename($queue_info['filename']);

        //print_r($file);

        if (!is_file($file['file_path'])) {
            file_storage::remove_from_queue($queue_info['id']);
            return false;
        }

        //prepare folder Y-m
        $folder = $this->folder_prepare($file['folder']);


        $dropbox = new Dropbox\Dropbox($cfg['access_token']);

        $resutl = $dropbox->files->upload($folder . $file['file'], $file['file_path'], "overwrite");

        if ($resutl and !strstr($resutl, 'invalid_access_token')) {
            unlink($file['file_path']);

            file_storage::remove_from_queue($queue_info['id']);
        } else {
            file_storage::remove_from_queue($queue_info['id']);

            $error = $this->title . ': ' . 'upload error' . ($resutl ? ' (' . $resutl . ')' : '');

            modules::log_file_storage($error, $file);

            die($error);
        }
    }

    function download($module_id, $filename)
    {
        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $file = attachments::parse_filename($filename);

        //prepare folder Y-m
        $folder = $this->folder_prepare($file['folder']);

        $dropbox = new Dropbox\Dropbox($cfg['access_token']);

        $resutl_errors = $dropbox->files->download($folder . $file['file'], DIR_FS_TMP . $file['file']);

        if (is_file(DIR_FS_TMP . $file['file']) and !$resutl_errors) {
            file_storage::download_file_content($file['name'], DIR_FS_TMP . $file['file']);
        }
    }

    function download_files($module_id, $files)
    {
        $cfg = modules::get_configuration($this->configuration(), $module_id);

        //download files to tmp folder

        $dropbox = new Dropbox\Dropbox($cfg['access_token']);

        foreach (explode(',', $files) as $filename) {
            $file = attachments::parse_filename($filename);
            $folder = $this->folder_prepare($file['folder']);
            $path = $folder . $file['file'];

            $dropbox->files->download($path, DIR_FS_TMP . $file['file']);
        }

        //create zip archive
        $zip = new ZipArchive();
        $zip_filename = "attachments-" . time() . ".zip";
        $zip_filepath = DIR_FS_TMP . $zip_filename;
        $zip->open($zip_filepath, ZipArchive::CREATE);

        foreach (explode(',', $files) as $filename) {
            $file = attachments::parse_filename($filename);
            $zip->addFile(DIR_FS_TMP . $filename, "/" . $file['name']);
        }

        $zip->close();

        //check if zip archive created
        if (!is_file($zip_filepath)) {
            exit("Error: cannot create zip archive in " . $zip_filepath);
        }

        //unlink downloaded files
        foreach (explode(',', $files) as $filename) {
            @unlink(DIR_FS_TMP . $filename);
        }

        //download archive
        file_storage::download_file_content($zip_filename, $zip_filepath);

        exit();
    }

    function delete($module_id, $files = [])
    {
        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $dropbox = new Dropbox\Dropbox($cfg['access_token']);

        foreach ($files as $filename) {
            $file = attachments::parse_filename($filename);
            $folder = $this->folder_prepare($file['folder']);
            $path = $folder . $file['file'];

            $resutl = $dropbox->files->delete_v2($path);
        }
    }
}