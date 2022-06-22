<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
    <h3 class="page-title"><?php
        echo $TEXT_CHECKING_ENVIRONMENT ?></h3>

<?php

if (count($error_list)) {
    foreach ($error_list as $v) {
        echo '<div class="alert alert-danger">' . $v . '</div>';
    }

    echo '<br><p>' . $TEXT_CHECK_ERRORS_ABOVE . '</p>';
    echo '<p><input type="button" value="' . $TEXT_BUTTON_CHECK_ENVIRONMENT . '" class="btn btn-default"
                onClick="location.href=\'' . $prevAction . '\'"></p>';
} else {
    echo '<p>' . $TEXT_CHECKING_ENVIRONMENT_SUCCESS . '</p>';
    echo '<p><input type="button" value="' . $TEXT_BUTTON_DATABASE_CONFIG . '" class="btn btn-primary"
                onClick="location.href=\'' . $nextAction . '\'"></p>';
}
?>