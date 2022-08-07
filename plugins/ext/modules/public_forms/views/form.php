<?php
echo ajax_modal_template_header(TEXT_EXT_PUBLIC_FORM) ?>

<?php
echo form_tag(
    'public_forms',
    url_for('ext/public_forms/public_forms', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body">


        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#configuration" data-toggle="tab"><?php
                    echo TEXT_EXT_FB_NOTIFICATIONS ?></a></li>
            <li><a href="#check_enquiry" data-toggle="tab"><?php
                    echo TEXT_EXT_PB_CHECK_ENQUIRY ?></a></li>
            <li><a href="#form_design" id="form_design_tab" data-toggle="tab"><?php
                    echo 'CSS' ?></a></li>
            <li><a href="#form_js_code" id="form_js_tab" data-toggle="tab"><?php
                    echo 'JS' ?></a></li>
            <li><a href="#note" data-toggle="tab"><?php
                    echo TEXT_NOTE ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_info">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_IS_ACTIVE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'is_active',
                            ['1' => TEXT_YES, '0' => TEXT_NO],
                            $obj['is_active'],
                            ['class' => 'form-control input-small']
                        ) ?>
                    </div>
                </div>

                <div class="form-group" form_display_rules="is_active:0">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_EXT_MESSAGE_TEXT ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag(
                            'inactive_message',
                            $obj['inactive_message'],
                            ['class' => 'form-control input-xlareg textarea-small']
                        ) ?>
                        <?php
                        echo tooltip_text(TEXT_DEFAULT . ': ' . TEXT_PAGE_NOT_FOUND_HEADING) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_NAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
                    </div>
                </div>

                <?php
                $choices = [];

                foreach (entities::get_tree(0, [], 0, [], [1]) as $v) {
                    $choices[$v['id']] = str_repeat('- ', $v['level']) . $v['name'];
                }
                ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="entities_id"><?php
                        echo TEXT_REPORT_ENTITY ?></label>
                    <div class="col-md-9"><?php
                        echo select_tag(
                            'entities_id',
                            $choices,
                            $obj['entities_id'],
                            ['class' => 'form-control input-large required']
                        ) ?>
                    </div>
                </div>

                <div id="parent_item_settings"></div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="page_title"><?php
                        echo TEXT_EXT_PAGE_TITLE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('page_title', $obj['page_title'], ['class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="description"><?php
                        echo TEXT_DESCRIPTION ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag('description', $obj['description'], ['class' => 'form-control editor']) ?>
                    </div>
                </div>

                <div id="available_fields"></div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="button_save_title"><?php
                        echo tooltip_icon(
                                TEXT_DEFAULT . ' "' . TEXT_EXT_PB_BUTTONS_SAVE_TITLE_DEFAULT . '"'
                            ) . TEXT_EXT_PB_BUTTONS_SAVE_TITLE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'button_save_title',
                            $obj['button_save_title'],
                            ['class' => 'form-control input-medium']
                        ) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="user_agreement"><?php
                        echo tooltip_icon(
                                TEXT_EXT_PB_USER_AGREEMENT_TEXT_INFO
                            ) . TEXT_EXT_PB_USER_AGREEMENT_TEXT ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag('user_agreement', $obj['user_agreement'], ['class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="successful_sending_message"><?php
                        echo tooltip_icon(
                                TEXT_EXT_PB_SUCCESSFUL_SENDING_MESSAGE_INFO
                            ) . TEXT_EXT_PB_SUCCESSFUL_SENDING_MESSAGE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag(
                            'successful_sending_message',
                            $obj['successful_sending_message'],
                            ['class' => 'form-control editor']
                        ) ?>
                    </div>
                </div>

                <?php
                $choices = [];
                $choices['stay_on_form'] = TEXT_EXT_STAY_ON_FORM_PAGE;
                $choices['display_success_text'] = TEXT_EXT_DISPLAY_TEXT_SUCCESSFUL_ONLY;
                $choices['goto'] = TEXT_EXT_GOTO_PAGE;
                ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="successful_sending_message"><?php
                        echo TEXT_EXT_ACTION_AFTER_FORM_SUBMISSION ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'after_submit_action',
                            $choices,
                            $obj['after_submit_action'],
                            ['class' => 'form-control input-xlarge', 'onChange' => 'check_after_submit_action()']
                        ) ?>
                        <div class="help-block"><?php
                            echo input_tag(
                                'after_submit_redirect',
                                $obj['after_submit_redirect'],
                                ['class' => 'form-control input-xlarge', 'placeholder' => TEXT_URL_HEADING]
                            ) ?></div>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="configuration">

                <h3 class="form-section form-section-desc"><?php
                    echo TEXT_EXT_PB_ADMINISTRATOR ?></h3>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="admin_name"><?php
                        echo TEXT_NAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('admin_name', $obj['admin_name'], ['class' => 'form-control input-large']); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="admin_email"><?php
                        echo TEXT_EMAIL ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('admin_email', $obj['admin_email'], ['class' => 'form-control input-large']); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="admin_notification"><?php
                        echo tooltip_icon(
                                TEXT_EXT_PB_ADMIN_NOTIFICATION_INFO
                            ) . TEXT_EXT_PB_ADMIN_NOTIFICATION ?></label>
                    <div class="col-md-9">
                        <div class="form-control-static"><?php
                            echo input_checkbox_tag('admin_notification', 1, ['checked' => $obj['admin_notification']]
                            ); ?></div>
                    </div>
                </div>

                <div id="client_fields"></div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="notify_message_title"><?php
                        echo TEXT_EXT_PB_NOTIFY_MESSAGE_TITLE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'customer_message_title',
                            $obj['customer_message_title'],
                            ['class' => 'form-control']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="customer_message"><?php
                        echo tooltip_icon(TEXT_EXT_PB_CUSTOMER_MESSAGE_INFO) . TEXT_EXT_PB_CUSTOMER_MESSAGE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag('customer_message', $obj['customer_message'], ['class' => 'form-control']) ?>
                    </div>
                </div>


            </div>

            <div class="tab-pane fade" id="check_enquiry">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="check_enquiry"><?php
                        echo tooltip_icon(
                                TEXT_EXT_PB_ALLOW_CHECK_ENQUIRY_INFO
                            ) . TEXT_EXT_PB_ALLOW_CHECK_ENQUIRY ?></label>
                    <div class="col-md-9">
                        <div class="form-control-static"><?php
                            echo input_checkbox_tag('check_enquiry', 1, ['checked' => $obj['check_enquiry']]); ?></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="disable_submit_form"><?php
                        echo tooltip_icon(TEXT_EXT_DISABLE_SUBMIT_PB_INFO) . TEXT_EXT_DISABLE_SUBMIT_PB ?></label>
                    <div class="col-md-9">
                        <div class="form-control-static"><?php
                            echo input_checkbox_tag('disable_submit_form', 1, ['checked' => $obj['disable_submit_form']]
                            ); ?></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="check_page_title"><?php
                        echo TEXT_EXT_PAGE_TITLE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('check_page_title', $obj['check_page_title'], ['class' => 'form-control']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="check_page_description"><?php
                        echo TEXT_DESCRIPTION ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag(
                            'check_page_description',
                            $obj['check_page_description'],
                            ['class' => 'form-control editor']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="check_button_title"><?php
                        echo tooltip_icon(
                                TEXT_DEFAULT . ' "' . TEXT_EXT_PB_BUTTONS_CHECK_TITLE_DEFAULT . '"'
                            ) . TEXT_EXT_PB_BUTTONS_CHECK_TITLE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'check_button_title',
                            $obj['check_button_title'],
                            ['class' => 'form-control input-medium']
                        ) ?>
                    </div>
                </div>

                <div id="check_page_available_fields"></div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="notify_message_title"><?php
                        echo TEXT_EXT_PB_NOTIFY_MESSAGE_TITLE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('notify_message_title', $obj['notify_message_title'], ['class' => 'form-control']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="notify_message_body"><?php
                        echo tooltip_icon(
                                TEXT_EXT_PB_CUSTOMER_MESSAGE_INFO
                            ) . TEXT_EXT_PB_NOTIFY_MESSAGE_BODY ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag(
                            'notify_message_body',
                            $obj['notify_message_body'],
                            ['class' => 'form-control']
                        ) ?>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="form_design">
                <p>
                    <?php
                    echo TEXT_EXT_FB_DESIGN_INFO ?>
                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#css_example"
                       href="#css_example"><?php
                        echo TEXT_MORE_INFO ?></a>
                <div id="css_example" class="collapse" style="height: auto;">
                    <?php
                    $html = '
/*link color*/
a{
	color: #01bad8;
}

/*iframe background color*/
.public-layout{
	background: #F6F5F3 !important;
}

/*form background color*/
.content-form{
	background: #F6F5F3 !important;
}

/*input styles*/
.form-control{
	border-radius: 0px;
}

/*form section style*/
.form-section{
	color: #000;
}

/*form text labels */
.form-horizontal .control-label{
	text-align: left;
}

/*general button*/
.btn{
	border-radius: 0px;
}

/*primary button colors*/
.btn-primary{
	background-color: #01bad8;
	border-color: #01bad8;
}

.btn-primary:hover{
	background-color: #00abc7;
	border-color: #00abc7;	
}

/*form footer*/
.modal-footer{
	text-align: center;
}';

                    echo textarea_tag('form_css_example', $html, ['class' => 'form-control', 'readonly' => 'readonly']);
                    ?>
                </div>
                </p>

                <div class="form-group">
                    <div class="col-md-12">
                        <?php
                        echo textarea_tag(
                            'form_css',
                            $obj['form_css'],
                            ['class' => 'form-control', 'style' => 'height: 450px;']
                        ) ?>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="form_js_code">
                <p><?php
                    echo TEXT_JAVASCRIPT_IN_FORM_INFO ?></p>
                <div class="form-group">
                    <div class="col-md-12">
                        <?php
                        echo textarea_tag('form_js', $obj['form_js'], ['class' => 'form-control']) ?>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="note">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="notes"><?php
                        echo TEXT_ADMINISTRATOR_NOTE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag('notes', $obj['notes'], ['class' => 'form-control']) ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<?php
echo app_include_codemirror(['javascript', 'css']) ?>

<script>
    $(function () {

        $('#public_forms').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });

//add codemirror
        $('#form_design_tab').click(function () {
            if (!$(this).hasClass('active-codemirror')) {
                setTimeout(function () {
                    var myCodeMirror1 = CodeMirror.fromTextArea(document.getElementById('form_css'), {
                        lineNumbers: true,
                        mode: 'css',
                        lineWrapping: true,
                        matchBrackets: true
                    });
                }, 300);

                $(this).addClass('active-codemirror')
            }
        })

        $('#form_js_tab').click(function () {
            if (!$(this).hasClass('active-codemirror')) {
                setTimeout(function () {
                    var myCodeMirror2 = CodeMirror.fromTextArea(document.getElementById('form_js'), {
                        lineNumbers: true,
                        mode: 'javascript',
                        lineWrapping: true,
                        matchBrackets: true
                    });
                }, 300);

                $(this).addClass('active-codemirror')
            }
        })

        check_after_submit_action();

        load_available_fields();
        load_check_page_available_fields();
        load_client_fields();
        load_parent_item_settings()

        $('#entities_id').change(function () {
            load_available_fields();
            load_check_page_available_fields();
            load_client_fields();
            load_parent_item_settings()
        })
    });


    function load_available_fields() {
        $('#available_fields').html('');
        $('#available_fields').addClass('ajax-loading');
        $('#available_fields').load('<?php echo url_for(
            "ext/public_forms/public_forms",
            "action=get_available_fields&id=" . $obj['id']
        ) ?>', {entities_id: $('#entities_id').val()}, function () {
            $('#available_fields').removeClass('ajax-loading');
        })
    }

    function load_client_fields() {
        $('#client_fields').html('');
        $('#client_fields').addClass('ajax-loading');
        $('#client_fields').load('<?php echo url_for(
            "ext/public_forms/public_forms",
            "action=get_client_fields&id=" . $obj['id']
        ) ?>', {entities_id: $('#entities_id').val()}, function () {
            $('#client_fields').removeClass('ajax-loading');
        })
    }

    function load_check_page_available_fields() {
        $('#check_page_available_fields').html('');
        $('#check_page_available_fields').addClass('ajax-loading');
        $('#check_page_available_fields').load('<?php echo url_for(
            "ext/public_forms/public_forms",
            "action=get_check_page_available_fields&id=" . $obj['id']
        ) ?>', {entities_id: $('#entities_id').val()}, function () {
            $('#check_page_available_fields').removeClass('ajax-loading');
        })
    }

    function load_parent_item_settings() {
        $('#parent_item_settings').html('');
        $('#parent_item_settings').addClass('ajax-loading');
        $('#parent_item_settings').load('<?php echo url_for(
            "ext/public_forms/public_forms",
            "action=get_parent_item_settings&id=" . $obj['id']
        ) ?>', {entities_id: $('#entities_id').val()}, function () {
            $('#parent_item_settings').removeClass('ajax-loading');
            appHandleUniform();
            check_parent_item_label()
        })
    }

    function check_parent_item_label() {
        if ($('#parent_item_id').val() > 0) {
            $('#hide_parent_item_label').show();
        } else {
            $('#hide_parent_item_label').hide();
        }
    }

    function check_after_submit_action() {
        if ($('#after_submit_action').val() == 'goto') {
            $('#after_submit_redirect').show();
        } else {
            $('#after_submit_redirect').hide();
        }
    }
</script>   


