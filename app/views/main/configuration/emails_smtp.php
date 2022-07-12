<h3 class="page-title"><?= \K::$fw->TEXT_EMAIL_SMTP_CONFIGURATION ?></h3>

<?= \Helpers\Html::form_tag('cfg', \Helpers\Urls::url_for('main/configuration/save'), ['class' => 'form-horizontal']) ?>
<?= \Helpers\Html::input_hidden_tag('redirect_to', 'main/configuration/emails_smtp') ?>
<div class="form-body">

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_EMAIL_USE_SMTP"><?= \K::$fw->TEXT_EMAIL_USE_SMTP ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[EMAIL_USE_SMTP]',
                \K::$fw->default_selector,
                \K::$fw->CFG_EMAIL_USE_SMTP,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_EMAIL_SMTP_SERVER"><?= \K::$fw->TEXT_EMAIL_SMTP_SERVER ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[EMAIL_SMTP_SERVER]',
                \K::$fw->CFG_EMAIL_SMTP_SERVER,
                ['class' => 'form-control input-large']
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_EMAIL_SMTP_PORT"><?= \K::$fw->TEXT_EMAIL_SMTP_PORT ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[EMAIL_SMTP_PORT]',
                \K::$fw->CFG_EMAIL_SMTP_PORT,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <?php
    $choices = [
        '' => \K::$fw->TEXT_NO,
        'ssl' => 'SSL (port 465)',
        'tls' => 'TLS (port 587)',
    ];
    ?>

    <div class="form-group">
        <label class="col-md-3 control-label"
               for="CFG_EMAIL_SMTP_ENCRYPTION"><?= \K::$fw->TEXT_EMAIL_SMTP_ENCRYPTION ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[EMAIL_SMTP_ENCRYPTION]',
                $choices,
                \K::$fw->CFG_EMAIL_SMTP_ENCRYPTION,
                ['class' => 'form-control input-medium']
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_EMAIL_SMTP_LOGIN"><?= \K::$fw->TEXT_EMAIL_SMTP_LOGIN ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[EMAIL_SMTP_LOGIN]',
                \K::$fw->CFG_EMAIL_SMTP_LOGIN,
                ['class' => 'form-control input-large']
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"
               for="CFG_EMAIL_SMTP_PASSWORD"><?= \K::$fw->TEXT_EMAIL_SMTP_PASSWORD ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[EMAIL_SMTP_PASSWORD]',
                \K::$fw->CFG_EMAIL_SMTP_PASSWORD,
                ['class' => 'form-control input-large']
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_EMAIL_SMTP_ENCRYPTION"><?= \K::$fw->TEXT_DEBUG_MODE ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[EMAIL_SMTP_DEBUG]',
                \K::$fw->default_selector,
                \K::$fw->CFG_EMAIL_SMTP_DEBUG,
                ['class' => 'form-control input-medium']
            ) . \Helpers\App::tooltip_text('log/smtp_log.txt'); ?>
        </div>
    </div>

    <?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>

</div>
</form>