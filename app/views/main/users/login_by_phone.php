<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<h3 class="form-title"><?= \K::$fw->TEXT_LOGIN_BY_PHONE_NUMBER ?></h3>

<p><?= \K::$fw->TEXT_ENTER_YOUR_PHONE_NUMBER ?></p>

<?= \Helpers\Html::form_tag(
    'login_form',
    \Helpers\Urls::url_for('main/users/login_by_phone/login'),
    ['class' => 'login-form']
) ?>

<div class="form-group">
    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
    <label class="control-label visible-ie8 visible-ie9"><?= \K::$fw->page_title ?></label>
    <div class="input-icon">
        <i class="fa fa-phone"></i>
        <input class="form-control placeholder-no-fix required" type="text" autocomplete="off" name="phone" id="phone"/>
    </div>
</div>

<?php
$cfg = new \Models\Main\Fields_types_cfg(
    \K::$fw->app_fields_cache[1][\K::$fw->CFG_2STEP_VERIFICATION_USER_PHONE]['configuration']
);

if (strlen($cfg->get('mask')) > 0) {
    echo '
        <script>
          jQuery(function($){
             $("#phone").mask("' . $cfg->get('mask') . '");
          });
        </script>
      ';
}
?>

<?php
if (\Helpers\App_recaptcha::is_enabled()): ?>
    <div class="form-group">
        <?= \Helpers\App_recaptcha::render() ?>
    </div>
<?php
endif ?>

<div class="form-actions">

    <button type="button" id="back-btn" class="btn btn-default"
            onClick="location.href='<?= \Helpers\Urls::url_for('main/users/login') ?>'"><i
                class="fa fa-arrow-circle-left"></i> <?= \K::$fw->TEXT_BUTTON_BACK ?></button>

    <button type="submit" class="btn btn-info pull-right"><?= \K::$fw->TEXT_BUTTON_LOGIN ?></button>
</div>

</form>

<script>
    $(function () {
        $('#login_form').validate();
    });
</script> 