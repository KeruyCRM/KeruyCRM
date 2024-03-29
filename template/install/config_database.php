<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<h3 class="page-title"><?php
    echo $TEXT_DATABASE_CONFIGURATION ?></h3>

<?php
if ($error) {
    echo '<div class="alert alert-danger">' . $error . '</div>';
} ?>

<form name="db_config" id="db_config" action="<?php
echo $nextAction ?>" method="post" class="form-horizontal">

    <div class="form-group">
        <label class="col-md-3 control-label" for="db_host"><?php
            echo $TEXT_DATABASE_HOST ?></label>
        <div class="col-md-9">
            <input type="text" name="db_host" id="db_host" value="<?php
            echo $params['db_host'] ?>" class="form-control input-medium required">
            <span class="help-block"><?php
                echo $TEXT_DATABASE_HOST_INFO ?></span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="db_port"><?php
            echo $TEXT_DATABASE_PORT ?></label>
        <div class="col-md-9">
            <input type="text" name="db_port" id="db_port" value="<?php
            echo $params['db_port'] ?>" class="form-control input-medium">
            <span class="help-block"><?php
                echo $TEXT_DATABASE_PORT_INFO ?></span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="db_name"><?php
            echo $TEXT_DATABASE_NAME ?></label>
        <div class="col-md-9">
            <input type="text" name="db_name" id="db_name" value="<?php
            echo $params['db_name'] ?>" class="form-control input-medium required">
            <span class="help-block"><?php
                echo $TEXT_DATABASE_NAME_INFO ?></span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="db_username"><?php
            echo $TEXT_DB_USERNAME ?></label>
        <div class="col-md-9">
            <input type="text" name="db_username" id="db_username" value="<?php
            echo $params['db_username'] ?>" class="form-control input-medium required">
            <span class="help-block"><?php
                echo $TEXT_DB_USERNAME_INFO ?></span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="db_password"><?php
            echo $TEXT_DB_PASSWORD ?></label>
        <div class="col-md-9">
            <input type="text" name="db_password" id="db_password" value="<?php
            echo $params['db_password'] ?>" class="form-control input-medium">
            <span class="help-block"><?php
                echo $TEXT_DB_PASSWORD_INFO ?></span>
        </div>
    </div>


    <div><input type="submit" value="<?php
        echo $TEXT_BUTTON_INSTALL_DATABASE ?>" class="btn btn-primary"></div>

</form>

<script>
    $('#db_config').validate();
</script>