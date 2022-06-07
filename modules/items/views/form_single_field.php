<div class="items-form-conteiner">

    <?php

    echo ajax_modal_template_header(items::get_heading_field($current_entity_id, $current_item_id));

    echo form_tag(
        'form_single_field',
        url_for('items/form_single_field', 'action=save&field_id=' . $field_info['id'] . '&path=' . $app_path),
        ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']
    );

    ?>

    <?php
    echo input_hidden_tag('parent_item_id', $obj['parent_item_id']) ?>
    <?php
    echo input_hidden_tag('parent_id', $obj['parent_id']) ?>

    <div class="modal-body">
        <div class="form-body">

            <?php
            // print_rr($_GET);

            $v = $field_info;

            $html = '
        <div class="form-group form-group-' . $v['id'] . ' form-group-' . $v['type'] . '">
                <label class="col-md-3 control-label" for="fields_' . $v['id'] . '">' .
                ($v['is_required'] == 1 ? '<span class="required-label">*</span>' : '') .
                ($v['tooltip_display_as'] == 'icon' ? tooltip_icon($v['tooltip']) : '') .
                fields_types::get_option($v['type'], 'name', $v['name']) .
                '</label>
            <div class="col-md-9">	
                  <div id="fields_' . $v['id'] . '_rendered_value">' . fields_types::render(
                    $v['type'],
                    $v,
                    $obj,
                    ['parent_entity_item_id' => $obj['parent_item_id'], 'form' => 'item', 'is_new_item' => false]
                ) . '</div>
              ' . ($v['tooltip_display_as'] != 'icon' ? tooltip_text($v['tooltip']) : '') . '
            </div>			
          </div> 
        ';

            echo $html;

            ?>

        </div>
    </div>

    <?php
    echo ajax_modal_template_footer() ?>

    </form>
</div>

<script>
    $(function () {

//autofocus
        $('#ajax-modal').on('shown.bs.modal', function () {
            $('.field_<?php echo $field_info['id'] ?>').focus()
        })

//add method to not accept space  	
        jQuery.validator.addMethod("noSpace", function (value, element) {
            return value == '' || value.trim().length != 0;
        }, '<?php echo addslashes(TEXT_ERROR_REQUIRED) ?>');

        jQuery.validator.addMethod("digitsCustom", function (value, element) {
            return this.optional(element) || /^-?\d+/.test(value);
        }, '<?php echo addslashes(TEXT_ERROR_REQUIRED_DIGITS) ?>');

//start form validation                    
        $('#form_single_field').validate({
            ignore: '.ignore-validation',

            //rules for ckeditor
            rules: {
                <?php echo fields::render_required_ckeditor_rules($current_entity_id); ?>
                <?php echo fields::render_unique_fields_rules($current_entity_id, $current_item_id); ?>
            },

            //custom error messages
            messages: {
                <?php echo fields::render_required_messages($current_entity_id); ?>
            },

            submitHandler: function (form) {

                if (CKEDITOR_holders["fields_<?php echo $field_info['id'] ?>"]) {
                    CKEDITOR_holders["fields_<?php echo $field_info['id'] ?>"].updateElement();
                }

                //replace submit button to Loading to stop double submit
                app_prepare_modal_action_loading(form)

                <?php
                $listing_container = 'entity_items_listing' . _GET('report_id') . '_' . $current_entity_id;

                echo '              
              $.ajax({type: "POST",
                url: $("#form_single_field").attr("action"),
                data: $("#form_single_field").serializeArray()
                }).done(function() {
                  $("#ajax-modal").modal("hide")
                  load_items_listing(\'' . $listing_container . '\',' . _GET('page') . ');    
                });
            ';
                ?>
            }
        })


        //start btn-submodal-open
        app_handle_submodal_open_btn('form_single_field')

    })

</script>



