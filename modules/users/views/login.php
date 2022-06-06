<h3 class="form-title"><?php
    echo(strlen(CFG_LOGIN_PAGE_HEADING) > 0 ? CFG_LOGIN_PAGE_HEADING : TEXT_HEADING_LOGIN) ?></h3>

<?php
echo(strlen(CFG_LOGIN_PAGE_CONTENT) > 0 ? '<p>' . CFG_LOGIN_PAGE_CONTENT . '</p>' : '') ?>

<?php
echo maintenance_mode::login_message() ?>

<?php
//check if default login is enabled
if (CFG_ENABLE_SOCIAL_LOGIN != 2) {
    echo form_tag('login_form', url_for('users/login', 'action=login'), ['class' => 'login-form'])
    ?>

    <div class="form-group">
        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
        <label class="control-label visible-ie8 visible-ie9"><?php
            echo TEXT_USERNAME ?></label>
        <div class="input-icon">
            <i class="fa fa-user"></i>
            <input class="form-control placeholder-no-fix required" type="text" autocomplete="off" placeholder="<?php
            echo TEXT_USERNAME ?>" name="username"/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label visible-ie8 visible-ie9"><?php
            echo TEXT_PASSWORD ?></label>
        <div class="input-icon">
            <i class="fa fa-lock"></i>
            <input class="form-control placeholder-no-fix required" type="password" autocomplete="off"
                   placeholder="<?php
                   echo TEXT_PASSWORD ?>" name="password"/>
        </div>
    </div>

    <?php
    if (app_recaptcha::is_enabled()): ?>
        <div class="form-group">
            <?php
            echo app_recaptcha::render() ?>
        </div>
    <?php
    endif ?>

    <div class="form-actions">
        <?php
        if (CFG_LOGIN_PAGE_HIDE_REMEMBER_ME != 1): ?>
            <label class="checkbox"> <?php
                echo input_checkbox_tag(
                        'remember_me',
                        1,
                        ['checked' => (isset($_COOKIE['app_remember_me']) ? true : false)]
                    ) . ' ' . TEXT_REMEMBER_ME ?></label>
        <?php
        endif; ?>

        <button type="submit" class="btn btn-info pull-right"><?php
            echo TEXT_BUTTON_LOGIN ?></button>
    </div>

    </form>

    <div class="forget-password">
        <?php
        if (CFG_USE_PUBLIC_REGISTRATION == 1) echo '<a style="float: right" class="btn btn-info btn-registration" href="' . url_for(
                'users/registration'
            ) . '">' . (strlen(
                CFG_REGISTRATION_BUTTON_TITLE
            ) ? CFG_REGISTRATION_BUTTON_TITLE : TEXT_BUTTON_REGISTRATION) . '</a>' ?>
        <p><a href="<?php
            echo url_for('users/restore_password') ?>"><?php
                echo TEXT_PASSWORD_FORGOTTEN ?></a></p>
    </div>

    <?php
}
?>


<?php
if (CFG_2STEP_VERIFICATION_ENABLED == 1 and CFG_LOGIN_BY_PHONE_NUMBER == 1 and CFG_2STEP_VERIFICATION_TYPE == 'sms'): ?>
    <div class="create-account">
        <p><a href="<?php
            echo url_for('users/login_by_phone') ?>"><?php
                echo TEXT_LOGIN_BY_PHONE_NUMBER ?></a></p>
    </div>
<?php
endif ?>

<?php
if (strlen(CFG_LOGIN_DIGITAL_SIGNATURE_MODULE)): ?>
    <div class="create-account">
        <p><a href="<?php
            echo url_for('users/signature_login') ?>"><?php
                echo TEXT_DIGITAL_SIGNATURE_LOGIN ?></a></p>
    </div>
<?php
endif ?>


<?php
if (CFG_LDAP_USE == 1): ?>
    <div class="create-account">
        <p><a href="<?php
            echo url_for('users/ldap_login') ?>"><?php
                echo TEXT_MENU_LDAP_LOGIN ?></a></p>
    </div>
<?php
endif ?>

<?php

if (guest_login::is_enabled()) {
    include(component_path('users/guest_login'));
}

//social login
if (CFG_ENABLE_SOCIAL_LOGIN != 0) {
    include(component_path('users/social_login'));
}
?>


<script>
    $(function () {
        $('#login_form').validate();
    });
</script> 



