<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for('ext/pivot_map_reports/reports', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
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
            <label class="col-md-4 control-label" for="display_legend"><?php
                echo tooltip_icon(TEXT_EXT_ENTITIES_DISPLAY_LEGEND_TIP) . TEXT_EXT_DISPLAY_LEGEND ?></label>
            <div class="col-md-8">
                <div class="checkbox-list"><label class="checkbox-inline"><?php
                        echo input_checkbox_tag('display_legend', '1', ['checked' => $obj['display_legend']]) ?></label>
                </div>
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
    });

</script>   