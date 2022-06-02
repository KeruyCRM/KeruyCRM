<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'track_changes_form',
    url_for('ext/track_changes/reports', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#confirmation_color" data-toggle="tab"><?php
                    echo TEXT_COLOR ?></a></li>
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
                    <label class="col-md-3 control-label" for="menu_icon"><?php
                        echo TEXT_ICON; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('menu_icon', $obj['menu_icon'], ['class' => 'form-control input-large']); ?>
                        <?php
                        echo tooltip_text(TEXT_MENU_ICON_TITLE_TOOLTIP) ?>
                    </div>
                </div>

                <?php
                $choices = [];
                $choices['in_menu'] = TEXT_EXT_IN_MENU;
                $choices['in_reports_menu'] = TEXT_EXT_IN_REPORTS_MENU;
                $choices['in_header_menu'] = TEXT_EXT_IN_HEADER_MENU;
                ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="button_position"><?php
                        echo TEXT_EXT_REPORT_POSITION; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'position[]',
                            $choices,
                            $obj['position'],
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                        ); ?>
                        <?php
                        echo tooltip_text(TEXT_EXT_TC_REPORT_POSITION_HEADER_NOTE) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="users_groups"><?php
                        echo TEXT_USERS_GROUPS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'users_groups[]',
                            access_groups::get_choices(),
                            (strlen($obj['users_groups']) ? $obj['users_groups'] : -1),
                            ['class' => 'form-control input-xlarge chosen-select required', 'multiple' => 'multiple']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="users_groups"><?php
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

                <div class="form-group">
                    <label class="col-md-3 control-label" for="menu_icon"><?php
                        echo TEXT_EXT_KEEP_HISTORY; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'keep_history',
                            $obj['keep_history'],
                            ['class' => 'form-control input-xsmall required number']
                        ); ?>
                        <?php
                        echo tooltip_text(TEXT_EXT_KEEP_HISTORY_DAYS_TIP) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="menu_icon"><?php
                        echo TEXT_ROWS_PER_PAGE; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'rows_per_page',
                            $obj['rows_per_page'],
                            ['class' => 'form-control input-xsmall number']
                        ); ?>
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" id="confirmation_color">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="button_color"><?php
                        echo TEXT_EXT_NEW_RECORD ?></label>
                    <div class="col-md-9">
                        <div class="input-group input-small color colorpicker-default" data-color="<?php
                        echo(strlen($obj['color_insert']) > 0 ? $obj['color_insert'] : '#428bca') ?>">
                            <?php
                            echo input_tag('color_insert', $obj['color_insert'], ['class' => 'form-control input-small']
                            ) ?>
                            <span class="input-group-btn">
		  				<button class="btn btn-default" type="button">&nbsp;</button>
		  			</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="button_color"><?php
                        echo TEXT_EXT_NEW_COMMENT ?></label>
                    <div class="col-md-9">
                        <div class="input-group input-small color colorpicker-default" data-color="<?php
                        echo(strlen($obj['color_comment']) > 0 ? $obj['color_comment'] : '#428bca') ?>">
                            <?php
                            echo input_tag(
                                'color_comment',
                                $obj['color_comment'],
                                ['class' => 'form-control input-small']
                            ) ?>
                            <span class="input-group-btn">
		  				<button class="btn btn-default" type="button">&nbsp;</button>
		  			</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="button_color"><?php
                        echo TEXT_EXT_CHANGED ?></label>
                    <div class="col-md-9">
                        <div class="input-group input-small color colorpicker-default" data-color="<?php
                        echo(strlen($obj['color_update']) > 0 ? $obj['color_update'] : '#428bca') ?>">
                            <?php
                            echo input_tag('color_update', $obj['color_update'], ['class' => 'form-control input-small']
                            ) ?>
                            <span class="input-group-btn">
		  				<button class="btn btn-default" type="button">&nbsp;</button>
		  			</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="button_color"><?php
                        echo TEXT_EXT_DELETED ?></label>
                    <div class="col-md-9">
                        <div class="input-group input-small color colorpicker-default" data-color="<?php
                        echo(strlen($obj['color_delete']) > 0 ? $obj['color_delete'] : '#ababab') ?>">
                            <?php
                            echo input_tag('color_delete', $obj['color_delete'], ['class' => 'form-control input-small']
                            ) ?>
                            <span class="input-group-btn">
		  				<button class="btn btn-default" type="button">&nbsp;</button>
		  			</span>
                        </div>
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
        $('#track_changes_form').validate({ignore: ''});
    });
</script>   
    
 
