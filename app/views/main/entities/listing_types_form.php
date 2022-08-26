<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_INFO) ?>

<?= \Helpers\Html::form_tag(
    'entities_form',
    \Helpers\Urls::url_for(
        'main/entities/listing_types/save',
        (isset(\K::$fw->GET['id']) ? '&id=' . \K::$fw->GET['id'] : '') . '&entities_id=' . \K::$fw->GET['entities_id']
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body ajax-modal-width-790">
        <?php
        if (\K::$fw->obj['type'] == 'table') {
            echo \Helpers\Html::input_hidden_tag('is_active', 1);
        } else {
            ?>
            <div class="form-group" id="is-heading-container">
                <label class="col-md-3 control-label" for="is_active"><?= \K::$fw->TEXT_IS_ACTIVE ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list"><label class="checkbox-inline"><?= \Helpers\Html::input_checkbox_tag(
                                'is_active',
                                '1',
                                ['checked' => \K::$fw->obj['is_active']]
                            ) ?></label></div>
                </div>
            </div>
            <?php
        } ?>
        <?php
        if (\K::$fw->obj['type'] != 'mobile'): ?>
            <div class="form-group" id="is-heading-container">
                <label class="col-md-3 control-label" for="is_default"><?= \K::$fw->TEXT_IS_DEFAULT ?></label>
                <div class="col-md-9">
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
        <?php
        if (\K::$fw->obj['type'] == 'grid'): ?>
            <div class="form-group">
                <label class="col-md-3 control-label" for="sort_order"><?= \K::$fw->TEXT_WIDTH ?> (px)</label>
                <div class="col-md-9">
                    <?= \Helpers\Html::input_tag('width', \K::$fw->obj['width'], ['class' => 'form-control input-small']
                    ) ?>
                    <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_GRID_WIDTH_INFO) ?>
                </div>
            </div>

        <?php
        endif ?>
        <?php
        if (\K::$fw->obj['type'] == 'tree_table') {
            $settings = new \Tools\Settings(\K::$fw->obj['settings']);

            $fields_choices = [];
            $fields_query = \Models\Main\Fields::get_query(\K::$fw->GET['entities_id']);

            //while ($v = db_fetch_array($fields_query)) {
            foreach ($fields_query as $v) {
                $fields_choices[$v['id']] = \Models\Main\Fields_types::get_option(
                        $v['type'],
                        'name',
                        $v['name']
                    ) . ' [#' . $v['id'] . ']';
            }

            $chosen_order = is_array($settings->get('fields_in_listing')) ? implode(
                ',',
                $settings->get('fields_in_listing')
            ) : '';

            ?>
            <div class="form-group">
                <label class="col-md-3 control-label"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_SORT_ITEMS_IN_LIST
                    ) . \K::$fw->TEXT_FIELDS_IN_LISTING ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'settings[fields_in_listing][]',
                        $fields_choices,
                        $settings->get('fields_in_listing'),
                        [
                            'class' => 'form-control chosen-select chosen-sortable',
                            'chosen_order' => $chosen_order,
                            'multiple' => 'multiple'
                        ]
                    ) ?>
                </div>
            </div>
            <script>
                $("#settings_fields_in_listing").on("change", function (e) {
                    $("#settings_fields_in_listing-error").hide();
                });
            </script>
            <div class="form-group">
                <label class="col-md-3 control-label"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_HEADING_WIDTH_BASED_CONTENT_INFO
                    ) . \K::$fw->TEXT_HEADING_WIDTH_BASED_CONTENT ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'settings[heading_width_based_content]',
                        ['1' => \K::$fw->TEXT_YES, '0' => \K::$fw->TEXT_NO],
                        (int)$settings->get('heading_width_based_content'),
                        ['class' => 'form-control input-small']
                    ) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?= \K::$fw->TEXT_CHANGE_COL_WIDTH_IN_LISTING ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'settings[change_col_width_in_listing]',
                        ['1' => \K::$fw->TEXT_YES, '0' => \K::$fw->TEXT_NO],
                        (int)$settings->get('change_col_width_in_listing'),
                        ['class' => 'form-control input-small']
                    ) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_EDITABLE_FIELDS_IN_LISTING_INFO
                    ) . \K::$fw->TEXT_EDITABLE_FIELDS_IN_LISTING ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'settings[editable_fields_in_listing]',
                        ['1' => \K::$fw->TEXT_YES, '0' => \K::$fw->TEXT_NO],
                        (int)$settings->get('editable_fields_in_listing'),
                        ['class' => 'form-control input-small']
                    ) ?>
                </div>
            </div>
            <h3 class="form-section"><?= \K::$fw->TEXT_NAV_ITEM_PAGE_CONFIG ?></h3>

            <div class="form-group">
                <label class="col-md-3 control-label"><?= \K::$fw->TEXT_DISPLAY_NESTED_RECORDS ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'settings[display_nested_records]',
                        [
                            '' => '',
                            'left_column' => \K::$fw->TEXT_LEFT_COLUMN,
                            'right_column' => \K::$fw->TEXT_RIGHT_COLUMN
                        ],
                        $settings->get('display_nested_records'),
                        ['class' => 'form-control input-medium']
                    ) ?>
                </div>
            </div>
        <?php
        $chosen_order = is_array($settings->get('fields_in_listing_info')) ? implode(
            ',',
            $settings->get('fields_in_listing_info')
        ) : '';
        ?>
            <div class="form-group">
                <label class="col-md-3 control-label"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_SORT_ITEMS_IN_LIST
                    ) . \K::$fw->TEXT_FIELDS_IN_LISTING ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'settings[fields_in_listing_info][]',
                        $fields_choices,
                        $settings->get('fields_in_listing_info'),
                        [
                            'class' => 'form-control chosen-select chosen-sortable',
                            'chosen_order' => $chosen_order,
                            'multiple' => 'multiple'
                        ]
                    ) ?>
                </div>
            </div>
            <?php
        } ?>
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