<div class="row" style="margin:0">

    <div class="col-md-4" style="padding:0">
        <div class="chat-users-header">
            <div class="btn-group chat-btn-group-options" role="group">
                <button type="button" class="btn btn-default btn-chat-start-conversation" title="<?php
                echo htmlspecialchars(TEXT_EXT_CHAT_ADD_DIALOG) ?>"><i class="fa fa-plus" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-default btn-chat-settings" title="<?php
                echo htmlspecialchars(TEXT_SETTINGS) ?>"><i class="fa fa-cog" aria-hidden="true"></i></button>
            </div>

            <div class="input-group">
                <?php
                echo input_tag(
                    'chat_users_search_field',
                    '',
                    [
                        'placeholder' => TEXT_SEARCH,
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                        'autocorrect' => 'off',
                        'autocapitalize' => 'off',
                        'spellcheck' => 'false'
                    ]
                ) ?>
                <span class="input-group-btn">
					<button class="btn btn-default" id="chat_users_search_field_clear" class=""><i class="fa fa-times"
                                                                                                   aria-hidden="true"></i></button>
				</span>
            </div>
        </div>

        <div class="chat-users">
            <div id="chat-users-list">
                <?php
                echo $app_chat->render_users_list() ?>
            </div>
        </div>
    </div>

    <div class="col-md-8" style="padding:0">
        <div id="chat_messages" class="chat-messages">
            <div class="chat-default-message"><?php
                echo TEXT_EXT_CHAT_PLEASE_SELECT_DIALOG ?></div>
        </div>
    </div>
</div>

<div style="clear:both"></div>

<script>

    $(function () {


        app_caht_users_timer = setInterval(function () {
            if (!app_caht_users_search) {
                $('#chat-users-list').load('<?php echo url_for('ext/app_chat/chat', 'action=render_users_list') ?>')
            }
        }, 10000);


//chat settings
        $('.btn-chat-settings').click(function () {

            $('.chat-user').removeClass('selected')

            //reset timer
            if (is_app_caht_timer) {
                clearInterval(app_caht_timer)
                is_app_caht_timer = false;
            }

            $('#chat_messages').html('<div class="fa-ajax-loader fa fa-spinner fa-spin"></div>');
            $('#chat_messages').load('<?php echo url_for('ext/app_chat/settings')?>');
        })

//new conversation
        $('.btn-chat-start-conversation').click(function () {

            $('.chat-user').removeClass('selected')

            //reset timer
            if (is_app_caht_timer) {
                clearInterval(app_caht_timer)
                is_app_caht_timer = false;
            }

            $('#chat_messages').html('<div class="fa-ajax-loader fa fa-spinner fa-spin"></div>');
            $('#chat_messages').load('<?php echo url_for('ext/app_chat/conversation_form')?>');
        })


//search users
        $('#chat_users_search_field').keyup(function () {

            keywords = $(this).val().toLowerCase();

            if (keywords.length > 0) {
                $('.chat-user').each(function () {
                    username = $(this).attr('data-user-name').toLowerCase();
                    if (username.search(keywords) == -1) {
                        $(this).hide();
                    }
                })

                app_caht_users_search = true;
            } else {
                $('.chat-user').show();

                app_caht_users_search = false;
            }
        })

//reset search field
        $('#chat_users_search_field_clear').click(function () {
            $('#chat_users_search_field').val('')
            $('.chat-user').show();
            app_caht_users_search = false;
        })
    })
</script>

<?php
require(component_path('ext/app_chat/chat.js')); ?>
