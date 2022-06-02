<?php
echo ajax_modal_template_header(TEXT_FILTERS) ?>

<?php
echo form_tag(
    'filters_form',
    url_for('ext/mail/filters', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">

        <div class="form-group">
            <label class="col-md-4 control-label" for="accounts_id"><?php
                echo TEXT_EXT_MAIL_ACCOUNT ?></label>
            <div class="col-md-8">
                <?php
                echo select_tag(
                    'accounts_id',
                    mail_accounts::get_choices_by_user(),
                    $obj['accounts_id'],
                    ['class' => 'form-control input-xlarge required']
                ) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="from_email"><?php
                echo TEXT_EXT_EMAIL_FROM ?></label>
            <div class="col-md-8">
                <?php
                echo input_tag('from_email', $obj['from_email'], ['class' => 'form-control input-xlarge']) ?>
                <?php
                echo tooltip_text(TEXT_EXT_ENTER_EMAIL_OR_DOMAIN) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="has_words"><?php
                echo TEXT_EXT_HAS_WORDS ?></label>
            <div class="col-md-8">
                <?php
                echo input_tag('has_words', $obj['has_words'], ['class' => 'form-control input-xlarge']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="action"><?php
                echo TEXT_ACTION ?></label>
            <div class="col-md-8">
                <?php
                echo select_tag(
                    'action',
                    mail_filters::get_action_choices(),
                    $obj['action'],
                    ['class' => 'form-control input-large']
                ) ?>
            </div>
        </div>


    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

<script>
    $(function () {

        $('#filters_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });
    });
</script>