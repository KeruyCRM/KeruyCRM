<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'templates_form',
    url_for(
        'ext/templates_docx/extra_rows',
        'templates_id=' . _GET(
            'templates_id'
        ) . '&action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '') . '&parent_block_id=' . $parent_block['id'] . '&row_id=' . $row_info['id']
    ),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body">

        <?php
        $settings = new settings($obj['settings']); ?>

        <?php
        if ($row_info['block_type'] == 'tfoot') { ?>
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_heading" data-toggle="tab"><?php
                        echo TEXT_HEADING ?></a></li>
                <li><a href="#tab_value" data-toggle="tab"><?php
                        echo TEXT_VALUE ?></a></li>
            </ul>
        <?php
        } ?>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="tab_heading">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="fields_id"><?php
                        echo TEXT_HEADING ?></label>
                    <div class="col-md-9"><?php
                        echo input_tag(
                            'settings[heading]',
                            $settings->get('heading'),
                            ['class' => 'form-control input-large']
                        ) ?></div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="fields_id"><?php
                        echo TEXT_SORT_ORDER ?></label>
                    <div class="col-md-9"><?php
                        echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-xsmall']
                        ) ?></div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="fields_id"><?php
                        echo TEXT_EXT_MERGE_CELLS . ' (colspan)' ?></label>
                    <div class="col-md-9"><?php
                        echo input_tag(
                                'settings[colspan]',
                                $settings->get('colspan'),
                                ['class' => 'form-control input-xsmall number']
                            ) . tooltip_text(TEXT_EXT_MERGE_CELLS_INFO) ?></div>
                </div>

                <div class="form-group settings-list">
                    <label class="col-md-3 control-label" for="fields_id"><?php
                        echo TEXT_EXT_FONT_SIZE ?></label>
                    <div class="col-md-9"><?php
                        echo input_tag(
                            'settings[heading_font_size]',
                            $settings->get('heading_font_size', ''),
                            ['class' => 'form-control input-small number']
                        ) ?></div>
                </div>

                <?php
                $font_style_choices = [
                    'bold' => '<i class="fa fa-bold" aria-hidden="true"></i>',
                    'italic' => '<i class="fa fa-italic" aria-hidden="true"></i>',
                    'underline' => '<i class="fa fa-underline" aria-hidden="true"></i>',
                ];
                ?>
                <div class="form-group settings-list">
                    <label class="col-md-3 control-label" for="fields_id"><?php
                        echo TEXT_EXT_FONT_STYLE ?></label>
                    <div class="col-md-9"><?php
                        echo select_checkboxes_button(
                            'settings[heading_font_style]',
                            $font_style_choices,
                            $settings->get('heading_font_style', '')
                        ) ?></div>
                </div>

                <?php
                $alignment_choices = [
                    'left' => '<i class="fa fa-align-left" aria-hidden="true"></i>',
                    'center' => '<i class="fa fa-align-center" aria-hidden="true"></i>',
                    'right' => '<i class="fa fa-align-right" aria-hidden="true"></i>',
                ];
                ?>
                <div class="form-group settings-list">
                    <label class="col-md-3 control-label" for="fields_id"><?php
                        echo TEXT_EXT_ALIGNMENT ?></label>
                    <div class="col-md-9"><?php
                        echo select_radioboxes_button(
                            'settings[heading_alignment]',
                            $alignment_choices,
                            $settings->get('heading_alignment', 'left')
                        ) ?></div>
                </div>

            </div>
            <div class="tab-pane fade" id="tab_value">

                <?php
                $choices = [];
                $choices[''] = '';
                $fields_query = fields::get_query($template_info['entities_id']);
                while ($fields = db_fetch_array($fields_query)) {
                    $choices[$fields['id']] = fields::get_name_by_id($fields['id']);
                }
                ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="fields_id"><?php
                        echo TEXT_FIELD ?></label>
                    <div class="col-md-9"><?php
                        echo select_tag(
                            'fields_id',
                            $choices,
                            $obj['fields_id'],
                            ['class' => 'form-control input-large']
                        ) ?></div>
                </div>

                <div id="field_settings"></div>

            </div>
        </div>


    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#templates_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });

        get_field_settings()

        $('#fields_id').change(function () {
            get_field_settings()
        })
    });

    function get_field_settings() {
        $('#field_settings').load("<?php echo url_for(
            'ext/templates_docx/extra_rows',
            'id=' . $obj['id'] . '&action=get_field_settings&templates_id=' . $template_info['id'] . '&parent_block_id=' . $parent_block['id']
        ) ?>", {fields_id: $('#fields_id').val()}, function () {
            appHandleUniform();
        })
    }
</script>