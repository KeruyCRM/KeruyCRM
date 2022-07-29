<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?= \K::$fw->TEXT_INFO ?></h4>
</div>

<?= \Helpers\Html::form_tag(
    'menu_form',
    \Helpers\Urls::url_for('main/entities/menu/save', (isset(\K::$fw->GET['id']) ? 'id=' . \K::$fw->GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<?= \Helpers\Html::input_hidden_tag('parent_id', \K::$fw->obj['parent_id']) ?>
<div class="modal-body">
    <div class="form-body">
        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?= \K::$fw->TEXT_NAME ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'name',
                    \K::$fw->obj['name'],
                    ['class' => 'form-control input-medium required']
                ) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="icon"><?= \K::$fw->TEXT_MENU_ICON_TITLE ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag('icon', \K::$fw->obj['icon'], ['class' => 'form-control input-medium ']) ?>
                <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_MENU_ICON_TITLE_TOOLTIP) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_COLOR ?></label>
            <div class="col-md-9">
                <table>
                    <tr>
                        <td>
                            <?= \Helpers\Html::input_color('icon_color', \K::$fw->obj['icon_color']) ?>
                            <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_ICON) ?>
                        </td>
                        <td style="padding-left: 10px;">
                            <?= \Helpers\Html::input_color('bg_color', \K::$fw->obj['bg_color']) ?>
                            <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_BACKGROUND) ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <?php
        $choices = [
            'entity' => \K::$fw->TEXT_ENTITY,
            'url' => \K::$fw->TEXT_URL
        ];
        ?>

        <div class="form-group">
            <label class="col-md-3 control-label" for="type"><?= \K::$fw->TEXT_TYPE ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'type',
                    $choices,
                    \K::$fw->obj['type'],
                    ['class' => 'form-control input-medium']
                ) ?>
            </div>
        </div>
        <div class="form-group" form_display_rules="type:entity">
            <label class="col-md-3 control-label" for="is_default"><?= \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_SORT_ITEMS_IN_LIST
                ) . \K::$fw->TEXT_SELECT_ENTITIES ?></label>
            <div class="col-md-9">
                <div class="checkbox-list"><label class="checkbox-inline"><?= \Helpers\Html::select_tag(
                            'entities_list[]',
                            \Models\Main\Entities::get_choices(true),
                            \K::$fw->obj['entities_list'],
                            [
                                'class' => 'form-control input-xlarge chosen-select chosen-sortable',
                                'chosen_order' => \K::$fw->obj['entities_list'],
                                'multiple' => 'multiple'
                            ]
                        ) ?></label></div>
            </div>
        </div>
        <div class="form-group" form_display_rules="type:entity">
            <label class="col-md-3 control-label" for="is_default"><?= \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_SORT_ITEMS_IN_LIST
                ) . \K::$fw->TEXT_SELECT_REPORTS ?></label>
            <div class="col-md-9">
                <div class="checkbox-list"><label class="checkbox-inline"><?= \Helpers\Html::select_tag(
                            'reports_list[]',
                            \Models\Main\Entities_menu::get_reports_choices(),
                            \K::$fw->obj['reports_list'],
                            [
                                'class' => 'form-control input-xlarge chosen-select chosen-sortable',
                                'chosen_order' => \K::$fw->obj['reports_list'],
                                'multiple' => 'multiple'
                            ]
                        ) ?></label></div>
            </div>
        </div>
        <?php
        if (\Helpers\App::is_ext_installed()): ?>
            <div class="form-group" form_display_rules="type:entity">
                <label class="col-md-3 control-label" for="is_default"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_SORT_ITEMS_IN_LIST
                    ) . \K::$fw->TEXT_EXT_IPAGES ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list"><label class="checkbox-inline"><?= \Helpers\Html::select_tag(
                                'pages_list[]',
                                ipages::get_choices(),
                                \K::$fw->obj['pages_list'],
                                [
                                    'class' => 'form-control input-xlarge chosen-select chosen-sortable',
                                    'chosen_order' => \K::$fw->obj['reports_list'],
                                    'multiple' => 'multiple'
                                ]
                            ) ?></label></div>
                </div>
            </div>
        <?php
        endif ?>

        <div class="form-group" form_display_rules="type:url">
            <label class="col-md-3 control-label" for="name"><?= \K::$fw->TEXT_URL ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag('url', \K::$fw->obj['url'], ['class' => 'form-control required']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="sort_order"><?= \K::$fw->TEXT_SORT_ORDER ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'sort_order',
                    \K::$fw->obj['sort_order'],
                    ['class' => 'form-control input-small number']
                ) ?>
            </div>
        </div>

        <h3 class="form-section" form_display_rules="type:url"><?= \K::$fw->TEXT_ACCESS ?></h3>

        <div class="form-group" form_display_rules="type:url">
            <label class="col-md-3 control-label" for="users_groups"><?= \K::$fw->TEXT_USERS_GROUPS ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'users_groups[]',
                    \Models\Main\Access_groups::get_choices(),
                    \K::$fw->obj['users_groups'],
                    ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                ) ?>
            </div>
        </div>
        <div class="form-group" form_display_rules="type:url">
            <label class="col-md-3 control-label" for="assigned_to"><?= \K::$fw->TEXT_ASSIGNED_TO ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'assigned_to[]',
                    \Models\Main\Users\Users::get_choices(),
                    \K::$fw->obj['assigned_to'],
                    [
                        'class' => 'form-control input-xlarge chosen-select',
                        'multiple' => 'multiple',
                        'data-placeholder' => \K::$fw->TEXT_SELECT_SOME_VALUES
                    ]
                ); ?>
            </div>
        </div>

    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#menu_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });
    });
</script>