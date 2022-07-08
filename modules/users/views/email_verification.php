<div id="email_verification_section">
    <h3 class="form-title"><?php
        echo TEXT_EMAIL_VERIFICATION_EMAIL_SUBJECT ?></h3>

    <p><?php
        echo sprintf(TEXT_CODE_FROM_EMAIL_INFO, '<br><b>' . $app_user['email'] . '</b>') ?></p>


    <?php
    echo form_tag(
        'email_verification_form',
        url_for('users/email_verification', 'action=check'),
        ['class' => 'login-form']
    ) ?>

    <div class="form-group">
        <div class="input-icon">
            <i class="fa fa-user"></i>
            <input class="form-control placeholder-no-fix required" type="text" autocomplete="off" placeholder="<?php
            echo TEXT_CODE_FROM_EMAIL ?>" name="code"/>
        </div>
        <?php
        echo tooltip_text(TEXT_CHECK_SPAM_FOLDER) ?>
    </div>

    <div class="form-actions">

        <button type="button" id="back-btn" class="btn btn-default" onClick="location.href='<?php
        echo url_for('users/login') ?>'"><i class="fa fa-arrow-circle-left"></i> <?php
            echo TEXT_BUTTON_BACK ?></button>

        <button type="submit" class="btn btn-info pull-right"><?php
            echo TEXT_CONTINUE ?></button>
    </div>

    </form>

    <div class="forget-password">
        <p><a href="<?php
            echo url_for('users/email_verification', 'action=resend') ?>"><?php
                echo TEXT_RESEND_CODE ?></a></p>
    </div>

    <div class="create-account">
        <p><a href="#" onClick="show_change_email_form()"><?php
                echo TEXT_CHANGE_EMAIL ?></a></p>
    </div>

</div>

<div id="update_email_section" style="display:none;">
    <h3 class="form-title"><?php
        echo TEXT_CHANGE_EMAIL ?></h3>


    <?php
    echo form_tag(
        'email_update_form',
        url_for('users/email_verification', 'action=update_email'),
        ['class' => 'login-form']
    ) ?>

    <div class="form-group">
        <div class="input-icon">
            <i class="fa fa-envelope"></i>
            <?php
            echo input_tag(
                'email',
                $app_user['email'],
                ['class' => 'form-control required', 'type' => 'email', 'autocomplete' => 'off']
            ) ?>
        </div>
    </div>

    <div class="form-actions">

        <button type="button" id="back-btn" class="btn btn-default" onClick="back_to_email_verification_form()"><i
                    class="fa fa-arrow-circle-left"></i> <?php
            echo TEXT_BUTTON_BACK ?></button>

        <button type="submit" class="btn btn-info pull-right"><?php
            echo TEXT_BUTTON_UPDATE ?></button>
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
