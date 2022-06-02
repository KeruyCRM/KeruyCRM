<?php

require('plugins/ext/file_storage_modules/yandex_disk/lib/yandex-master/autoload.php');

use Yandex\Disk\DiskClient;

class yandex_disk
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_YANDEX_DISK_TITLE;
        $this->site = 'https://disk.yandex.ru';
        $this->api = 'https://github.com/nixsolutions/yandex-php-library/wiki/Yandex-Disk';
        $this->version = '1.0';
        $this->country = 'RU';
    }

    public function configuration()
    {
        $cfg = [];


        $cfg[] = [
            'key' => 'access_token',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_YANDEX_DISK_ACCESS_TOKEN,
            'description' => TEXT_MODULE_YANDEX_DISK_ACCESS_TOKEN_INFO,
            'params' => ['class' => 'form-control input-large required'],
        ];


        return $cfg;
    }

    function folder_prepare($folder)
    {
        $folder = explode('/', $folder);
        return $folder[0] . '-' . $folder[1];
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

        //try to connect and prepare folder
        try {
            $disk = new DiskClient();

            $disk->setAccessToken($cfg['access_token']);

            $disk->createDirectory($folder);
        } catch (Exception $e) {
            if ($e->getCode() != '405') {
                modules::log_file_storage($this->title . ': ' . $e->getMessage(), $file);

                die($this->title . ': ' . $e->getMessage());
            }
        }

        //try upload file
        try {
            $disk->uploadFile(
                $folder . '/',
                [
                    'path' => $file['file_path'],
                    'size' => filesize($file['file_path']),
                    'name' => $file['file']
                ]
            );

            //if success remove file and remove queue

            unlink($file['file_path']);

            file_storage::remove_from_queue($queue_info['id']);
        } catch (Exception $e) {
            file_storage::remove_from_queue($queue_info['id']);

            modules::log_file_storage($this->title . ': ' . $e->getMessage(), $file);

            die($this->title . ': ' . $e->getMessage());
        }
    }

    function download($module_id, $filename)
    {
        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $file = attachments::parse_filename($filename);

        //prepare folder Y-m
        $folder = $this->folder_prepare($file['folder']);

        try {
            $disk = new DiskClient();

            $disk->setAccessToken($cfg['access_token']);

            $path = $folder . '/' . $file['file'];
            $destination = DIR_FS_TMP;
            $name = $file['file'];
            if ($disk->downloadFile($path, $destination, $name)) {
                file_storage::download_file_content($file['name'], $destination . $name);
            }
        } catch (Exception $e) {
            //Do nothing. Try to download file from current server if exist
            modules::log_file_storage($this->title . ': ' . $e->getMessage(), $file);
        }
    }

    function download_files($module_id, $files)
    {
        $cfg = modules::get_configuration($this->configuration(), $module_id);

        //download files to tmp folder
        try {
            $disk = new DiskClient();

            $disk->setAccessToken($cfg['access_token']);

            foreach (explode(',', $files) as $filename) {
                $file = attachments::parse_filename($filename);
                $folder = $this->folder_prepare($file['folder']);
                $path = $folder . '/' . $file['file'];
                $destination = DIR_FS_TMP;
                $name = $file['file'];
                $disk->downloadFile($path, $destination, $name);
            }
        } catch (Exception $e) {
            modules::log_file_storage($this->title . ': ' . $e->getMessage(), $file);
            die($this->title . ': ' . $e->getMessage() . ' ' . $file);
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

        $disk = new DiskClient();

        $disk->setAccessToken($cfg['access_token']);

        //print_r($files);
        //exit();

        foreach ($files as $filename) {
            $file = attachments::parse_filename($filename);
            $folder = $this->folder_prepare($file['folder']);
            $path = $folder . '/' . $file['file'];

            try {
                $disk->delete($path);
            } catch (Exception $e) {
                //do nothing if can't delete
                //echo $e->getMessage() . ' ' . $path;
            }
        }
    }
}