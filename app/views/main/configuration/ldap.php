<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<h3 class="page-title"><?= \K::$fw->TEXT_HEADING_LDAP ?></h3>

<p><?= \K::$fw->TEXT_LDAP_INFO ?></p>

<?= \Helpers\Html::form_tag('cfg', \Helpers\Urls::url_for('main/configuration/save'), ['class' => 'form-horizontal']) ?>
<?= \Helpers\Html::input_hidden_tag('redirect_to', 'main/configuration/ldap') ?>
<div class="form-body">

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_LDAP_USE"><?= \K::$fw->TEXT_LDAP_USE ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[LDAP_USE]',
                \K::$fw->default_selector,
                \K::$fw->CFG_LDAP_USE,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_LDAP_USE"><?= \K::$fw->TEXT_USE_LDAP_LOGIN_ONLY ?></label>
        <div class="col-md-9">
            <p class="form-control-static"><?= \Helpers\App::app_render_status_label(
                    \K::$fw->CFG_USE_LDAP_LOGIN_ONLY
                ) ?></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_LDAP_SERVER_NAME"><?= \K::$fw->TEXT_LDAP_SERVER_NAME ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[LDAP_SERVER_NAME]',
                \K::$fw->CFG_LDAP_SERVER_NAME,
                ['class' => 'form-control input-large']
            ) . '<span class="help-block">' . \K::$fw->TEXT_LDAP_SERVER_NAME_NOTES . '</span>'; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_LDAP_SERVER_PORT"><?= \K::$fw->TEXT_LDAP_SERVER_PORT ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[LDAP_SERVER_PORT]',
                \K::$fw->CFG_LDAP_SERVER_PORT,
                ['class' => 'form-control input-large']
            ) . '<span class="help-block">' . \K::$fw->TEXT_LDAP_SERVER_PORT_NOTES . '</span>'; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_LDAP_BASE_DN"><?= \K::$fw->TEXT_LDAP_BASE_DN ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[LDAP_BASE_DN]',
                \K::$fw->CFG_LDAP_BASE_DN,
                ['class' => 'form-control input-large']
            ) . '<span class="help-block">' . \K::$fw->TEXT_LDAP_BASE_DN_NOTES . '</span>'; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_LDAP_UID"><?= \K::$fw->TEXT_LDAP_UID ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag('CFG[LDAP_UID]', \K::$fw->CFG_LDAP_UID, ['class' => 'form-control input-large']
            ) . '<span class="help-block">' . \K::$fw->TEXT_LDAP_UID_NOTES . '</span>'; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_LDAP_USER"><?= \K::$fw->TEXT_LDAP_USER_FILTER ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[LDAP_USER]',
                \K::$fw->CFG_LDAP_USER,
                ['class' => 'form-control input-large']
            ) . '<span class="help-block">' . \K::$fw->TEXT_LDAP_USER_FILTER_NOTES . '</span>'; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"
               for="CFG_LDAP_EMAIL_ATTRIBUTE"><?= \K::$fw->TEXT_LDAP_EMAIL_ATTRIBUTE ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[LDAP_EMAIL_ATTRIBUTE]',
                \K::$fw->CFG_LDAP_EMAIL_ATTRIBUTE,
                ['class' => 'form-control input-large']
            ) . '<span class="help-block">' . \K::$fw->TEXT_LDAP_EMAIL_ATTRIBUTE_NOTES . '</span>'; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"
               for="CFG_LDAP_FIRSTNAME_ATTRIBUTE"><?= \K::$fw->TEXT_LDAP_FIRSTNAME ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[LDAP_FIRSTNAME_ATTRIBUTE]',
                \K::$fw->CFG_LDAP_FIRSTNAME_ATTRIBUTE,
                ['class' => 'form-control input-large']
            ) . '<span class="help-block">' . \K::$fw->TEXT_LDAP_FIRSTNAME_NOTE . '</span>'; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"
               for="CFG_LDAP_LASTNAME_ATTRIBUTE"><?= \K::$fw->TEXT_LDAP_LASTNAME ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[LDAP_LASTNAME_ATTRIBUTE]',
                \K::$fw->CFG_LDAP_LASTNAME_ATTRIBUTE,
                ['class' => 'form-control input-large']
            ) . '<span class="help-block">' . \K::$fw->TEXT_LDAP_LASTNAME_NOTE . '</span>'; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_LDAP_USER_DN"><?= \K::$fw->TEXT_LDAP_USER_DN ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[LDAP_USER_DN]',
                \K::$fw->CFG_LDAP_USER_DN,
                ['class' => 'form-control input-large']
            ) . '<span class="help-block">' . \K::$fw->TEXT_LDAP_USER_DN_NOTES . '</span>'; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_LDAP_PASSWORD"><?= \K::$fw->TEXT_LDAP_PASSWORD ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[LDAP_PASSWORD]',
                \K::$fw->CFG_LDAP_PASSWORD,
                ['class' => 'form-control input-large']
            ) . '<span class="help-block">' . \K::$fw->TEXT_LDAP_PASSWORD_NOTES . '</span>'; ?>
        </div>
    </div>

    <?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>

</div>
</form>