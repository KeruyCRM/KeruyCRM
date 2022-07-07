<div class="forget-password guest-login">
    <?php
    echo '<a href="' . \Helpers\Urls::url_for(
            'main/users/guest_login'
        ) . '" class="btn btn-social btn-guest-login"><span><i class="fa fa-user" aria-hidden="true"></i></span> ' . (strlen(
            \K::$fw->CFG_GUEST_LOGIN_BUTTON_TITLE
        ) ? \K::$fw->CFG_GUEST_LOGIN_BUTTON_TITLE : \K::$fw->TEXT_LOGIN_AS_GUEST) . '</a>';
    ?>
</div>