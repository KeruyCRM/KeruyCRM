<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for('ext/map_reports/reports', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">


        <div class="form-group">
            <label class="col-md-4 control-label" for="name"><?php
                echo TEXT_NAME ?></label>
            <div class="col-md-8">
                <?php
                echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="in_menu"><?php
                echo TEXT_IN_MENU ?></label>
            <div class="col-md-8">
                <div class="checkbox-list"><label class="checkbox-inline"><?php
                        echo input_checkbox_tag('in_menu', '1', ['checked' => $obj['in_menu']]) ?></label></div>
            </div>
        </div>

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
                        'class' => 'form-control input-large required',
                        'onChange' => 'ext_get_entities_fields(this.value)'
                    ]
                ) ?>
            </div>
        </div>

        <div id="reports_entities_fields"></div>

        <?php
        $choices = [];
        for ($i = 3; $i <= 18; $i++) {
            $choices[$i] = $i;
        }
        ?>
        <div class="form-group">
            <label class="col-md-4 control-label" for="allowed_groups"><?php
                echo TEXT_DEFAULT_ZOOM ?></label>
            <div class="col-md-8">
                <?php
                echo select_tag('zoom', $choices, $obj['zoom'], ['class' => 'form-control input-small']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="name"><?php
                echo TEXT_DEFAULT_POSITION ?></label>
            <div class="col-md-8">
                <?php
                echo input_tag('latlng', $obj['latlng'], ['class' => 'form-control input-medium']) ?>
                <?php
                echo tooltip_text(TEXT_DEFAULT_POSITION_TIP) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="allowed_groups"><?php
                echo tooltip_icon(TEXT_EXT_USERS_GROUPS_INFO) . TEXT_EXT_USERS_GROUPS ?></label>
            <div class="col-md-8">
                <?php
                echo select_tag(
                    'users_groups[]',
                    access_groups::get_choices(false),
                    $obj['users_groups'],
                    ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                ) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="is_public_access"><?php
                echo tooltip_icon(TEXT_EXT_PUBLIC_ACCESS_REPORT_INFO) . TEXT_EXT_PUBLIC_ACCESS ?></label>
            <div class="col-md-8">
                <div class="checkbox-list"><label class="checkbox-inline"><?php
                        echo input_checkbox_tag('is_public_access', '1', ['checked' => $obj['is_public_access']]
                        ) ?></label></div>
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
            "ext/map_reports/reports",
            "action=get_entities_fields"
        ) ?>', {entities_id: entities_id, id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
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
        if (fields_type_by_id[field_id] == 'fieldtype_google_map') {
            $('.from-group-background').hide();
        } else {
            $('.from-group-background').show();
        }
    }

</script>   