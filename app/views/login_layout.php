<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="<?= \K::$fw->APP_LANGUAGE_SHORT_CODE ?>" dir="<?= \K::$fw->APP_LANGUAGE_TEXT_DIRECTION ?>" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <meta name="robots" content="noindex,nofollow">
    <title><?= \K::$fw->app_title ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, user-scalable=no" name="viewport"/>
    <meta content="" name="description"/>
    <?= \Helpers\App::app_author_text() ?>
    <meta name="MobileOptimized" content="320">
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="<?= \K::$fw->DOMAIN ?>template/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/plugins/line-awesome/css/line-awesome.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/plugins/uniform/css/uniform.default.css" rel="stylesheet"
          type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="<?= \K::$fw->DOMAIN ?>template/plugins/select2/select2_conquer.css"/>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME STYLES -->
    <link href="<?= \K::$fw->DOMAIN ?>template/css/style-conquer.css" rel="stylesheet" type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/css/style-responsive.css" rel="stylesheet" type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>css/skins/<?= \K::$fw->app_skin ?>" rel="stylesheet" type="text/css"/>

    <script src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>

    <script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/validation/jquery.validate.min.js"></script>
    <script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/validation/additional-methods.min.js"></script>
    <?php
    require('js/validation/validator_messages.php'); ?>

    <script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/main.js"></script>

    <script>
        var app_cfg_drop_down_menu_on_hover = <?php echo \K::$fw->CFG_DROP_DOWN_MENU_ON_HOVER ?>;

        function keep_session() {
            $.ajax({url: '<?php echo \K::$fw->DOMAIN . \Helpers\Urls::url_for("main/dashboard/keep_session") ?>'});
        }

        $(function () {
            setInterval("keep_session()", 600000);
        });
    </script>

    <link rel="stylesheet" type="text/css" href="<?= \K::$fw->DOMAIN ?>css/default.css"/>
    <?= \Helpers\App::app_include_custom_css() ?>

    <?= \Helpers\App::render_login_page_background() ?>
    <?= \Helpers\App_recaptcha::render_js() ?>

    <!-- END THEME STYLES -->
    <?= \Helpers\App::app_favicon() ?>
</head>
<!-- BEGIN BODY -->
<body class="login">

<div class="login-fade-in"></div>

<!-- BEGIN LOGO -->
<div class="login-page-logo">

    <?php
    if (is_file(\K::$fw->DIR_FS_UPLOADS . '/' . \K::$fw->CFG_APP_LOGO)) {
        if (\Helpers\App::is_image(\K::$fw->DIR_FS_UPLOADS . '/' . \K::$fw->CFG_APP_LOGO)) {
            $html = '<img src="uploads/' . \K::$fw->CFG_APP_LOGO . '" border="0" title="' . \K::$fw->CFG_APP_NAME . '">';

            if (strlen(\K::$fw->CFG_APP_LOGO_URL) > 0) {
                $html = '<a href="' . \K::$fw->CFG_APP_LOGO_URL . '" target="_new">' . $html . '</a>';
            }

            echo $html;
        }
    } else {
        echo \K::$fw->CFG_APP_NAME;
    }
    ?>

</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content <?= 'content-' . \K::$fw->app_action ?>">

    <?php
    //output alerts if they exist.
    echo \Helpers\Html::getAlerts();

    if (\K::fw()->exists('subTemplate')) {
        echo \K::view()->render(\K::$fw->subTemplate);
    }

    ?>

</div>
<!-- END LOGIN -->


<!-- BEGIN COPYRIGHT -->
<div class="copyright">
    <?= (strlen(\K::$fw->CFG_APP_COPYRIGHT_NAME) > 0 ? '&copy; ' . \K::$fw->CFG_APP_COPYRIGHT_NAME . ' ' . date(
            'Y'
        ) . '<br>' : '') ?>
    <?= \Helpers\App::app_powered_by_text() ?>
</div>
<!-- END COPYRIGHT -->

<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/respond.min.js"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.js"
        type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery-slimscroll/jquery.slimscroll.min.js"
        type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/maskedinput/jquery.maskedinput.js"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>template/plugins/select2/select2.min.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?= \K::$fw->DOMAIN ?>template/scripts/app.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->

<?php
if (\Helpers\App::is_ext_installed()) {
    echo smart_input::render_js_includes();
}
?>

<script>
    jQuery(document).ready(function () {
        App.init();
    });
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>