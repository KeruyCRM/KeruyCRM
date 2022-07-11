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
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title><?= \K::$fw->app_title ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, user-scalable=no" name="viewport"/>
    <meta content="" name="description"/>
    <?= \Helpers\App::app_author_text() ?>
    <meta name="MobileOptimized" content="320">

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="<?= \K::$fw->DOMAIN ?>template/plugins/font-awesome/css/font-awesome.min.css?v=4.7.0" rel="stylesheet" type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/plugins/line-awesome/css/line-awesome.min.css?v=1.3.0" rel="stylesheet" type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="<?= \K::$fw->DOMAIN ?>template/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.css" rel="stylesheet"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css" rel="stylesheet" type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-modal/css/bootstrap-modal.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-datepicker/css/datepicker.css"/>
    <link rel="stylesheet" type="text/css"
          href="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-datetimepicker-master/css/bootstrap-datetimepicker.css"/>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME STYLES -->
    <link href="<?= \K::$fw->DOMAIN ?>template/css/style-conquer.css?v=2" rel="stylesheet" type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/css/style.css?v=<?= \K::$fw->PROJECT_VERSION ?>" rel="stylesheet" type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/css/style-responsive.css?v=<?= \K::$fw->PROJECT_VERSION ?>" rel="stylesheet" type="text/css"/>
    <link href="<?= \K::$fw->DOMAIN ?>template/css/plugins.css?v=<?= \K::$fw->PROJECT_VERSION ?>" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="<?= \K::$fw->DOMAIN ?>js/izoColorPicker/1.0/izoColorPicker.css"/>
    <link rel="stylesheet" type="text/css" href="<?= \K::$fw->DOMAIN ?>js/izoAutocomplete/1.0/izoAutocomplete.css"/>
    <link href="<?= \K::$fw->DOMAIN ?>js/uploadifive/uploadifive.css" rel="stylesheet" media="screen">
    <link href="<?= \K::$fw->DOMAIN ?>js/chosen/chosen.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" type="text/css" href="<?= \K::$fw->DOMAIN ?>template/plugins/jquery-nestable/jquery.nestable.css"/>
    <link rel="stylesheet" type="text/css" media="screen"
          href="<?= \K::$fw->DOMAIN ?>template/plugins/ckeditor/4.16.2/plugins/codesnippet/lib/highlight/styles/default.css"/>
    <link rel="stylesheet" type="text/css" href="<?= \K::$fw->DOMAIN ?>js/DataTables-1.10.15/media/css/dataTables.bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="<?= \K::$fw->DOMAIN ?>js/select2/dist/css/select2.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?= \K::$fw->DOMAIN ?>js/multistep-indicator-master/css/style.css"/>
    <link rel="stylesheet" type="text/css" href="<?= \K::$fw->DOMAIN ?>js/JalaliCalendar/jquery.Bootstrap-PersianDateTimePicker.css"/>
    <link rel="stylesheet" type="text/css" href="<?= \K::$fw->DOMAIN ?>js/treetable-master/jquery-treetable.css">


    <?php
    require('js/mapbbcode-master/includes.css.php'); ?>

    <link href="<?= \K::$fw->DOMAIN ?>css/skins/<?= \K::$fw->app_skin . '?v=' . \K::$fw->PROJECT_VERSION ?>" rel="stylesheet" type="text/css"/>

    <script src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>

    <script src="<?= \K::$fw->DOMAIN ?>js/jquery.ui.touch-punch/jquery.ui.touch-punch.min.js" type="text/javascript"></script>

    <script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/validation/jquery.validate.min.js"></script>
    <script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/validation/additional-methods.min.js"></script>
    <?php
    require('js/validation/validator_messages.php'); ?>

    <!-- Add fancyBox -->
    <link rel="stylesheet" href="<?= \K::$fw->DOMAIN ?>js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen"/>
    <script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

    <script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/izoColorPicker/1.0/izoColorPicker.js"></script>

    <script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/main.js?v=<?= \K::$fw->PROJECT_VERSION ?>"></script>

    <script type="text/javascript">
        var CKEDITOR = false;
        var CKEDITOR_holders = new Array();

        var app_key_ctrl_pressed = false;
        $(window).keydown(function (evt) {
            if (evt.which == 17) {
                app_key_ctrl_pressed = true;
            }
        }).keyup(function (evt) {
            if (evt.which == 17) {
                app_key_ctrl_pressed = false;
            }
        });

        function keep_session() {
            $.ajax({url: '<?php echo \Helpers\Urls::url_for("main/dashboard/dashboard/keep_session") ?>'});
        }

        $(function () {
            setInterval("keep_session()", 600000);
        });

        var app_cfg_first_day_of_week = <?php echo \K::$fw->CFG_APP_FIRST_DAY_OF_WEEK ?>;
        var app_language_short_code = '<?php echo \K::$fw->APP_LANGUAGE_SHORT_CODE ?>';
        var app_cfg_ckeditor_images = '<?php echo \Helpers\Urls::url_for("main/dashboard/ckeditor_image")?>';
        var app_language_text_direction = '<?php echo \K::$fw->APP_LANGUAGE_TEXT_DIRECTION ?>'
        var app_user_saved_colors = '<?php echo \K::app_users_cfg()->get("my_saved_colors") ?>'
        var app_cfg_drop_down_menu_on_hover = <?php echo \K::$fw->CFG_DROP_DOWN_MENU_ON_HOVER ?>;

    </script>

    <?php
    \Tools\Plugins::include_part('layout_head') ?>

    <link rel="stylesheet" type="text/css" href="<?= \K::$fw->DOMAIN ?>css/default.css?v=<?= K::$fw->PROJECT_VERSION ?>"/>
    <?= \Helpers\App::app_include_custom_css() ?>

    <?php
    $sidebar_pos_option = \K::app_users_cfg()->get('sidebar-pos-option', '');

    if (\K::$fw->APP_LANGUAGE_TEXT_DIRECTION == 'rtl') {
        require(component_path('dashboard/direction_rtl'));
    }
    ?>

    <!-- END THEME STYLES -->
    <?= \Helpers\App::app_favicon() ?>

    <!-- Custom code at the end of </head> tag -->
    <?= \K::$fw->CFG_CUSTOM_HTML_HEAD ?>

</head>
<!-- BEGIN BODY -->
<body class="page-header-fixed <?php
echo \K::app_users_cfg()->get('sidebar-option', '') . ' ' . $sidebar_pos_option . ' ' . \K::app_users_cfg()->get(
        'page-scale-option',
        ''
    ) . ' ' . \K::app_users_cfg()->get(
        'sidebar-status'
    ) . ' page-user-' . \K::$fw->app_user['id'] . ' page-usergroup-' . \K::$fw->app_user['group_id'] ?>">

<!-- BEGIN HEADER -->
<?= \K::view()->render('header.php'); ?>
<!-- END HEADER -->

<div class="clearfix"></div>

<!-- BEGIN CONTAINER -->
<div class="page-container">

    <!-- BEGIN SIDEBAR -->
    <?= \K::view()->render('sidebar.php'); ?>
    <!-- END SIDEBAR -->

    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content-wrapper">
            <div class="page-content">
                <div id="ajax-modal" class="modal fade" tabindex="-1" data-replace="true" data-keyboard="false"
                     data-backdrop="static" data-focus-on=".autofocus"></div>
                <!-- BEGIN PAGE CONTENT-->
                <div class="row">
                    <div class="col-md-12">

                        <?php
                        //output users alerts

                        echo \Helpers\Html::getAlerts();
                        echo \Models\Main\Users\Users_alerts::output();

                        if (\K::fw()->exists('subTemplate')) {
                            echo \K::view()->render(\K::$fw->subTemplate);
                        }
                        ?>

                    </div>
                </div>
                <!-- END PAGE CONTENT-->
            </div>
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<!-- END CONTAINER -->

<!-- BEGIN FOOTER -->
<?= \K::view()->render('footer.php'); ?>
<!-- END FOOTER -->

<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/respond.min.js"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.js?v=2.2.2"
        type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="<?= \K::$fw->DOMAIN ?>template/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>template/plugins/ckeditor/4.16.2/ckeditor.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript"
        src="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-datetimepicker-master/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-modal/js/bootstrap-modalmanager.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-modal/js/bootstrap-modal.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>template/plugins/jquery-nestable/jquery.nestable.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>template/plugins/bootstrap-wizard/jquery.bootstrap.wizard.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/izoAutocomplete/1.0/izoAutocomplete.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/uploadifive/jquery.uploadifive.js?v=1.2.2"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/chosen/jquery-chosen-sortable.min.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/chosen/chosen-order/chosen.order.jquery.min.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/maskedinput/jquery.maskedinput.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/totop/jquery.ui.totop.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/jquery-number-master/jquery.number.min.js"></script>
<script type="text/javascript"
        src="<?= \K::$fw->DOMAIN ?>template/plugins/ckeditor/4.16.2/plugins/codesnippet/lib/highlight/highlight.pack.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/jquery-resizable/jquery-resizable.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/select2/dist/js/select2.full.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/jquery.taboverride-master/build/taboverride.min.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/jquery.taboverride-master/build/jquery.taboverride.min.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/JalaliCalendar/jalaali.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/JalaliCalendar/jquery.Bootstrap-PersianDateTimePicker.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/inputmask/5.0.5/jquery.inputmask.min.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/scannerdetection/1.1.2/jquery.scannerdetection.js"></script>
<script type="text/javascript" src="<?= \K::$fw->DOMAIN ?>js/treetable-master/jquery-treetable.js"></script>


<!-- END PAGE LEVEL PLUGINS -->

<?php
if (\K::$fw->app_module_path == 'items/info') {
    require(component_path('dashboard/data_tables'));
} ?>

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

<?php
\Tools\Plugins::include_part('layout_bottom') ?>

<script>
    jQuery(document).ready(function () {

        $.fn.datepicker.dates['en'] = {
            days: [<?= \K::$fw->TEXT_DATEPICKER_DAYS ?>],
            daysShort: [<?= \K::$fw->TEXT_DATEPICKER_DAYSSHORT ?>],
            daysMin: [<?= \K::$fw->TEXT_DATEPICKER_DAYSMIN ?>],
            months: [<?= \K::$fw->TEXT_DATEPICKER_MONTHS ?>],
            monthsShort: [<?= \K::$fw->TEXT_DATEPICKER_MONTHSSHORT ?>],
            today: "<?= \K::$fw->TEXT_DATEPICKER_TODAY ?>",
            clear: "<?= \K::$fw->TEXT_CLEAR ?>",
        };

        $.fn.datetimepicker.dates['en'] = {
            days: [<?= \K::$fw->TEXT_DATEPICKER_DAYS ?>],
            daysShort: [<?= \K::$fw->TEXT_DATEPICKER_DAYSSHORT ?>],
            daysMin: [<?= \K::$fw->TEXT_DATEPICKER_DAYSMIN ?>],
            months: [<?= \K::$fw->TEXT_DATEPICKER_MONTHS ?>],
            monthsShort: [<?= \K::$fw->TEXT_DATEPICKER_MONTHSSHORT ?>],
            meridiem: ["am", "pm"],
            suffix: ["st", "nd", "rd", "th"],
            today: "<?= \K::$fw->TEXT_DATEPICKER_TODAY ?>",
            clear: "<?= \K::$fw->TEXT_CLEAR ?>",
        };

        App.init();

        keruycrm_app_init();

        <?php if (strlen(
            \K::$fw->app_current_version
        ) == 0 and \K::$fw->CFG_DISABLE_CHECK_FOR_UPDATES == 0) echo "$.ajax({url: '" . \Helpers\Urls::url_for(
            "main/dashboard/check_project_version"
        ) . "'});" ?>
    });
</script>

<?= \Helpers\App::i18n_js() ?>

<!-- END JAVASCRIPTS -->

<!-- Custom code before </body> tag -->
<?= \K::$fw->CFG_CUSTOM_HTML_BODY ?>

</body>
<!-- END BODY -->
</html>