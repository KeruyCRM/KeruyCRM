<h3 class="page-title"><?php
    echo TEXT_EMAIL_SMTP_CONFIGURATION ?></h3>

<?php
echo form_tag('cfg', url_for('configuration/save'), ['class' => 'form-horizontal']) ?>
<?php
echo input_hidden_tag('redirect_to', 'configuration/emails_smtp') ?>
<div class="form-body">

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_EMAIL_USE_SMTP"><?php
            echo TEXT_EMAIL_USE_SMTP ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'CFG[EMAIL_USE_SMTP]',
                $default_selector,
                CFG_EMAIL_USE_SMTP,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_EMAIL_SMTP_SERVER"><?php
            echo TEXT_EMAIL_SMTP_SERVER ?></label>
        <div class="col-md-9">
            <?php
            echo input_tag('CFG[EMAIL_SMTP_SERVER]', CFG_EMAIL_SMTP_SERVER, ['class' => 'form-control input-large']); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_EMAIL_SMTP_PORT"><?php
            echo TEXT_EMAIL_SMTP_PORT ?></label>
        <div class="col-md-9">
            <?php
            echo input_tag('CFG[EMAIL_SMTP_PORT]', CFG_EMAIL_SMTP_PORT, ['class' => 'form-control input-small']); ?>
        </div>
    </div>

    <?php
    $choices = [
        '' => TEXT_NO,
        'ssl' => 'SSL (port 465)',
        'tls' => 'TLS (port 587)',
    ];
    ?>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_EMAIL_SMTP_ENCRYPTION"><?php
            echo TEXT_EMAIL_SMTP_ENCRYPTION ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'CFG[EMAIL_SMTP_ENCRYPTION]',
                $choices,
                CFG_EMAIL_SMTP_ENCRYPTION,
                ['class' => 'form-control input-medium']
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_EMAIL_SMTP_LOGIN"><?php
            echo TEXT_EMAIL_SMTP_LOGIN ?></label>
        <div class="col-md-9">
            <?php
            echo input_tag('CFG[EMAIL_SMTP_LOGIN]', CFG_EMAIL_SMTP_LOGIN, ['class' => 'form-control input-large']); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_EMAIL_SMTP_PASSWORD"><?php
            echo TEXT_EMAIL_SMTP_PASSWORD ?></label>
        <div class="col-md-9">
            <?php
            echo input_tag('CFG[EMAIL_SMTP_PASSWORD]', CFG_EMAIL_SMTP_PASSWORD, ['class' => 'form-control input-large']
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_EMAIL_SMTP_ENCRYPTION"><?php
            echo TEXT_DEBUG_MODE ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                    'CFG[EMAIL_SMTP_DEBUG]',
                    $default_selector,
                    CFG_EMAIL_SMTP_DEBUG,
                    ['class' => 'form-control input-medium']
                ) . tooltip_text('log/smtp_log.txt'); ?>
        </div>
    </div>

    <?php
    echo submit_tag(TEXT_BUTTON_SAVE) ?>

</div>
</form>

