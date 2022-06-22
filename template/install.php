<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title><?php
        echo $app_title ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <meta name="MobileOptimized" content="320">

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="<?= $DOMAIN ?>template/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?= $DOMAIN ?>template/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= $DOMAIN ?>template/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="<?= $DOMAIN ?>template/plugins/select2/select2_conquer.css"/>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME STYLES -->
    <link href="<?= $DOMAIN ?>template/css/style-conquer.css" rel="stylesheet" type="text/css"/>
    <link href="<?= $DOMAIN ?>template/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="<?= $DOMAIN ?>template/css/style-responsive.css" rel="stylesheet" type="text/css"/>
    <link href="<?= $DOMAIN ?>template/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="<?= $DOMAIN ?>css/skins/default/default.css" rel="stylesheet" type="text/css"/>

    <style>
        .login .content {
            width: auto;
            max-width: 750px;
        }
    </style>

    <script src="<?= $DOMAIN ?>template/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>

    <script type="text/javascript" src="<?= $DOMAIN ?>js/validation/jquery.validate.min.js"></script>
    <script type="text/javascript" src="<?= $DOMAIN ?>js/validation/additional-methods.min.js"></script>

    <script type="text/javascript" src="<?= $DOMAIN ?>js/main.js"></script>

    <link rel="stylesheet" type="text/css" href="<?= $DOMAIN ?>css/default.css"/>

    <script type="text/javascript">
        $.extend($.validator.messages, {
            required: '<?php echo $TEXT_FIELD_IS_REQUIRED ?>',
            email: '<?php echo $TEXT_FIELD_IS_REQUIRED_EMAIL ?>'
        });
    </script>


    <!-- END THEME STYLES -->
    <link rel="shortcut icon" href="<?= $DOMAIN ?>favicon.ico"/>
</head>
<!-- BEGIN BODY -->
<body class="login">
<!-- BEGIN LOGO -->
<div class="login-page-logo"><?php
    echo $app_title ?></div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">

    <?= \View::instance()->render($subTemplate); ?>

</div>
<!-- END LOGIN -->
<!-- BEGIN COPYRIGHT -->
<div class="copyright">
    <a href="https://www.keruy.com.ua" target="_blank">KeruyCRM <?php
        echo $PROJECT_VERSION ?></a><br>
    Copyright &copy; <?php
    echo date('Y') ?> <a target="_blank" href="https://www.keruy.com.ua">www.keruy.com.ua</a>
</div>
<!-- END COPYRIGHT -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="<?= $DOMAIN ?>template/plugins/respond.min.js"></script>
<script src="<?= $DOMAIN ?>template/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="<?= $DOMAIN ?>template/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="<?= $DOMAIN ?>template/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?= $DOMAIN ?>template/plugins/bootstrap-hover-dropdown/twitter-bootstrap-hover-dropdown.min.js"
        type="text/javascript"></script>
<script src="<?= $DOMAIN ?>template/plugins/jquery-slimscroll/jquery.slimscroll.min.js"
        type="text/javascript"></script>
<script src="<?= $DOMAIN ?>template/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?= $DOMAIN ?>template/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="<?= $DOMAIN ?>template/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?= $DOMAIN ?>template/plugins/select2/select2.min.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?= $DOMAIN ?>template/scripts/app.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->

<script>
    jQuery(document).ready(function () {
        App.init();
    });
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>