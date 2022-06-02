<?php

$msg = fields::check_before_delete(_GET('entities_id'), $_GET['id']);

if (strlen($msg) > 0) {
    $heading = TEXT_WARNING;
    $content = $msg;
    $button_title = 'hide-save-button';
} else {
    $heading = TEXT_HEADING_DELETE;
    $content = sprintf(
            TEXT_DEFAULT_DELETE_CONFIRMATION,
            fields::get_name_by_id($_GET['id'])
        ) . '<br><br><p class="alert alert-warning">' . TEXT_DELETE_FIELD_WARNING . '</p>';
    $button_title = TEXT_BUTTON_DELETE;
}