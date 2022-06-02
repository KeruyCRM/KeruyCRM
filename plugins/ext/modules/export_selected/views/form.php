<?php
echo ajax_modal_template_header(TEXT_EXT_HEADING_TEMPLATE_IFNO) ?>

<?php
echo form_tag(
    'export_templates_form',
    url_for('ext/export_selected/templates', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body">


        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#access_configuration" data-toggle="tab"><?php
                    echo TEXT_ACCESS ?></a></li>
            <li><a href="#configuration" data-toggle="tab"><?php
                    echo TEXT_SETTINGS ?></a></li>
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
                            ['class' => 'form-control input-xlarge required']
                        ) ?>
                    </div>
                </div>

                <?php
                $choices = [
                    'xlsx' => 'xlsx',
                    'csv' => 'csv',
                    'txt' => 'txt',
                    'docx' => 'docx',
                ];
                ?>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="type"><?php
                        echo TEXT_TYPE ?></label>
                    <div class="col-md-9"><?php
                        echo select_tag('type', $choices, $obj['type'], ['class' => 'form-control input-small required']
                        ) ?>
                        <span class="tip-docx" style="display:none;"><?php
                            echo tooltip_text(TEXT_EXT_EXPORT_SELECTED_TYPE_DOCX_INFO) ?></span>
                    </div>
                </div>

                <div class="form-group form-group-filename" style="display:none">
                    <label class="col-md-3 control-label" for="type"><?php
                        echo TEXT_FILENAME ?> (docx)</label>
                    <div class="col-md-9"><?php
                        echo input_file_tag(
                            'filename',
                            fieldtype_attachments::get_accept_types_by_file(
                                'docx'
                            ) + ['class' => 'form-control input-xlarge']
                        ) ?>
                        <?php
                        echo tooltip_text($obj['filename']) ?>
                    </div>
                </div>

                <div class="form-group-export-fields"></div>


                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_NAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="button_title"><?php
                        echo TEXT_EXT_PROCESS_BUTTON_TITLE; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('button_title', $obj['button_title'], ['class' => 'form-control input-large']
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
                            export_selected::get_position_choices(),
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

            <div class="tab-pane fade" id="configuration">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_FILENAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'template_filename',
                            $obj['template_filename'],
                            ['class' => 'form-control input-xlarge']
                        ) ?>
                        <?php
                        echo tooltip_text(
                            TEXT_EXAMPLE . ': ' . TEXT_FILENAME . ' [current_user_name] [current_date]'
                        ) ?>
                    </div>
                </div>

                <?php
                $settings = new settings($obj['settings']);

                $direction_choices = [
                    '' => '<i class="fa fa-long-arrow-right" aria-hidden="true"></i>',
                    'BTLR' => '<i class="fa fa-long-arrow-up" aria-hidden="true"></i>',
                    'TBRL' => '<i class="fa fa-long-arrow-down" aria-hidden="true"></i>',
                ];

                $html = '
                    <div form_display_rules="type:docx">
                      <div class="form-group  ">
                        <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_FONT_NAME . '</label>
                        <div class="col-md-4">' . input_tag(
                        'settings[font_name]',
                        $settings->get('font_name', 'Times New Roman'),
                        ['class' => 'form-control input-medium required']
                    ) . tooltip_text(TEXT_EXAMPLE . ': Times New Roman, Arial') . '</div>
                        <div class="col-md-3 ">' . input_color('settings[font_color]', $settings->get('font_color')) . '</div>			
                      </div>

                      <div class="form-group  ">
                        <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_FONT_SIZE . '</label>
                        <div class="col-md-9">' . input_tag(
                        'settings[font_size]',
                        $settings->get('font_size', '12'),
                        ['class' => 'form-control input-small required number']
                    ) . '</div>			
                      </div>
                      <div class="form-group ">
                        <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_BORDER . '</label>
                        <div class="col-md-1">' . input_tag(
                        'settings[border]',
                        $settings->get('border', '0.1'),
                        ['class' => 'form-control input-xsmall required number']
                    ) . '</div>
                        <div class="col-md-3">' . input_color(
                        'settings[border_color]',
                        $settings->get('border_color')
                    ) . '</div> 
                      </div>
                      <div class="form-group ">
                        <label class="col-md-3 control-label" for="fields_id">' . TEXT_BACKGROUND_COLOR . '</label>           
                        <div class="col-md-3">' . input_color('settings[table_color]', $settings->get('table_color')) . '</div> 
                      </div>
                      <div class="form-group ">
                        <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_CELL_MARGIN . '</label>
                        <div class="col-md-9">' . input_tag(
                        'settings[cell_margin]',
                        $settings->get('cell_margin', '3'),
                        ['class' => 'form-control input-xsmall required number']
                    ) . '</div>			
                      </div>
                      <div class="form-group ">
                        <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_CELL_SPACING . '</label>
                        <div class="col-md-9">' . input_tag(
                        'settings[cell_spacing]',
                        $settings->get('cell_spacing', '0'),
                        ['class' => 'form-control input-xsmall required number']
                    ) . '</div>			
                      </div>
                      <div class="form-group ">
                        <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_HEADER_HEIGHT . '</label>
                        <div class="col-md-1">' . input_tag(
                        'settings[header_height]',
                        $settings->get('header_height', ''),
                        ['class' => 'form-control input-xsmall number']
                    ) . '</div>
                        <div class="col-md-3">' . input_color(
                        'settings[header_color]',
                        $settings->get('header_color')
                    ) . '</div>			
                      </div>
                      <div class="form-group ">
                        <label class="col-md-3 control-label" for="settings_line_numbering">' . TEXT_EXT_LINE_NUMBERING . '</label>
                        <div class="col-md-1"><p class="form-control-static">' . input_checkbox_tag(
                        'settings[line_numbering]',
                        1,
                        ['checked' => $settings->get('line_numbering')]
                    ) . '</p></div>
                        <div class="col-md-2">' . input_tag(
                        'settings[line_numbering_heading]',
                        $settings->get('line_numbering_heading'),
                        ['class' => 'form-control input-small', 'placeholder' => TEXT_HEADING]
                    ) . '</div>
                        <div class="col-md-3">' . select_radioboxes_button(
                        'settings[line_numbering_direction]',
                        $direction_choices,
                        $settings->get('line_numbering_direction', '')
                    ) . '</div>			
                      </div>
                      <div class="form-group ">
                        <label class="col-md-3 control-label" for="settings_column_numbering">' . TEXT_EXT_COLUMN_NUMBERING . '</label>
                        <div class="col-md-1"><p class="form-control-static">' . input_checkbox_tag(
                        'settings[column_numbering]',
                        1,
                        ['checked' => $settings->get('column_numbering')]
                    ) . '</p></div>            		
                      </div> 
                    </div>  
                    ';


                echo $html;
                ?>

            </div>

        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<?php
echo app_include_codemirror(['css']) ?>

<script>
    $(function () {
        $('#export_templates_form').validate({
            ignore: 'hidden',
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });


        $('#type').change(function () {
            check_template_type();
        })

        $('#entities_id').change(function () {
            load_entities_fields()
        })

        check_template_type();

        load_entities_fields()

    });


    function check_template_type() {
        //docx
        $('.form-group-filename, .tip-docx, .form-group-export-fields').hide()

        if ($('#type').val() == 'docx') {
            $('.form-group-filename, .tip-docx').show()
        } else {
            $('.form-group-export-fields').show()
        }
    }

    function load_entities_fields() {
        $('.form-group-export-fields').load('<?php echo url_for(
            'ext/export_selected/templates',
            'action=get_entities_fields'
        ) ?>', {entities_id: $('#entities_id').val(), id: '<?php echo $obj['id'] ?>'}, function () {
            appHandleUniform();
            jQuery(window).resize();
        })
    }

</script>  