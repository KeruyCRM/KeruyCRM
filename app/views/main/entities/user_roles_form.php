<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_HEADING_VALUE_INFO) ?>

<?= \Helpers\Html::form_tag(
    'fields_form',
    \Helpers\Urls::url_for(
        'main/entities/user_roles/save',
        'fields_id=' . \K::$fw->GET['fields_id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . (isset(\K::$fw->GET['id']) ? '&id=' . \K::$fw->GET['id'] : '')
    ),
    ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']
) ?>
<div class="modal-body">
    <div class="form-body">
        <div class="form-group">
            <label class="col-md-4 control-label" for="name"><?= \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_CHOICES_NAME_INFO
                ) . \K::$fw->TEXT_NAME ?></label>
            <div class="col-md-8">
                <?= \Helpers\Html::input_tag(
                    'name',
                    \K::$fw->obj['name'],
                    ['class' => 'form-control input-large required autofocus']
                ) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label" for="sort_order"><?= \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_CHOICES_SORT_ORDER_INFO
                ) . \K::$fw->TEXT_SORT_ORDER ?></label>
            <div class="col-md-8">
                <?= \Helpers\Html::input_tag(
                    'sort_order',
                    \K::$fw->obj['sort_order'],
                    ['class' => 'form-control input-small']
                ) ?>
            </div>
        </div>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#fields_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });
    });
</script>