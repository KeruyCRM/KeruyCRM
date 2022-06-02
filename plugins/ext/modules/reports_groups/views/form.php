<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'reports_form',
    url_for('ext/reports_groups/reports', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#access" data-toggle="tab"><?php
                    echo TEXT_ACCESS ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_info">

                <div class="form-group">
                    <label class="col-md-4 control-label" for="name"><?php
                        echo TEXT_NAME ?></label>
                    <div class="col-md-8">
                        <?php
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="cfg_menu_title"><?php
                        echo TEXT_MENU_ICON_TITLE; ?></label>
                    <div class="col-md-8">
                        <?php
                        echo input_tag('menu_icon', $obj['menu_icon'], ['class' => 'form-control input-large']); ?>
                        <?php
                        echo tooltip_text(TEXT_MENU_ICON_TITLE_TOOLTIP) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label"><?php
                        echo TEXT_COLOR ?></label>
                    <div class="col-md-8">
                        <table>
                            <tr>
                                <td>
                                    <?php
                                    echo input_color('icon_color', $obj['icon_color']) ?>
                                    <?php
                                    echo tooltip_text(TEXT_ICON) ?>
                                </td>
                                <td style="padding-left: 10px;">
                                    <?php
                                    echo input_color('bg_color', $obj['bg_color']) ?>
                                    <?php
                                    echo tooltip_text(TEXT_BACKGROUND) ?>
                                </td>
                            </tr>
                        </table>
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
                    <label class="col-md-4 control-label" for="in_dashboard"><?php
                        echo tooltip_icon(TEXT_DISPLAYS_AS_TAB) . TEXT_IN_DASHBOARD ?></label>
                    <div class="col-md-8">
                        <div class="checkbox-list"><label class="checkbox-inline"><?php
                                echo input_checkbox_tag('in_dashboard', '1', ['checked' => $obj['in_dashboard']]
                                ) ?></label></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="name"><?php
                        echo TEXT_SORT_ORDER ?></label>
                    <div class="col-md-8">
                        <?php
                        echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-small']) ?>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="access">

                <div class="form-group">
                    <label class="col-md-4 control-label" for="allowed_groups"><?php
                        echo TEXT_EXT_USERS_GROUPS ?></label>
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
                    <label class="col-md-4 control-label" for="assigned_to"><?php
                        echo TEXT_ASSIGNED_TO ?></label>
                    <div class="col-md-8">
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
            </div>
        </div>


    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#reports_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });
    });
</script>   


