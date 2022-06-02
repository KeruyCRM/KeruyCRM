<?php
echo ajax_modal_template_header(TEXT_EXT_LINK_RECORDS_HEADING) ?>

<?php

$choices = related_records::get_fields_choices_available_to_relate_to_entity(_GET('entities_id'));

if (!isset($app_selected_items[$_GET['reports_id']])) {
    $app_selected_items[$_GET['reports_id']] = [];
}

if (count($app_selected_items[$_GET['reports_id']]) == 0) {
    echo '
    <div class="modal-body">    
      <div>' . TEXT_PLEASE_SELECT_ITEMS . '</div>
    </div>    
  ' . ajax_modal_template_footer('hide-save-button');
} elseif (count($choices) == 0) {
    echo '
    <div class="modal-body">    
      <div>' . TEXT_EXT_WARNING_THERE_IS_NO_FIELDS_TO_LINK . '</div>
    </div>    
  ' . ajax_modal_template_footer('hide-save-button');
} else {
    if (count($choices) > 1) {
        $choices = array_merge(['' => ''], $choices);
    }

    ?>

    <?php
    echo form_tag(
        'add_related_items',
        url_for('ext/with_selected/link', 'action=add_related_items&reports_id=' . _GET('reports_id')),
        ['class' => 'form-horizontal']
    ) ?>


    <div class="modal-body ajax-modal-width-790" style="padding-bottom: 0;">
        <div class="form-body">
            <div id="modal-body-content">

                <div class="form-group">

                    <?php
                    if (count($choices) > 1) {
                        echo '<div class="col-md-4">' . select_tag(
                                'related_to_field',
                                $choices,
                                '',
                                ['class' => 'form-control chosen-select', 'onChange' => 'set_related_to_field()']
                            ) . '</div>';
                    } else {
                        echo '<label class="col-md-4 control-label">' . current($choices) . input_hidden_tag(
                                'related_to_field',
                                key($choices)
                            ) . '</label>';
                    }
                    ?>
                    <div class="col-md-8">
                        <div id="items_select"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php
    echo ajax_modal_template_footer('hide-save-button') ?>

    </form>

    <script>

        function set_related_to_field() {
            $('#items_select').html('<div class="ajax-loading"></div>');
            $('#items_select').load('<?php echo url_for(
                'ext/with_selected/link',
                'action=items_select&path=' . $app_path . '&entities_id=' . _GET('entities_id') . '&reports_id=' . _GET(
                    'reports_id'
                )
            ) ?>', {related_to_field: $("#related_to_field").val()})
        }

        $(function () {

            set_related_to_field();

            $("#add_related_items").validate({
                ignore: "",
                submitHandler: function (form) {
                    $('#modal-body-content').css('visibility', 'hidden').css('height', '1px');
                    $('#modal-body-content').after('<div class="ajax-loading"></div>');

                    $('#modal-body-content').load($(form).attr('action'), $(form).serializeArray(), function () {
                        $('.ajax-loading').css('display', 'none');
                        $('#modal-body-content').css('visibility', 'visible').css('height', 'auto');
                    })

                }
            });
        })
    </script>

<?php
} ?>
    
    
 
