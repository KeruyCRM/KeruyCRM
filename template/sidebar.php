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
                    if (is_file(DIR_FS_UPLOADS . '/' . CFG_APP_LOGO)) {
                        if (is_image(DIR_FS_UPLOADS . '/' . CFG_APP_LOGO)) {
                            $html = '<img src="uploads/' . CFG_APP_LOGO . '" border="0" title="' . CFG_APP_NAME . '">';

                            if (strlen(CFG_APP_LOGO_URL) > 0) {
                                $html = '<div class="logo"><a href="' . CFG_APP_LOGO_URL . '" target="_new">' . $html . '</a></div>';
                            } else {
                                $html = '<div class="logo"><a href="' . url_for(
                                        'dashboard/'
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
                $sidebarMenu = build_main_menu();
                //print_rr($sidebarMenu);
                echo renderSidebarMenu($sidebarMenu)
                ?>

            </ul>
            <!-- END SIDEBAR MENU -->
        </div>
    </div>
</div>

