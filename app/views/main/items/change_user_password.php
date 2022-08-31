<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<ul class="page-breadcrumb breadcrumb">
    <?= \Models\Main\Items\Items::render_breadcrumb(\K::$fw->app_breadcrumb) ?>
</ul>

<h3 class="page-title"><?= \K::$fw->TEXT_HEADING_CHANGE_PASSWORD ?></h3>

<?= \Helpers\Html::form_tag(
    'change_password_form',
    \Helpers\Urls::url_for('main/items/change_user_password/change', 'path=' . \K::$fw->GET['path']),
    ['class' => 'form-horizontal']
) ?>

<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label" for="password_new"><?= \K::$fw->TEXT_NEW_PASSWORD ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_password_tag(
                'password_new',
                ['autocomplete' => 'off', 'class' => 'form-control input-medium required']
            ) ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label"
               for="password_confirmation"><?= \K::$fw->TEXT_PASSWORD_CONFIRMATION ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_password_tag(
                'password_confirmation',
                ['autocomplete' => 'off', 'class' => 'form-control input-medium  required']
            ) ?>
        </div>
    </div>
    <?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_CHANGE, ['class' => 'btn btn-primary']) ?>
</div>

</form>

<script>
    $(function () {
        $('#change_password_form').validate();
    });
</script>