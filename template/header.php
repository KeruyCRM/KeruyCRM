<div class="header navbar navbar-inverse navbar-fixed-top noprint">
    <!-- BEGIN TOP NAVIGATION BAR -->
    <div class="header-inner">

        <!-- BEGIN LOGO -->
        <a class="navbar-brand" href="<?php
        echo url_for('dashboard/') ?>">
            <?php
            echo CFG_APP_NAME ?>
            <?php
            echo maintenance_mode::header_message() ?>
        </a>
        <!-- END LOGO -->

        <?php
        if (\Helpers\App::is_ext_installed()) {
            echo global_search::render();
        }
        ?>

        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="navbar-toggle collapsed" data-toggle="collapse"
           data-target=".main-navbar-collapse" aria-expanded="false">
            <img src="template/img/menu-toggler.png" alt=""/>
        </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        <ul class="nav navbar-nav pull-right">

            <?php

            if (\Helpers\App::is_ext_installed()) {
                echo currencies::exchange_rate_widget();
            }

            if (app_session_is_registered('app_current_version') and strlen(
                    $app_current_version
                ) > 0 and $app_current_version > PROJECT_VERSION and $app_user['group_id'] == 0) {
                ?>
                <li class="dropdown" id="header_new_release_bar">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                       data-close-others="true"><i class="fa fa-warning"></i><span class="badge badge-warning">1</span></a>
                    <ul class="dropdown-menu extended tasks">
                        <li>
                            <p><?php
                                echo TEXT_NEW_PROJECT_VERSION ?></p>
                        </li>
                        <li>
                            <ul class="dropdown-menu-list scroller" style="height: 80px;">
                                <li>
                                    <a href="https://www.keruy.com.ua/new_release.php"
                                       target="_new"><?php
                                        echo sprintf(TEXT_NEW_PROJECT_VERSION_INFO, $app_current_version) ?></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            <?php
            } ?>

            <?php
            plugins::include_part('header_dropdown_menu') ?>

            <?php
            $hot_reports = new hot_reports();
            echo $hot_reports->render();

            echo favorites::render_header_nofitifcation();

            echo users_notifications::render();

            if (\Helpers\App::is_ext_installed()) {
                echo mail_accounts::render_dropdown_notification();
            }
            ?>

            <!-- BEGIN USER LOGIN DROPDOWN -->
            <li class="dropdown user">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                   data-close-others="true">

                    <?php
                    echo(is_file(DIR_FS_USERS . $app_user['photo']) ? image_tag(
                        DIR_WS_USERS . $app_user['photo'],
                        ['class' => 'user-photo-header']
                    ) : image_tag('images/' . 'no_photo.png', ['class' => 'user-photo-header'])) ?>
                    <span class="username">
                 <?php
                 echo $app_user['name'] ?>
        </span>

                    <?php
                    if (CFG_DISPLAY_USER_GROUP_IN_MENU == 1 and (!strlen(CFG_DISPLAY_USER_GROUP_ID_IN_MENU) or in_array(
                                $app_user['group_id'],
                                explode(',', CFG_DISPLAY_USER_GROUP_ID_IN_MENU)
                            ))) {
                        echo '<span class="username usergroup">(' . ($app_user['group_id'] == 0 ? TEXT_ADMINISTRATOR : $app_user['group_name']) . ')</span>';
                    }
                    ?>


                    <i class="fa fa-angle-down"></i>
                </a>

                <?php
                echo renderDropDownMenu(build_user_menu()) ?>

            </li>
            <!-- END USER LOGIN DROPDOWN -->

            <?php
            if ($app_previously_logged_user > 0) {
                echo '<li class="dropdown">' . link_to(
                        '<i class="fa fa-undo"></i>&nbsp;&nbsp;',
                        url_for('users/login_as', 'action=login_back&users_id=' . $app_previously_logged_user),
                        ['class' => 'dropdown-toggle', 'title' => TEXT_LOGIN_BACK_AS_ADMIN]
                    ) . '</li>';
            }
            ?>
        </ul>
        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END TOP NAVIGATION BAR -->
</div>

<script>
    function set_user_cfg(key, value) {
        switch (key) {
            case 'sidebar-option':
                if (value == 'fixed') value = 'page-sidebar-fixed'; else value = '';
                break;
            case 'sidebar-pos-option':
                if (value == 'right') value = 'page-sidebar-reversed'; else value = '';
                break;
            case 'page-scale-option':
                if (value == 'reduced') value = 'page-scale-reduced'; else value = '';
                break;
        }

        $.ajax({
            method: "POST",
            url: "<?php echo url_for('users/account', 'action=set_cfg')?>",
            data: {key: key, value: value}
        })
    }

</script>