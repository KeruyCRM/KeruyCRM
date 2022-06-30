<?php
echo ajax_modal_template_header(TEXT_EXT_HEADING_TEMPLATE_INFO) ?>

<?php
echo form_tag(
    'comments_templates_form',
    url_for('ext/templates/comments_templates', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#comment_info" data-toggle="tab"><?php
                    echo TEXT_COMMENT_INFO ?></a></li>
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
                    <label class="col-md-3 control-label" for="entities_id"><?php
                        echo TEXT_ENTITY ?></label>
                    <div class="col-md-9"><?php
                        echo select_tag(
                            'entities_id',
                            entities::get_choices(),
                            $obj['entities_id'],
                            ['class' => 'form-control input-large required']
                        ) ?>
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
                    <label class="col-md-3 control-label" for="users_groups"><?php
                        echo TEXT_USERS_GROUPS ?></label>
                    <div class="col-md-9">
                        <div class="checkbox-list"><label class="checkbox-inline"><?php
                                echo select_checkboxes_tag(
                                    'users_groups',
                                    access_groups::get_choices(),
                                    $obj['users_groups']
                                ) ?></label></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="users_groups"><?php
                        echo TEXT_ASSIGNED_TO ?></label>
                    <div class="col-md-9">
                        <?php
                        $attributes = [
                            'class' => 'form-control input-xlarge chosen-select',
                            'multiple' => 'multiple',
                            'data-placeholder' => TEXT_SELECT_SOME_VALUES
                        ];

                        $assigned_to = (strlen($obj['assigned_to']) > 0 ? explode(',', $obj['assigned_to']) : '');

                        echo select_tag(
                            'assigned_to[]',
                            users::get_choices(),
                            explode(',', $obj['assigned_to']),
                            $attributes
                        );
                        ?>
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
            <div class="tab-pane fade" id="comment_info">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="sort_order"><?php
                        echo TEXT_DESCRIPTION ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag('description', $obj['description'], ['class' => 'form-control']) ?>
                        <?php
                        echo tooltip_text(TEXT_EXT_COMMENTS_TEMPLATES_DESCRIPTION_INFO) ?>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<?php
echo comments_templates::render_entities_cfg_js() ?>

<script>
    $(function () {
        $('#comments_templates_form').validate();

        $('#entities_id').change(function () {
            check_editor_in_comments()
        })

        check_editor_in_comments()
    });

    function check_editor_in_comments() {
        entities_id = $('#entities_id').val();

        if (use_editor_in_comments[entities_id] == true && !$('#description').hasClass('editor')) {
            $('#description').addClass('editor')

            use_editor('description');
        }

        if (use_editor_in_comments[entities_id] == false && $('#description').hasClass('editor')) {
            $('#description').removeClass('editor')

            CKEDITOR_holders['description'].destroy();
        }
    }
</script>  