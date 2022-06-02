<div class="chat-msg-header">
    <div class="chat-msg-header-user"><?php
        echo TEXT_SETTINGS; ?></div>
</div>


<?php
echo form_tag('chat_sending_settings_form', url_for('ext/app_chat/settings', 'action=save_sending_settings')) ?>
<div class="form-body">
    <div class="form-group">
        <label class="control-label"><?php
            echo TEXT_EXT_SENDING_SETTINGS ?></label>

        <?php
        $choices = [];
        $choices['enter'] = TEXT_EXT_CHAT_SENDING_ENTER;
        $choices['ctrl_enter'] = TEXT_EXT_CHAT_SENDING_CTRL_ENTER;

        echo select_radioboxes_tag(
            'chat_sending_settings',
            $choices,
            $app_users_cfg->get('chat_sending_settings', 'enter'),
            ['class' => 'settings-value']
        )
        ?>

    </div>

    <div class="form-group">
        <label control-label" for="chat_sound_notification"><?php
        echo TEXT_EXT_SOUND_NOTIFICATION . (strlen(
                CFG_CHAT_SOUND_NOTIFICATION
            ) ? ' <small>(' . TEXT_DEFAULT . ': ' . CFG_CHAT_SOUND_NOTIFICATION . ')</small>' : '') ?></label>
        <div>
            <div class="input-group input-large">
                <?php
                echo select_tag(
                    'chat_sound_notification',
                    app_chat_notification::get_sound_choices(),
                    (strlen($app_users_cfg->get('chat_sound_notification')) ? $app_users_cfg->get(
                        'chat_sound_notification'
                    ) : CFG_CHAT_SOUND_NOTIFICATION),
                    ['class' => 'form-control settings-value']
                ) ?>
                <span class="input-group-addon" style="cursor: pointer"
                      onClick="play_sond_by_id('chat_sound_notification')">
        		<i class="fa fa-play"></i>
        	</span>
            </div>
            <?php
            echo tooltip_text(TEXT_EXT_SOUND_NOTIFICATION_INFO) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label" for="chat_instant_notification"><?php
            echo TEXT_EXT_INSTANT_NOTIFICATION ?></label>
        <div>
            <?php
            echo select_tag(
                'chat_instant_notification',
                ['1' => TEXT_YES, '0' => TEXT_NO],
                (strlen($app_users_cfg->get('chat_instant_notification')) ? $app_users_cfg->get(
                    'chat_instant_notification'
                ) : CFG_CHAT_INSTANT_NOTIFICATION),
                ['class' => 'form-control input-small settings-value']
            ); ?>
            <?php
            echo tooltip_text(TEXT_EXT_INSTANT_NOTIFICATION_INFO) ?>
        </div>
    </div>


</div>
</form>

<script>


    $(function () {

        appHandleUniform();

        $('.settings-value').change(function () {
            var obj = $('#chat_sending_settings_form');
            $.ajax({type: 'POST', url: obj.attr('action'), data: obj.serializeArray()})
        })

    })
</script>