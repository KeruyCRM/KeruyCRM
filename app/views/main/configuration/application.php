<h3 class="page-title"><?= \K::$fw->TEXT_HEADING_APPLICATION ?></h3>

<?= \Helpers\Html::form_tag(
    'cfg_form',
    \Helpers\Urls::url_for('main/configuration/save', 'redirect_to=main/configuration/application'),
    ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']
) ?>
<div class="form-body">


    <div class="tabbable tabbable-custom">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?= \K::$fw->TEXT_GENERAL_INFO ?></a></li>

        </ul>


        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_info">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_NAME"><?= \K::$fw->TEXT_APPLICATION_NAME ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag('CFG[APP_NAME]', \K::$fw->CFG_APP_NAME, ['class' => 'form-control input-large required']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_SHORT_NAME"><?= \K::$fw->TEXT_APPLICATION_SHORT_NAME ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag(
                            'CFG[APP_SHORT_NAME]',
                            \K::$fw->CFG_APP_SHORT_NAME,
                            ['class' => 'form-control input-small required']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="APP_LOGO"><?= \K::$fw->TEXT_APPLICATION_LOGO ?></label>
                    <div class="col-md-9">
                        <p class="form-control-static">
                            <?php echo \Helpers\Html::input_file_tag(
                                    'APP_LOGO',
                                    ['accept' => \Tools\FieldsTypes\Fieldtype_attachments::get_accept_types_by_extensions('gif,jpg,png')]
                                ) . \Helpers\Html::input_hidden_tag('CFG[APP_LOGO]', \K::$fw->CFG_APP_LOGO);

                            if (is_file(\K::$fw->DIR_FS_UPLOADS . '/' . \K::$fw->CFG_APP_LOGO)) {
                                echo '<span class="help-block">' . \K::$fw->CFG_APP_LOGO . '<label class="checkbox">' . \Helpers\Html::input_checkbox_tag(
                                        'delete_logo'
                                    ) . ' ' . \K::$fw->TEXT_DELETE . '</label></span>';
                            }
                            ?>
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_LOGO_URL"><?= \K::$fw->TEXT_APP_LOGO_URL ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag('CFG[APP_LOGO_URL]', \K::$fw->CFG_APP_LOGO_URL, ['class' => 'form-control input-large']
                        ); ?>
                        <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_APP_LOGO_URL_TOOLTIP) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="APP_LOGO"><?= \K::$fw->TEXT_FAVICON ?> (32x32)</label>
                    <div class="col-md-9">
                        <p class="form-control-static">
                            <?php
                            echo \Helpers\Html::input_file_tag(
                                    'APP_FAVICON',
                                    [
                                        'accept' => \Tools\FieldsTypes\Fieldtype_attachments::get_accept_types_by_extensions(
                                            'gif,jpg,png,ico'
                                        )
                                    ]
                                ) . \Helpers\Html::input_hidden_tag('CFG[APP_FAVICON]', \K::$fw->CFG_APP_FAVICON);

                            if (is_file(\K::$fw->DIR_FS_UPLOADS . '/' . \K::$fw->CFG_APP_FAVICON)) {
                                echo '<span class="help-block">' . \K::$fw->CFG_APP_FAVICON . '<label class="checkbox">' . \Helpers\Html::input_checkbox_tag(
                                        'delete_favicon'
                                    ) . ' ' . \K::$fw->TEXT_DELETE . '</label></span>';
                            }
                            ?>
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_NAME"><?= \K::$fw->TEXT_COPYRIGHT_NAME ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag(
                            'CFG[APP_COPYRIGHT_NAME]',
                            \K::$fw->CFG_APP_COPYRIGHT_NAME,
                            ['class' => 'form-control input-large']
                        ); ?>
                        <span class="help-block"><?= \K::$fw->TEXT_COPYRIGHT_NAME_TOOLTIP ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_LANGUAGE"><?= \K::$fw->TEXT_LANGUAGE ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[APP_LANGUAGE]',
                            \Helpers\App::app_get_languages_choices(),
                            \K::$fw->CFG_APP_LANGUAGE,
                            ['class' => 'form-control input-medium']
                        ); ?>
                        <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_APP_LANGUAGE_TIP) ?>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_SKIN"><?= \K::$fw->TEXT_SKIN ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[APP_SKIN]',
                            \Helpers\App::app_get_skins_choices(),
                            \K::$fw->CFG_APP_SKIN,
                            ['class' => 'form-control input-medium']
                        ); ?>
                        <span class="help-block"><?= \K::$fw->TEXT_SKIN_TOOLTIP ?></span>
                    </div>
                </div>

                <?php
                $timezone_list = [];
                $timezone_identifiers = DateTimeZone::listIdentifiers();
                for ($i = 0; $i < sizeof($timezone_identifiers); $i++) {
                    $timezone_list[$timezone_identifiers[$i]] = $timezone_identifiers[$i];
                }
                ?>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_TIMEZONE"><?= \K::$fw->TEXT_APPLICATION_TIMEZONE ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[APP_TIMEZONE]',
                            $timezone_list,
                            \K::$fw->CFG_APP_TIMEZONE,
                            ['class' => 'form-control input-large']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_TIMEZONE"><a href="https://en.wikipedia.org/wiki/Coordinated_Universal_Time" target="_blank">UTC</a></label>
                    <div class="col-md-9">
                        <p class="form-control-static"><?= date('P'); ?></p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_ROWS_PER_PAGE"><?= \K::$fw->TEXT_ROWS_PER_PAGE ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag(
                            'CFG[APP_ROWS_PER_PAGE]',
                            \K::$fw->CFG_APP_ROWS_PER_PAGE,
                            ['class' => 'form-control input-small required number']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_DATE_FORMAT"><?= \K::$fw->TEXT_DATE_FORMAT ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag(
                            'CFG[APP_DATE_FORMAT]',
                            \K::$fw->CFG_APP_DATE_FORMAT,
                            ['class' => 'form-control input-small required']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_DATETIME_FORMAT"><?= \K::$fw->TEXT_DATETIME_FORMAT ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag(
                            'CFG[APP_DATETIME_FORMAT]',
                            \K::$fw->CFG_APP_DATETIME_FORMAT,
                            ['class' => 'form-control input-small required']
                        ) ?>
                        <?= '<span class="help-block">' . \K::$fw->TEXT_DATE_FORMAT_INFO . '</span>'; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_NUMBER_FORMAT"><?= \Helpers\App::tooltip_icon(\K::$fw->TEXT_NUMBER_FORMAT_INFO) . \K::$fw->TEXT_NUMBER_FORMAT ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag(
                            'CFG[APP_NUMBER_FORMAT]',
                            \K::$fw->CFG_APP_NUMBER_FORMAT,
                            ['class' => 'form-control input-small input-masked', 'data-mask' => '9/~/~']
                        ) ?>
                        <?= '<span class="help-block">' . \K::$fw->TEXT_NUMBER_FORMAT_INFO_NOTE . '</span>'; ?>
                    </div>
                </div>

                <?php
                $days_array = explode(',', str_replace('"', '', \K::$fw->TEXT_DATEPICKER_DAYS));
                $days_list = [
                    '0' => $days_array[0],
                    '1' => $days_array[1],
                    '2' => $days_array[2],
                    '3' => $days_array[3],
                    '4' => $days_array[4],
                    '5' => $days_array[5],
                    '6' => $days_array[6],
                ];
                ?>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_FIRST_DAY_OF_WEEK"><?= \K::$fw->TEXT_FIRST_DAY_OF_WEEK ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[APP_FIRST_DAY_OF_WEEK]',
                            $days_list,
                            \K::$fw->CFG_APP_FIRST_DAY_OF_WEEK,
                            ['class' => 'form-control input-medium required']
                        ) ?>
                        <?= '<span class="help-block">' . \K::$fw->TEXT_FIRST_DAY_OF_WEEK_INFO . '</span>'; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_DROP_DOWN_MENU_ON_HOVER"><?= \K::$fw->TEXT_DROP_DOWN_MENU_ON_HOVER ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[DROP_DOWN_MENU_ON_HOVER]',
                            \K::$fw->default_selector,
                            \K::$fw->CFG_DROP_DOWN_MENU_ON_HOVER,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_DISABLE_CHECK_FOR_UPDATES"><?= \K::$fw->TEXT_DISABLE_CHECK_FOR_UPDATES ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[DISABLE_CHECK_FOR_UPDATES]',
                            \K::$fw->default_selector,
                            \K::$fw->CFG_DISABLE_CHECK_FOR_UPDATES,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_HIDE_POWERED_BY_TEXT"><?= \K::$fw->TEXT_HIDE . ' "' . \K::$fw->TEXT_POWERED_BY . ' KeruyCRM"' ?></label>
                    <div class="col-md-9">
                        <?= (\Helpers\App::is_ext_installed() ? \Helpers\Html::select_tag(
                            'CFG[HIDE_POWERED_BY_TEXT]',
                            \K::$fw->default_selector,
                            \K::$fw->CFG_HIDE_POWERED_BY_TEXT,
                            ['class' => 'form-control input-small']
                        ) : '<p class="form-control-static">' . \K::$fw->TEXT_EXTENSION_REQUIRED_URL . '</p>'); ?>
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