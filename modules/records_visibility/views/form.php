<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'pages_form',
    url_for(
        'records_visibility/rules',
        'action=save&entities_id=' . _get::int('entities_id') . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body">


        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#merged_fields_tab" data-toggle="tab"><?php
                    echo TEXT_LINKED_ENTITIES ?></a></li>
            <li><a href="#note" data-toggle="tab"><?php
                    echo TEXT_NOTE ?></a></li>
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
                    <label class="col-md-3 control-label" for="users_groups"><?php
                        echo TEXT_USERS_GROUPS ?></label>
                    <div class="col-md-9">
                        <?php
                        $attributes = [
                            'class' => 'form-control input-xlarge chosen-select required',
                            'multiple' => 'multiple',
                            'data-placeholder' => TEXT_SELECT_SOME_VALUES
                        ];

                        $users_groups = (strlen($obj['users_groups']) > 0 ? explode(',', $obj['users_groups']) : []);
                        echo select_tag(
                            'users_groups[]',
                            access_groups::get_choices(false),
                            $users_groups,
                            $attributes
                        );
                        echo tooltip_text(TEXT_USERS_GROUPS_FOR_RULE_TIP);
                        ?>
                    </div>
                </div>

            </div>

            <?php
            $merged_fields_choices = records_visibility::merget_fields_choices(_get::int('entities_id'));
            ?>
            <div class="tab-pane fade" id="merged_fields_tab">
                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_SETTINGS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'merged_fields[]',
                            $merged_fields_choices,
                            $obj['merged_fields'],
                            ['class' => 'form-control chosen-select', 'multiple' => 'multiple']
                        ) ?>
                        <?php
                        echo tooltip_text(TEXT_RECORDS_VISIBILITY_LINK_ENTITY_INFO) ?>
                    </div>
                </div>
                <?php
                if (count($merged_fields_choices) > 1) {
                    $empty_values_fields_choices = [];
                    foreach ($merged_fields_choices as $merged_fields => $value) {
                        $merged_fields = explode('-', $merged_fields);
                        $fields_id = $merged_fields[1];
                        if (isset($app_fields_cache[_get::int('entities_id')][$fields_id])) {
                            $empty_values_fields_choices[$fields_id] = $app_entities_cache[_get::int(
                                    'entities_id'
                                )]['name'] . ': ' . fields::get_name_by_id($fields_id);
                        }
                    }

                    ?>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="name"><?php
                            echo TEXT_CONDITION_EMPTY_VALUE ?></label>
                        <div class="col-md-9">
                            <?php
                            echo select_tag(
                                'merged_fields_empty_values[]',
                                $empty_values_fields_choices,
                                $obj['merged_fields_empty_values'],
                                ['class' => 'form-control chosen-select', 'multiple' => 'multiple']
                            ) ?>
                            <?php
                            echo tooltip_text(TEXT_RECORDS_VISIBILITY_EMPTY_VALUE_INFO) ?>
                        </div>
                    </div>
                <?php
                } ?>

            </div>

            <div class="tab-pane fade" id="note">
                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_ADMINISTRATOR_NOTE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag('notes', $obj['notes'], ['class' => 'form-control']) ?>
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
        $('#pages_form').validate({
            ignore: '',
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });
    });

</script>   
    
 
