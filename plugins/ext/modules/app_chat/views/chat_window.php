<?php
if (IS_AJAX) {
    echo ajax_modal_template_header(TEXT_EXT_CHAT_DIALOGUES);
} else {
    echo '<h3 class="page-title">' . TEXT_EXT_CHAT_DIALOGUES . '</h3>';
}
?>

<div class="<?php
echo(IS_AJAX ? 'modal-body' : '') ?> chat-modal-body">
    <div class="ajax-modal-width-1100">
        <?php
        require(component_path('ext/app_chat/chat')); ?>
    </div>
</div>


<div class="modal-footer chat-modal-footer">

</div>


<script>
    jQuery(document).ready(function () {
        appHandleUniform()
    });
</script>