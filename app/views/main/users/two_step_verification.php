<h3 class="form-title"><?= \K::$fw->page_title ?></h3>

<p><?= \K::$fw->page_body ?></p>


<?php
echo \Helpers\Html::form_tag(
    'login_form',
    \Helpers\Urls::url_for('main/users/two_step_verification/check'),
    ['class' => 'login-form']
) ?>

<div class="form-group">
    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
    <label class="control-label visible-ie8 visible-ie9"><?= \K::$fw->page_title ?></label>
    <div class="input-icon">
        <i class="fa fa-user"></i>
        <input class="form-control placeholder-no-fix required" type="text" autocomplete="off"
               placeholder="<?= \K::$fw->page_title ?>" name="code"/>
    </div>
</div>

<div class="form-actions">

    <button type="button" id="back-btn" class="btn btn-default"
            onClick="location.href='<?= \Helpers\Urls::url_for('main/users/login') ?>'">
        <i class="fa fa-arrow-circle-left"></i> <?= \K::$fw->TEXT_BUTTON_BACK ?></button>

    <button type="submit" class="btn btn-info pull-right"><?= \K::$fw->TEXT_BUTTON_LOGIN ?></button>
</div>

</form>

<script>
    $(function () {
        $('#login_form').validate();
    });
</script> 