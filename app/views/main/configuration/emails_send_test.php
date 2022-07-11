<h3 class="page-title"><?php
    echo TEXT_BUTTON_SEND_TEST_EMAIL ?></h3>

<p><?php
    echo sprintf(TEXT_SEND_TEST_EMAIL_INFO, TEXT_TEST_EMAIL_SUBJECT) ?></p>

<?php
echo form_tag('cfg', url_for('configuration/emails_send_test', 'action=send'), ['class' => 'form-horizontal']) ?>

<div class="form-body">
    <div class="form-group">
        <label class="col-md-2 control-label"><?php
            echo TEXT_EMAIL ?></label>
        <div class="col-md-10">
            <?php
            echo input_tag(
                'send_to',
                $_GET['send_to'] ?? $app_user['email'],
                ['class' => 'form-control input-xlarge required email']
            ); ?>
        </div>
    </div>

    <?php
    echo submit_tag(TEXT_BUTTON_SEND) ?>

</div>
</form>

<script>
    $('#cfg').validate()
</script>
