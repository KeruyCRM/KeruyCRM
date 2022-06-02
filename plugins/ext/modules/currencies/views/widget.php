<?php
echo ajax_modal_template_header(TEXT_EXT_EXCHANGE_RATES) ?>

<?php
echo form_tag(
    'currencies_form',
    url_for('ext/currencies/currencies', 'action=save_widget' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">


        <div class="form-group">
            <label class="col-md-3 control-label" for="name"></label>
            <div class="col-md-9">
                <p class="form-control-static"><?php
                    echo TEXT_EXT_EXCHANGE_RATES_INFO ?></p>
            </div>
        </div>


        <div class="form-group">
            <label class="col-md-3 control-label" for="users_groups"><?php
                echo TEXT_USERS_GROUPS ?></label>
            <div class="col-md-9">
                <div class="checkbox-list"><label class="checkbox-inline"><?php
                        echo select_checkboxes_tag(
                            'users_groups',
                            access_groups::get_choices(),
                            CFG_CURRENCIES_WIDGET_USERS_GROUPS
                        ) ?></label></div>
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