<?php
echo ajax_modal_template_header(TEXT_HEADING_REPORTS_IFNO) ?>

<?php
echo form_tag(
    'common_filters_form',
    url_for('ext/common_filters/reports', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body ajax-modal-width-790">


        <ul class="nav nav-tabs" id="form_tabs">
            <li class="active"><a data-toggle="tab" href="#form_tab_general"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a data-toggle="tab" href="#form_tab_counter"><?php
                    echo TEXT_DISPLAY_AS_COUNTER ?></a></li>
            <li><a data-toggle="tab" href="#listing_configuration"><?php
                    echo TEXT_NAV_LISTING_CONFIG ?></a></li>
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

                <div class="form-group">
                    <label class="col-md-4 control-label" for="name"><?php
                        echo TEXT_NAME ?></label>
                    <div class="col-md-8">
                        <?php
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="users_groups"><?php
                        echo TEXT_USERS_GROUPS ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'users_groups[]',
                            access_groups::get_choices(),
                            $obj['users_groups'],
                            ['class' => 'form-onctrol chosen-select', 'multiple' => 'multiple']
                        ) ?>
                        <?php
                        echo tooltip_text(TEXT_SELECT_USER_GROUPS_COMMON_INFO) ?>
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


            </div>

            <div class="tab-pane" id="form_tab_counter">

                <div class="form-group">
                    <label class="col-md-4 control-label" for="in_dashboard_counter"><?php
                        echo TEXT_DISPLAY_AS_COUNTER ?></label>
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
                    <label class="col-md-4 control-label" for="menu_icon"><?php
                        echo TEXT_ICON; ?></label>
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


            </div>

            <div class="tab-pane" id="listing_configuration">

                <div id="listing_fields"></div>

            </div>


        </div>


    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#common_filters_form').validate({
            ignore: '',
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });

        $('#entities_id').change(function () {
            load_numeric_fields();
            load_listing_fields();
        })

        load_numeric_fields();

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
    
 
