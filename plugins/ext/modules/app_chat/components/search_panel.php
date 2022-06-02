<div class="chat-msg-search">
    <div class="chat-msg-search-form">
        <?php
        echo form_tag('chat_messages_seach', url_for($caht_action_url, '&action=search')) . input_hidden_tag(
                'assigned_to',
                $assigned_to
            ) ?>
        <?php
        echo input_tag(
            'keywords',
            '',
            ['placeholder' => TEXT_SEARCH, 'class' => 'form-control input-large', 'style' => 'display:inline']
        ) ?>
        <?php
        echo submit_tag(TEXT_BUTTON_SEARCH) ?>
        <?php
        echo '<button type="button" class="btn btn-default btn-chat-search-cancel">' . TEXT_BUTTON_CANCEL . '</button>' ?>
        </form>
    </div>
    <div class="chat-msg-search-result">
    </div>
</div>

<script>
    $('.btn-chat-search-messages').click(function () {
        $('.chat-msg-search').show();
        $('.chat-msg-content').hide();
        $('.chat-msg-footer').hide();
        return false;
    })

    $('.btn-chat-search-cancel').click(function () {
        $('.chat-msg-search').hide();
        $('.chat-msg-content').show();
        $('.chat-msg-footer').show();
        return false;
    })

    $('#chat_messages_seach').submit(function () {

        var obj = $(this);
        $('.chat-msg-search-result').html('<div class="fa-ajax-loader fa fa-spinner fa-spin"></div>');
        $('.chat-msg-search-result').load(obj.attr('action'), obj.serializeArray());
        return false;
    })
</script>