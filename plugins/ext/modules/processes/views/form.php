<?php
echo ajax_modal_template_header(TEXT_EXT_PROCESS_IFNO) ?>

<?php
echo form_tag(
    'process_form',
    url_for('ext/processes/processes', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>

<?php
$default_selector = ['1' => TEXT_YES, '0' => TEXT_NO]; ?>

<div class="modal-body">
    <div class="form-body">


        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#confirmation_window" data-toggle="tab"><?php
                    echo TEXT_EXT_PROCESS_CONFIRMATION_WINDOW ?></a></li>
            <li><a href="#extra_info" data-toggle="tab"><?php
                    echo TEXT_EXTRA ?></a></li>
            <?php
            $modules = new modules('payment');
            $payment_modules = $modules->get_active_modules();
            if (count($payment_modules)) {
                echo '<li><a href="#payment_modules"  data-toggle="tab">' . TEXT_EXT_PAYMENT_MODULES . '</a></li>';
            }
            ?>
            <li><a href="#note" data-toggle="tab"><?php
                    echo TEXT_NOTE ?></a></li>
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
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-xlarge required']) ?>
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
                    <label class="col-md-3 control-label" for="button_title"><?php
                        echo TEXT_EXT_PROCESS_BUTTON_TITLE; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('button_title', $obj['button_title'], ['class' => 'form-control input-medium']
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

                <div id="buttons_positions_section"></div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="sort_order"><?php
                        echo TEXT_SORT_ORDER ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-xsmall']) ?>
                    </div>
                </div>


                <h3 class="form-section"><?php
                    echo TEXT_ACCESS ?></h3>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="users_groups"><?php
                        echo tooltip_icon(TEXT_EXT_PROCESS_BUTTON_ACCESS_IFNO) . TEXT_USERS_GROUPS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'users_groups[]',
                            access_groups::get_choices(),
                            (strlen($obj['users_groups']) ? $obj['users_groups'] : -1),
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="assigned_to"><?php
                        echo TEXT_ASSIGNED_TO ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'assigned_to[]',
                            users::get_choices(),
                            $obj['assigned_to'],
                            [
                                'class' => 'form-control input-xlarge chosen-select',
                                'multiple' => 'multiple',
                                'data-placeholder' => TEXT_SELECT_SOME_VALUES
                            ]
                        ); ?>
                    </div>
                </div>

                <div id="entities_users_fields"></div>

            </div>

            <div class="tab-pane fade" id="confirmation_window">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="window_width"><?php
                        echo TEXT_WINDOW_WIDTH; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'window_width',
                            [
                                '' => TEXT_AUTOMATIC,
                                'ajax-modal-width-790' => TEXT_WIDE,
                                'ajax-modal-width-1100' => TEXT_XWIDE
                            ],
                            $obj['window_width'],
                            ['class' => 'form-control input-medium']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="button_title"><?php
                        echo TEXT_BUTTON_TITLE; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'submit_button_title',
                            $obj['submit_button_title'],
                            ['class' => 'form-control input-medium']
                        ); ?>
                        <?php
                        echo tooltip_text(TEXT_DEFAULT . ': ' . TEXT_CONTINUE) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="confirmation_text"><?php
                        echo TEXT_EXT_PROCESS_CONFIRMATION_TEXT ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag(
                            'confirmation_text',
                            $obj['confirmation_text'],
                            ['class' => 'form-control editor', 'toolbar' => 'small']
                        ) ?>
                        <?php
                        echo tooltip_text(TEXT_EXT_PROCESS_CONFIRMATION_WINDOW_INFO) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="allow_comments"><?php
                        echo tooltip_icon(
                                TEXT_EXT_PROCESS_ALLOW_COMMENTS_INFO . ' ' . TEXT_EXT_PROCESS_ALLOW_COMMENTS_INFO_2
                            ) . TEXT_EXT_PROCESS_ALLOW_COMMENTS ?></label>
                    <div class="col-md-9">
                        <div class="checkbox-list"><label class="checkbox-inline"><?php
                                echo input_checkbox_tag('allow_comments', '1', ['checked' => $obj['allow_comments']]
                                ) ?></label></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="preview_prcess_actions"><?php
                        echo tooltip_icon(
                                TEXT_EXT_PREVIEW_PRCESS_ACTIONS_INFO
                            ) . TEXT_EXT_PREVIEW_PRCESS_ACTIONS ?></label>
                    <div class="col-md-9">
                        <div class="checkbox-list"><label class="checkbox-inline"><?php
                                echo input_checkbox_tag(
                                    'preview_prcess_actions',
                                    '1',
                                    ['checked' => $obj['preview_prcess_actions']]
                                ) ?></label></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="success_message"><?php
                        echo tooltip_icon(TEXT_EXT_PROCESSES_WARNING_MESSAGE_TIP) . TEXT_EXT_WARNING_TEXT ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag(
                            'warning_text',
                            $obj['warning_text'],
                            ['class' => 'form-control editor', 'toolbar' => 'small']
                        ) ?>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="extra_info">


                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_settings" data-toggle="tab"><?php
                            echo TEXT_SETTINGS ?></a></li>
                    <li><a href="#tab_form_wizard" data-toggle="tab"><?php
                            echo TEXT_FORM_WIZARD ?></a></li>
                    <li><a href="#tab_js_in_form" data-toggle="tab" id="js_in_form_tab"><?php
                            echo TEXT_JAVASCRIPT_IN_FORM ?></a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade active in" id="tab_settings">

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="success_message"><?php
                                echo tooltip_icon(
                                        TEXT_EXT_PROCESSES_SUCCESS_MESSAGE_TIP
                                    ) . TEXT_EXT_SUCCESS_MESSAGE ?></label>
                            <div class="col-md-8">
                                <?php
                                echo textarea_tag(
                                    'success_message',
                                    $obj['success_message'],
                                    ['class' => 'form-control textarea-small']
                                ) ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="redirect_to_items_listing"><?php
                                echo TEXT_EXT_AFTER_PROCESS_COMPLETE ?></label>
                            <div class="col-md-8">
                                <?php
                                echo select_tag(
                                    'redirect_to_items_listing',
                                    ['0' => TEXT_EXT_STAY_ON_THE_SAME_PAGE, '1' => TEXT_EXT_REDIRECT_TO_ITEMS_LISTING],
                                    $obj['redirect_to_items_listing'],
                                    ['class' => 'form-control input-large']
                                ) ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="apply_fields_access_rules"><?php
                                echo tooltip_icon(
                                        TEXT_EXT_APPLY_FIELDS_ACCESS_RULES_TIP
                                    ) . TEXT_EXT_APPLY_FIELDS_ACCESS_RULES ?></label>
                            <div class="col-md-8">
                                <p class="form-control-static"><?php
                                    echo input_checkbox_tag(
                                        'apply_fields_access_rules',
                                        '1',
                                        ['checked' => $obj['apply_fields_access_rules']]
                                    ) ?></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="apply_fields_display_rules"><?php
                                echo tooltip_icon(
                                        TEXT_EXT_APPLY_FIELDS_DISPLAY_RULES_TIP
                                    ) . TEXT_EXT_APPLY_FIELDS_DISPLAY_RULES ?></label>
                            <div class="col-md-8">
                                <p class="form-control-static"><?php
                                    echo input_checkbox_tag(
                                        'apply_fields_display_rules',
                                        '1',
                                        ['checked' => $obj['apply_fields_display_rules']]
                                    ) ?></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="hide_entity_name"><?php
                                echo tooltip_icon(
                                        TEXT_EXT_HIDE_ENTITY_NAME_IN_PROCESS_FORM_TIP
                                    ) . TEXT_HIDE_ENTITY_NAME ?></label>
                            <div class="col-md-8">
                                <p class="form-control-static"><?php
                                    echo input_checkbox_tag(
                                        'hide_entity_name',
                                        '1',
                                        ['checked' => $obj['hide_entity_name']]
                                    ) ?></p>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-4 control-label" for="disable_comments"><?php
                                echo tooltip_icon(
                                        TEXT_EXT_PROCESSES_DISABLE_COMMENTS_TIP
                                    ) . TEXT_EXT_PROCESSES_DISABLE_COMMENTS ?></label>
                            <div class="col-md-8">
                                <p class="form-control-static"><?php
                                    echo input_checkbox_tag(
                                        'disable_comments',
                                        '1',
                                        ['checked' => $obj['disable_comments']]
                                    ) ?></p>
                            </div>
                        </div>


                    </div>

                    <!-- form wizard -->
                    <div class="tab-pane fade" id="tab_form_wizard">
                        <p><?php
                            echo TEXT_FORM_WIZARD_INFO ?></p>

                        <div class="form-group">
                            <label class="col-md-4 control-label"><?php
                                echo TEXT_IS_ACTIVE; ?></label>
                            <div class="col-md-8">
                                <?php
                                echo select_tag(
                                    'is_form_wizard',
                                    $default_selector,
                                    $obj['is_form_wizard'],
                                    ['class' => 'form-control input-small']
                                ); ?>
                            </div>
                        </div>

                        <div class="form-group" form_display_rules="is_form_wizard:1">
                            <label class="col-md-4 control-label"><?php
                                echo TEXT_DISPLAY_PROGRESS_BAR; ?></label>
                            <div class="col-md-8">
                                <?php
                                echo select_tag(
                                    'is_form_wizard_progress_bar',
                                    $default_selector,
                                    $obj['is_form_wizard_progress_bar'],
                                    ['class' => 'form-control input-small']
                                ); ?>
                            </div>
                        </div>

                    </div>

                    <div class="tab-pane fade" id="tab_js_in_form">


                        <p><?php
                            echo TEXT_JAVASCRIPT_IN_FORM_INFO ?></p>
                        <div class="form-group">
                            <div class="col-md-12">
                                <?php
                                echo textarea_tag(
                                    'javascript_in_from',
                                    $obj['javascript_in_from'],
                                    ['class' => 'form-control']
                                ) ?>
                            </div>
                        </div>

                        <p><?php
                            echo TEXT_JAVASCRIPT_ONSUBMIT_FORM_INFO ?></p>
                        <div class="form-group">
                            <div class="col-md-12">
                                <?php
                                echo textarea_tag(
                                    'javascript_onsubmit',
                                    $obj['javascript_onsubmit'],
                                    ['class' => 'form-control']
                                ) ?>
                            </div>
                        </div>

                    </div>
                </div>


            </div>

            <?php
            //check installed payment modules
            $check_query = db_query(
                "select * from app_ext_processes " . (isset($_GET['id']) ? " where id!='" . $_GET['id'] . "'" : '')
            );
            while ($check = db_fetch_array($check_query)) {
                if (strlen($check['payment_modules'])) {
                    foreach (explode(',', $check['payment_modules']) as $id) {
                        if (isset($payment_modules[$id])) {
                            unset($payment_modules[$id]);
                        }
                    }
                }
            }
            ?>
            <div class="tab-pane fade" id="payment_modules">
                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_EXT_PAYMENT_MODULES ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'payment_modules[]',
                            $payment_modules,
                            $obj['payment_modules'],
                            ['class' => 'form-control input-large chosen-select', 'multiple' => 'multiple']
                        ) ?>
                    </div>
                </div>

                <?php
                echo '<div><b>' . TEXT_INFO . '</b></div>' . TEXT_EXT_PROCESS_PAYMENT_MODULES_INFO ?>
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

<?php
echo app_include_codemirror(['javascript']) ?>

<script>
    $(function () {
        $('#process_form').validate({
            ignore: '',
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });

        $('#entities_id').change(function () {
            ext_get_entities_users_fields();
            ext_get_entities_buttons_positions();
        })

        ext_get_entities_users_fields();
        ext_get_entities_buttons_positions();


        $('#js_in_form_tab').click(function () {
            if (!$(this).hasClass('acitve-codemirror')) {
                setTimeout(function () {
                    var myCodeMirror1 = CodeMirror.fromTextArea(document.getElementById('javascript_in_from'), {
                        lineNumbers: true,
                        lineWrapping: true,
                        matchBrackets: true
                    });

                    var myCodeMirror2 = CodeMirror.fromTextArea(document.getElementById('javascript_onsubmit'), {
                        lineNumbers: true,
                        lineWrapping: true,
                        matchBrackets: true
                    });
                }, 300);

                $(this).addClass('acitve-codemirror')
            }
        })

    });


    function ext_get_entities_users_fields(entities_id) {
        entities_id = $('#entities_id').val();

        $('#entities_users_fields').html('<div class="ajax-loading"></div>');

        $('#entities_users_fields').load('<?php echo url_for(
            "ext/processes/processes",
            "action=get_entities_users_fields"
        ) ?>', {entities_id: entities_id, id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();
            }
        });
    }

    function ext_get_entities_buttons_positions(entities_id) {
        entities_id = $('#entities_id').val();

        $('#buttons_positions_section').html('<div class="ajax-loading"></div>');

        $('#buttons_positions_section').load('<?php echo url_for(
            "ext/processes/processes",
            "action=get_entities_buttons_positions"
        ) ?>', {entities_id: entities_id, id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();
            }
        });
    }

</script>   


