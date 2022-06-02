<?php
echo ajax_modal_template_header(TEXT_EXT_UPDATE_CURRENCIES) ?>

<?php
echo form_tag(
    'currencies_form',
    url_for('ext/currencies/currencies', 'action=update' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">

        <?php
        $currencies = new currencies; ?>
        <div class="form-group">
            <label class="col-md-3 control-label" for="is_default"><?php
                echo TEXT_EXT_MODULE ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'module',
                    $currencies->get_modules(),
                    CFG_CURRENCIES_UPDATE_MODULE,
                    ['class' => 'form-control input-xlarge']
                ) ?>
                <?php
                echo tooltip_text(TEXT_EXT_CURRENCIES_MODULE_INFO . '<br>' . DIR_FS_CATALOG . 'cron/currencies.php') ?>
            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer(TEXT_UPDATE) ?>

</form>

<script>
    $(function () {
        $('#currencies_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });
    });
</script>  