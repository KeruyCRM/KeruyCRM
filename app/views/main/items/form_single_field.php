<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<div class="items-form-container">
    <?= \Helpers\App::ajax_modal_template_header(
        \Models\Main\Items\Items::get_heading_field(\K::$fw->current_entity_id, \K::$fw->current_item_id)
    ); ?>

    <?= \Helpers\Html::form_tag(
        'form_single_field',
        \Helpers\Urls::url_for(
            'main/items/form_single_field/save',
            'field_id=' . \K::$fw->field_info['id'] . '&path=' . \K::$fw->app_path
        ),
        ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']
    ); ?>

    <?= \Helpers\Html::input_hidden_tag('parent_item_id', \K::$fw->obj['parent_item_id']) ?>
    <?= \Helpers\Html::input_hidden_tag('parent_id', \K::$fw->obj['parent_id']) ?>

    <div class="modal-body">
        <div class="form-body">
            <?php
            $v = \K::$fw->field_info;

            $html = '
        <div class="form-group form-group-' . $v['id'] . ' form-group-' . $v['type'] . '">
                <label class="col-md-3 control-label" for="fields_' . $v['id'] . '">' .
                ($v['is_required'] == 1 ? '<span class="required-label">*</span>' : '') .
                ($v['tooltip_display_as'] == 'icon' ? \Helpers\App::tooltip_icon($v['tooltip']) : '') .
                \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']) .
                '</label>
            <div class="col-md-9">	
                  <div id="fields_' . $v['id'] . '_rendered_value">' . \Models\Main\Fields_types::render(
                    $v['type'],
                    $v,
                    \K::$fw->obj,
                    [
                        'parent_entity_item_id' => \K::$fw->obj['parent_item_id'],
                        'form' => 'item',
                        'is_new_item' => false
                    ]
                ) . '</div>
              ' . ($v['tooltip_display_as'] != 'icon' ? \Helpers\App::tooltip_text($v['tooltip']) : '') . '
            </div>			
          </div> 
        ';
            echo $html;
            ?>
        </div>
    </div>
    <?= \Helpers\App::ajax_modal_template_footer() ?>

    </form>
</div>

<script>
    $(function () {

        //autofocus
        $('#ajax-modal').on('shown.bs.modal', function () {
            $('.field_<?= \K::$fw->field_info['id'] ?>').focus()
        })

        //add method to not accept space
        jQuery.validator.addMethod("noSpace", function (value, element) {
            return value == '' || value.trim().length != 0;
        }, '<?= addslashes(\K::$fw->TEXT_ERROR_REQUIRED) ?>');

        jQuery.validator.addMethod("digitsCustom", function (value, element) {
            return this.optional(element) || /^-?\d+/.test(value);
        }, '<?= addslashes(\K::$fw->TEXT_ERROR_REQUIRED_DIGITS) ?>');

        //start form validation
        $('#form_single_field').validate({
            ignore: '.ignore-validation',

            //rules for ckeditor
            rules: {
                <?= \Models\Main\Fields::render_required_ckeditor_rules(
                    \K::$fw->current_entity_id
                ) . \Models\Main\Fields::render_unique_fields_rules(
                    \K::$fw->current_entity_id,
                    \K::$fw->current_item_id
                ); ?>
            },

            //custom error messages
            messages: {
                <?= \Models\Main\Fields::render_required_messages(\K::$fw->current_entity_id); ?>
            },

            submitHandler: function (form) {

                if (CKEDITOR_holders["fields_<?= \K::$fw->field_info['id'] ?>"]) {
                    CKEDITOR_holders["fields_<?= \K::$fw->field_info['id'] ?>"].updateElement();
                }

                //replace submit button to Loading to stop double submit
                app_prepare_modal_action_loading(form)

                $.ajax({
                    type: "POST",
                    url: $("#form_single_field").attr("action"),
                    data: $("#form_single_field").serializeArray()
                }).done(function () {
                    $("#ajax-modal").modal("hide")
                    load_items_listing('entity_items_listing<?= \K::$fw->GET['report_id'] . '_' . \K::$fw->current_entity_id ?>', '<?= \K::$fw->GET['page'] ?>');
                });
            }
        })

        //start btn-submodal-open
        app_handle_submodal_open_btn('form_single_field')
    })
</script>