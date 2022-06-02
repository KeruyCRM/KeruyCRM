<h3 class="page-title"><?php
    echo TEXT_EXT_INTERNAL_CHAT ?></h3>

<?php
echo form_tag('configuration_form', url_for('ext/app_chat/configuration', 'action=save'), ['class' => 'form-horizontal']
) ?>
<div class="form-body">

    <div class="tabbable tabbable-custom">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#alerts_settings" data-toggle="tab"><?php
                    echo TEXT_EXT_ALERTS_SETTINGS ?></a></li>
        </ul>

        <?php
        $default_selector = ['1' => TEXT_YES, '0' => TEXT_NO]; ?>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_info">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_ENABLE_CHAT"><?php
                        echo TEXT_EXT_ENABLE_CHAT ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[ENABLE_CHAT]',
                            $default_selector,
                            CFG_ENABLE_CHAT,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <br>
                <div class="form-group">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-9">
                        <p style="margin-bottom: 0;"><?php
                            echo TEXT_EXT_CHAT_ACCESS ?></p>
                    </div>
                </div>

                <?php
                foreach (access_groups::get_choices() as $group_id => $group_name): ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="allowed_groups"><?php
                            echo $group_name ?></label>
                        <div class="col-md-9">
                            <?php
                            echo select_tag(
                                'access[' . $group_id . '][]',
                                access_groups::get_choices(),
                                $app_chat->get_access($group_id),
                                ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                            ) ?>
                        </div>
                    </div>
                <?php
                endforeach ?>

            </div>
            <div class="tab-pane fade" id="alerts_settings">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_CHAT_SOUND_NOTIFICATION"><?php
                        echo TEXT_EXT_SOUND_NOTIFICATION ?></label>
                    <div class="col-md-9">

                        <div class="input-group input-large">
                            <?php
                            echo select_tag(
                                'CFG[CHAT_SOUND_NOTIFICATION]',
                                app_chat_notification::get_sound_choices(),
                                CFG_CHAT_SOUND_NOTIFICATION,
                                ['class' => 'form-control']
                            ) ?>
                            <span class="input-group-addon" style="cursor: pointer"
                                  onClick="play_sond_by_id('CFG_CHAT_SOUND_NOTIFICATION')">
            		<i class="fa fa-play"></i>
            	</span>
                        </div>
                        <?php
                        echo tooltip_text(TEXT_EXT_SOUND_NOTIFICATION_INFO) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_CHAT_INSTANT_NOTIFICATION"><?php
                        echo TEXT_EXT_INSTANT_NOTIFICATION ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[CHAT_INSTANT_NOTIFICATION]',
                            $default_selector,
                            CFG_CHAT_INSTANT_NOTIFICATION,
                            ['class' => 'form-control input-small']
                        ); ?>
                        <?php
                        echo tooltip_text(TEXT_EXT_INSTANT_NOTIFICATION_INFO) ?>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_CHAT_SEND_ALERTS"><?php
                        echo TEXT_EXT_CHAT_SEND_ALERTS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[CHAT_SEND_ALERTS]',
                            $default_selector,
                            CFG_CHAT_SEND_ALERTS,
                            ['class' => 'form-control input-small']
                        ); ?>
                        <?php
                        echo tooltip_text(
                            TEXT_EXT_CHAT_SEND_ALERTS_INFO . '<br>' . DIR_FS_CATALOG . 'cron/chat.php' . '<br>' . TEXT_EXT_CHAT_ALERTS_TIME_TIP
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_CHAT_ALERTS_SUBJECT"><?php
                        echo TEXT_EXT_ALERTS_EMAIL_SUBJECT ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'CFG[CHAT_ALERTS_SUBJECT]',
                            CFG_CHAT_ALERTS_SUBJECT,
                            ['class' => 'form-control input-large']
                        ); ?>
                        <?php
                        echo tooltip_text(TEXT_DEFAULT . ': ' . TEXT_EXT_CHAT_ALERTS_SUBJECT) ?>
                    </div>
                </div>


            </div>
        </div>

    </div>

</div>

<?php
echo submit_tag(TEXT_BUTTON_SAVE) ?>
</form> 
