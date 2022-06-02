<?php
echo ajax_modal_template_header(TEXT_GENERAL_INFO) ?>

<?php
echo form_tag(
    'report_page_form',
    url_for('ext/report_page/reports', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_SETTINGS ?></a></li>
            <li><a href="#access_configuration" data-toggle="tab"><?php
                    echo TEXT_ACCESS ?></a></li>
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
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_NAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="entities_id"><?php
                        echo TEXT_ENTITY ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'entities_id',
                            ['0' => TEXT_NONE] + entities::get_choices(),
                            $obj['entities_id'],
                            ['class' => 'form-control chosen-select input-xlarge']
                        ) ?>
                        <?php
                        echo tooltip_text(TEXT_EXT_REPORT_LINKED_TO_ENTITY_INFO) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_USE_HTML_EDITOR ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'use_editor',
                            ['1' => TEXT_YES, '0' => TEXT_NO],
                            $obj['use_editor'],
                            ['class' => 'form-control input-small']
                        ) ?>
                    </div>
                </div>


                <div form_display_rules="entities_id:!0">
                    <?php
                    $choices = [
                        'print' => TEXT_EXT_PRINTABLE_REPORT,
                        'page' => TEXT_EXT_SINGLE_PAGE,
                    ];
                    ?>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="type"><?php
                            echo TEXT_TYPE ?></label>
                        <div class="col-md-9">
                            <?php
                            echo select_tag(
                                'type',
                                $choices,
                                $obj['type'],
                                ['class' => 'form-control input-medium required']
                            ) ?>
                        </div>
                    </div>

                    <div form_display_rules="type:print">
                        <div class="form-group form-group-page-orientation">
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


                        <div class="form-group">
                            <label class="col-md-3 control-label" for="name"><?php
                                echo tooltip_icon(TEXT_ENTER_TEXT_PATTERN_INFO) . TEXT_FILENAME ?></label>
                            <div class="col-md-9">
                                <?php
                                echo input_tag(
                                    'save_filename',
                                    $obj['save_filename'],
                                    ['class' => 'form-control input-large']
                                ) ?>
                            </div>
                        </div>

                        <?php
                        $choices = [];
                        $choices['print'] = TEXT_PRINT;
                        $choices['pdf'] = 'PDF';
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

                        <hr>
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

                    <?php
                    $choices = [];
                    $choices['default'] = TEXT_EXT_IN_RECORD_PAGE;
                    $choices['menu_more_actions'] = TEXT_EXT_MENU_MORE_ACTIONS;
                    $choices['menu_print'] = TEXT_EXT_PRINT_BUTTON;
                    ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="button_position"><?php
                            echo TEXT_EXT_PROCESS_BUTTON_POSITION; ?></label>
                        <div class="col-md-9">
                            <?php
                            echo select_tag(
                                'button_position[]',
                                export_templates::get_position_choices(),
                                $obj['button_position'],
                                [
                                    'class' => 'form-control input-xlarge chosen-select required',
                                    'multiple' => 'multiple'
                                ]
                            ); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="menu_icon"><?php
                            echo TEXT_ICON; ?></label>
                        <div class="col-md-9">
                            <?php
                            echo input_tag('button_icon', $obj['button_icon'], ['class' => 'form-control input-large']
                            ); ?>
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
                                echo input_tag(
                                    'button_color',
                                    $obj['button_color'],
                                    ['class' => 'form-control input-small']
                                ) ?>
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">&nbsp;</button>
                            </span>
                            </div>
                        </div>
                    </div>

                    <hr>

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
        $('#report_page_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });

    });

</script>  