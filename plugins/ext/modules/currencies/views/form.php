<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'currencies_form',
    url_for('ext/currencies/currencies', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">

        <div class="form-group">
            <label class="col-md-3 control-label" for="is_default"><?php
                echo TEXT_IS_DEFAULT ?></label>
            <div class="col-md-9">
                <div class="checkbox-list"><label class="checkbox-inline"><?php
                        echo input_checkbox_tag('is_default', '1', ['checked' => $obj['is_default']]) ?></label></div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_TITLE ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('title', $obj['title'], ['class' => 'form-control input-medium required']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_EXT_CODE ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('code', $obj['code'], ['class' => 'form-control input-small required']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_EXT_SYMBOL ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('symbol', $obj['symbol'], ['class' => 'form-control input-small required']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_EXT_VALUE ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('value', $obj['value'], ['class' => 'form-control input-medium']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_SORT_ORDER ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-xsmall']) ?>
            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

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