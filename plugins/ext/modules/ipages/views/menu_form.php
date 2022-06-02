<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for('ext/ipages/configuration', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<?php
echo input_hidden_tag('is_menu', '1') ?>
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

                <?php
                if (db_count('app_ext_ipages', '1', 'is_menu')): ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="name"><?php
                            echo TEXT_PARENT ?></label>
                        <div class="col-md-9">
                            <?php
                            echo select_tag(
                                'parent_id',
                                ipages::get_menu_choices(),
                                $obj['parent_id'],
                                ['class' => 'form-control input-large']
                            ) ?>
                        </div>
                    </div>
                <?php
                endif; ?>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_NAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-3 control-label" for="cfg_menu_title"><?php
                        echo TEXT_MENU_ICON_TITLE; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('menu_icon', $obj['menu_icon'], ['class' => 'form-control input-large required']
                        ); ?>
                        <?php
                        echo tooltip_text(TEXT_MENU_ICON_TITLE_TOOLTIP) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"><?php
                        echo TEXT_COLOR ?></label>
                    <div class="col-md-9">
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
                    <label class="col-md-3 control-label" for="sort_order"><?php
                        echo TEXT_SORT_ORDER ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-xsmall']) ?>
                    </div>
                </div>


            </div>
            <div class="tab-pane fade" id="access">

                <p><?php
                    echo TEXT_EXT_IPAGES_USERS_GROUPS_INFO ?></p>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="allowed_groups"><?php
                        echo TEXT_EXT_USERS_GROUPS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'users_groups[]',
                            access_groups::get_choices(),
                            $obj['users_groups'],
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => true]
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="allowed_groups"><?php
                        echo TEXT_ASSIGNED_TO ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'assigned_to[]',
                            users::get_choices(),
                            $obj['assigned_to'],
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => true]
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
        $('#configuration_form').validate();
    });
</script>   