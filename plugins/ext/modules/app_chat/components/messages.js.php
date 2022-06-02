<script>

    function chat_attachment_remove(id)
    {
        $('.attachment-row-' + id).hide();

        $.ajax({
        type:'POST',
        url: '<?php echo url_for("ext/app_chat/chat", "action=attachment_delete") ?>',
        data: {id:id}
    }).done(function(){
        $("#uploadifive_attachments_list").load("<?php echo url_for(
        'ext/app_chat/chat',
        'action=attachments_preview&token=' . $attachments_form_token
    ) ?>");
    })
    }

    $(function(){

    app_caht_selection_process = false;

    $('[data-hover="dropdown"]').dropdownHover();

    jQuery(window).resize();

    var chat = new app_chat();

    chat.scroll_msg_content();

    is_app_caht_timer = true;

    var form_token = $('#chat-msg-form').attr('form-token');

//start timer  
    app_caht_timer = setInterval(function(){
    $.ajax({
    type:'POST',
    url:'<?php echo url_for($caht_action_url, 'action=get_messages'); ?>',
    data:{assigned_to:<?php echo $assigned_to ?>}})
    .done(function(data){

    $(".chat-msg-content").append(data);

    if(data.length>0)
{
    chat.scroll_msg_content();
}

})
},<?php echo $app_chat->messages_delay ?>);

//reset timer  
    $('[data-dismiss="modal"]').click(function(){
    clearInterval(app_caht_timer)
    clearInterval(app_caht_users_timer)
    is_app_caht_timer = false;
})

//submit chat form
    $('#chat-msg-form').submit(function(){

    //reset msg cookie
    setCookie(form_token,'',1)

    message = $('#chat_message_text',this).html()

    message_text = $('#chat_message_text',this).text()

    if($('#chat_message_attachments').length)
{
    attachments = $('#chat_message_attachments').val();
}
    else
{
    attachments = '';
}

    if(message_text.length>0 || attachments.length>0)
{
    $('#chat_message_text',this).html('');
    $('#chat_message').val(message);
    $.ajax({type:'POST', url:$(this).attr('action'), data:$(this).serializeArray()}).done(function(){})

    //reset attachments list
    $('#uploadifive_attachments_list').html('');
}

    return false;
})

//send msg cfg	
    <?php echo($app_users_cfg->get(
    'chat_sending_settings',
    'enter'
) == 'enter' ? 'chat.send_msg_by_enter();' : 'chat.send_msg_by_ctrl_enter()') ?>

//messages pager
    var chat_msg_current_page = 1;
    var chat_msg_number_of_pages = $('#chat_msg_number_of_pages').val();
    var chat_msg_pager_skip_id = $('#chat_msg_pager_skip_id').val()

    $('.chat-msg-content').scroll(function() {
    height = $('.chat-msg-content').scrollTop();
    //console.log(height);

    if(height==0 && chat_msg_current_page<chat_msg_number_of_pages)
{
    chat_msg_current_page = chat_msg_current_page+1;

    $.ajax({
    type:'POST',
    url:'<?php echo url_for(
    $caht_action_url,
    'action=get_previous_messages'
); ?>&page='+chat_msg_current_page+'&skip_id='+chat_msg_pager_skip_id,
    data:{assigned_to:<?php echo $assigned_to ?>}})
    .done(function(data){
    $(".chat-msg-content").prepend(data);

    //get page height
    height = $('.chat-msg-page-'+chat_msg_current_page).height();

    //scroll to page height
    $('.chat-msg-content').scrollTop(height);

})
}
})

//handle msg to cookie		
    $('#chat_message_text').keyup(function(){
    msg = $(this).html();
    //alert(form_token+' '+msg)
    setCookie(form_token,msg,1)
})

    msg_cookie = getCookie(form_token);
    if(msg_cookie!='')
{
    $('#chat_message_text').html(msg_cookie)
}

//paste text only
    $('[contenteditable]').on('paste', function(e) {
    e.preventDefault();
    var text = '';
    if (e.clipboardData || e.originalEvent.clipboardData) {
    text = (e.originalEvent || e).clipboardData.getData('text/plain');
} else if (window.clipboardData) {
    text = window.clipboardData.getData('Text');
}
    if (document.queryCommandSupported('insertText')) {
    document.execCommand('insertText', false, text);
} else {
    document.execCommand('paste', false, text);
}
});


})
</script>