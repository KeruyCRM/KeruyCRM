<h3 class="page-title"><?php
    echo TEXT_CHECKING_ENVIRONMENT ?></h3>

<?php
$error_list = [];

if (!version_compare(phpversion(), '7.2', '>=')) {
    $error_list[] = sprintf(TEXT_ERROR_PHP_VERSION, phpversion());
}

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

$check_folders = [
    '../backups',
    '../log',
    '../uploads',
    '../uploads/attachments',
    '../uploads/users',
    '../uploads/images',
    '../cache',
    '../config'
];

foreach ($check_folders as $v) {
    if (is_dir($v)) {
        if (!is_writable($v)) {
            $error_list[] = sprintf(TEXT_ERRRO_FOLDER_NOT_WRITABLE, str_replace('..', '', $v));
        }
    } else {
        $error_list[] = sprintf(TEXT_ERRRO_FOLDER_NOT_EXIST, str_replace('..', '', $v));
    }
}

if (count($error_list)) {
    foreach ($error_list as $v) {
        echo '<div class="alert alert-danger">' . $v . '</div>';
    }

    echo '<br><p>' . TEXT_CHECK_ERROS_ABOVE . '</p>';

    echo '<p><input type="button" value="' . TEXT_BUTTON_CHECK_ENVIRONMENT . '"  class="btn btn-default"  onClick="location.href=\'index.php?step=checking_environment&lng=' . $_GET['lng'] . '\'"></p>';
} else {
    echo '<p>' . TEXT_CHECKING_ENVIRONMENT_SUCCESS . '</p>';

    echo '<p><input type="button" value="' . TEXT_BUTTON_DATABASE_CONFIG . '"  class="btn btn-primary" onClick="location.href=\'index.php?step=database_config&lng=' . $_GET['lng'] . '\'"></p>';
}