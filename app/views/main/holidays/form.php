<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_INFO) ?>

<?= \Helpers\Html::form_tag(
    'holidays_form',
    \Helpers\Urls::url_for('main/holidays/holidays/save', (\K::$fw->GET['id'] ? 'id=' . \K::$fw->GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?= \K::$fw->TEXT_NAME ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'name',
                    \K::$fw->obj['name'],
                    ['class' => 'form-control input-large required']
                ) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="start_date"><?= \K::$fw->TEXT_START_DATE ?></label>
            <div class="col-md-9">
                <div class="input-group input-medium date datepicker">
                    <?= \Helpers\Html::input_tag(
                        'start_date',
                        \K::$fw->obj['start_date'],
                        ['class' => 'form-control required']
                    ) ?>
                    <span class="input-group-btn">
            <button class="btn btn-default date-set" type="button"><i class="fa fa-calendar"></i></button>
          </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="end_date"><?= \K::$fw->TEXT_END_DATE ?></label>
            <div class="col-md-9">
                <div class="input-group input-medium date datepicker">
                    <?= \Helpers\Html::input_tag(
                        'end_date',
                        \K::$fw->obj['end_date'],
                        ['class' => 'form-control required']
                    ) ?>
                    <span class="input-group-btn">
            <button class="btn btn-default date-set" type="button"><i class="fa fa-calendar"></i></button>
          </span>
                </div>
            </div>
        </div>


    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#holidays_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });

    });
</script>  