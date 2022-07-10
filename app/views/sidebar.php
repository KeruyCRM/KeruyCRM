<div class="page-sidebar-wrapper noprint">
    <div class="page-sidebar-wrapper">
        <div class="page-sidebar main-navbar-collapse collapse">
            <!-- BEGIN SIDEBAR MENU -->
            <ul class="page-sidebar-menu">
                <li class="sidebar-toggler-wrapper">

                    <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                    <div class="sidebar-toggler"></div>
                    <!-- BEGIN SIDEBAR TOGGLER BUTTON -->

                    <?php
                    if (is_file(\K::$fw->DIR_FS_UPLOADS . '/' . \K::$fw->CFG_APP_LOGO)) {
                        if (\Helpers\App::is_image(\K::$fw->DIR_FS_UPLOADS . '/' . \K::$fw->CFG_APP_LOGO)) {
                            $html = '<img src="uploads/' . \K::$fw->CFG_APP_LOGO . '" border="0" title="' . \K::$fw->CFG_APP_NAME . '">';

                            if (strlen(\K::$fw->CFG_APP_LOGO_URL) > 0) {
                                $html = '<div class="logo"><a href="' . \K::$fw->CFG_APP_LOGO_URL . '" target="_new">' . $html . '</a></div>';
                            } else {
                                $html = '<div class="logo"><a href="' . \Helpers\Urls::url_for(
                                        'main/dashboard'
                                    ) . '">' . $html . '</a></div>';
                            }

                            echo $html;
                        }
                    }
                    ?>

                    <div class="clearfix"></div>


                </li>
                <li>

                    <?php
                    if (\Helpers\App::is_ext_installed()) {
                        echo global_search::render('search-form-sidebar');
                    }
                    ?>
                </li>

                <?php
                /*$sidebarMenu = build_main_menu();
                //print_rr($sidebarMenu);
                echo renderSidebarMenu($sidebarMenu)*/
                ?>

            </ul>
            <!-- END SIDEBAR MENU -->
        </div>
    </div>
</div>

