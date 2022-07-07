<h3 class="form-title"><?php
    echo(strlen(
        \K::$fw->CFG_LOGIN_PAGE_HEADING
    ) > 0 ? \K::$fw->CFG_LOGIN_PAGE_HEADING : \K::$fw->TEXT_HEADING_LOGIN) ?></h3>

<?php
echo(strlen(\K::$fw->CFG_LOGIN_PAGE_CONTENT) > 0 ? '<p>' . \K::$fw->CFG_LOGIN_PAGE_CONTENT . '</p>' : '') ?>

<?php
echo \Tools\Maintenance_mode::login_message() ?>

<?php
//check if default login is enabled
if (\K::$fw->CFG_ENABLE_SOCIAL_LOGIN != 2) {
    echo \Helpers\Html::form_tag(
        'login_form',
        \Helpers\Urls::url_for('main/users/login/login'),
        ['class' => 'login-form']
    )
    ?>

    <div class="form-group">
        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
        <label class="control-label visible-ie8 visible-ie9"><?php
            echo \K::$fw->TEXT_USERNAME ?></label>
        <div class="input-icon">
            <i class="fa fa-user"></i>
            <input class="form-control placeholder-no-fix required" type="text" autocomplete="off" placeholder="<?php
            echo \K::$fw->TEXT_USERNAME ?>" name="username"/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label visible-ie8 visible-ie9"><?php
            echo \K::$fw->TEXT_PASSWORD ?></label>
        <div class="input-icon">
            <i class="fa fa-lock"></i>
            <input class="form-control placeholder-no-fix required" type="password" autocomplete="off"
                   placeholder="<?php
                   echo \K::$fw->TEXT_PASSWORD ?>" name="password"/>
        </div>
    </div>

    <?php
    if (\Helpers\App_recaptcha::is_enabled()): ?>
        <div class="form-group">
            <?php
            echo \Helpers\App_recaptcha::render() ?>
        </div>
    <?php
    endif ?>

    <div class="form-actions">
        <?php
        if (\K::$fw->CFG_LOGIN_PAGE_HIDE_REMEMBER_ME != 1): ?>
            <label class="checkbox"> <?php
                echo \Helpers\Html::input_checkbox_tag(
                        'remember_me',
                        1,
                        ['checked' => \K::cookieExists('app_remember_me')]
                    ) . ' ' . \K::$fw->TEXT_REMEMBER_ME ?></label>
        <?php
        endif; ?>

        <button type="submit" class="btn btn-info pull-right"><?php
            echo \K::$fw->TEXT_BUTTON_LOGIN ?></button>
    </div>

    </form>

    <div class="forget-password">
        <?php
        if (\K::$fw->CFG_USE_PUBLIC_REGISTRATION == 1) echo '<a style="float: right" class="btn btn-info btn-registration" href="' . \Helpers\Urls::url_for(
                'main/users/registration'
            ) . '">' . (strlen(
                \K::$fw->CFG_REGISTRATION_BUTTON_TITLE
            ) ? \K::$fw->CFG_REGISTRATION_BUTTON_TITLE : \K::$fw->TEXT_BUTTON_REGISTRATION) . '</a>' ?>
        <p><a href="<?php
            echo \Helpers\Urls::url_for('main/users/restore_password') ?>"><?php
                echo \K::$fw->TEXT_PASSWORD_FORGOTTEN ?></a></p>
    </div>

    <?php
}
?>

<?php
if (\K::$fw->CFG_2STEP_VERIFICATION_ENABLED == 1 and \K::$fw->CFG_LOGIN_BY_PHONE_NUMBER == 1 and \K::$fw->CFG_2STEP_VERIFICATION_TYPE == 'sms'): ?>
    <div class="create-account">
        <p><a href="<?php
            echo \Helpers\Urls::url_for('main/users/login_by_phone') ?>"><?php
                echo \K::$fw->TEXT_LOGIN_BY_PHONE_NUMBER ?></a></p>
    </div>
<?php
endif ?>

<?php
if (strlen(\K::$fw->CFG_LOGIN_DIGITAL_SIGNATURE_MODULE)): ?>
    <div class="create-account">
        <p><a href="<?php
            echo \Helpers\Urls::url_for('main/users/signature_login') ?>"><?php
                echo \K::$fw->TEXT_DIGITAL_SIGNATURE_LOGIN ?></a></p>
    </div>
<?php
endif ?>


<?php
if (\K::$fw->CFG_LDAP_USE == 1): ?>
    <div class="create-account">
        <p><a href="<?php
            echo \Helpers\Urls::url_for('main/users/ldap_login') ?>"><?php
                echo \K::$fw->TEXT_MENU_LDAP_LOGIN ?></a></p>
    </div>
<?php
endif ?>

<?php

if (\Models\Main\Users\Guest_login::is_enabled()) {
    //include(component_path('users/guest_login'));
    echo \K::view()->render(\Helpers\Urls::component_path('main/users/guest_login'));
}

//social login
if (\K::$fw->CFG_ENABLE_SOCIAL_LOGIN != 0) {
    //include(component_path('users/social_login'));
    echo \K::view()->render(\Helpers\Urls::component_path('main/users/social_login'));
}
?>

<script>
    $(function () {
        $('#login_form').validate();
    });
</script>