<h3 class="page-title"><?= \K::$fw->TEXT_HEADING_EMAIL_OPTIONS ?></h3>

<?= \Helpers\Html::form_tag('cfg', \Helpers\Urls::url_for('main/configuration/save'), ['class' => 'form-horizontal']) ?>
<?= \Helpers\Html::input_hidden_tag('redirect_to', 'main/configuration/emails') ?>
<div class="form-body">
    <div class="tabbable tabbable-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?= \K::$fw->TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#send_on_schedule" data-toggle="tab"><?= \K::$fw->TEXT_SEND_ON_SCHEDULE ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_info">

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_EMAIL_USE_NOTIFICATION"><?= \K::$fw->TEXT_EMAIL_USE_NOTIFICATION ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[EMAIL_USE_NOTIFICATION]',
                            \K::$fw->default_selector,
                            \K::$fw->CFG_EMAIL_USE_NOTIFICATION,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_EMAIL_SUBJECT_LABEL"><?= \K::$fw->TEXT_EMAIL_SUBJECT_LABEL ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag(
                            'CFG[EMAIL_SUBJECT_LABEL]',
                            \K::$fw->CFG_EMAIL_SUBJECT_LABEL,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_EMAIL_AMOUNT_PREVIOUS_COMMENTS"><?= \K::$fw->TEXT_EMAIL_AMOUNT_PREVIOUS_COMMENTS ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag(
                            'CFG[EMAIL_AMOUNT_PREVIOUS_COMMENTS]',
                            \K::$fw->CFG_EMAIL_AMOUNT_PREVIOUS_COMMENTS,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_EMAIL_COPY_SENDER"><?= \K::$fw->TEXT_EMAIL_COPY_SENDER ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[EMAIL_COPY_SENDER]',
                            \K::$fw->default_selector,
                            \K::$fw->CFG_EMAIL_COPY_SENDER,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_NOTIFICATIONS_SCHEDULE"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_NOTIFICATIONS_SCHEDULE_INFO
                        ) . \K::$fw->TEXT_NOTIFICATIONS_SCHEDULE ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[NOTIFICATIONS_SCHEDULE]',
                            \K::$fw->default_selector,
                            \K::$fw->CFG_NOTIFICATIONS_SCHEDULE,
                            ['class' => 'form-control input-small']
                        ); ?>
                        <?= \Helpers\App::tooltip_text(
                            \K::$fw->TEXT_NOTIFICATIONS_SCHEDULE_TIP . '<br>' . \K::$fw->DIR_FS_CATALOG . 'cron/notification.php'
                        ) ?>
                    </div>
                </div>

                <h3 class="form-section"><?= \K::$fw->TEXT_TECHNICAL_SUPPORT ?></h3>

                <p><?= \K::$fw->TEXT_TECHNICAL_SUPPORT_INFO ?></p>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_EMAIL_NAME_FROM"><?= \K::$fw->TEXT_EMAIL_NAME_FROM ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag(
                            'CFG[EMAIL_NAME_FROM]',
                            \K::$fw->CFG_EMAIL_NAME_FROM,
                            ['class' => 'form-control input-large required']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_EMAIL_ADDRESS_FROM"><?= \K::$fw->TEXT_EMAIL_ADDRESS_FROM ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag(
                            'CFG[EMAIL_ADDRESS_FROM]',
                            \K::$fw->CFG_EMAIL_ADDRESS_FROM,
                            ['class' => 'form-control input-large required']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_EMAIL_SEND_FROM_SINGLE"><?= \K::$fw->TEXT_EMAIL_SEND_FROM_SINGLE ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[EMAIL_SEND_FROM_SINGLE]',
                            \K::$fw->default_selector,
                            \K::$fw->CFG_EMAIL_SEND_FROM_SINGLE,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="send_on_schedule">

                <p><?= \K::$fw->TEXT_SEND_EMAILS_ON_SCHEDULE_DESCRIPTION ?></p>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_SEND_EMAILS_ON_SCHEDULE"><?= \K::$fw->TEXT_SEND_EMAILS_ON_SCHEDULE ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[SEND_EMAILS_ON_SCHEDULE]',
                            \K::$fw->default_selector,
                            \K::$fw->CFG_SEND_EMAILS_ON_SCHEDULE,
                            ['class' => 'form-control input-small']
                        ); ?>
                        <?= \Helpers\App::tooltip_text(
                            \K::$fw->TEXT_SEND_EMAILS_ON_SCHEDULE_INFO . '<br>' . \K::$fw->DIR_FS_CATALOG . 'cron/email.php'
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_MAXIMUM_NUMBER_EMAILS"><?= \K::$fw->TEXT_MAXIMUM_NUMBER_EMAILS ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag(
                            'CFG[MAXIMUM_NUMBER_EMAILS]',
                            \K::$fw->CFG_MAXIMUM_NUMBER_EMAILS,
                            ['class' => 'form-control input-small']
                        ); ?>
                        <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_MAXIMUM_NUMBER_EMAILS_INFO) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>

</div>
</form>

<script>
    $(function () {
        $('#cfg').validate();
    })
</script>