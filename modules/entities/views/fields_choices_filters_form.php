<?php
echo ajax_modal_template_header(TEXT_HEADING_REPORTS_FILTER_INFO) ?>

<?php
echo form_tag(
    'reports_filters',
    url_for(
        'entities/fields_choices_filters',
        'action=save&choices_id=' . $_GET['choices_id'] . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '') . '&entities_id=' . $_GET['entities_id'] . '&fields_id=' . $_GET['fields_id']
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">

        <div class="form-group">
            <label class="col-md-3 control-label" for="fields_id"><?php
                echo TEXT_SELECT_FIELD ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'fields_id',
                    fields::get_filters_choices($reports_info['entities_id'], false),
                    $obj['fields_id'],
                    ['class' => 'form-control required', 'onChange' => 'load_filters_options(this.value)']
                ) ?>
            </div>
        </div>

        <div id="filters_options"></div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>


    $(function () {
        $('#reports_filters').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });

        load_filters_options($('#fields_id').val());
    });


    function load_filters_options(fields_id) {
        if (fields_id > 0) {
            $('#filters_options').html('<div class="ajax-loading"></div>');

            $('#filters_options').load('<?php echo url_for("reports/filters_options")?>', {
                is_internal: 1,
                fields_id: fields_id,
                id: '<?php echo $obj["id"] ?>'
            }, function (response, status, xhr) {
                if (status == "error") {
                    $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
                } else {
                    appHandleUniform();
                }
            });
        }
    }

</script>  

    
 
