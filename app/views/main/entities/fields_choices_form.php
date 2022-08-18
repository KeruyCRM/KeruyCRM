<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXTHEADING_VALUE_INFO) ?>

<?= \Helpers\Html::form_tag(
    'fields_form',
    \Helpers\Urls::url_for(
        'main/entities/fields_choices/save',
        (isset(\K::$fw->GET['id']) ? '&id=' . \K::$fw->GET['id'] : '')
    ),
    ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']
) ?>
<div class="modal-body">
    <div class="form-body">
        <?= \Helpers\Html::input_hidden_tag(
            'entities_id',
            \K::$fw->GET['entities_id']
        ) . \Helpers\Html::input_hidden_tag(
            'fields_id',
            \K::$fw->GET['fields_id']
        ) ?>
        <div class="form-group">
            <label class="col-md-4 control-label" for="is_active"><?= \K::$fw->TEXTIS_ACTIVE ?></label>
            <div class="col-md-8">
                <div class="checkbox-list"><label class="checkbox-inline"><?= \Helpers\Html::input_checkbox_tag(
                            'is_active',
                            '1',
                            ['checked' => \K::$fw->obj['is_active']]
                        ) ?></label></div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label" for="parent_id"><?= \Helpers\App::tooltip_icon(
                    \K::$fw->TEXTCHOICES_PARENT_INFO
                ) . \K::$fw->TEXTPARENT ?></label>
            <div class="col-md-8">
                <?= \Helpers\Html::select_tag(
                    'parent_id',
                    \Models\Main\Fields_choices::get_choices(\K::$fw->GET['fields_id']),
                    (\K::$fw->GET['parent_id'] ?? \K::$fw->obj['parent_id']),
                    ['class' => 'form-control input-medium']
                ) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label" for="name"><?= \Helpers\App::tooltip_icon(
                    \K::$fw->TEXTCHOICES_NAME_INFO
                ) . \K::$fw->TEXTNAME ?></label>
            <div class="col-md-8">
                <?= \Helpers\Html::input_tag(
                    'name',
                    \K::$fw->obj['name'],
                    ['class' => 'form-control input-large required autofocus']
                ) ?>
            </div>
        </div>
        <?php
        if (\K::$fw->fields_info['type'] != 'fieldtype_autostatus'): ?>
            <div class="form-group">
                <label class="col-md-4 control-label" for="is_default"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXTCHOICES_IS_DEFAULT_INFO
                    ) . \K::$fw->TEXTIS_DEFAULT ?></label>
                <div class="col-md-8">
                    <div class="checkbox-list"><label class="checkbox-inline"><?= \Helpers\Html::input_checkbox_tag(
                                'is_default',
                                '1',
                                ['checked' => \K::$fw->obj['is_default']]
                            ) ?></label>
                    </div>
                </div>
            </div>
        <?php
        endif ?>

        <div class="form-group">
            <label class="col-md-4 control-label" for="bg_color"><?= \Helpers\App::tooltip_icon(
                    \K::$fw->TEXTCHOICES_BACKGROUND_COLOR_INFO
                ) . \K::$fw->TEXTBACKGROUND_COLOR ?></label>
            <div class="col-md-8">
                <div class="input-group input-small color colorpicker-default"
                     data-color="<?= (strlen(\K::$fw->obj['bg_color']) > 0 ? \K::$fw->obj['bg_color'] : '#ff0000') ?>">
                    <?= \Helpers\Html::input_tag(
                        'bg_color',
                        \K::$fw->obj['bg_color'],
                        ['class' => 'form-control input-small']
                    ) ?>
                    <span class="input-group-btn">
  				<button class="btn btn-default" type="button">&nbsp;</button>
  			</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label" for="sort_order"><?= \Helpers\App::tooltip_icon(
                    \K::$fw->TEXTCHOICES_SORT_ORDER_INFO
                ) . \K::$fw->TEXTSORT_ORDER ?></label>
            <div class="col-md-8">
                <?= \Helpers\Html::input_tag(
                    'sort_order',
                    \K::$fw->obj['sort_order'],
                    ['class' => 'form-control input-small']
                ) ?>
            </div>
        </div>
        <?php
        if (\K::$fw->fields_info['type'] == 'fieldtype_image_map') {
            $cfg = new \Tools\Settings(\K::$fw->fields_info['configuration']);

            if (!strlen($cfg->get('use_image_field'))) {
                \K::view()->render(\Helpers\Urls::components_path('entities/choices_map_form'));
                //require(component_path('entities/choices_map_form'));
            }
        } else { ?>
            <div class="form-group">
                <label class="col-md-4 control-label" for="sort_order"><?= \K::$fw->TEXTVALUE ?></label>
                <div class="col-md-8">
                    <?= \Helpers\Html::input_tag(
                        'value',
                        \K::$fw->obj['value'],
                        ['class' => 'form-control input-small number']
                    ) ?>
                    <?= \Helpers\App::tooltip_text(\K::$fw->TEXTCHOICES_VALUE_INFO) ?>
                </div>
            </div>
            <?php
        } ?>
        <?php
        if (\K::$fw->fields_info['type'] == 'fieldtype_grouped_users'): ?>
            <div class="form-group">
                <label class="col-md-4 control-label" for="users"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXTCHOICES_USERS_INFO
                    ) . \K::$fw->TEXTUSERS_LIST ?></label>
                <div class="col-md-8">
                    <?php
                    $attributes = [
                        'class' => 'form-control chosen-select required',
                        'multiple' => 'multiple',
                        'data-placeholder' => \K::$fw->TEXTSELECT_SOME_VALUES
                    ];

                    echo \Helpers\Html::select_tag(
                        'users[]',
                        \Models\Main\Users\Users::get_choices(),
                        explode(',', \K::$fw->obj['users']),
                        $attributes
                    );
                    ?>
                </div>
            </div>
        <?php
        endif ?>
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