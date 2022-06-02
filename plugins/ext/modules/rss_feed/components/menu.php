<?php

if (rss_feed::has_user_feeds()) {
    $app_plugin_menu['account_menu'][] = [
        'title' => TEXT_EXT_RSS_FEED,
        'url' => url_for('users/rss_feeds'),
        'class' => 'fa-rss'
    ];
}
