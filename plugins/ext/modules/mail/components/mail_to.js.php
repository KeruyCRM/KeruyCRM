<style>
    #select2-mail_to-results{
    display:none;
}

    #select2-mail_to-results li:first-child{
    display:none;
}
</style>

<script>

    var input_cliced = false

    $(function() {

    $('#mail_form').validate({
        ignore: '',
        rules: {
            "body": {
                required: function (element) {
                    CKEDITOR_holders["body"].updateElement();
                    return true;
                }
            },
        },
        submitHandler: function (form) {
            app_prepare_modal_action_loading(form)

            if ($(form).attr('is_ajax') && $(form).attr('is_ajax') == 1) {
                $.ajax({
                    type: 'POST',
                    url: $(form).attr('action'),
                    data: $(form).serializeArray()
                }).done(function (msg) {
                    //alert(msg)
                    $('.form-body', form).html(msg)
                    $('.primary-modal-action-loading').hide()
                    $(window).resize()

                    if (msg.search('alert-success') != -1) {
                        setTimeout(function () {
                            $('#ajax-modal').modal('toggle')
                        }, 1500)
                    }
                })

                return false
            } else {
                return true
            }
        }

    });

//apply select2    
    var $mail_to = $("#mail_to").select2({
    tags: true,
    width: '100%',
    selectOnClose: true,
    dropdownParent: $('#ajax-modal'),
    tokenSeparators: [',', ' '],
    "language":{
    "noResults" : function () {return '';},
    "searching" : function () {return '';}
},
    ajax: {
    url: '<?php echo url_for('ext/mail/accounts', 'action=search_contacts') ?>',
    dataType: 'json',
    delay: 0,
    processResults: function (data) {
    //console.log(data);
    if(data['results'].length>=1)
{
    $('#select2-mail_to-results').show()
}
    else
{
    $('#select2-mail_to-results').hide()
}
    return data;
},
},

});

//check email once it's entered
    var $search_field = $('.select2-search__field');

    $('#mail_to').on('select2:select', function (e) {
    var data = e.params.data;
    email = data.id;
    email_list = $mail_to.val();

    if(email.indexOf('<')>-1)
{
    email = email.substr(email.indexOf('<') + 1).slice(0, -1);
}

    if(!is_valid_email(email))
{
    $('.select2').after('<div class="error select2-error"><?php echo TEXT_ERROR_REQUIRED_EMAIL ?></div>')

    var index = email_list.indexOf(email);

    if (index > -1) {
    email_list.splice(index, 1);
}

    $mail_to.val(email_list).trigger("change");

    $search_field.val(email).css('width', ($search_field.val().length+1*0.75)+'em');
}


    $search_field.keydown(function(){
    $('.select2-error').remove();
})

    //console.log(data.id);
    //console.log($mail_to.val())
});

//focus input field
    $('#ajax-modal').on('shown.bs.modal', function () {
    if($('#mail_form').attr('is_ajax')==1)
{
    $("#subject").focus()
}
    else
{
    $("#mail_to").select2('focus')
}
})

});
</script>