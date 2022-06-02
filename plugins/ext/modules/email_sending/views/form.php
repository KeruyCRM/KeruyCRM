<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for(
        'ext/email_sending/rules',
        'action=save&entities_id=' . _get::int('entities_id') . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>
<?php
echo input_hidden_tag('entities_id', _get::int('entities_id')) ?>
<div class="modal-body">
    <div class="form-body">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#message_text" data-toggle="tab"><?php
                    echo TEXT_EXT_MESSAGE_TEXT ?></a></li>
            <li><a href="#message_note" data-toggle="tab"><?php
                    echo TEXT_NOTE ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_info">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="is_active"><?php
                        echo TEXT_IS_ACTIVE ?></label>
                    <div class="col-md-9">
                        <p class="form-control-static"><?php
                            echo input_checkbox_tag('is_active', 1, ['checked' => $obj['is_active']]) ?></p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="type"><?php
                        echo TEXT_EXT_RULE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'action_type',
                            email_rules::get_action_type_choices(),
                            $obj['action_type'],
                            [
                                'class' => 'form-control required chosen-select',
                                'onChange' => 'ext_get_entities_fields()'
                            ]
                        ) ?>
                    </div>
                </div>

                <div id="rules_entities_fields"></div>


                <div class="form-group">
                    <label class="col-md-3 control-label" for="attach_attachments"><?php
                        echo tooltip_icon(
                                TEXT_EXT_ATTACH_ATTACHMENTS_TO_EMAIL_INFO
                            ) . TEXT_EXT_ATTACH_ATTACHMENTS_TO_EMAIL ?></label>
                    <div class="col-md-9">
                        <p class="form-control-static"><?php
                            echo input_checkbox_tag('attach_attachments', 1, ['checked' => $obj['attach_attachments']]
                            ) ?></p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="cfg_sms_send_to_number_text"><?php
                        echo tooltip_icon(
                                TEXT_ENTER_TEXT_PATTERN_INFO
                            ) . TEXT_EMAIL_SUBJECT . fields::get_available_fields_helper(
                                _GET('entities_id'),
                                'subject',
                                TEXT_AVAILABLE_FIELDS,
                                [],
                                false,
                                true
                            ); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'subject',
                            $obj['subject'],
                            ['class' => 'form-control input-xlarge textarea-small required']
                        ); ?>
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" id="message_text">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="cfg_sms_send_to_number_text"><?php
                        echo tooltip_icon(TEXT_ENTER_TEXT_PATTERN_INFO) . TEXT_EXT_MESSAGE_TEXT; ?>

                    </label>
                    <div class="col-md-3">
                        <div id="available_fields"></div>
                    </div>

                    <div class="col-md-3">

                        <?php
                        $entities_query = db_query(
                            "select * from app_entities where parent_id='" . _GET('entities_id') . "'"
                        );
                        if (db_num_rows($entities_query)) {
                            ?>
                            <div class="dropdown">
                                <button class="btn btn-default btn-sm dropdown-toggle" type="button" id="dropdownMenu1"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php
                                    echo TEXT_SUB_ENTITIES ?>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1"
                                    style="max-height: 250px; overflow-y: auto">

                                    <?php
                                    $html = '';
                                    while ($entities = db_fetch_array($entities_query)) {
                                        $html .= '<li><a href="#"><b>' . $entities['name'] . '</b></a></li>';
                                        $html .= '<li><a href="#" class="insert_to_template_description" data-field="{#entity' . $entities['id'] . ':[]}"> - ' . TEXT_LIST . ' {#entity' . $entities['id'] . ': [field_id] [field_id]}</a></li>';
                                        $html .= '<li><a href="#" class="insert_to_template_description" data-field="{#entity' . $entities['id'] . ':<>}"> - ' . TEXT_TABLE . ' {#entity' . $entities['id'] . ':&lt;field_id, field_id&gt}</a></li>';
                                    }

                                    echo $html;
                                    ?>


                                </ul>
                            </div>
                            <?php
                        }
                        ?>

                    </div>
                    <div class="col-md-3">
                        <?php
                        $blocks_query = db_query("select * from app_ext_email_rules_blocks  order by name");
                        if (db_num_rows($blocks_query)) {
                            $html = '
                                <div class="dropdown">
                                <button class="btn btn-default btn-sm dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    ' . TEXT_EXT_HTML_BLOCKS . '
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1" style="max-height: 250px; overflow-y: auto">
                                ';

                            while ($blocks = db_fetch_array($blocks_query)) {
                                $html .= '<li><a href="#" class="insert_to_template_description" data-field="[block_' . $blocks['id'] . ']">' . $blocks['name'] . ' [block_' . $blocks['id'] . ']</a></li>';
                            }


                            $html .= '
                                </ul>
                            </div>  
                                ';

                            echo $html;
                        }
                        ?>
                    </div>

                    <div class="col-md-12" style="padding-top: 5px;">
                        <?php
                        echo textarea_tag(
                            'description',
                            $obj['description'],
                            ['class' => 'form-control input-xlarge full-editor', 'editor-height' => 350]
                        ); ?>
                    </div>
                </div>

                <?php
                $choices = [];
                $templates_query = db_query(
                    "select id, name, type from app_ext_export_templates where entities_id='" . _GET(
                        'entities_id'
                    ) . "'"
                );
                while ($templates = db_fetch_array($templates_query)) {
                    if ($templates['type'] == 'docx') {
                        $choices[$templates['id'] . '_pdf'] = $templates['name'] . ' (PDF)';
                        $choices[$templates['id'] . '_docx'] = $templates['name'] . ' (DOCX)';
                    } else {
                        $choices[$templates['id'] . '_pdf'] = $templates['name'] . ' (PDF)';
                    }
                }

                if (count($choices)) {
                    ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="cfg_sms_send_to_number_text"><?php
                            echo TEXT_EXT_ATTACH_TEMPLATE; ?></label>
                        <div class="col-md-9">
                            <?php
                            echo select_tag(
                                'attach_template[]',
                                $choices,
                                $obj['attach_template'],
                                ['class' => 'form-control chosen-select', 'multiple' => true]
                            ); ?>
                        </div>
                    </div>
                <?php
                } ?>

            </div>

            <div class="tab-pane fade" id="message_note">
                <div class="form-group">
                    <label class="col-md-3 control-label" for="type"><?php
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
        $('#configuration_form').validate({
            ignore: '',
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });

        ext_get_entities_fields();

        ext_get_entities_available_fields();

    });


    function ext_get_entities_available_fields() {
        var entities_id = $('#entities_id').val();

        $('#available_fields').html('<div class="ajax-loading"></div>');

        $('#available_fields').load('<?php echo url_for(
            "ext/email_sending/rules",
            "action=get_available_fields"
        ) ?>', {entities_id: entities_id}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();
            }
        });
    }

    function ext_get_entities_fields() {
        var entities_id = $('#entities_id').val();
        var action_type = $('#action_type').val();

        $('#rules_entities_fields').html('<div class="ajax-loading"></div>');

        $('#rules_entities_fields').load('<?php echo url_for(
            "ext/email_sending/rules",
            "action=get_entities_fields"
        ) ?>', {
            action_type: action_type,
            entities_id: entities_id,
            id: '<?php echo $obj["id"] ?>'
        }, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();
            }
        });
    }

    function get_monitor_choices() {
        var entities_id = $('#entities_id').val();
        var action_type = $('#action_type').val();
        var fields_id = $('#monitor_fields_id').val();

        $('#monitor_choices_row').html('<div class="ajax-loading"></div>');

        $('#monitor_choices_row').load('<?php echo url_for(
            "ext/email_sending/rules",
            "action=get_monitor_choices"
        ) ?>', {
            action_type: action_type,
            fields_id: fields_id,
            entities_id: entities_id,
            id: '<?php echo $obj["id"] ?>'
        }, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();
            }
        });
    }
</script>   