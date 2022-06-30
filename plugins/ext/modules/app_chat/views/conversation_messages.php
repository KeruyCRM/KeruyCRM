<?php
$users_cfg = new users_cfg; ?>
<?php
$chat_conversation_info = $app_chat->get_conversations_info($chat_conversation); ?>

<div class="chat-msg-header">
    <div class="chat-msg-header-user-photo chat-converstation-icon"><?php
        echo $chat_conversation_info['menu_icon'] ?></div>

    <div class="chat-msg-header-tools">

        <div class="btn-group">
            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown">
                <i class="fa fa-angle-down"></i>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li>
                    <a href="#" class="btn-chat-search-messages"><?php
                        echo TEXT_SEARCH ?></a>
                </li>

                <?php
                if ($app_user['id'] == $chat_conversation['users_id']): ?>
                    <li>
                        <a href="#" class="btn-chat-edit-conversation"><?php
                            echo TEXT_BUTTON_EDIT ?></a>
                    </li>
                    <li>
                        <a href="#" class="btn-chat-delete-conversation"><?php
                            echo TEXT_BUTTON_DELETE ?></a>
                    </li>
                <?php
                endif ?>
            </ul>
        </div>

    </div>


    <div class="chat-msg-header-user">
        <?php
        echo $chat_conversation['name']; ?>

        <div class="btn-group">
            <button class="btn btn-default dropdown-toggle btn-conversations-users" type="button" data-toggle="dropdown"
                    data-hover="dropdown">
                <?php
                echo TEXT_EXT_PARTICIPANTS . ' (' . $chat_conversation_info['count_users'] . ')' ?>
            </button>
            <ul class="dropdown-menu" role="menu">
                <?php
                echo $app_chat->get_conversations_users_dropdown(
                    $chat_conversation_info['assigned_to'],
                    $chat_conversation['users_id']
                ) ?>

            </ul>
        </div>
    </div>

</div>

<?php
$assigned_to = $chat_conversation['id'];
$caht_action_url = "ext/app_chat/conversation_messages";

require(component_path('ext/app_chat/search_panel'));
?>

<div class="chat-msg-content">
    <?php
    echo $app_chat->render_conversations_messages_list($chat_conversation['id']) ?>
    <?php
    echo input_hidden_tag(
        'chat_msg_number_of_pages',
        $app_chat->get_conversations_msg_number_of_pages($chat_conversation['id'])
    ) ?>
</div>

<div class="chat-msg-footer">
    <?php
    echo form_tag(
            'chat-msg-form',
            url_for('ext/app_chat/conversation_messages', 'action=save'),
            ['form-token' => $attachments_form_token]
        ) . input_hidden_tag('assigned_to', $chat_conversation['id']) . input_hidden_tag('chat_message'); ?>
    <table class="chat-msg-table">
        <tr>
            <td class="chat-msg-td-attachments">
                <?php
                require(component_path('ext/app_chat/smiles')); ?>
                <?php
                require(component_path('ext/app_chat/attachments_button')); ?>
            </td>
            <td class="chat-msg-td">
                <div class="chat-msg-text" id="chat_message_text" contenteditable="true"></div>
                <div id="uploadifive_attachments_list"><?php
                    echo $app_chat->render_attachments_preview($attachments_form_token) ?></div>
                <div id="uploadifive_queue_list"></div>
            </td>
            <td class="chat-msg-td-submit">
                <button type="submit" class="chat-btn-submit"><i class="fa fa-paper-plane-o" aria-hidden="true"></i>
                </button>
            </td>
        </tr>
    </table>
    </form>
</div>

<?php
require(component_path('ext/app_chat/messages.js'));
?>

<script>
    //new conversation
    $('.btn-chat-edit-conversation').click(function () {

        $('.chat-user').removeClass('selected')

        //reset timer
        if (is_app_caht_timer) {
            clearInterval(app_caht_timer)
            is_app_caht_timer = false;
        }

        $('#chat_messages').html('<div class="fa-ajax-loader fa fa-spinner fa-spin"></div>');
        $('#chat_messages').load('<?php echo url_for(
            'ext/app_chat/conversation_form',
            'id=' . $chat_conversation['id']
        )?>');
    })

    $('.btn-chat-delete-conversation').click(function () {

        if (confirm('<?php echo htmlspecialchars(TEXT_EXT_DELETE_CONVERSATION_CONFIRM) ?>')) {
            $('.chat-user').removeClass('selected')

            //reset timer
            if (is_app_caht_timer) {
                clearInterval(app_caht_timer)
                is_app_caht_timer = false;
            }

            $('#chat_messages').html('<div class="chat-default-message"><?php echo htmlspecialchars(
                TEXT_EXT_CHAT_PLEASE_SELECT_DIALOG
            ) ?></div>');

            $.ajax({
                type: 'POST',
                url: '<?php echo url_for('ext/app_chat/conversation', 'action=delete&id=' . $chat_conversation['id'])?>'
            }).done(function () {
                $('#chat-users-list').load('<?php echo url_for('ext/app_chat/chat', 'action=render_users_list') ?>');
            })
        }
    })

</script>