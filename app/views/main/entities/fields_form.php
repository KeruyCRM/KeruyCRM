<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_HEADING_FIELD_INFO) ?>

<?= \Helpers\Html::form_tag(
    'fields_form',
    \Helpers\Urls::url_for('main/entities/fields/save', (isset(\K::$fw->GET['id']) ? '&id=' . \K::$fw->GET['id'] : '')),
    ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']
) ?>
<div class="modal-body">
    <div class="form-body ajax-modal-width-790">
        <?= \Helpers\Html::input_hidden_tag('entities_id', \K::$fw->GET['entities_id']) ?>
        <?php
        if (isset(\K::$fw->GET['redirect_to'])) echo \Helpers\Html::input_hidden_tag(
            'redirect_to',
            \K::$fw->GET['redirect_to']
        ) ?>

        <?php
        $forms_tabs_choices = \Models\Main\Forms_tabs::get_choices(\K::$fw->GET['entities_id']);
        if (count($forms_tabs_choices) == 1) echo \Helpers\Html::input_hidden_tag(
            'forms_tabs_id',
            key($forms_tabs_choices)
        )
        ?>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?= \K::$fw->TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#is_required_tab" data-toggle="tab"><?= \K::$fw->TEXT_IS_REQUIRED ?></a></li>
            <li><a href="#tooltip" data-toggle="tab"><?= \K::$fw->TEXT_TOOLTIP ?></a></li>
            <li><a href="#access_tab" data-toggle="tab"><?= \K::$fw->TEXT_ACCESS ?></a></li>
            <li><a href="#note" data-toggle="tab"><?= \K::$fw->TEXT_NOTE ?></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_info">
                <?php
                if (count($forms_tabs_choices) > 1): ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="forms_tabs_id"><?=
                            \Helpers\App::tooltip_icon(\K::$fw->TEXT_FORM_TAB_INFO) . \K::$fw->TEXT_FORM_TAB ?></label>
                        <div class="col-md-9">
                            <?= \Helpers\Html::select_tag(
                                'forms_tabs_id',
                                $forms_tabs_choices,
                                (\K::$fw->GET['forms_tabs_id'] ?? \K::$fw->obj['forms_tabs_id']),
                                ['class' => 'form-control input-medium required']
                            ) ?>
                        </div>
                    </div>
                <?php
                endif ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_FIELD_NAME_INFO
                        ) . \K::$fw->TEXT_NAME ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag(
                            'name',
                            \K::$fw->obj['name'],
                            ['class' => 'form-control input-large required autofocus']
                        ) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="short_name"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_FIELD_SHORT_NAME_INFO
                        ) . \K::$fw->TEXT_SHORT_NAME ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag(
                            'short_name',
                            \K::$fw->obj['short_name'],
                            ['class' => 'form-control input-small']
                        ) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="type"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_FIELD_TYPE_INFO
                        ) . \K::$fw->TEXT_TYPE ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'type',
                            \Models\Main\Fields_types::get_choices(),
                            \K::$fw->obj['type'],
                            [
                                'class' => 'form-control input-xlarge required chosen-select',
                                'onChange' => 'fields_types_configuration(this.value)'
                            ]
                        ) ?>
                    </div>
                </div>
                <div class="form-group" id="is-heading-container">
                    <label class="col-md-3 control-label" for="is_heading"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_IS_HEADING_INFO
                        ) . \K::$fw->TEXT_IS_HEADING ?></label>
                    <div class="col-md-9">
                        <div class="checkbox-list"><label class="checkbox-inline"><?= \Helpers\Html::input_checkbox_tag(
                                    'is_heading',
                                    '1',
                                    ['checked' => \K::$fw->obj['is_heading']]
                                ) ?></label>
                        </div>
                    </div>
                </div>
                <div id="fields_types_configuration"></div>
            </div>
            <div class="tab-pane fade" id="is_required_tab">
                <div class="form-group">
                    <label class="col-md-3 control-label" for="is_required"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_IS_REQUIRED_INFO
                        ) . \K::$fw->TEXT_IS_REQUIRED ?></label>
                    <div class="col-md-9">
                        <div class="checkbox-list"><label class="checkbox-inline"><?= \Helpers\Html::input_checkbox_tag(
                                    'is_required',
                                    '1',
                                    ['checked' => \K::$fw->obj['is_required']]
                                ) ?></label></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="required_message"><?= \K::$fw->TEXT_REQUIRED_MESSAGE ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::textarea_tag(
                            'required_message',
                            \K::$fw->obj['required_message'],
                            ['rows' => 3, 'class' => 'form-control']
                        ) ?>
                        <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_REQUIRED_MESSAGE_INFO); ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tooltip">
                <div class="form-group">
                    <label class="col-md-3 control-label" for="tooltip"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_TOOLTIP_INFO
                        ) . \K::$fw->TEXT_TOOLTIP ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::textarea_tag(
                            'tooltip',
                            \K::$fw->obj['tooltip'],
                            ['rows' => 3, 'class' => 'form-control textarea-small']
                        ) ?>
                        <div style="padding-top: 5px;"><label><?= \Helpers\Html::input_checkbox_tag(
                                    'tooltip_display_as',
                                    'icon',
                                    ['checked' => \K::$fw->obj['tooltip_display_as']]
                                ) . \K::$fw->TEXT_TOOLTIP_DISPLAY_AS_ICON . ' ' . \Helpers\App::tooltip_icon(
                                    \K::$fw->TEXT_TOOLTIP_DISPLAY_AS_ICON_INFO
                                ) ?></label></div>
                        <div style="padding-top: 5px;"><label><?= \Helpers\Html::input_checkbox_tag(
                                    'tooltip_in_item_page',
                                    '1',
                                    ['checked' => \K::$fw->obj['tooltip_in_item_page']]
                                ) . \K::$fw->TEXT_DISPLAY_ON_ITEM_PAGE ?></label></div>
                    </div>
                </div>
                <div class="form-group tooltip_item_page">
                    <label class="col-md-3 control-label"
                           for="tooltip"><?= \K::$fw->TEXT_TOOLTIP_ON_ITEM_PAGE ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::textarea_tag(
                            'tooltip_item_page',
                            \K::$fw->obj['tooltip_item_page'],
                            ['rows' => 3, 'class' => 'form-control textarea-small']
                        ) ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="access_tab">
                <p><?= \K::$fw->TEXT_FIELD_ACCESS_INFO ?></p>
                <?php
                $access_choices_default = [
                    'yes' => \K::$fw->TEXT_YES,
                    'view' => \K::$fw->TEXT_VIEW_ONLY,
                    'hide' => \K::$fw->TEXT_HIDE
                ];
                $access_choices_internal = ['yes' => \K::$fw->TEXT_YES, 'hide' => \K::$fw->TEXT_HIDE];

                $access_choices = (in_array(
                    \K::$fw->obj['type'],
                    ['fieldtype_id', 'fieldtype_date_added', 'fieldtype_date_updated', 'fieldtype_created_by']
                ) ? $access_choices_internal : $access_choices_default);
                ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?= \K::$fw->TEXT_ACCESS ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'access_template',
                            ['' => ''] + $access_choices,
                            '',
                            ['class' => 'form-control input-medium']
                        ) ?>
                    </div>
                </div>

                <script>
                    $(function () {
                        $('#access_template').change(function () {
                            $('.access-group-cfg').val($(this).val());
                        })
                    })
                </script>

                <?php
                $groups_query = \K::model()->db_fetch('app_access_groups', [], ['order' => 'sort_order, name']);

                //while ($groups = db_fetch_array($groups_query)) {
                foreach ($groups_query as $groups) {
                    $entities_access_schema = \Models\Main\Users\Users::get_entities_access_schema(
                        \K::$fw->GET['entities_id'],
                        $groups['id']
                    );

                    if (!in_array('view', $entities_access_schema) and !in_array(
                            'view_assigned',
                            $entities_access_schema
                        ) and \K::$fw->GET['entities_id'] != 1) {
                        continue;
                    }

                    $access_schema = \Models\Main\Users\Users::get_fields_access_schema(\K::$fw->GET['entities_id'], $groups['id']);

                    $value = ($access_schema[\K::$fw->obj['id']] ?? 'yes');

                    ?>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="name"><?= $groups['name'] ?></label>
                        <div class="col-md-9">
                            <?= \Helpers\Html::select_tag(
                                'access[' . $groups['id'] . ']',
                                $access_choices,
                                $value,
                                ['class' => 'form-control input-medium access-group-cfg access_group_' . $groups['id']]
                            ) ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="tab-pane fade" id="note">
                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?= \K::$fw->TEXT_ADMINISTRATOR_NOTE ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::textarea_tag('notes', \K::$fw->obj['notes'], ['class' => 'form-control']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#fields_form').validate({
            ignore: '',

            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            },

            invalidHandler: function (e, validator) {

                var errors = validator.numberOfInvalids();

                if (errors) {
                    var message = '<?= \K::$fw->TEXT_ERROR_GENERAL ?>';
                    $("div#form-error-container").html('<div class="alert alert-danger">' + message + '</div>');
                    $("div#form-error-container").show();
                    $("div#form-error-container").delay(5000).fadeOut();
                }
            }
        });


        fields_types_configuration($('#type').val());

        check_is_heading_option()

        check_tooltip_item_page();

        $('#tooltip_in_item_page').change(function () {
            check_tooltip_item_page();
        })
    });

    function check_tooltip_item_page() {
        if ($('#tooltip_in_item_page').prop('checked')) {
            $('.tooltip_item_page').hide();
        } else {
            $('.tooltip_item_page').show();
        }
    }

    function fields_types_configuration(field_type) {
        check_is_heading_option()

        $('#fields_types_configuration').html('<div class="ajax-loading"></div>');

        $('#fields_types_configuration').load('<?= \Helpers\Urls::url_for(
            'main/entities/fields_configuration'
        )?>', {
            field_type: field_type,
            id: '<?= \K::$fw->obj["id"] ?>',
            entities_id: '<?= \K::$fw->GET["entities_id"] ?>'
        }, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();

                jQuery(window).resize();
            }
        });
    }

    //ajax configuration
    function fields_types_ajax_configuration(name, value) {
        field_type = $('#type').val();

        $('#fields_types_ajax_configuration_' + name).html('<div class="ajax-loading"></div>');

        $('#fields_types_ajax_configuration_' + name).load('<?= \Helpers\Urls::url_for(
            'main/entities/fields_ajax_configuration'
        )?>', {
            name: name,
            value: value,
            field_type: field_type,
            id: '<?= \K::$fw->obj["id"] ?>',
            entities_id: '<?= \K::$fw->GET["entities_id"]?>'
        }, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();

                jQuery(window).resize();
            }
        });
    }

    function check_is_heading_option() {
        selected_type = $('#type').val()

        if ($.inArray(selected_type, ["fieldtype_color", "fieldtype_nested_calculations", "fieldtype_subentity_form", "fieldtype_input_encrypted", "fieldtype_textarea_encrypted", "fieldtype_video", "fieldtype_ajax_request", "fieldtype_digital_signature", "fieldtype_time", "fieldtype_iframe", "fieldtype_input_protected", "fieldtype_google_map", "fieldtype_mapbbcode", "fieldtype_todo_list", "fieldtype_mysql_query", "fieldtype_image_map", "fieldtype_mind_map", "fieldtype_section", "fieldtype_input_numeric_comments", "fieldtype_input_url", "fieldtype_attachments", "fieldtype_input_file", "fieldtype_image", "fieldtype_image_ajax", "fieldtype_textarea_wysiwyg", "fieldtype_formula", "fieldtype_related_records", "fieldtype_boolean", "fieldtype_boolean_checkbox"]) == -1) {
            $('#is-heading-container').show()
        } else {
            $('#is-heading-container').hide()
            $('#is_heading').prop('checked', false)
        }
    }
</script>