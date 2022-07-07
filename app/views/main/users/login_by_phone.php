<h3 class="form-title"><?php
    echo TEXT_LOGIN_BY_PHONE_NUMBER ?></h3>

<p><?php
    echo TEXT_ENTER_YOUR_PHONE_NUMBER ?></p>

<?php
echo form_tag('login_form', url_for('users/login_by_phone', 'action=login'), ['class' => 'login-form']) ?>

<div class="form-group">
    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
    <label class="control-label visible-ie8 visible-ie9"><?php
        echo $page_title ?></label>
    <div class="input-icon">
        <i class="fa fa-phone"></i>
        <input class="form-control placeholder-no-fix required" type="text" autocomplete="off" name="phone" id="phone"/>
    </div>
</div>

<?php
$cfg = new fields_types_cfg($app_fields_cache[1][CFG_2STEP_VERIFICATION_USER_PHONE]['configuration']);

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
if (app_recaptcha::is_enabled()): ?>
    <div class="form-group">
        <?php
        echo app_recaptcha::render() ?>
    </div>
<?php
endif ?>

<div class="form-actions">

    <button type="button" id="back-btn" class="btn btn-default" onClick="location.href='<?php
    echo url_for('users/login') ?>'"><i class="fa fa-arrow-circle-left"></i> <?php
        echo TEXT_BUTTON_BACK ?></button>

    <button type="submit" class="btn btn-info pull-right"><?php
        echo TEXT_BUTTON_LOGIN ?></button>
</div>

</form>

<script>
    $(function () {
        $('#login_form').validate();
    });
</script> 