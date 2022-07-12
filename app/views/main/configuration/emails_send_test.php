<h3 class="page-title"><?= \K::$fw->TEXT_BUTTON_SEND_TEST_EMAIL ?></h3>

<p><?= sprintf(\K::$fw->TEXT_SEND_TEST_EMAIL_INFO, \K::$fw->TEXT_TEST_EMAIL_SUBJECT) ?></p>

<?= \Helpers\Html::form_tag(
    'cfg',
    \Helpers\Urls::url_for('main/configuration/emails_send_test/send'),
    ['class' => 'form-horizontal']
) ?>

<div class="form-body">
    <div class="form-group">
        <label class="col-md-2 control-label"><?= \K::$fw->TEXT_EMAIL ?></label>
        <div class="col-md-10">
            <?= \Helpers\Html::input_tag(
                'send_to',
                \K::$fw->GET['send_to'] ?? \K::$fw->app_user['email'],
                ['class' => 'form-control input-xlarge required email']
            ); ?>
        </div>
    </div>

    <?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SEND) ?>

</div>
</form>

<script>
    $('#cfg').validate()
</script>
