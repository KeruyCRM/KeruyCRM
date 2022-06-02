<?php
echo ajax_modal_template_header(TEXT_FILTERS) ?>

<?php
echo form_tag(
    'filters_form',
    url_for(
        'ext/mail_integration/entities_rules',
        'action=save&account_entities_id=' . $accounts_entities['id'] . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">

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
                echo TEXT_EXT_AND_HAS_WORDS ?></label>
            <div class="col-md-8">
                <?php
                echo input_tag('has_words', $obj['has_words'], ['class' => 'form-control input-xlarge']) ?>
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
                return true;
            }
        });
    });
</script>