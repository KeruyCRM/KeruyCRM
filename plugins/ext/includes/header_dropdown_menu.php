<?php

if (defined('CFG_PLUGIN_EXT_INSTALLED')) {
    $events = calendar::get_events(date('Y-m-d'), date('Y-m-d'), 'personal');

    if (($events_count = count($events)) > 0) {
        ?>
        <li class="dropdown" id="header_new_release_bar">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                <i class="fa fa-calendar"></i>
                <span class="badge badge-info"><?php
                    echo $events_count ?></span>
            </a>
            <ul class="dropdown-menu extended tasks">
                <li>
                    <p>
                        <?php
                        echo TEXT_EXT_EVENTS_ON_TODAY ?>
                    </p>
                </li>
                <li>
                    <ul class="dropdown-menu-list scroller" style="height: <?php
                    echo(42 * $events_count) ?>px;">
                        <?php
                        foreach ($events as $event): ?>
                            <li>
                                <a href="<?php
                                echo url_for('ext/calendar/personal') ?>"><?php
                                    echo $event['name'] ?></a>
                            </li>
                        <?php
                        endforeach ?>

                    </ul>
                </li>
                <li class="external">
                    <a href="<?php
                    echo url_for('ext/calendar/personal') ?>"><?php
                        echo TEXT_EXT_MY_СALENDAR ?><i class="fa fa-angle-right"></i></a>
                </li>
            </ul>
        </li>
        <?php
    }

//render timer
    echo timer::render_header_menu();

//render tracking changes menu  
    echo track_changes::render_header_menu();
}
?>


<?php
$plugin_ext_current_version = session::get('plugin_ext_current_version');

if (strlen(
        $plugin_ext_current_version
    ) > 0 and $plugin_ext_current_version > PLUGIN_EXT_VERSION and $app_user['group_id'] == 0):
    ?>
    <li class="dropdown" id="header_new_release_bar">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
            <i class="fa fa-warning"></i>
            <span class="badge badge-warning">1</span>
        </a>
        <ul class="dropdown-menu extended tasks">
            <li>
                <p>
                    <?php
                    echo TEXT_NEW_PLUGIN_EXT_VERSION ?>
                </p>
            </li>
            <li>
                <ul class="dropdown-menu-list scroller" style="height: 80px;">

                    <li>
                        <a href="http://keruy.com.ua/ext_new_release.php" target="_new"><?php
                            echo sprintf(TEXT_NEW_PLUGIN_EXT_VERSION_INFO, $plugin_ext_current_version) ?></a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
<?php
endif ?>