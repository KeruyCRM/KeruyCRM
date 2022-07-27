<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_RULE_FOR_FIELD) ?>

<?= \Helpers\Html::form_tag(
    'rules_form',
    \Helpers\Urls::url_for(
        'main/access_rules/rules/save',
        'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id'] . (isset(\K::$fw->GET['id']) ? '&id=' . \K::$fw->GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body ajax-modal-width-790">
        <div class="form-group">
            <label class="col-md-4 control-label" for="name"><?= \K::$fw->TEXT_SELECT_FIELD_VALUES ?></label>
            <div class="col-md-8">
                <?= \Helpers\Html::select_tag(
                    'choices[]',
                    \K::$fw->choices,
                    \K::$fw->obj['choices'],
                    ['class' => 'form-control input-xlarge chosen-select required', 'multiple' => 'multiple']
                ) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label" for="name"><?= \K::$fw->TEXT_USERS_GROUPS ?></label>
            <div class="col-md-8">
                <?= \Helpers\Html::select_tag(
                    'users_groups[]',
                    \K::$fw->users_groups,
                    \K::$fw->obj['users_groups'],
                    ['class' => 'form-control input-xlarge chosen-select required', 'multiple' => 'multiple']
                ) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label" for="name"><?= \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_ACCESS_RULES_SELECT_ACCESS
                ) . \K::$fw->TEXT_ACCESS ?></label>
            <div class="col-md-8">
                <?= \Helpers\Html::select_tag(
                    'access_schema[]',
                    \K::$fw->access_schema,
                    \K::$fw->obj['access_schema'],
                    ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                ) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label" for="name"><?= \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_ACCESS_RULES_FIELDS_VIEW_ONLY_ACCESS
                ) . \K::$fw->TEXT_VIEW_ONLY ?></label>
            <div class="col-md-8">
                <?= \Helpers\Html::select_tag(
                    'fields_view_only_access[]',
                    \K::$fw->fields_view_only_access,
                    \K::$fw->obj['fields_view_only_access'],
                    ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                ) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label" for="name"><?= \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_USE_DEFAULT_IF_NOT_SELECTED
                ) . \K::$fw->TEXT_NAV_COMMENTS_ACCESS ?></label>
            <div class="col-md-8">
                <?= \Helpers\Html::select_tag(
                    'comments_access_schema',
                    \K::$fw->comments_access_schema,
                    str_replace(',', '_', \K::$fw->obj['comments_access_schema']),
                    ['class' => 'form-control input-large']
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
            ignore: '',
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });
    });
</script>