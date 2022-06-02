<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'entities_form',
    url_for(
        'entities/listing_types',
        'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '') . '&entities_id=' . $_GET['entities_id']
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body ajax-modal-width-790">

        <?php
        if ($obj['type'] == 'table') {
            echo input_hidden_tag('is_active', 1);
        } else {
            ?>
            <div class="form-group" id="is-heading-container">
                <label class="col-md-3 control-label" for="is_active"><?php
                    echo TEXT_IS_ACTIVE ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list"><label class="checkbox-inline"><?php
                            echo input_checkbox_tag('is_active', '1', ['checked' => $obj['is_active']]) ?></label></div>
                </div>
            </div>
        <?php
        } ?>

        <?php
        if ($obj['type'] != 'mobile'): ?>
            <div class="form-group" id="is-heading-container">
                <label class="col-md-3 control-label" for="is_default"><?php
                    echo TEXT_IS_DEFAULT ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list"><label class="checkbox-inline"><?php
                            echo input_checkbox_tag('is_default', '1', ['checked' => $obj['is_default']]) ?></label>
                    </div>
                </div>
            </div>
        <?php
        endif ?>

        <?php
        if ($obj['type'] == 'grid'): ?>

            <div class="form-group">
                <label class="col-md-3 control-label" for="sort_order"><?php
                    echo TEXT_WIDHT ?> (px)</label>
                <div class="col-md-9">
                    <?php
                    echo input_tag('width', $obj['width'], ['class' => 'form-control input-small']) ?>
                    <?php
                    echo tooltip_text(TEXT_GRID_WIDHT_INFO) ?>
                </div>
            </div>

        <?php
        endif ?>


        <?php
        if ($obj['type'] == 'tree_table') {
            $settings = new settings($obj['settings']);

            $fields_choices = [];
            $fields_query = fields::get_query(_GET('entities_id'));
            while ($v = db_fetch_array($fields_query)) {
                $fields_choices[$v['id']] = fields_types::get_option(
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
                <label class="col-md-3 control-label"><?php
                    echo tooltip_icon(TEXT_SORT_ITEMS_IN_LIST) . TEXT_FIELDS_IN_LISTING ?></label>
                <div class="col-md-9">
                    <?php
                    echo select_tag(
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
                <label class="col-md-3 control-label"><?php
                    echo tooltip_icon(
                            TEXT_HEADING_WIDTH_BASED_CONTENT_INFO
                        ) . TEXT_HEADING_WIDTH_BASED_CONTENT ?></label>
                <div class="col-md-9">
                    <?php
                    echo select_tag(
                        'settings[heading_width_based_content]',
                        ['1' => TEXT_YES, '0' => TEXT_NO],
                        (int)$settings->get('heading_width_based_content'),
                        ['class' => 'form-control input-small']
                    ) ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php
                    echo TEXT_CHANGE_COL_WIDTH_IN_LISTIN ?></label>
                <div class="col-md-9">
                    <?php
                    echo select_tag(
                        'settings[change_col_width_in_listing]',
                        ['1' => TEXT_YES, '0' => TEXT_NO],
                        (int)$settings->get('change_col_width_in_listing'),
                        ['class' => 'form-control input-small']
                    ) ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php
                    echo tooltip_icon(TEXT_EDITABLE_FIELDS_IN_LISTING_INFO) . TEXT_EDITABLE_FIELDS_IN_LISTING ?></label>
                <div class="col-md-9">
                    <?php
                    echo select_tag(
                        'settings[editable_fields_in_listing]',
                        ['1' => TEXT_YES, '0' => TEXT_NO],
                        (int)$settings->get('editable_fields_in_listing'),
                        ['class' => 'form-control input-small']
                    ) ?>
                </div>
            </div>

            <h3 class="form-section"><?php
                echo TEXT_NAV_ITEM_PAGE_CONFIG ?></h3>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php
                    echo TEXT_DISPLAY_NESTED_RECORDS ?></label>
                <div class="col-md-9">
                    <?php
                    echo select_tag(
                        'settings[display_nested_records]',
                        ['' => '', 'left_column' => TEXT_LEFT_COLUMN, 'right_column' => TEXT_RIGHT_COLUMN],
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
                <label class="col-md-3 control-label"><?php
                    echo tooltip_icon(TEXT_SORT_ITEMS_IN_LIST) . TEXT_FIELDS_IN_LISTING ?></label>
                <div class="col-md-9">
                    <?php
                    echo select_tag(
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

<?php
echo ajax_modal_template_footer() ?>

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


