<?php

require(component_path('ext/app_chat/smiles_icons'));

$html = '<ul class=\'chat-smiles-list\'>';
foreach (array_merge($smiles, $smiles2, $smiles3) as $smile) {
    $html .= '<li onClick=\'insert_smile_icon(this)\'>' . $smile . '</li>';
}
$html .= '</ul>';

?>

<div id="chat_smiles" class="chat-smile-icon" data-container="body" data-toggle="popover" data-placement="top"
     data-content="<?php
     echo $html ?>">
    <i class="fa fa-smile-o"></i>
</div>

<script>

    var parentNode;
    var range;
    var selection;

    $(function () {

        $('#chat_message_text').focus();

        $('#chat_message_text').on('keyup mouseup', function (e) {

            selection = window.getSelection();
            range = selection.getRangeAt(0);
            parentNode = range.commonAncestorContainer.parentNode;
        });


        $('#chat_smiles').popover({
            trigger: 'manual',
            html: true,
            animation: false
        })
            .on('mouseenter', function () {
                var _this = this;
                $(this).popover('show');
                $('.popover').on('mouseleave', function () {
                    $(_this).popover('hide');
                });
            }).on('mouseleave', function () {
            var _this = this;
            setTimeout(function () {
                if (!$('.popover:hover').length) {
                    $(_this).popover('hide');
                }
            }, 300);
        });


        $('html').on('click', function (e) {

            if (!$(e.target).closest('.popover').length && !$(e.target).closest('#chat_smiles').length) {
                $('#chat_smiles').popover('hide')
            }
        });

    })

    function insert_smile_icon(icon) {
        icon = icon.innerText;
        insertTextAtCursor(icon)
    }

    function insertTextAtCursor(text) {

        if ($(parentNode).parents().is('#chat_message_text') || $(parentNode).is('#chat_message_text')) {
            var span = document.createElement('span');
            span.innerHTML = text;

            range.deleteContents();
            range.insertNode(span);
            //cursor at the last with this
            range.collapse(false);
            selection.removeAllRanges();
            selection.addRange(range);

        } else {
            msg_text = $("#chat_message_text").html()
            $("#chat_message_text").html(text + msg_text).focus()
        }
    }

</script>