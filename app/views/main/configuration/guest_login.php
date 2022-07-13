<h3 class="page-title"><?= \K::$fw->TEXT_GUEST_LOGIN ?></h3>

<p><?= \K::$fw->TEXT_GUEST_LOGIN_INFO ?></p>

<?= \Helpers\Html::form_tag(
    'cfg',
    \Helpers\Urls::url_for('main/configuration/save'),
    ['class' => 'form-horizontal']
) ?>
<?= \Helpers\Html::input_hidden_tag('redirect_to', 'main/configuration/guest_login') ?>
<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label"><?= \K::$fw->TEXT_ENABLE_GUEST_LOGIN ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[ENABLE_GUEST_LOGIN]',
                \K::$fw->default_selector,
                \K::$fw->CFG_ENABLE_GUEST_LOGIN,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"><?= \K::$fw->TEXT_USER ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[GUEST_LOGIN_USER]',
                \K::$fw->choices,
                \K::$fw->CFG_GUEST_LOGIN_USER,
                ['class' => 'form-control input-xlarge chosen-select required']
            ); ?>
            <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_GUEST_LOGIN_USER_INFO) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"><?= \K::$fw->TEXT_BUTTON_TITLE ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[GUEST_LOGIN_BUTTON_TITLE]',
                \K::$fw->CFG_GUEST_LOGIN_BUTTON_TITLE,
                ['class' => 'form-control input-medium']
            ); ?>
            <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->TEXT_LOGIN_AS_GUEST) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"><?= \K::$fw->TEXT_URL ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'url',
                \Helpers\Urls::url_for('main/users/guest_login'),
                ['class' => 'form-control input-xlarge select-all', 'readonly' => 'readonly']
            ); ?>
        </div>
    </div>


    <?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>

</div>
</form>

<script>
    $(function () {
        $('#cfg').validate({ignore: ''});
    });
</script>