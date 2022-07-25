<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_INFO) ?>

<?= \Helpers\Html::form_tag(
    'users_alerts_form',
    \Helpers\Urls::url_for(
        'main/users_alerts/users_alerts/save',
        (isset(\K::$fw->GET['id']) ? 'id=' . \K::$fw->GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body">
        <div class="form-group">
            <label class="col-md-3 control-label" for="is_active"><?= \K::$fw->TEXT_IS_ACTIVE ?></label>
            <div class="col-md-9">
                <p class="form-control-static"><?= \Helpers\Html::input_checkbox_tag(
                        'is_active',
                        \K::$fw->obj['is_active'],
                        ['checked' => (\K::$fw->obj['is_active'] == 1 ? 'checked' : '')]
                    ) ?></p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="type"><?= \K::$fw->TEXT_TYPE ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'type',
                    \Models\Main\Users\Users_alerts::get_types_choices(),
                    \K::$fw->obj['type'],
                    ['class' => 'form-control input-medium']
                ) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="title"><?= \K::$fw->TEXT_TITLE ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'title',
                    \K::$fw->obj['title'],
                    ['class' => 'form-control input-xlarge required']
                ) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="description"><?= \K::$fw->TEXT_DESCRIPTION ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::textarea_tag('description', \K::$fw->obj['description'], ['class' => 'editor']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="location"><?= \K::$fw->TEXT_LOCATION ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'location',
                    \Models\Main\Users\Users_alerts::get_location_choices(),
                    \K::$fw->obj['location'],
                    ['class' => 'form-control input-medium']
                ) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="start_date"><?= \K::$fw->TEXT_DISPLAY_DATE ?></label>
            <div class="col-md-9">
                <div class="input-group input-large datepicker input-daterange daterange-filter">
            <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
            </span>
                    <?= \Helpers\Html::input_tag(
                        'start_date',
                        (\K::$fw->obj['start_date'] > 0 ? date('Y-m-d', \K::$fw->obj['start_date']) : ''),
                        ['class' => 'form-control', 'placeholder' => \K::$fw->TEXT_DATE_FROM]
                    ) ?>
                    <span class="input-group-addon">
                    <i style="cursor:pointer" class="fa fa-refresh" aria-hidden="true"
                       title="<?= \K::$fw->TEXT_RESET ?>"
                       onClick="app_reset_date_range_input('daterange-filter','start_date','end_date')"></i>
            </span>
                    <?= \Helpers\Html::input_tag(
                        'end_date',
                        (\K::$fw->obj['end_date'] > 0 ? date('Y-m-d', \K::$fw->obj['end_date']) : ''),
                        ['class' => 'form-control', 'placeholder' => \K::$fw->TEXT_DATE_TO]
                    ) ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="users_groups"><?= \K::$fw->TEXT_USERS_GROUPS ?></label>
            <div class="col-md-9">
                <?php
                $attributes = [
                    'class' => 'form-control input-xlarge chosen-select',
                    'multiple' => 'multiple',
                    'data-placeholder' => \K::$fw->TEXT_SELECT_SOME_VALUES
                ];

                $users_groups = (strlen(\K::$fw->obj['users_groups']) > 0 ? explode(
                    ',',
                    \K::$fw->obj['users_groups']
                ) : []);
                echo \Helpers\Html::select_tag(
                    'users_groups[]',
                    \Models\Main\Access_groups::get_choices(),
                    $users_groups,
                    $attributes
                );
                ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="assigned_to"><?= \K::$fw->TEXT_ASSIGNED_TO ?></label>
            <div class="col-md-9">
                <?php
                $attributes = [
                    'class' => 'form-control input-xlarge chosen-select',
                    'multiple' => 'multiple',
                    'data-placeholder' => \K::$fw->TEXT_SELECT_SOME_VALUES
                ];

                $assigned_to = (strlen(\K::$fw->obj['assigned_to']) > 0 ? explode(
                    ',',
                    \K::$fw->obj['assigned_to']
                ) : '');
                echo \Helpers\Html::select_tag(
                    'assigned_to[]',
                    \Models\Main\Users\Users::get_choices(),
                    $assigned_to,
                    $attributes
                );
                echo \Helpers\App::tooltip_text(\K::$fw->TEXT_IF_NOT_ASSIGNED_DISPLAY_EVERYONE);
                ?>
            </div>
        </div>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#users_alerts_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });
    });
</script>