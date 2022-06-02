<div class="chat-msg-header">
    <div class="chat-msg-header-user"><?php
        echo TEXT_EXT_CHAT_CONVERSATION_INFO; ?></div>
</div>


<?php
echo form_tag(
    'chat_sending_conversation_form',
    url_for('ext/app_chat/conversation', 'action=save' . (isset($_GET['id']) ? '&id=' . (int)$_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="form-body">

    <div class="form-group">
        <label class="col-md-3 control-label" for="name"><?php
            echo TEXT_NAME ?></label>
        <div class="col-md-9">
            <?php
            echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="description"><?php
            echo TEXT_DESCRIPTION ?></label>
        <div class="col-md-9">
            <?php
            echo input_tag('description', $obj['description'], ['class' => 'form-control input-large']) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="description"><?php
            echo TEXT_MENU_ICON_TITLE ?></label>
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-6">
                    <?php
                    echo input_tag('menu_icon', $obj['menu_icon'], ['class' => 'form-control input-medium']) ?>
                </div>
                <div class="col-md-6">
                    <?php
                    $field = '
		              <div class="input-group input-small color colorpicker-default" data-color="' . (strlen(
                            $obj['menu_icon_color']
                        ) ? $obj['menu_icon_color'] : '#4c4c4c') . '" >
		          	   ' . input_tag(
                            'menu_icon_color',
                            (strlen($obj['menu_icon_color']) ? $obj['menu_icon_color'] : '#4c4c4c'),
                            ['class' => 'form-control input-small']
                        ) . '
		                <span class="input-group-btn">
		          				<button class="btn btn-default" type="button">&nbsp;</button>
		          			</span>
		          		</div>';

                    echo $field;
                    ?>
                </div>
            </div>
            <?php
            echo tooltip_text(TEXT_MENU_ICON_TITLE_TOOLTIP) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="description"><?php
            echo TEXT_USERS ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'assigned_to[]',
                $app_chat->get_users_choices(),
                $obj['assigned_to'],
                ['class' => 'form-control chosen-select required', 'multiple' => 'multiple']
            ) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"></label>
        <div class="col-md-9">
            <?php
            echo submit_tag(TEXT_BUTTON_SAVE); ?>
        </div>
    </div>

</div>
</form>


<script>
    $(function () {

        appHandleUniform();

        $('#chat_sending_conversation_form').validate({
            ignore: '',
            submitHandler: function (form) {
                var obj = $('#chat_sending_conversation_form');

                $.ajax({
                    type: 'POST',
                    url: obj.attr('action'),
                    data: obj.serializeArray()
                }).done(function (conversations_id) {

                    $('#chat_messages').html('<div class="fa-ajax-loader fa fa-spinner fa-spin"></div>');

                    $('#chat_messages').load('<?php echo url_for(
                        'ext/app_chat/conversation_messages'
                    )?>', {assigned_to: conversations_id}, function () {
                        $('#chat-users-list').load('<?php echo url_for(
                            'ext/app_chat/chat',
                            'action=render_users_list'
                        ) ?>');
                    })

                })

                return false;
            }
        });

    })
</script>
