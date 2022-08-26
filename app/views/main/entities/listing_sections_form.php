<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_INFO) ?>

<?= \Helpers\Html::form_tag(
    'entities_form',
    \Helpers\Urls::url_for(
        'main/entities/listing_sections/save',
        (isset(\K::$fw->GET['id']) ? '&id=' . \K::$fw->GET['id'] : '') . '&listing_types_id=' . \K::$fw->GET['listing_types_id'] . '&entities_id=' . \K::$fw->GET['entities_id']
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">
        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?= \K::$fw->TEXT_TITLE ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag('name', \K::$fw->obj['name'], ['class' => 'form-control input-large']) ?>
                <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_NOT_REQUIRED_FIELD) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="fields"><?= \K::$fw->TEXT_FIELDS ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'fields[]',
                    \K::$fw->choices,
                    \K::$fw->obj['fields'],
                    [
                        'class' => 'form-control input-xlarge chosen-select chosen-sortable required',
                        'chosen_order' => \K::$fw->obj['fields'],
                        'multiple' => 'multiple'
                    ]
                ) ?>
                <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_SORT_ITEMS_IN_LIST) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="display_as"><?= \K::$fw->TEXT_DISPLAY_AS ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'display_as',
                    \Models\Main\Listing_types::get_sections_display_choices(),
                    \K::$fw->obj['display_as'],
                    ['class' => 'form-control input-small']
                ) ?>
            </div>
        </div>
        <div class="form-group" id="is-heading-container">
            <label class="col-md-3 control-label"
                   for="display_field_names"><?= \K::$fw->TEXT_DISPLAY_FIELD_NAMES ?></label>
            <div class="col-md-9">
                <div class="checkbox-list"><label class="checkbox-inline"><?= \Helpers\Html::input_checkbox_tag(
                            'display_field_names', '1', ['checked' => \K::$fw->obj['display_field_names']]
                        ) ?></label></div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="text_align"><?= \K::$fw->TEXT_ALIGN ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'text_align',
                    \Models\Main\Listing_types::get_sections_align_choices(),
                    \K::$fw->obj['text_align'],
                    ['class' => 'form-control input-medium']
                ) ?>
            </div>
        </div>
        <?php
        if (\K::$fw->listing_types_info['type'] == 'list'): ?>
            <div class="form-group">
                <label class="col-md-3 control-label" for="sort_order"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_SECTION_WIDTH_TIP
                    ) . \K::$fw->TEXT_WIDTH ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::input_tag('width', \K::$fw->obj['width'], ['class' => 'form-control input-small']
                    ) ?>
                </div>
            </div>
        <?php
        endif ?>
        <div class="form-group">
            <label class="col-md-3 control-label" for="sort_order"><?= \K::$fw->TEXT_SORT_ORDER ?></label>
            <div class="col-md-9">
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
        $('#entities_form').validate({
            ignore: '',
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });
    });
</script>