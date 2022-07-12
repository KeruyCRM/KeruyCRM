<h3 class="page-title"><?php
    echo TEXT_HEADING_EMAIL_OPTIONS ?></h3>

<?php
echo form_tag('cfg', url_for('configuration/save'), ['class' => 'form-horizontal']) ?>
<?php
echo input_hidden_tag('redirect_to', 'configuration/emails') ?>
<div class="form-body">


    <div class="tabbable tabbable-custom">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#send_on_schedule" data-toggle="tab"><?php
                    echo TEXT_SEND_ON_SCHEDULE ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_info">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_EMAIL_USE_NOTIFICATION"><?php
                        echo TEXT_EMAIL_USE_NOTIFICATION ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[EMAIL_USE_NOTIFICATION]',
                            $default_selector,
                            CFG_EMAIL_USE_NOTIFICATION,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_EMAIL_SUBJECT_LABEL"><?php
                        echo TEXT_EMAIL_SUBJECT_LABEL ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'CFG[EMAIL_SUBJECT_LABEL]',
                            CFG_EMAIL_SUBJECT_LABEL,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_EMAIL_AMOUNT_PREVIOUS_COMMENTS"><?php
                        echo TEXT_EMAIL_AMOUNT_PREVIOUS_COMMENTS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'CFG[EMAIL_AMOUNT_PREVIOUS_COMMENTS]',
                            CFG_EMAIL_AMOUNT_PREVIOUS_COMMENTS,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_EMAIL_COPY_SENDER"><?php
                        echo TEXT_EMAIL_COPY_SENDER ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[EMAIL_COPY_SENDER]',
                            $default_selector,
                            CFG_EMAIL_COPY_SENDER,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_NOTIFICATIONS_SCHEDULE"><?php
                        echo tooltip_icon(TEXT_NOTIFICATIONS_SCHEDULE_INFO) . TEXT_NOTIFICATIONS_SCHEDULE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[NOTIFICATIONS_SCHEDULE]',
                            $default_selector,
                            CFG_NOTIFICATIONS_SCHEDULE,
                            ['class' => 'form-control input-small']
                        ); ?>
                        <?php
                        echo tooltip_text(
                            TEXT_NOTIFICATIONS_SCHEDULE_TIP . '<br>' . DIR_FS_CATALOG . 'cron/notification.php'
                        ) ?>
                    </div>
                </div>

                <h3 class="form-section"><?php
                    echo TEXT_TECHNICAL_SUPPORT ?></h3>

                <p><?php
                    echo TEXT_TECHNICAL_SUPPORT_INFO ?></p>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_EMAIL_NAME_FROM"><?php
                        echo TEXT_EMAIL_NAME_FROM ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'CFG[EMAIL_NAME_FROM]',
                            CFG_EMAIL_NAME_FROM,
                            ['class' => 'form-control input-large required']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_EMAIL_ADDRESS_FROM"><?php
                        echo TEXT_EMAIL_ADDRESS_FROM ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'CFG[EMAIL_ADDRESS_FROM]',
                            CFG_EMAIL_ADDRESS_FROM,
                            ['class' => 'form-control input-large required']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_EMAIL_SEND_FROM_SINGLE"><?php
                        echo TEXT_EMAIL_SEND_FROM_SINGLE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[EMAIL_SEND_FROM_SINGLE]',
                            $default_selector,
                            CFG_EMAIL_SEND_FROM_SINGLE,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>


            </div>

            <div class="tab-pane fade" id="send_on_schedule">

                <p><?php
                    echo TEXT_SEND_EMAILS_ON_SCHEDULE_DESCRIPTION ?></p>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_SEND_EMAILS_ON_SCHEDULE"><?php
                        echo TEXT_SEND_EMAILS_ON_SCHEDULE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[SEND_EMAILS_ON_SCHEDULE]',
                            $default_selector,
                            CFG_SEND_EMAILS_ON_SCHEDULE,
                            ['class' => 'form-control input-small']
                        ); ?>
                        <?php
                        echo tooltip_text(
                            TEXT_SEND_EMAILS_ON_SCHEDULE_INFO . '<br>' . DIR_FS_CATALOG . 'cron/email.php'
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_MAXIMUM_NUMBER_EMAILS"><?php
                        echo TEXT_MAXIMUM_NUMBER_EMAILS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'CFG[MAXIMUM_NUMBER_EMAILS]',
                            CFG_MAXIMUM_NUMBER_EMAILS,
                            ['class' => 'form-control input-small']
                        ); ?>
                        <?php
                        echo tooltip_text(TEXT_MAXIMUM_NUMBER_EMAILS_INFO) ?>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <?php
    echo submit_tag(TEXT_BUTTON_SAVE) ?>

</div>
</form>

<script>
    $(function () {
        $('#cfg').validate();
    })
</script> 

