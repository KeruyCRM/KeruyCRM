<div class="header navbar navbar-inverse navbar-fixed-top noprint">
    <!-- BEGIN TOP NAVIGATION BAR -->
    <div class="header-inner">

        <!-- BEGIN LOGO -->
        <a class="navbar-brand" href="<?= \Helpers\Urls::url_for('main/main/dashboard') ?>">
            <?= \K::$fw->CFG_APP_NAME ?>
            <?= \Tools\Maintenance_mode::header_message() ?>
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
            <img src="<?= \K::$fw->DOMAIN ?>template/img/menu-toggler.png" alt=""/>
        </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        <ul class="nav navbar-nav pull-right">

            <?php

            if (\Helpers\App::is_ext_installed()) {
                echo currencies::exchange_rate_widget();
            }

            if (\K::app_session_is_registered('app_current_version') and strlen(
                    \K::$fw->app_current_version
                ) > 0 and \K::$fw->app_current_version > \K::$fw->PROJECT_VERSION and \K::$fw->app_user['group_id'] == 0) {
                ?>
                <li class="dropdown" id="header_new_release_bar">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                       data-close-others="true"><i class="fa fa-warning"></i><span class="badge badge-warning">1</span></a>
                    <ul class="dropdown-menu extended tasks">
                        <li>
                            <p><?= \K::$fw->TEXT_NEW_PROJECT_VERSION ?></p>
                        </li>
                        <li>
                            <ul class="dropdown-menu-list scroller" style="height: 80px;">
                                <li>
                                    <a href="https://www.keruy.com.ua/new_release.php"
                                       target="_new"><?= sprintf( \K::$fw->TEXT_NEW_PROJECT_VERSION_INFO,  \K::$fw->app_current_version) ?></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <?php
            } ?>

            <?php
            \Tools\Plugins::include_part('header_dropdown_menu') ?>

            <?php
            $hot_reports = new \Models\Main\Reports\Hot_reports();
            echo $hot_reports->render();

            echo \Models\Main\Items\Favorites::render_header_nofitifcation();

            echo \Models\Main\Users\Users_notifications::render();

            if (\Helpers\App::is_ext_installed()) {
                echo mail_accounts::render_dropdown_notification();
            }
            ?>

            <!-- BEGIN USER LOGIN DROPDOWN -->
            <li class="dropdown user">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                   data-close-others="true">

                    <?php
                    echo(is_file( \K::$fw->DIR_FS_USERS .  \K::$fw->app_user['photo']) ? \Helpers\Html::image_tag(
                        \K::$fw->DIR_WS_USERS .  \K::$fw->app_user['photo'],
                        ['class' => 'user-photo-header']
                    ) : \Helpers\Html::image_tag('images/' . 'no_photo.png', ['class' => 'user-photo-header'])) ?>
                    <span class="username">
                 <?php
                 echo  \K::$fw->app_user['name'] ?>
        </span>

                    <?php
                    if ( \K::$fw->CFG_DISPLAY_USER_GROUP_IN_MENU == 1 and (!strlen( \K::$fw->CFG_DISPLAY_USER_GROUP_ID_IN_MENU) or in_array(
                                \K::$fw->app_user['group_id'],
                                explode(',',  \K::$fw->CFG_DISPLAY_USER_GROUP_ID_IN_MENU)
                            ))) {
                        echo '<span class="username usergroup">(' . ( \K::$fw->app_user['group_id'] == 0 ?  \K::$fw->TEXT_ADMINISTRATOR :  \K::$fw->app_user['group_name']) . ')</span>';
                    }
                    ?>


                    <i class="fa fa-angle-down"></i>
                </a>

                <?= ''//\Helpers\Menu::renderDropDownMenu(\Helpers\Menu::build_user_menu()) ?>

            </li>
            <!-- END USER LOGIN DROPDOWN -->

            <?php
            if ( \K::$fw->app_previously_logged_user > 0) {
                echo '<li class="dropdown">' . \Helpers\Urls::link_to(
                        '<i class="fa fa-undo"></i>&nbsp;&nbsp;',
                        \Helpers\Urls::url_for('main/users/login_as/login_back', 'users_id=' . \K::$fw->app_previously_logged_user),
                        ['class' => 'dropdown-toggle', 'title' =>  \K::$fw->TEXT_LOGIN_BACK_AS_ADMIN]
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
            url: "<?= \Helpers\Urls::url_for('main/users/account/set_cfg')?>",
            data: {key: key, value: value}
        })
    }
</script>