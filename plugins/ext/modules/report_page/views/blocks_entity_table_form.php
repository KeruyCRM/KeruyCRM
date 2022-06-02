<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'templates_form',
    url_for(
        'ext/report_page/blocks_entity_table',
        'report_id=' . _GET(
            'report_id'
        ) . '&action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '') . '&block_id=' . $block_info['id']
    ),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body">

        <?php
        $cfg = new fields_types_cfg($block_info['field_configuration']);

        //print_rr($parent_block);

        switch ($block_info['field_type']) {
            case 'fieldtype_entity':
            case 'fieldtype_entity_ajax':
            case 'fieldtype_entity_multilevel':
            case 'fieldtype_related_records':
                $field_entity_id = $cfg->get('entity_id');
                break;
            default:
                $field_entity_id = 1;
                break;
        }

        if ($block_info['field_type'] == 'fieldtype_id' and $app_entities_cache[$block_info['entities_id']]['parent_id'] == $report_page['entities_id']) {
            $field_entity_id = $block_info['entities_id'];
        }

        $settings = new settings($obj['settings']);
        ?>


        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_HEADING ?></label>
            <div class="col-md-9"><?php
                echo input_tag('settings[heading]', $settings->get('heading'), ['class' => 'form-control input-large']
                ) ?></div>
        </div>

        <?php
        $choices = [
            'field' => TEXT_FIELD,
            'php_code' => TEXT_PHP_CODE,
            'empty' => TEXT_EMPTY_VALUE,
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

        <div class="form-group settings-list">
            <label class="col-md-3 control-label"><?php
                echo sprintf(TEXT_EXT_TAG_X_ATTRIBUTES, 'TD') ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag(
                    'settings[tag_td_attributes]',
                    $settings->get('tag_td_attributes', ''),
                    ['class' => 'form-control input-xlarge']
                ) ?>
                <?php
                echo tooltip_text(TEXT_EXAMPLE . ': <code>style="width:100px;"</code>') ?>
            </div>
        </div>


        <?php
        $choices = [];
        $fields_query = fields::get_query(
            $field_entity_id,
            " and f.type not in ('fieldtype_action','fieldtype_parent_item_id')"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$app_entities_cache[$field_entity_id]['name']][$fields['id']] = fields_types::get_option(
                $fields['type'],
                'name',
                $fields['name']
            );
        }
        ?>
        <div form_display_rules="settings_value_type:field">
            <div class="form-group">
                <label class="col-md-3 control-label"><?php
                    echo TEXT_FIELD ?></label>
                <div class="col-md-9"><?php
                    echo select_tag(
                        'fields_id',
                        $choices,
                        $obj['field_id'],
                        ['class' => 'form-control input-xlarge chosen-select required']
                    ) ?>
                </div>
            </div>

            <div id="field_settings"></div>
        </div>

        <div class="form-group" form_display_rules="settings_value_type:php_code">
            <label class="col-md-3 control-label"><?php
                echo TEXT_PHP_CODE . report_page\blocks_php::render_helper(
                        ['type' => 'item', 'entity_id' => $block_info['field_entity_id']]
                    ) ?></label>
            <div class="col-md-9">
                <?php
                echo textarea_tag(
                    'settings[php_code]',
                    $settings->get('php_code'),
                    ['class' => ($settings->get('value_type') == 'php_code' ? 'code_mirror' : ''), 'mode' => 'php']
                ) ?>
                <?php
                echo tooltip_text(TEXT_EXAMPLE . ': <code>$output_value = $item[\'field_13\']*0.2;</code>') ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_SORT_ORDER ?></label>
            <div class="col-md-9"><?php
                echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-xsmall']) ?></div>
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
            'ext/report_page/blocks_entity_table',
            'id=' . $obj['id'] . '&action=get_field_settings&report_id=' . $report_page['id'] . '&block_id=' . $block_info['id']
        ) ?>", {fields_id: $('#fields_id').val()}, function () {
            appHandleUniform();
        })
    }
</script>