<div id="email_verification_section">
    <h3 class="form-title"><?= \K::$fw->TEXT_EMAIL_VERIFICATION_EMAIL_SUBJECT ?></h3>

    <p><?= sprintf(\K::$fw->TEXT_CODE_FROM_EMAIL_INFO, '<br><b>' . \K::$fw->app_user['email'] . '</b>') ?></p>


    <?= \Helpers\Html::form_tag(
        'email_verification_form',
        \Helpers\Urls::url_for('main/users/email_verification/check'),
        ['class' => 'login-form']
    ) ?>

    <div class="form-group">
        <div class="input-icon">
            <i class="fa fa-user"></i>
            <input class="form-control placeholder-no-fix required" type="text" autocomplete="off"
                   placeholder="<?= \K::$fw->TEXT_CODE_FROM_EMAIL ?>" name="code"/>
        </div>
        <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_CHECK_SPAM_FOLDER) ?>
    </div>

    <div class="form-actions">

        <button type="button" id="back-btn" class="btn btn-default"
                onClick="location.href='<?= \Helpers\Urls::url_for('main/users/login') ?>'"><i
                    class="fa fa-arrow-circle-left"></i> <?= \K::$fw->TEXT_BUTTON_BACK ?></button>

        <button type="submit" class="btn btn-info pull-right"><?php
            echo \K::$fw->TEXT_CONTINUE ?></button>
    </div>

    </form>

    <div class="forget-password">
        <p><a href="<?= \Helpers\Urls::url_for(
                'main/users/email_verification/resend'
            ) ?>"><?= \K::$fw->TEXT_RESEND_CODE ?></a></p>
    </div>

    <div class="create-account">
        <p><a href="#" onClick="show_change_email_form()"><?= \K::$fw->TEXT_CHANGE_EMAIL ?></a></p>
    </div>

</div>

<div id="update_email_section" style="display:none;">
    <h3 class="form-title"><?= \K::$fw->TEXT_CHANGE_EMAIL ?></h3>


    <?= \Helpers\Html::form_tag(
        'email_update_form',
        \Helpers\Urls::url_for('main/users/email_verification/update_email', 'action='),
        ['class' => 'login-form']
    ) ?>

    <div class="form-group">
        <div class="input-icon">
            <i class="fa fa-envelope"></i>
            <?= \Helpers\Html::input_tag(
                'email',
                \K::$fw->app_user['email'],
                ['class' => 'form-control required', 'type' => 'email', 'autocomplete' => 'off']
            ) ?>
        </div>
    </div>

    <div class="form-actions">

        <button type="button" id="back-btn" class="btn btn-default" onClick="back_to_email_verification_form()"><i
                    class="fa fa-arrow-circle-left"></i> <?= \K::$fw->TEXT_BUTTON_BACK ?></button>

        <button type="submit" class="btn btn-info pull-right"><?= \K::$fw->TEXT_BUTTON_UPDATE ?></button>
    </div>

    </form>
</div>

<script>
    $(function () {
        $('#email_verification_form').validate();

        $('#email_update_form').validate();
    });

    function show_change_email_form() {
        $('#email_verification_section').hide();
        $('#update_email_section').show();
    }

    function back_to_email_verification_form() {
        $('#email_verification_section').show();
        $('#update_email_section').hide();
    }
</script>