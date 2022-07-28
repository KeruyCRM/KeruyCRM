<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_RULE_FOR_FIELD) ?>

<?= \Helpers\Html::form_tag(
    'rules_form',
    \Helpers\Urls::url_for(
        'main/access_rules/fields/save',
        'entities_id=' . \K::$fw->GET['entities_id'] . (isset(\K::$fw->GET['id']) ? '&id=' . \K::$fw->GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body ajax-modal-width-790">
        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?= \K::$fw->TEXT_SELECT_FIELD ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'fields_id',
                    \K::$fw->choices,
                    \K::$fw->obj['fields_id'],
                    ['class' => 'form-control input-large required ', 'onChange' => 'get_fields_choices()']
                ) ?>
                <?= \Helpers\App::tooltip_text(
                    \K::$fw->TEXT_AVAILABLE_FIELDS . ': ' . \K::$fw->TEXT_FIELDTYPE_DROPDOWN_TITLE . ', ' . \K::$fw->TEXT_FIELDTYPE_RADIOBOXES_TITLE . ', ' . \K::$fw->TEXT_FIELDTYPE_AUTOSTATUS_TITLE
                ) ?>
            </div>
        </div>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#rules_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });
    });
</script>