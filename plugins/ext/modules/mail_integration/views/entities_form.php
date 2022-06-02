<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for('ext/mail_integration/entities', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">


        <ul class="nav nav-tabs">
            <li class="active"><a href="#general" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#fields" data-toggle="tab"><?php
                    echo TEXT_FIELDS ?></a></li>
            <li><a href="#settings" data-toggle="tab"><?php
                    echo TEXT_SETTINGS ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="general">

                <div class="form-group">
                    <label class="col-md-4 control-label" for="name"><?php
                        echo TEXT_EXT_MAIL_ACCOUNT ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'accounts_id',
                            mail_accounts::get_choices(),
                            $obj['accounts_id'],
                            ['class' => 'form-control input-xlarge required']
                        ) ?>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-4 control-label" for="entities_id"><?php
                        echo TEXT_ENTITY ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'entities_id',
                            entities::get_choices(),
                            $obj['entities_id'],
                            ['class' => 'form-control input-xlarge required']
                        ) ?>
                    </div>
                </div>

                <div id="entities_parent_item"></div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="bind_to_sender"><?php
                        echo tooltip_icon(
                                TEXT_EXT_BIND_TO_SENDER_ADDRESS_INFO
                            ) . TEXT_EXT_BIND_TO_SENDER_ADDRESS ?></label>
                    <div class="col-md-8">
                        <p class="form-control-static"><?php
                            echo input_checkbox_tag(
                                'bind_to_sender',
                                $obj['bind_to_sender'],
                                ['checked' => ($obj['bind_to_sender'] == 1 ? 'checked' : '')]
                            ) ?></p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="auto_create"><?php
                        echo tooltip_icon(
                                TEXT_EXT_AUTO_CREATE_RELATED_MAIL_RECORD
                            ) . TEXT_EXT_AUTO_CREATE_RECORD ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'auto_create',
                            mail_accounts::get_auto_create_choices(),
                            $obj['auto_create'],
                            ['class' => 'form-control input-large']
                        ) ?>
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" id="fields">

                <p><?php
                    echo TEXT_EXT_MAIL_ACCOUNT_FIELDS_INFO ?></p>

                <div id="entities_fields"></div>

            </div>
            <div class="tab-pane fade" id="settings">

                <div class="form-group">
                    <label class="col-md-4 control-label" for="entities_id"><?php
                        echo TEXT_SORT_ORDER ?></label>
                    <div class="col-md-8">
                        <?php
                        echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-xsmall']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="auto_create"><?php
                        echo TEXT_HIDE_BUTTONS ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'hide_buttons[]',
                            [
                                'add' => TEXT_BUTTON_ADD,
                                'bind' => TEXT_BUTTON_BIND,
                                'with_selected' => TEXT_WITH_SELECTED
                            ],
                            $obj['hide_buttons'],
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                        ) ?>
                    </div>
                </div>

                <div id="entities_fields_settings"></div>

                <div class="form-section"><?php
                    echo TEXT_NAV_ITEM_PAGE_CONFIG ?></div>

                <?php
                $choices = [];
                $choices[''] = '';
                $choices['left_column'] = TEXT_LEFT_COLUMN;
                $choices['right_column'] = TEXT_RIGHT_COLUMN;

                ?>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="auto_create"><?php
                        echo TEXT_EXT_RELATED_EMAILS ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'related_emails_position',
                            $choices,
                            $obj['related_emails_position'],
                            ['class' => 'form-control input-large']
                        ) ?>
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
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });

        $('#entities_id').change(function () {
            ext_get_entities_fields()
            ext_get_entities_fields_settings()
            ext_get_entities_parent_item()
        })

        ext_get_entities_fields()

        ext_get_entities_fields_settings()

        ext_get_entities_parent_item()

    });


    function ext_get_entities_fields() {
        entities_id = $('#entities_id').val();

        $('#entities_fields').html('<div class="ajax-loading"></div>');

        $('#entities_fields').load('<?php echo url_for(
            "ext/mail_integration/entities",
            "action=get_entities_fields"
        )?>', {entities_id: entities_id, id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();
            }
        });
    }

    function ext_get_entities_fields_settings() {
        entities_id = $('#entities_id').val();

        $('#entities_fields').html('<div class="ajax-loading"></div>');

        $('#entities_fields_settings').load('<?php echo url_for(
            "ext/mail_integration/entities",
            "action=get_entities_fields_settings"
        )?>', {entities_id: entities_id, id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();
            }
        });
    }

    function ext_get_entities_parent_item() {
        entities_id = $('#entities_id').val();

        $('#entities_parent_item').html('<div class="ajax-loading"></div>');

        $('#entities_parent_item').load('<?php echo url_for(
            "ext/mail_integration/entities",
            "action=get_entities_parent_item"
        )?>', {entities_id: entities_id, id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();
            }
        });
    }

</script>  