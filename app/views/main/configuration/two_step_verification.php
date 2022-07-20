<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<h3 class="page-title"><?= \K::$fw->TEXT_2STEP_VERIFICATION ?></h3>

<p><?= \K::$fw->TEXT_2STEP_VERIFICATION_INFO ?></p>

<?= \Helpers\Html::form_tag(
    'cfg',
    \Helpers\Urls::url_for('main/configuration/save'),
    ['class' => 'form-horizontal']
) ?>
<?= \Helpers\Html::input_hidden_tag('redirect_to', 'main/configuration/two_step_verification') ?>
<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label"
               for="CFG_2STEP_VERIFICATION_ENABLED"><?= \K::$fw->TEXT_ENABLE_TEXT_2STEP_VERIFICATION ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[2STEP_VERIFICATION_ENABLED]',
                \K::$fw->default_selector,
                \K::$fw->CFG_2STEP_VERIFICATION_ENABLED,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <?php
    $choices = [
        'email' => 'Email',
        'sms' => 'SMS'
    ];
    ?>

    <div class="form-group">
        <label class="col-md-3 control-label"
               for="CFG_2STEP_VERIFICATION_TYPE"><?= \K::$fw->TEXT_SEND_CODE_BY ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[2STEP_VERIFICATION_TYPE]',
                $choices,
                \K::$fw->CFG_2STEP_VERIFICATION_TYPE,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>


    <div id="sms_settings" style="display:none">
        <?php
        if (\Helpers\App::is_ext_installed()) {
            $modules = new \Tools\Modules('sms');
            $choices = $modules->get_active_modules();
            ?>
            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="CFG_2STEP_VERIFICATION_SMS_MODULE"><?= \K::$fw->TEXT_EXT_SMS_MODULE ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'CFG[2STEP_VERIFICATION_SMS_MODULE]',
                        $choices,
                        \K::$fw->CFG_2STEP_VERIFICATION_SMS_MODULE,
                        ['class' => 'form-control input-large required']
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="CFG_2STEP_VERIFICATION_USER_PHONE"><?= \K::$fw->TEXT_PHONE ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'CFG[2STEP_VERIFICATION_USER_PHONE]',
                        \K::$fw->choices,
                        \K::$fw->CFG_2STEP_VERIFICATION_USER_PHONE,
                        ['class' => 'form-control input-large required']
                    ); ?>
                    <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_EXT_SEND_TO_USER_NUMBER_INFO) ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="CFG_LOGIN_BY_PHONE_NUMBER"><?= \K::$fw->TEXT_ALLOW_LOGIN_BY_PHONE_NUMBER ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'CFG[LOGIN_BY_PHONE_NUMBER]',
                        \K::$fw->default_selector,
                        \K::$fw->CFG_LOGIN_BY_PHONE_NUMBER,
                        ['class' => 'form-control input-small']
                    ); ?>
                    <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_ALLOW_LOGIN_BY_PHONE_NUMBER_INFO) ?>
                </div>
            </div>

            <?php
        } else {
            ?>
            <div class="form-group">
                <label class="col-md-3 control-label"></label>
                <div class="col-md-3">
                    <?= \Helpers\App::alert_error(\K::$fw->TEXT_EXTENSION_REQUIRED); ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>

    <?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>

</div>
</form>

<script>
    $(function () {
        $('#cfg').validate();

        $('#CFG_2STEP_VERIFICATION_TYPE').change(function () {
            show_sms_settings($(this).val())
        })

        show_sms_settings($('#CFG_2STEP_VERIFICATION_TYPE').val())
    })

    function show_sms_settings(type) {
        if (type == 'sms') {
            $('#sms_settings').show();
        } else {
            $('#sms_settings').hide();
        }
    }
</script>