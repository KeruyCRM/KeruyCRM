<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'reports_form',
    url_for('ext/pivot_tables/reports', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">


        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#graphic_report" data-toggle="tab"><?php
                    echo TEXT_EXT_GRAPHIC_REPORT ?></a></li>
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
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-xlarge required']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="type"><?php
                        echo TEXT_REPORT_ENTITY ?></label>
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

                <?php
                $choices = [
                    '' => '',
                    'default' => TEXT_DEFAULT,
                    'quick_filters' => TEXT_QUICK_FILTERS_PANELS,
                ];
                ?>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="default_view"><?php
                        echo TEXT_FILTERS_PANELS ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'filters_panel',
                            $choices,
                            $obj['filters_panel'],
                            ['class' => 'form-control input-medium']
                        ) ?>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-4 control-label" for="height"><?php
                        echo tooltip_icon(TEXT_DEFAULT . ': 600') . TEXT_HEIGHT ?></label>
                    <div class="col-md-8">
                        <?php
                        echo input_tag(
                            'height',
                            $obj['height'],
                            ['class' => 'form-control input-small', 'type' => 'number']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="in_menu"><?php
                        echo tooltip_icon(TEXT_EXT_DISPLYA_IN_MAIN_MENU_TIP) . TEXT_EXT_DISPLYA_IN_MAIN_MENU ?></label>
                    <div class="col-md-8">
                        <div class="checkbox-list"><label class="checkbox-inline"><?php
                                echo input_checkbox_tag('in_menu', '1', ['checked' => $obj['in_menu']]) ?></label></div>
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

            <div class="tab-pane fade" id="graphic_report">

                <?php
                $choices = [
                    '' => '',
                    'pie' => TEXT_EXT_PIE_CHART,
                    'column' => TEXT_EXT_COLUMN_CHART,
                    'stacked_column' => TEXT_EXT_STACKED_COLUMN_CHART,
                    'stacked_percent' => TEXT_EXT_STACKED_PERCENT_COLUMN_CHART,
                    'bar' => TEXT_EXT_BAR_CHART,
                    'line' => TEXT_EXT_LINE_CHART,
                    'funnel' => TEXT_EXT_FUNNEL_CHART,
                    'pyramid' => TEXT_EXT_PYRAMID_CHART,
                    'area' => TEXT_EXT_AREA_CHART,
                    'stacked_area' => TEXT_EXT_STACKED_AREA_CHART,

                ];
                ?>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="type"><?php
                        echo TEXT_TYPE ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'chart_type',
                            $choices,
                            $obj['chart_type'],
                            ['class' => 'form-control input-large']
                        ) ?>
                    </div>
                </div>

                <?php
                $choices = [
                    'right' => TEXT_ON_RIGHT,
                    'left' => TEXT_ON_LEFT,
                    'top' => TEXT_ON_TOP,
                    'bottom' => TEXT_ON_BOTTOM,
                    'only_chart' => TEXT_EXT_ONLY_CHART,
                ]
                ?>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="position"><?php
                        echo TEXT_POSITION ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'chart_position',
                            $choices,
                            $obj['chart_position'],
                            ['class' => 'form-control input-medium']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="chart_height"><?php
                        echo tooltip_icon(TEXT_DEFAULT . ': 600') . TEXT_HEIGHT ?></label>
                    <div class="col-md-8">
                        <?php
                        echo input_tag(
                            'chart_height',
                            $obj['chart_height'],
                            ['class' => 'form-control input-small', 'type' => 'number']
                        ) ?>
                    </div>
                </div>
                <?php
                $colors = strlen($obj['colors']) ? explode(',', $obj['colors']) : '';
                $html = '';
                for ($i = 0; $i < 10; $i++) {
                    $html .= input_color('colors[' . $i . ']', (isset($colors[$i]) ? $colors[$i] : ''));
                }
                ?>
                <div class="form-group">
                    <label class="col-md-4 control-label"><?php
                        echo tooltip_icon(TEXT_EXT_PIVOT_TABLES_COLORS_TIP) . TEXT_COLOR ?></label>
                    <div class="col-md-8">
                        <?php
                        echo $html ?>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="access">
                <?php
                $users_groups = strlen($obj['users_groups']) ? json_decode($obj['users_groups'], true) : [];
                foreach (access_groups::get_choices(false) as $group_id => $group_name) {
                    ?>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="allowed_groups"><?php
                            echo $group_name ?></label>
                        <div class="col-md-8">
                            <?php
                            echo select_tag(
                                'access[' . $group_id . ']',
                                ['' => '', 'view' => TEXT_VIEW_ONLY_ACCESS, 'full' => TEXT_FULL_ACCESS],
                                (isset($users_groups[$group_id]) ? $users_groups[$group_id] : ''),
                                ['class' => 'form-control input-medium']
                            ) ?>
                        </div>
                    </div>
                    <?php
                }
                ?>


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
            ignore: '',
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });

    });
</script>  
 