<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php
        echo TEXT_INFO ?></h4>
</div>


<?php
echo form_tag(
    'menu_form',
    url_for('entities/menu', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<?php
echo input_hidden_tag('parent_id', $obj['parent_id']) ?>
<div class="modal-body">
    <div class="form-body">

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_NAME ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('name', $obj['name'], ['class' => 'form-control input-medium required']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="icon"><?php
                echo TEXT_MENU_ICON_TITLE ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('icon', $obj['icon'], ['class' => 'form-control input-medium ']) ?>
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

        <?php
        $choices = [
            'entity' => TEXT_ENTITY,
            'url' => TEXT_URL
        ];
        ?>

        <div class="form-group">
            <label class="col-md-3 control-label" for="type"><?php
                echo TEXT_TYPE ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag('type', $choices, $obj['type'], ['class' => 'form-control input-medium']) ?>
            </div>
        </div>

        <div class="form-group" form_display_rules="type:entity">
            <label class="col-md-3 control-label" for="is_default"><?php
                echo tooltip_icon(TEXT_SORT_ITEMS_IN_LIST) . TEXT_SELECT_ENTITIES ?></label>
            <div class="col-md-9">
                <div class="checkbox-list"><label class="checkbox-inline"><?php
                        echo select_tag(
                            'entities_list[]',
                            entities::get_choices(true),
                            $obj['entities_list'],
                            [
                                'class' => 'form-control input-xlarge chosen-select chosen-sortable',
                                'chosen_order' => $obj['entities_list'],
                                'multiple' => 'multiple'
                            ]
                        ) ?></label></div>
            </div>
        </div>

        <div class="form-group" form_display_rules="type:entity">
            <label class="col-md-3 control-label" for="is_default"><?php
                echo tooltip_icon(TEXT_SORT_ITEMS_IN_LIST) . TEXT_SELECT_REPORTS ?></label>
            <div class="col-md-9">
                <div class="checkbox-list"><label class="checkbox-inline"><?php
                        echo select_tag(
                            'reports_list[]',
                            entities_menu::get_reports_choices(),
                            $obj['reports_list'],
                            [
                                'class' => 'form-control input-xlarge chosen-select chosen-sortable',
                                'chosen_order' => $obj['reports_list'],
                                'multiple' => 'multiple'
                            ]
                        ) ?></label></div>
            </div>
        </div>

        <?php
        if (is_ext_installed()): ?>
            <div class="form-group" form_display_rules="type:entity">
                <label class="col-md-3 control-label" for="is_default"><?php
                    echo tooltip_icon(TEXT_SORT_ITEMS_IN_LIST) . TEXT_EXT_IPAGES ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list"><label class="checkbox-inline"><?php
                            echo select_tag(
                                'pages_list[]',
                                ipages::get_choices(),
                                $obj['pages_list'],
                                [
                                    'class' => 'form-control input-xlarge chosen-select chosen-sortable',
                                    'chosen_order' => $obj['reports_list'],
                                    'multiple' => 'multiple'
                                ]
                            ) ?></label></div>
                </div>
            </div>
        <?php
        endif ?>

        <div class="form-group" form_display_rules="type:url">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_URL ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('url', $obj['url'], ['class' => 'form-control required']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="sort_order"><?php
                echo TEXT_SORT_ORDER ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-small number']) ?>
            </div>
        </div>

        <h3 class="form-section" form_display_rules="type:url"><?php
            echo TEXT_ACCESS ?></h3>

        <div class="form-group" form_display_rules="type:url">
            <label class="col-md-3 control-label" for="users_groups"><?php
                echo TEXT_USERS_GROUPS ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'users_groups[]',
                    access_groups::get_choices(),
                    $obj['users_groups'],
                    ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                ) ?>
            </div>
        </div>

        <div class="form-group" form_display_rules="type:url">
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

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#menu_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });
    });

</script>   