<?php
echo ajax_modal_template_header(TEXT_EXT_HEADING_TEMPLATE_INFO) ?>

<?php
echo form_tag(
    'export_templates_form',
    url_for('ext/templates/export_templates', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
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
            <li class="template-tab-css" style="display: none"><a href="#template_css_content" id="template_css_tab"
                                                                  data-toggle="tab"><?php
                    echo 'CSS' ?></a></li>
            <li class="template-tab-extra" style="display: none"><a href="#template_extra" data-toggle="tab"><?php
                    echo TEXT_EXTRA ?></a></li>
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
                    'html' => 'html',
                    'html_code' => 'html_code',
                    'docx' => 'docx',
                    'label' => TEXT_EXT_LABEL
                ];
                ?>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="type"><?php
                        echo TEXT_TYPE ?></label>
                    <div class="col-md-9"><?php
                        echo select_tag('type', $choices, $obj['type'], ['class' => 'form-control input-small required']
                        ) ?>
                        <span class="tip-html"><?php
                            echo tooltip_text(TEXT_EXT_EXPORT_TEMPLATE_TYPE_HTML_INFO) ?></span>
                        <span class="tip-html-code" style="display:none;"><?php
                            echo tooltip_text(TEXT_EXT_EXPORT_TEMPLATE_TYPE_HTML_CODE_INFO) ?></span>
                        <span class="tip-docx" style="display:none;"><?php
                            echo tooltip_text(TEXT_EXT_EXPORT_TEMPLATE_TYPE_DOCX_INFO) ?></span>
                        <span class="tip-label" style="display:none;"><?php
                            echo tooltip_text(TEXT_EXT_EXPORT_TEMPLATE_TYPE_LABEL_INFO) ?></span>
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

                <div class="form-group form-group-label-size" style="display:none">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo tooltip_icon(TEXT_EXT_ENTER_SIZE_IN_MM) . TEXT_EXT_LABEL_SIZE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('label_size', $obj['label_size'], ['class' => 'form-control input-large']) ?>
                        <?php
                        echo tooltip_text(TEXT_EXAMPLE . ': 57x40') ?>
                    </div>
                </div>

                <script>
                    $("#label_size").inputmask({
                        mask: "9[9][9]x9[9][9]",
                        greedy: false,
                        clearIncomplete: true,
                    })
                </script>

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
                            export_templates::get_position_choices(),
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

                <div class="form-group form-group-page-orientation" style="display:none">
                    <label class="col-md-3 control-label" for="page_orientation"><?php
                        echo TEXT_EXT_PAGE_ORIENTATION ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'page_orientation',
                            [
                                'portrait' => TEXT_EXT_PAGE_ORIENTATION_PORTRAIT,
                                'landscape' => TEXT_EXT_PAGE_ORIENTATION_LANDSCAPE
                            ],
                            $obj['page_orientation'],
                            ['class' => 'form-control input-medium']
                        ) ?>
                    </div>
                </div>

                <div class="form-group form-group-split-pages" style="display:none">
                    <label class="col-md-3 control-label" for="split_into_pages"><?php
                        echo TEXT_EXT_PRINT_MULTIPLE_RECORDS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'split_into_pages',
                            ['1' => TEXT_EXT_EACH_RECORD_FROM_NEW_PAGE, '0' => TEXT_EXT_DO_NOT_SPLIT_INTO_PAGES],
                            $obj['split_into_pages'],
                            ['class' => 'form-control input-large']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo tooltip_icon(TEXT_ENTER_TEXT_PATTERN_INFO) . TEXT_FILENAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'template_filename',
                            $obj['template_filename'],
                            ['class' => 'form-control input-large']
                        ) ?>
                    </div>
                </div>

                <?php
                $choices = [];
                $choices['print'] = TEXT_PRINT;
                $choices['docx'] = 'DOCX';
                $choices['pdf'] = 'PDF';
                $choices['zip'] = 'ZIP';

                ?>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php
                        echo TEXT_SAVE_AS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'save_as[]',
                            $choices,
                            $obj['save_as'],
                            ['class' => 'form-control input-large chosen-select', 'multiple' => 'multiple']
                        ) ?>
                    </div>
                </div>

                <div id="attachments_fields"></div>

            </div>

            <div class="tab-pane fade" id="template_css_content">

                <p><?php
                    echo TEXT_EXT_DESIGN_CSS_NOTE ?></p>

                <?php
                $css = TEXT_EXAMPLE . ':
.export-template{
	font-family: "Arial";
	font-size: 13px;
}
.export-template table{
	border: 1px solid #b7b7b7;
}
.export-template table th{
	background-color: #eee;
}';
                ?>
                <div class="form-group">
                    <div class="col-md-12">
                        <?php
                        echo textarea_tag('template_css', $obj['template_css'], ['class' => 'form-control']) ?>
                        <?php
                        echo tooltip_text(nl2br($css)) ?>

                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="template_extra">

                <p><?php
                    echo TEXT_EXT_TEMPLATES_HEADER_FOOTER_TIP ?></p>

                <h3 class="form-section" style="margin-bottom: 10px;"><?php
                    echo TEXT_HEADER ?></h3>

                <div id="template_header_fields"></div>

                <div class="form-group">
                    <div class="col-md-12">
                        <?php
                        echo textarea_tag(
                            'template_header',
                            $obj['template_header'],
                            ['class' => 'form-control  input-xlarge']
                        ) ?>
                    </div>
                </div>

                <h3 class="form-section" style="margin-bottom: 10px;"><?php
                    echo TEXT_FOOTER ?></h3>

                <div id="template_footer_fields"></div>

                <div class="form-group">
                    <div class="col-md-12">
                        <?php
                        echo textarea_tag(
                            'template_footer',
                            $obj['template_footer'],
                            ['class' => 'form-control  input-xlarge']
                        ) ?>
                    </div>
                </div>

                <script>

                    $(function () {

                        use_editor_full('template_header', false, 150)
                        use_editor_full('template_footer', false, 150)

                        load_template_parent_fields();

                        $('#entities_id').change(function () {
                            load_template_parent_fields();
                        })

                    })

                    function load_template_parent_fields() {
                        $('#template_header_fields').load('<?php echo url_for(
                            'ext/templates/export_templates',
                            'action=get_parent_fields'
                        ) ?>', {editor: 'template_header', entities_id: $('#entities_id').val()})
                        $('#template_footer_fields').load('<?php echo url_for(
                            'ext/templates/export_templates',
                            'action=get_parent_fields'
                        ) ?>', {editor: 'template_footer', entities_id: $('#entities_id').val()})

                        //attachments fields
                        $('#attachments_fields').load('<?php echo url_for(
                            'ext/templates/export_templates',
                            'action=get_attachments_fields&id=' . $obj['id']
                        ) ?>', {entities_id: $('#entities_id').val()}, function () {
                            appHandleUniform();
                        })
                    }

                </script>

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
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });

        //add codemirror
        $('#template_css_tab').click(function () {
            if (!$(this).hasClass('active-codemirror')) {
                setTimeout(function () {
                    var myCodeMirror1 = CodeMirror.fromTextArea(document.getElementById('template_css'), {
                        lineNumbers: true,
                        mode: 'css',
                        lineWrapping: true,
                        matchBrackets: true
                    });
                }, 300);

                $(this).addClass('active-codemirror')
            }
        })

        $('#type').change(function () {
            check_template_type();
        })

        check_template_type();

    });


    function check_template_type() {
        //docx
        $('.form-group-filename, .tip-docx').hide()

        //html
        $('.form-group-page-orientation, .tip-html, .tip-html-code, .template-tab-extra, .template-tab-css, .form-group-split-pages').hide()

        //label
        $('.form-group-label-size, .tip-label').hide();


        if ($('#type').val() == 'html_code') {
            $('.form-group-page-orientation, .tip-html-code, .template-tab-extra, .template-tab-css, .form-group-split-pages').show()
        } else if ($('#type').val() == 'html') {
            $('.form-group-page-orientation, .tip-html, .template-tab-extra, .template-tab-css, .form-group-split-pages').show()

        } else if ($('#type').val() == 'label') {
            $('.form-group-page-orientation, .tip-label, .form-group-label-size, .template-tab-css, .form-group-split-pages').show()
        } else {
            $('.form-group-filename, .tip-docx').show()
        }
    }

</script>  