<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="<?php
echo APP_LANGUAGE_SHORT_CODE ?>" dir="<?php
echo APP_LANGUAGE_TEXT_DIRECTION ?>">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <meta name="robots" content="noindex,nofollow">
    <title><?php
        echo $app_title ?></title>


    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="template/plugins/font-awesome/css/font-awesome.min.css?v=4.7.0" rel="stylesheet" type="text/css"/>
    <link href="template/plugins/line-awesome/css/line-awesome.min.css?v=1.3.0" rel="stylesheet" type="text/css"/>
    <link href="template/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>

    <?php
    require('js/mapbbcode-master/includes.css.php'); ?>

    <script src="template/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>

    <script type="text/javascript" src="js/main.js?v=<?php
    echo PROJECT_VERSION ?>"></script>

    <script type="text/javascript">
        var CKEDITOR = false;
        var CKEDITOR_holders = new Array();

        var app_cfg_first_day_of_week = <?php echo CFG_APP_FIRST_DAY_OF_WEEK ?>;
        var app_language_short_code = '<?php echo APP_LANGUAGE_SHORT_CODE ?>';
        var app_cfg_ckeditor_images = '<?php echo url_for("dashboard/ckeditor_image")?>';
        var app_language_text_direction = '<?php echo APP_LANGUAGE_TEXT_DIRECTION ?>'

    </script>

    <link rel="stylesheet" type="text/css" href="css/default.css?v=<?php
    echo PROJECT_VERSION ?>"/>


    <!-- END THEME STYLES -->
    <?php
    echo app_favicon() ?>
</head>
<!-- BEGIN BODY -->
<body>

<?php

//include module views  
if (is_file($path = $app_plugin_path . 'modules/' . $app_module . '/views/' . $app_action . '.php')) {
    require($path);
}
?>

</body>
<!-- END BODY -->
</html>