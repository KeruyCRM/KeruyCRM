<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for('ext/mail_integration/accounts', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">


        <div class="form-group">
            <label class="col-md-3 control-label" for="is_active"><?php
                echo TEXT_IS_ACTIVE ?></label>
            <div class="col-md-9">
                <p class="form-control-static"><?php
                    echo input_checkbox_tag(
                        'is_active',
                        $obj['is_active'],
                        ['checked' => ($obj['is_active'] == 1 ? 'checked' : '')]
                    ) ?></p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_TITLE ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('name', $obj['name'], ['class' => 'form-control input-xlarge required']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="bg_color"><?php
                echo TEXT_BACKGROUND_COLOR ?></label>
            <div class="col-md-9">
                <div class="input-group input-small color colorpicker-default" data-color="<?php
                echo(strlen($obj['bg_color']) > 0 ? $obj['bg_color'] : '#ff0000') ?>">
                    <?php
                    echo input_tag('bg_color', $obj['bg_color'], ['class' => 'form-control input-small']) ?>
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button">&nbsp;</button>
                    </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="is_default"><?php
                echo TEXT_IS_DEFAULT ?></label>
            <div class="col-md-9">
                <div class="checkbox-list"><label class="checkbox-inline"><?php
                        echo input_checkbox_tag('is_default', '1', ['checked' => $obj['is_default']]) ?></label></div>
            </div>
        </div>

        <ul class="nav nav-tabs">
            <li class="active"><a href="#receiving_mail" data-toggle="tab"><?php
                    echo TEXT_EXT_RECEIVING_MAIL ?></a></li>
            <li><a href="#sending_mail" data-toggle="tab"><?php
                    echo TEXT_EXT_SENDING_MAIL ?></a></li>
            <li><a href="#sending_autoreply" data-toggle="tab"><?php
                    echo TEXT_EXT_AUTO_REPLY ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="receiving_mail">


                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_EXT_IMAP_SERVER ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'imap_server',
                            $obj['imap_server'],
                            ['class' => 'form-control input-xlarge required']
                        ) ?>
                        <?php
                        echo tooltip_text(TEXT_EXT_IMAP_SERVER_INFO) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_EXT_MAILBOX ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('mailbox', $obj['mailbox'], ['class' => 'form-control input-small required']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_USERNAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('login', $obj['login'], ['class' => 'form-control input-large required']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="email"><?php
                        echo TEXT_EMAIL ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('email', $obj['email'], ['class' => 'form-control email input-large']); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_PASSWORD ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('password', $obj['password'], ['class' => 'form-control input-large required']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="delete_emails"><?php
                        echo TEXT_EXT_DELETE_EMAILS_FROM_SERVER ?></label>
                    <div class="col-md-9">
                        <p class="form-control-static"><?php
                            echo input_checkbox_tag(
                                'delete_emails',
                                $obj['delete_emails'],
                                ['checked' => ($obj['delete_emails'] == 1 ? 'checked' : '')]
                            ) ?></p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="not_group_by_subject"><?php
                        echo TEXT_EXT_DO_NOT_GROUP_BY_SUBJECT ?></label>
                    <div class="col-md-9">
                        <p class="form-control-static"><?php
                            echo input_checkbox_tag(
                                'not_group_by_subject',
                                $obj['not_group_by_subject'],
                                ['checked' => ($obj['not_group_by_subject'] == 1 ? 'checked' : '')]
                            ) ?></p>
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" id="sending_mail">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="use_smtp"><?php
                        echo TEXT_EMAIL_USE_SMTP ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'use_smtp',
                            ['1' => TEXT_YES, '0' => TEXT_NO],
                            $obj['use_smtp'],
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="smtp_server"><?php
                        echo TEXT_EMAIL_SMTP_SERVER ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('smtp_server', $obj['smtp_server'], ['class' => 'form-control input-large']); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="smtp_port"><?php
                        echo TEXT_EMAIL_SMTP_PORT ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('smtp_port', $obj['smtp_port'], ['class' => 'form-control input-small']); ?>
                    </div>
                </div>

                <?php
                $choices = [
                    '' => TEXT_NO,
                    'ssl' => 'SSL (port 465)',
                    'tls' => 'TLS (port 587)',
                ];
                ?>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="smtp_encryption"><?php
                        echo TEXT_EMAIL_SMTP_ENCRYPTION ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'smtp_encryption',
                            $choices,
                            $obj['smtp_encryption'],
                            ['class' => 'form-control input-medium']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="smtp_login"><?php
                        echo TEXT_EMAIL_SMTP_LOGIN ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('smtp_login', $obj['smtp_login'], ['class' => 'form-control input-large']); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="smtp_password"><?php
                        echo TEXT_EMAIL_SMTP_PASSWORD ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('smtp_password', $obj['smtp_password'], ['class' => 'form-control input-large']
                        ); ?>
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" id="sending_autoreply">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="send_autoreply"><?php
                        echo TEXT_EXT_SEND_AUTO_REPLY ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'send_autoreply',
                            ['0' => TEXT_NO, '1' => TEXT_YES],
                            $obj['send_autoreply'],
                            ['class' => 'form-control input-small']
                        ); ?>
                        <?php
                        echo tooltip_text(TEXT_EXT_AUTO_REPLY_INFO) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="autoreply_msg"><?php
                        echo TEXT_EXT_MESSAGE_TEXT ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag('autoreply_msg', $obj['autoreply_msg'], ['class' => 'editor']); ?>
                    </div>
                </div>

            </div>
        </div>


    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {

        $('#configuration_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });

    });
</script>  