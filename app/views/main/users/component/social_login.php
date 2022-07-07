<div class="forget-password social-login">
    <?php

    if (CFG_ENABLE_VKONTAKTE_LOGIN) {
        echo '<a href="' . url_for(
                'social_login/vkontakte'
            ) . '" class="btn btn-social btn-vk"><span><i class="fa fa-vk" aria-hidden="true"></i></span> ' . (strlen(
                CFG_VKONTAKTE_BUTTON_TITLE
            ) ? CFG_VKONTAKTE_BUTTON_TITLE : TEXT_LOGIN_WITH . ' ' . TEXT_VKONTAKTE) . '</a>';
    }

    if (CFG_ENABLE_YANDEX_LOGIN) {
        echo '<a href="' . url_for(
                'social_login/yandex'
            ) . '" class="btn btn-social btn-yandex"><span><i class="lab la-yandex"></i></span> ' . (strlen(
                CFG_YANDEX_BUTTON_TITLE
            ) ? CFG_YANDEX_BUTTON_TITLE : TEXT_LOGIN_WITH . ' ' . TEXT_YANDEX) . '</a>';
    }

    if (CFG_ENABLE_GOOGLE_LOGIN) {
        echo '<a href="' . url_for(
                'social_login/google'
            ) . '" class="btn btn-social btn-google"><span><i class="fa fa-google" aria-hidden="true"></i></span> ' . (strlen(
                CFG_GOOGLE_BUTTON_TITLE
            ) ? CFG_GOOGLE_BUTTON_TITLE : TEXT_LOGIN_WITH . ' ' . TEXT_GOOGLE) . '</a>';
    }

    if (CFG_ENABLE_FACEBOOK_LOGIN) {
        echo '<a href="' . url_for(
                'social_login/facebook'
            ) . '" class="btn btn-social btn-facebook"><span><i class="fa fa-facebook" aria-hidden="true"></i></span> ' . (strlen(
                CFG_FACEBOOK_BUTTON_TITLE
            ) ? CFG_FACEBOOK_BUTTON_TITLE : TEXT_LOGIN_WITH . ' ' . TEXT_FACEBOOK) . '</a>';
    }

    if (CFG_ENABLE_LINKEDIN_LOGIN) {
        echo '<a href="' . url_for(
                'social_login/linkedin'
            ) . '" class="btn btn-social btn-linkedin"><span><i class="fa fa-linkedin" aria-hidden="true"></i></span> ' . (strlen(
                CFG_LINKEDIN_BUTTON_TITLE
            ) ? CFG_LINKEDIN_BUTTON_TITLE : TEXT_LOGIN_WITH . ' ' . TEXT_LINKEDIN) . '</a>';
    }

    if (CFG_ENABLE_TWITTER_LOGIN) {
        echo '<a href="' . url_for(
                'social_login/twitter'
            ) . '" class="btn btn-social btn-twitter"><span><i class="fa fa-twitter" aria-hidden="true"></i></span> ' . (strlen(
                CFG_TWITTER_BUTTON_TITLE
            ) ? CFG_TWITTER_BUTTON_TITLE : TEXT_LOGIN_WITH . ' ' . TEXT_TWITTER) . '</a>';
    }

    if (CFG_ENABLE_STEAM_LOGIN) {
        echo '<a href="' . url_for(
                'social_login/steam'
            ) . '" class="btn btn-social btn-steam"><span><i class="fa fa-steam" aria-hidden="true"></i></span> ' . (strlen(
                CFG_STEAM_BUTTON_TITLE
            ) ? CFG_STEAM_BUTTON_TITLE : TEXT_LOGIN_WITH . ' ' . TEXT_STEAM) . '</a>';
    }

    ?>
</div>