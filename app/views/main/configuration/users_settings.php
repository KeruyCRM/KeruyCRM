<h3 class="page-title"><?= \K::$fw->TEXT_HEADING_APPLICATION ?></h3>

<?= \Helpers\Html::form_tag(
    'cfg_form',
    \Helpers\Urls::url_for('main/configuration/save'),
    ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']
) ?>
<?= \Helpers\Html::input_hidden_tag('redirect_to', 'main/configuration/users_settings') ?>
<div class="form-body">
    <div class="tabbable tabbable-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#users_configuration"
                                  data-toggle="tab"><?= \K::$fw->TEXT_USERS_CONFIGURATION ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="users_configuration">

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_APP_DISPLAY_USER_NAME_ORDER"><?= \K::$fw->TEXT_DISPLAY_USER_NAME_ORDER ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[APP_DISPLAY_USER_NAME_ORDER]',
                            [
                                'firstname_lastname' => \K::$fw->TEXT_FIRSTNAME_LASTNAME,
                                'lastname_firstname' => \K::$fw->TEXT_LASTNAME_FIRSTNAME
                            ],
                            \K::$fw->CFG_APP_DISPLAY_USER_NAME_ORDER,
                            ['class' => 'form-control input-medium']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_PASSWORD_MIN_LENGTH"><?= \K::$fw->TEXT_MIN_PASSWORD_LENGTH ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag(
                            'CFG[PASSWORD_MIN_LENGTH]',
                            \K::$fw->CFG_PASSWORD_MIN_LENGTH,
                            ['class' => 'form-control input-small required']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_PASSWORD_MIN_LENGTH"><?= \K::$fw->TEXT_STRONG_PASSWORD ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[IS_STRONG_PASSWORD]',
                            \K::$fw->default_selector,
                            \K::$fw->CFG_IS_STRONG_PASSWORD,
                            ['class' => 'form-control input-small required']
                        ); ?>
                        <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_STRONG_PASSWORD_TIP) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_ALLOW_CHANGE_USERNAME"><?= \K::$fw->TEXT_ALLOW_CHANGE_USERNAME ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[ALLOW_CHANGE_USERNAME]',
                            \K::$fw->default_selector,
                            \K::$fw->CFG_ALLOW_CHANGE_USERNAME,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_ALLOW_REGISTRATION_WITH_THE_SAME_EMAIL"><?= \K::$fw->TEXT_ALLOW_REGISTRATION_WITH_THE_SAME_EMAIL ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[ALLOW_REGISTRATION_WITH_THE_SAME_EMAIL]',
                            \K::$fw->default_selector,
                            \K::$fw->CFG_ALLOW_REGISTRATION_WITH_THE_SAME_EMAIL,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_APP_DISABLE_CHANGE_PWD"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_SELECT_USERS_GROUPS
                        ) . \K::$fw->TEXT_DISABLE_CHANGE_PWD ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[APP_DISABLE_CHANGE_PWD][]',
                            ['' => \K::$fw->TEXT_NONE] + \Models\Main\Access_groups::get_choices(false),
                            \K::$fw->CFG_APP_DISABLE_CHANGE_PWD,
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                        ); ?>
                    </div>
                </div>

                <h3 class="form-section"><?= \K::$fw->TEXT_USERS_GROUPS ?></h3>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_ENABLE_MULTIPLE_ACCESS_GROUPS"><?= \K::$fw->TEXT_ASSIGN_USER_TO_MULTIPLE_GROUPS ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[ENABLE_MULTIPLE_ACCESS_GROUPS]',
                            \K::$fw->default_selector,
                            \K::$fw->CFG_ENABLE_MULTIPLE_ACCESS_GROUPS,
                            ['class' => 'form-control input-small']
                        ); ?>
                        <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_ASSIGN_USER_TO_MULTIPLE_GROUPS_INFO) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_DISPLAY_USER_GROUP_IN_MENU"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_SELECT_USERS_GROUPS
                        ) . \K::$fw->TEXT_DISPLAY_USER_GROUP_IN_MENU ?></label>
                    <div class="col-md-2" style="width: 150px">
                        <?= \Helpers\Html::select_tag(
                            'CFG[DISPLAY_USER_GROUP_IN_MENU]',
                            \K::$fw->default_selector,
                            \K::$fw->CFG_DISPLAY_USER_GROUP_IN_MENU,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                    <div class="col-md-6">
                        <?= \Helpers\Html::select_tag(
                            'CFG[DISPLAY_USER_GROUP_ID_IN_MENU][]',
                            ['' => \K::$fw->TEXT_NONE] + \Models\Main\Access_groups::get_choices(),
                            \K::$fw->CFG_DISPLAY_USER_GROUP_ID_IN_MENU,
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                        ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>

</div>
</form>

<script>
    $(function () {
        $('#cfg_form').validate({
            rules: {
                APP_LOGO: {
                    required: false,
                    extension: "gif|jpeg|jpg|png"
                }
            }
        });

        $(".input-masked").each(function () {
            $.mask.definitions["~"] = "[,. *]";
            $(this).mask($(this).attr("data-mask"));
        })

    });
</script> 