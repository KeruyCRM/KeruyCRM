<div class="forget-password guest-login">
    <?php
    echo '<a href="' . url_for(
            'users/guest_login'
        ) . '" class="btn btn-social btn-guest-login"><span><i class="fa fa-user" aria-hidden="true"></i></span> ' . (strlen(
            CFG_GUEST_LOGIN_BUTTON_TITLE
        ) ? CFG_GUEST_LOGIN_BUTTON_TITLE : TEXT_LOGIN_AS_GUEST) . '</a>';
    ?>
</div>