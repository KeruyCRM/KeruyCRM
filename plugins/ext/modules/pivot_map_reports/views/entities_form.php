<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for(
        'ext/pivot_map_reports/entities',
        'action=save&reports_id=' . _get::int('reports_id') . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">


        <div class="form-group">
            <label class="col-md-4 control-label" for="type"><?php
                echo TEXT_REPORT_ENTITY ?></label>
            <div class="col-md-8">
                <?php
                echo select_tag(
                    'entities_id',
                    entities::get_choices(),
                    $obj['entities_id'],
                    [
                        'class' => 'form-control input-xlarge required',
                        'onChange' => 'ext_get_entities_fields(this.value)'
                    ]
                ) ?>
            </div>
        </div>

        <div id="reports_entities_fields"></div>


        <div class="form-group from-group-marker-icon">
            <label class="col-md-4 control-label" for="type"><?php
                echo TEXT_EXT_MARKER_ICON ?></label>
            <div class="col-md-8">
                <?php
                echo input_tag('marker_icon', $obj['marker_icon'], ['class' => 'form-control']) ?>
                <?php
                echo tooltip_text(TEXT_EXT_MARKER_ICON_INFO) ?>
            </div>
        </div>

        <div class="form-group from-group-background">
            <label class="col-md-4 control-label" for="bg_color"><?php
                echo TEXT_EXT_MARKER_COLOR ?></label>
            <div class="col-md-8">
                <div class="input-group input-small color colorpicker-default" data-color="<?php
                echo(strlen($obj['marker_color']) > 0 ? $obj['marker_color'] : '#ff0000') ?>">
                    <?php
                    echo input_tag('marker_color', $obj['marker_color'], ['class' => 'form-control input-small']) ?>
                    <span class="input-group-btn">
      				<button class="btn btn-default" type="button">&nbsp;</button>
      			</span>
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

        $('#configuration_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });

        ext_get_entities_fields($('#entities_id').val());

    });

    function ext_get_entities_fields(entities_id) {
        $('#reports_entities_fields').html('<div class="ajax-loading"></div>');

        $('#reports_entities_fields').load('<?php echo url_for(
            "ext/pivot_map_reports/entities",
            "action=get_entities_fields&reports_id=" . _get::int('reports_id')
        )?>', {entities_id: entities_id, id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();

                check_field_type();
            }
        });
    }

    function check_field_type() {
        field_id = $('#fields_id').val();
        //alert(field_id)
        if (fields_type_by_id[field_id] == 'fieldtype_google_map' || fields_type_by_id[field_id] == 'fieldtype_google_map_directions') {
            $('.from-group-background').hide();
            $('.from-group-marker-icon').show();

        } else if (fields_type_by_id[field_id] == 'fieldtype_yandex_map') {
            $('.from-group-background').show();
            $('.from-group-marker-icon').show();

        } else {
            $('.from-group-background').show();
            $('.from-group-marker-icon').hide();
        }
    }

</script>   