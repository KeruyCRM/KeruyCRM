<?php
echo ajax_modal_template_header(TEXT_HEADING_FUNCTION_IFNO) ?>

<?php
echo form_tag(
    'common_reports_form',
    url_for('ext/functions/functions', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body">


        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#note" data-toggle="tab"><?php
                    echo TEXT_NOTE ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_info">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_NAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="entities_id"><?php
                        echo TEXT_REPORT_ENTITY ?></label>
                    <div class="col-md-9"><?php
                        echo select_tag(
                            'entities_id',
                            entities::get_choices(),
                            $obj['entities_id'],
                            ['class' => 'form-control input-large required']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="functions_name"><?php
                        echo TEXT_EXT_FUNCTION ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'functions_name',
                            functions::get_choices(),
                            $obj['functions_name'],
                            ['class' => 'form-control input-large required']
                        ) ?>
                    </div>
                </div>

                <div class="form-group form-group-function-select" style="display:none">
                    <label class="col-md-3 control-label" for="functions_name"><?php
                        echo TEXT_INFO ?></label>
                    <div class="col-md-9">
                        <p class="form-control-static"><?php
                            echo TEXT_EXT_FUNCTION_SELECT_INFO ?></p>
                    </div>
                </div>

                <div class="form-group function-prop">
                    <label class="col-md-3 control-label" for="functions_formula"><?php
                        echo TEXT_FORMULA ?>
                        <div id="available_fields"></div>
                    </label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag('functions_formula', $obj['functions_formula'], ['class' => 'form-control']
                        ) ?>
                        <?php
                        echo tooltip_text(TEXT_EXT_FUNCTIONS_FORMULA_INFO); ?>
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" id="note">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_ADMINISTRATOR_NOTE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag('notes', $obj['notes'], ['class' => 'form-control']) ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#common_reports_form').validate();

        load_available_fields();
        check_functions_prop($('#functions_name').val());

        $('#entities_id').change(function () {
            load_available_fields();
        })

        $('#functions_name').change(function () {
            check_functions_prop($(this).val())
        });
    });

    function check_functions_prop(val) {
        if (val == 'SELECT') {
            $('.form-group-function-select').show()
        } else {
            $('.form-group-function-select').hide()
        }


        if (val == 'COUNT') {
            $('.function-prop').fadeOut();
        } else {
            $('.function-prop').fadeIn();
        }
    }

    function load_available_fields() {
        $('#available_fields').html('');
        $('#available_fields').addClass('ajax-loading');
        $('#available_fields').load('<?php echo url_for(
            "ext/functions/functions",
            "action=get_available_fields"
        ) ?>', {entities_id: $('#entities_id').val()}, function () {
            $('#available_fields').removeClass('ajax-loading');
        })
    }

</script>   
    
 
