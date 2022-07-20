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
    <link href="<?= \K::$fw->DOMAIN ?>template/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.css" rel="stylesheet"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css"
          rel="stylesheet"
          type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-modal/css/bootstrap-modal.css" rel="stylesheet"
          type="text/css"/>
    <link rel="stylesheet" type="text/css"
          href="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-datepicker/css/datepicker.css"/>
    <link rel="stylesheet" type="text/css"
          href="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-datetimepicker-master/css/bootstrap-datetimepicker.css"/>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME STYLES -->
    <link href="<?= \K::$fw->DOMAIN ?>template/css/style-conquer.css" rel="stylesheet" type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/css/style-responsive.css" rel="stylesheet" type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>js/uploadifive/uploadifive.css" rel="stylesheet" media="screen">
    <link href="<?= \K::$fw->DOMAIN ?>js/chosen/chosen.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" type="text/css"
          href="<?= \K::$fw->DOMAIN ?>template/plugins/jquery-nestable/jquery.nestable.css"/>
    <link rel="stylesheet" type="text/css" href="<?= \K::$fw->DOMAIN ?>js/select2/dist/css/select2.min.css"/>

    <?php
    require('js/mapbbcode-master/includes.css.php'); ?>

    <link href="css/skins/<?= \K::$fw->app_skin ?>" rel="stylesheet" type="text/css"/>

    <script src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>

    <script src="<?= \K::$fw->DOMAIN ?>js/jquery.ui.touch-punch/jquery.ui.touch-punch.min.js"
            type="text/javascript"></script>

    <script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/validation/jquery.validate.min.js"></script>
    <script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/validation/additional-methods.min.js"></script>
    <?php
    require('js/validation/validator_messages.php'); ?>

    <!--izoAutocomplete-->
    <link rel="stylesheet" type="text/css" href="<?= \K::$fw->DOMAIN ?>js/izoAutocomplete/1.0/izoAutocomplete.css"/>
    <script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/izoAutocomplete/1.0/izoAutocomplete.js"></script>

    <!-- Add fancyBox -->
    <link rel="stylesheet" href="<?= \K::$fw->DOMAIN ?>js/fancybox/source/jquery.fancybox.css" type="text/css"
          media="screen"/>
    <script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/fancybox/source/jquery.fancybox.pack.js"></script>

    <script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/main.js?v=<?= \K::$fw->PROJECT_VERSION ?>"></script>

    <script type="text/javascript">
        var CKEDITOR = false;
        var CKEDITOR_holders = new Array();

        var app_cfg_first_day_of_week = <?php echo \K::$fw->CFG_APP_FIRST_DAY_OF_WEEK ?>;
        var app_language_short_code = '<?php echo \K::$fw->APP_LANGUAGE_SHORT_CODE ?>';
        var app_cfg_ckeditor_images = '<?php echo \Helpers\Urls::url_for("main/dashboard/ckeditor_image")?>';
        var app_language_text_direction = '<?php echo \K::$fw->APP_LANGUAGE_TEXT_DIRECTION ?>'
        var app_cfg_drop_down_menu_on_hover = <?php echo \K::$fw->CFG_DROP_DOWN_MENU_ON_HOVER ?>;

        function keep_session() {
            $.ajax({url: '<?php echo \Helpers\Urls::url_for("main/dashboard/keep_session") ?>'});
        }

        $(function () {
            setInterval("keep_session()", 600000);
        });

    </script>

    <link rel="stylesheet" type="text/css"
          href="<?= \K::$fw->DOMAIN ?>css/default.css?v=<?= \K::$fw->PROJECT_VERSION ?>"/>
    <?= \Helpers\App::app_include_custom_css() ?>

    <?php
    echo \Helpers\App::render_login_page_background() ?>
    <?= \Helpers\App_recaptcha::render_js() ?>

    <script>
        if (isIframe()) {
            document.write('<link href="css/iframe.css" rel="stylesheet" type="text/css" />');
        }
    </script>

    <!-- END THEME STYLES -->
    <?= \Helpers\App::app_favicon() ?>
</head>
<!-- BEGIN BODY -->
<body class="login public-layout page-scale-reduced">

<div class="login-fade-in"></div>

<!-- BEGIN LOGO -->
<div class="login-page-logo">

    <?php
    if (is_file(\K::$fw->DIR_FS_UPLOADS . '/' . \K::$fw->CFG_APP_LOGO)) {
        if (\Helpers\App::is_image(\K::$fw->DIR_FS_UPLOADS . '/' . \K::$fw->CFG_APP_LOGO)) {
            $html = '<img src="uploads/' . \K::$fw->CFG_APP_LOGO . '" border="0" title="' . CFG_APP_NAME . '">';

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
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js"
        type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.js"
        type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery-slimscroll/jquery.slimscroll.min.js"
        type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>template/plugins/ckeditor/4.16.2/ckeditor.js"></script>
<script type="text/javascript"
        src="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript"
        src="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-datetimepicker-master/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript"
        src="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-modal/js/bootstrap-modalmanager.js"></script>
<script type="text/javascript"
        src="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-modal/js/bootstrap-modal.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery-nestable/jquery.nestable.js"></script>
<script type="text/javascript"
        src="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-wizard/jquery.bootstrap.wizard.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/uploadifive/jquery.uploadifive.min.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/chosen/chosen.jquery.min.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/chosen/jquery-chosen-sortable.min.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/maskedinput/jquery.maskedinput.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/totop/jquery.ui.totop.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/jquery-number-master/jquery.number.min.js"></script>
<script type="text/javascript"
        src="<?= \K::$fw->DOMAIN ?>template/plugins/ckeditor/4.16.2/plugins/codesnippet/lib/highlight/highlight.pack.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/select2/dist/js/select2.full.js"></script>
<script type="text/javascript"
        src="<?= \K::$fw->DOMAIN ?>js/jquery.taboverride-master/build/taboverride.min.js"></script>
<script type="text/javascript"
        src="<?= \K::$fw->DOMAIN ?>js/jquery.taboverride-master/build/jquery.taboverride.min.js"></script>
<script type="text/javascript"
        src="<?= \K::$fw->DOMAIN ?>js/scannerdetection/1.1.2/jquery.scannerdetection.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/inputmask/5.0.5/jquery.inputmask.min.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/izoColorPicker/1.0/izoColorPicker.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/treetable-master/jquery-treetable.js"></script>
<!-- END PAGE LEVEL PLUGINS -->

<?php
require('js/mapbbcode-master/includes.js.php'); ?>

<?php
if (\Helpers\App::is_ext_installed()) {
    echo smart_input::render_js_includes();
}
?>

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?= \K::$fw->DOMAIN ?>template/scripts/app.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->

<script>
    jQuery(document).ready(function () {

        $.fn.datepicker.dates['en'] = {
            days: [<?php echo \K::$fw->TEXT_DATEPICKER_DAYS ?>],
            daysShort: [<?php echo \K::$fw->TEXT_DATEPICKER_DAYSSHORT ?>],
            daysMin: [<?php echo \K::$fw->TEXT_DATEPICKER_DAYSMIN ?>],
            months: [<?php echo \K::$fw->TEXT_DATEPICKER_MONTHS ?>],
            monthsShort: [<?php echo \K::$fw->TEXT_DATEPICKER_MONTHSSHORT ?>],
            today: "<?php echo \K::$fw->TEXT_DATEPICKER_TODAY ?>"
        };

        $.fn.datetimepicker.dates['en'] = {
            days: [<?php echo \K::$fw->TEXT_DATEPICKER_DAYS ?>],
            daysShort: [<?php echo \K::$fw->TEXT_DATEPICKER_DAYSSHORT ?>],
            daysMin: [<?php echo \K::$fw->TEXT_DATEPICKER_DAYSMIN ?>],
            months: [<?php echo \K::$fw->TEXT_DATEPICKER_MONTHS ?>],
            monthsShort: [<?php echo \K::$fw->TEXT_DATEPICKER_MONTHSSHORT ?>],
            meridiem: ["am", "pm"],
            suffix: ["st", "nd", "rd", "th"],
            today: "<?php echo \K::$fw->TEXT_DATEPICKER_TODAY ?>"
        };

        App.init();

        keruycrm_app_init();

        appHandleUniform()

    });
</script>

<?= \Helpers\App::i18n_js() ?>

</body>
<!-- END BODY -->
</html>