<?php
$users_cfg = new users_cfg; ?>

<div class="chat-msg-header">
    <div class="chat-msg-header-user-photo"><?php
        echo render_user_photo($app_users_cache[$chat_user['id']]['photo']) ?></div>

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
            </ul>
        </div>

    </div>

    <div class="chat-msg-header-user"><?php
        echo $app_users_cache[$chat_user['id']]['name']; ?></div>

</div>


<?php
$assigned_to = $chat_user['id'];
$caht_action_url = "ext/app_chat/messages";

require(component_path('ext/app_chat/search_panel'));
?>

<div class="chat-msg-content">
    <?php
    echo $app_chat->render_messages_list($chat_user['id']) ?>
    <?php
    echo input_hidden_tag('chat_msg_number_of_pages', $app_chat->get_msg_number_of_pages($chat_user['id'])) ?>
</div>

<div class="chat-msg-footer">
    <?php
    if ($chat_user['field_5'] == 1) {
        echo form_tag(
                'chat-msg-form',
                url_for('ext/app_chat/messages', 'action=save'),
                ['form-token' => $attachments_form_token]
            ) . input_hidden_tag('assigned_to', $chat_user['id']) . input_hidden_tag('chat_message'); ?>
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
        <?php
    } else {
        echo TEXT_EXT_CHAT_INACTIVE_USER;
    }
    ?>
</div>

<?php
require(component_path('ext/app_chat/messages.js'));
?>

