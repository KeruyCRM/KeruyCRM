<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'templates_form',
    url_for(
        'ext/report_page/extra_rows',
        'report_id=' . _GET(
            'report_id'
        ) . '&action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '') . '&block_id=' . $block_info['id'] . '&row_id=' . $row_info['id']
    ),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body">

        <?php
        $settings = new settings($obj['settings']); ?>


        <?php
        if ($row_info['block_type'] == 'tfoot') {
            $choices = [
                'text' => TEXT_TEXT,
                'field' => TEXT_FIELD,
                'php_code' => TEXT_PHP_CODE,
            ];
            ?>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php
                    echo TEXT_TYPE ?></label>
                <div class="col-md-9"><?php
                    echo select_tag(
                        'settings[value_type]',
                        $choices,
                        $settings->get('value_type'),
                        ['class' => 'form-control input-medium']
                    ) ?></div>
            </div>


            <?php
            $choices = [];
            $choices[''] = '';
            $fields_query = fields::get_query($report_page['entities_id']);
            while ($fields = db_fetch_array($fields_query)) {
                $choices[$fields['id']] = fields::get_name_by_id($fields['id']);
            }
            ?>

            <div form_display_rules="settings_value_type:field">
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php
                        echo TEXT_FIELD ?></label>
                    <div class="col-md-9"><?php
                        echo select_tag('fields_id', $choices, $obj['field_id'], ['class' => 'form-control input-large']
                        ) ?></div>
                </div>

                <div id="field_settings"></div>
            </div>

            <div class="form-group" form_display_rules="settings_value_type:text,php_code">
                <label class="col-md-3 control-label"><?php
                    echo TEXT_HEADING ?></label>
                <div class="col-md-9"><?php
                    echo input_tag(
                        'settings[heading]',
                        $settings->get('heading'),
                        ['class' => 'form-control input-large']
                    ) ?></div>
            </div>

            <div class="form-group" form_display_rules="settings_value_type:php_code">
                <label class="col-md-3 control-label"><?php
                    echo TEXT_PHP_CODE . report_page\blocks_php::render_helper(
                            ['type' => 'total', 'block_id' => $block_info['id']]
                        ) ?></label>
                <div class="col-md-9">
                    <?php
                    echo textarea_tag(
                        'settings[php_code]',
                        $settings->get('php_code'),
                        ['class' => ($settings->get('value_type') == 'php_code' ? 'code_mirror' : ''), 'mode' => 'php']
                    ) ?>
                    <?php
                    echo tooltip_text(TEXT_EXAMPLE . ': <code>$output_value = $total[\'column_13\'];</code>') ?>
                </div>
            </div>
            <?php
        } else {
            ?>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php
                    echo TEXT_HEADING ?></label>
                <div class="col-md-9"><?php
                    echo input_tag(
                        'settings[heading]',
                        $settings->get('heading'),
                        ['class' => 'form-control input-large']
                    ) ?></div>
            </div>
            <?php
        }
        ?>

        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_SORT_ORDER ?></label>
            <div class="col-md-9"><?php
                echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-xsmall']) ?></div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="fields_id"><?php
                echo sprintf(TEXT_EXT_TAG_X_ATTRIBUTES, 'TD') ?></label>
            <div class="col-md-9"><?php
                echo input_tag(
                        'settings[tag_td_attributes]',
                        $settings->get('tag_td_attributes'),
                        ['class' => 'form-control input-xlarge code']
                    ) . tooltip_text(TEXT_EXAMPLE . ': <code>colspan="2"</code>') ?></div>
        </div>


    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<?php
echo app_include_codemirror(['javascript', 'php', 'clike', 'css', 'xml']) ?>

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

        $('#settings_value_type').change(function () {
            if ($(this).val() == 'php_code') {
                $('#settings_php_code').addClass('code_mirror')
                appHandleCodeMirror()
            }
        })
    });

    function get_field_settings() {
        $('#field_settings').load("<?php echo url_for(
            'ext/report_page/extra_rows',
            'id=' . $obj['id'] . '&action=get_field_settings&report_id=' . $report_page['id'] . '&block_id=' . $block_info['id']
        ) ?>", {fields_id: $('#fields_id').val()}, function () {
            appHandleUniform();
        })
    }
</script>