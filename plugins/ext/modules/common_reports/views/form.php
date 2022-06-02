<?php
echo ajax_modal_template_header(TEXT_HEADING_REPORTS_IFNO) ?>

<?php
echo form_tag(
    'common_reports_form',
    url_for('ext/common_reports/reports', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body ajax-modal-width-790">
    <div class="form-body">


        <ul class="nav nav-tabs" id="form_tabs">
            <li class="active"><a data-toggle="tab" href="#form_tab_general"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a data-toggle="tab" href="#form_tab_counter"><?php
                    echo TEXT_DISPLAY_AS_COUNTER ?></a></li>
            <li><a data-toggle="tab" href="#form_tab_top_menu"><?php
                    echo TEXT_HEADER_TOP_MENU ?></a></li>
            <li><a data-toggle="tab" href="#listing_configuration"><?php
                    echo TEXT_NAV_LISTING_CONFIG ?></a></li>
            <?php
            if (CFG_NOTIFICATIONS_SCHEDULE == 1): ?>
                <li><a data-toggle="tab" href="#form_tab_notification"><?php
                        echo TEXT_NOTIFICATION ?></a></li>
            <?php
            endif ?>

        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="form_tab_general">

                <div class="form-group">
                    <label class="col-md-4 control-label" for="entities_id"><?php
                        echo TEXT_REPORT_ENTITY ?></label>
                    <div class="col-md-8"><?php
                        echo select_tag(
                            'entities_id',
                            entities::get_choices(),
                            $obj['entities_id'],
                            ['class' => 'form-control input-large required']
                        ) ?>
                    </div>
                </div>

                <div id="listing_types"></div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="name"><?php
                        echo TEXT_NAME ?></label>
                    <div class="col-md-8">
                        <?php
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="menu_icon"><?php
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
                        echo TEXT_IN_DASHBOARD ?></label>
                    <div class="col-md-8">
                        <div class="checkbox-list"><label class="checkbox-inline"><?php
                                echo input_checkbox_tag('in_dashboard', '1', ['checked' => $obj['in_dashboard']]
                                ) ?></label></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="displays_assigned_only"><?php
                        echo tooltip_icon(
                                TEXT_DISPLAYS_ASSIGNED_ITEMS_ONLY_INFO
                            ) . TEXT_DISPLAYS_ASSIGNED_ITEMS_ONLY ?></label>
                    <div class="col-md-8">
                        <div class="checkbox-list"><label class="checkbox-inline"><?php
                                echo input_checkbox_tag(
                                    'displays_assigned_only',
                                    '1',
                                    ['checked' => $obj['displays_assigned_only']]
                                ) ?></label></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="sort_order"><?php
                        echo TEXT_SORT_ORDER ?></label>
                    <div class="col-md-8">
                        <?php
                        echo input_tag(
                            'sort_order',
                            $obj['dashboard_sort_order'],
                            ['class' => 'form-control input-xsmall']
                        ) ?>
                    </div>
                </div>

                <h3 class="form-section"><?php
                    echo TEXT_ACCESS ?></h3>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="users_groups"><?php
                        echo TEXT_USERS_GROUPS ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'users_groups[]',
                            access_groups::get_choices(false),
                            $obj['users_groups'],
                            ['class' => 'form-control chosen-select', 'multiple' => 'multiple']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="users_groups"><?php
                        echo TEXT_USERS ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'assigned_to[]',
                            users::get_choices(false),
                            $obj['assigned_to'],
                            ['class' => 'form-control chosen-select', 'multiple' => 'multiple']
                        ) ?>
                    </div>
                </div>


            </div>

            <div class="tab-pane" id="form_tab_counter">

                <div class="form-group">
                    <label class="col-md-4 control-label" for="in_dashboard_counter"><?php
                        echo TEXT_DISPLAY_COUNTER_ON_DASHBOARD ?></label>
                    <div class="col-md-8">
                        <div class="checkbox-list"><label class="checkbox-inline"><?php
                                echo input_checkbox_tag(
                                    'in_dashboard_counter',
                                    '1',
                                    ['checked' => $obj['in_dashboard_counter']]
                                ) ?></label></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="in_dashboard_icon"><?php
                        echo TEXT_DISPLAY_ICON ?></label>
                    <div class="col-md-8">
                        <div class="checkbox-list"><label class="checkbox-inline"><?php
                                echo input_checkbox_tag(
                                    'in_dashboard_icon',
                                    '1',
                                    ['checked' => $obj['in_dashboard_icon']]
                                ) ?></label></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label"><?php
                        echo TEXT_COLOR ?></label>
                    <div class="col-md-2">
                        <?php
                        echo input_color('in_dashboard_counter_color', $obj['in_dashboard_counter_color']) ?>
                        <?php
                        echo tooltip_text(TEXT_TEXT) ?>
                    </div>
                    <div class="col-md-2">
                        <?php
                        echo input_color('in_dashboard_counter_bg_color', $obj['in_dashboard_counter_bg_color']) ?>
                        <?php
                        echo tooltip_text(TEXT_BACKGROUND) ?>
                    </div>
                </div>

                <div id="form_numeric_fields"></div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="dashboard_counter_hide_zero_count"><?php
                        echo TEXT_HIDE_COUNTER_IF_NO_RECORDS ?></label>
                    <div class="col-md-8">
                        <div class="checkbox-list"><label class="checkbox-inline"><?php
                                echo input_checkbox_tag(
                                    'dashboard_counter_hide_zero_count',
                                    '1',
                                    ['checked' => $obj['dashboard_counter_hide_zero_count']]
                                ) ?></label></div>
                    </div>
                </div>

            </div>

            <div class="tab-pane" id="form_tab_top_menu">

                <div class="form-group">
                    <label class="col-md-4 control-label" for="in_header"><?php
                        echo tooltip_icon(TEXT_DISPLAY_IN_HEADER_TOOLTIP) . TEXT_DISPLAY_IN_HEADER ?></label>
                    <div class="col-md-8">
                        <div class="checkbox-list"><label class="checkbox-inline"><?php
                                echo input_checkbox_tag('in_header', '1', ['checked' => $obj['in_header']]) ?></label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="in_header_autoupdate"><?php
                        echo tooltip_icon(TEXT_DISPLAY_IN_HEADER_AUTOUPDATE_TOOLTIP) . TEXT_AUTOUPDATE ?></label>
                    <div class="col-md-8">
                        <div class="checkbox-list"><label class="checkbox-inline"><?php
                                echo input_checkbox_tag(
                                    'in_header_autoupdate',
                                    '1',
                                    ['checked' => $obj['in_header_autoupdate']]
                                ) ?></label></div>
                    </div>
                </div>

            </div>

            <div class="tab-pane" id="listing_configuration">

                <div id="listing_fields"></div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="sort_order"><?php
                        echo TEXT_ROWS_PER_PAGE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'rows_per_page',
                            ($obj['rows_per_page'] == 0 ? '' : $obj['rows_per_page']),
                            ['class' => 'form-control input-xsmall']
                        ) ?>
                    </div>
                </div>

            </div>

            <div class="tab-pane" id="form_tab_notification">

                <div class="form-group">
                    <label class="col-md-4 control-label" for="name"><?php
                        echo TEXT_DAY ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_checkboxes_tag(
                            'notification_days',
                            app_get_days_choices(),
                            $obj['notification_days']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="name"><?php
                        echo TEXT_TIME ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'notification_time[]',
                            app_get_hours_choices(),
                            $obj['notification_time'],
                            ['multiple' => 'multiple', 'class' => 'form-control chosen-select']
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
        $('#common_reports_form').validate({
            ignore: '',
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });

        $('#entities_id').change(function () {
            load_numeric_fields();
            load_listing_types();
            load_listing_fields();
        })

        load_numeric_fields();
        load_listing_types();
        load_listing_fields();

    });

    function load_numeric_fields() {
        $('#form_numeric_fields').html('');
        $('#form_numeric_fields').addClass('ajax-loading');
        $('#form_numeric_fields').load('<?php echo url_for(
            "reports/reports",
            "action=get_numeric_fields&id=" . $obj['id']
        ) ?>', {entities_id: $('#entities_id').val()}, function () {
            $('#form_numeric_fields').removeClass('ajax-loading');
            appHandleUniform();
        })
    }

    function load_listing_types() {
        $('#listing_types').load('<?php echo url_for(
            "reports/reports",
            "action=get_listing_types&id=" . $obj['id']
        ) ?>', {entities_id: $('#entities_id').val()}, function () {

        })
    }

    function load_listing_fields() {
        $('#listing_fields').html('');
        $('#listing_fields').addClass('ajax-loading');
        $('#listing_fields').load('<?php echo url_for(
            "reports/reports",
            "action=get_listing_fields&id=" . $obj['id']
        ) ?>', {entities_id: $('#entities_id').val()}, function () {
            $('#listing_fields').removeClass('ajax-loading');
            appHandleUniform();
        })
    }

</script>   
    
 
