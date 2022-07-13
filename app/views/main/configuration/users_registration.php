<h3 class="page-title"><?= \K::$fw->TEXT_MENU_USERS_REGISTRATION ?></h3>

<?= \Helpers\Html::form_tag(
    'cfg',
    \Helpers\Urls::url_for('main/configuration/save'),
    ['class' => 'form-horizontal']
) ?>
<?= \Helpers\Html::input_hidden_tag('redirect_to', 'main/configuration/users_registration') ?>
<div class="form-body">
    <div class="tabbable tabbable-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#user_registration"
                                  data-toggle="tab"><?= \K::$fw->TEXT_MENU_USER_REGISTRATION_EMAIL ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="user_registration">

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_REGISTRATION_EMAIL_SUBJECT"><?= \K::$fw->TEXT_REGISTRATION_EMAIL_SUBJECT ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag(
                            'CFG[REGISTRATION_EMAIL_SUBJECT]',
                            \K::$fw->CFG_REGISTRATION_EMAIL_SUBJECT,
                            ['class' => 'form-control input-xlarge']
                        ); ?>
                        <span class="help-block"><?= \K::$fw->TEXT_EXAMPLE . ': ' . \K::$fw->TEXT_NEW_USER_DEFAULT_EMAIL_SUBJECT ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_REGISTRATION_EMAIL_BODY"><?= \K::$fw->TEXT_REGISTRATION_EMAIL_BODY ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::textarea_tag(
                            'CFG[REGISTRATION_EMAIL_BODY]',
                            \K::$fw->CFG_REGISTRATION_EMAIL_BODY,
                            ['class' => 'form-control input-xlarge editor']
                        ); ?>
                        <span class="help-block"><?= \K::$fw->TEXT_REGISTRATION_EMAIL_BODY_NOTE ?></span>
                        <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_EXAMPLE . ': [FirstName] [LastName]') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>

</div>
</form>