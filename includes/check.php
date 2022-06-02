<?php

$error_list = [];

//check reuired libs
$requried_php_extensions = [
    'gd',
    'mbstring',
    'xmlwriter',
    'curl',
    'zip',
    'xml',
    'fileinfo',
];

foreach ($requried_php_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $error_list[] = sprintf(TEXT_ERROR_LIB, strtoupper($ext));
    }
}

//check folder
$check_folders = [
    DIR_FS_UPLOADS,
    DIR_FS_ATTACHMENTS,
    DIR_FS_ATTACHMENTS_PREVIEW,
    DIR_FS_IMAGES,
    DIR_FS_USERS,
    DIR_FS_BACKUPS,
    DIR_FS_TMP,
    DIR_FS_CACHE,
    DIR_FS_CATALOG . 'log/'
];

foreach ($check_folders as $v) {
    if (is_dir($v)) {
        if (!is_writable($v)) {
            $error_list[] = sprintf('Error: folder "%s" is not writable!', str_replace(DIR_FS_CATALOG, '', $v));
        }
    } else {
        $error_list[] = sprintf('Error: folder "%s" does not exist', str_replace(DIR_FS_CATALOG, '', $v));
    }
}

//dispaly errors if exist  
if (count($error_list)) {
    foreach ($error_list as $v) {
        $alerts->add($v, 'error');
    }
}