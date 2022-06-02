<?php
if ($app_chat->has_access and $app_action != 'chat_window'): ?>

    <div class="app-chat-button noprint" onClick="open_dialog('<?php
    echo url_for('ext/app_chat/chat_window') ?>')">
        <i class="fa fa-comments" aria-hidden="true"></i>&nbsp;
        <?php
        echo TEXT_EXT_CHAT_MESSAGES ?>
        <span id="app-chat-button-count-unread"><?php
            echo $app_chat->render_count_all_unrad() ?></span>
    </div>

    <script>
        var app_meta_title = $('title').html();

        app_chat_set_meta_title()

        setInterval(function () {
            $('#app-chat-button-count-unread').load('<?php echo url_for(
                'ext/app_chat/chat',
                'action=get_count_unrad_messages'
            ) ?>', function () {
                app_chat_set_meta_title()
            })
        }, 10000);
    </script>

<?php
endif ?>
