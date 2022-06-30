<?php
echo ajax_modal_template_header(TEXT_EXT_HEADING_TEMPLATE_INFO) ?>

<?php
echo form_tag(
    'templates_form',
    url_for('ext/xml_import/templates', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body">


        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#access_configuration" data-toggle="tab"><?php
                    echo TEXT_ACCESS ?></a></li>
            <li><a href="#template_configuration" data-toggle="tab"><?php
                    echo TEXT_EXT_TEMPLATE ?></a></li>
            <li><a href="#import_by_url" data-toggle="tab"><?php
                    echo TEXT_EXT_IMPORT_BY_URL ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_info">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="is_active"><?php
                        echo TEXT_IS_ACTIVE ?></label>
                    <div class="col-md-9">
                        <p class="form-control-static"><?php
                            echo input_checkbox_tag(
                                'is_active',
                                $obj['is_active'],
                                ['checked' => ($obj['is_active'] == 1 ? 'checked' : '')]
                            ) ?></p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="entities_id"><?php
                        echo TEXT_ENTITY ?></label>
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
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_NAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_DESCRIPTION ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag(
                            'description',
                            $obj['description'],
                            ['class' => 'form-control input-large editor']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="button_title"><?php
                        echo TEXT_EXT_PROCESS_BUTTON_TITLE; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('button_title', $obj['button_title'], ['class' => 'form-control input-medium']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="button_position"><?php
                        echo TEXT_EXT_PROCESS_BUTTON_POSITION; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'button_position[]',
                            xml_import::get_position_choices(),
                            $obj['button_position'],
                            ['class' => 'form-control input-xlarge chosen-select required', 'multiple' => 'multiple']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="menu_icon"><?php
                        echo TEXT_ICON; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('button_icon', $obj['button_icon'], ['class' => 'form-control input-large']); ?>
                        <?php
                        echo tooltip_text(TEXT_MENU_ICON_TITLE_TOOLTIP) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="button_color"><?php
                        echo TEXT_EXT_PROCESS_BUTTON_COLOR ?></label>
                    <div class="col-md-9">
                        <div class="input-group input-small color colorpicker-default" data-color="<?php
                        echo(strlen($obj['button_color']) > 0 ? $obj['button_color'] : '#428bca') ?>">
                            <?php
                            echo input_tag('button_color', $obj['button_color'], ['class' => 'form-control input-small']
                            ) ?>
                            <span class="input-group-btn">
  				<button class="btn btn-default" type="button">&nbsp;</button>
  			</span>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-3 control-label" for="sort_order"><?php
                        echo TEXT_SORT_ORDER ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-xsmall']) ?>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="access_configuration">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="users_groups"><?php
                        echo TEXT_USERS_GROUPS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'users_groups[]',
                            access_groups::get_choices(),
                            $obj['users_groups'],
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="users_groups"><?php
                        echo TEXT_ASSIGNED_TO ?></label>
                    <div class="col-md-9">
                        <?php
                        $attributes = [
                            'class' => 'form-control input-xlarge chosen-select',
                            'multiple' => 'multiple',
                            'data-placeholder' => TEXT_SELECT_SOME_VALUES
                        ];

                        $assigned_to = (strlen($obj['assigned_to']) > 0 ? explode(',', $obj['assigned_to']) : '');
                        echo select_tag('assigned_to[]', users::get_choices(), $assigned_to, $attributes);
                        ?>
                    </div>
                </div>


            </div>

            <div class="tab-pane fade" id="template_configuration">

                <div id="template_fields"></div>

            </div>

            <div class="tab-pane fade" id="import_by_url">

                <p><?php
                    echo TEXT_EXT_XML_FILE_PATH_INFO . '<br>' . DIR_FS_CATALOG . 'cron/xml_import.php' ?></p>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="filepath"><?php
                        echo TEXT_EXT_FILE_PATH ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('filepath', $obj['filepath'], ['class' => 'form-control']) ?>
                    </div>
                </div>

                <div id="parent_items_choices"></div>
            </div>


        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#templates_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });

        load_xml_template_fields();
        load_xml_parent_items_choices();

        $('#entities_id').change(function () {
            load_xml_template_fields();
            load_xml_parent_items_choices();
        })

    });

    function load_xml_template_fields() {

        $('#template_fields').html('<div class="ajax-loading"></div>');

        $('#template_fields').load('<?php echo url_for(
            "ext/xml_import/templates",
            "action=get_fields"
        )?>', {entities_id: $('#entities_id').val(), id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();
            }
        });
    }

    function load_xml_parent_items_choices() {
        $('#parent_items_choices').html('<div class="ajax-loading"></div>');

        $('#parent_items_choices').load('<?php echo url_for(
            "ext/xml_import/templates",
            "action=get_parent_items_choices"
        )?>', {entities_id: $('#entities_id').val(), id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();
            }
        });
    }
</script>  