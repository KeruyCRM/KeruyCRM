<h3 class="page-title"><?php
    echo TEXT_HEADING_APPLICATION ?></h3>

<?php
echo form_tag(
    'cfg_form',
    url_for('configuration/save', 'redirect_to=configuration/application'),
    ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']
) ?>
<div class="form-body">


    <div class="tabbable tabbable-custom">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>

        </ul>


        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_info">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_NAME"><?php
                        echo TEXT_APPLICATION_NAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('CFG[APP_NAME]', CFG_APP_NAME, ['class' => 'form-control input-large required']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_SHORT_NAME"><?php
                        echo TEXT_APPLICATION_SHORT_NAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'CFG[APP_SHORT_NAME]',
                            CFG_APP_SHORT_NAME,
                            ['class' => 'form-control input-small required']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="APP_LOGO"><?php
                        echo TEXT_APPLICATION_LOGO ?></label>
                    <div class="col-md-9">
                        <p class="form-control-static">
                            <?php
                            echo input_file_tag(
                                    'APP_LOGO',
                                    ['accept' => fieldtype_attachments::get_accept_types_by_extensions('gif,jpg,png')]
                                ) . input_hidden_tag('CFG[APP_LOGO]', CFG_APP_LOGO);

                            if (is_file(DIR_FS_UPLOADS . '/' . CFG_APP_LOGO)) {
                                echo '<span class="help-block">' . CFG_APP_LOGO . '<label class="checkbox">' . input_checkbox_tag(
                                        'delete_logo'
                                    ) . ' ' . TEXT_DELETE . '</label></span>';
                            }
                            ?>
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_LOGO_URL"><?php
                        echo TEXT_APP_LOGO_URL ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('CFG[APP_LOGO_URL]', CFG_APP_LOGO_URL, ['class' => 'form-control input-large']
                        ); ?>
                        <?php
                        echo tooltip_text(TEXT_APP_LOGO_URL_TOOLTIP) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="APP_LOGO"><?php
                        echo TEXT_FAVICON ?> (32x32)</label>
                    <div class="col-md-9">
                        <p class="form-control-static">
                            <?php
                            echo input_file_tag(
                                    'APP_FAVICON',
                                    [
                                        'accept' => fieldtype_attachments::get_accept_types_by_extensions(
                                            'gif,jpg,png,ico'
                                        )
                                    ]
                                ) . input_hidden_tag('CFG[APP_FAVICON]', CFG_APP_FAVICON);

                            if (is_file(DIR_FS_UPLOADS . '/' . CFG_APP_FAVICON)) {
                                echo '<span class="help-block">' . CFG_APP_FAVICON . '<label class="checkbox">' . input_checkbox_tag(
                                        'delete_favicon'
                                    ) . ' ' . TEXT_DELETE . '</label></span>';
                            }
                            ?>
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_NAME"><?php
                        echo TEXT_COPYRIGHT_NAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'CFG[APP_COPYRIGHT_NAME]',
                            CFG_APP_COPYRIGHT_NAME,
                            ['class' => 'form-control input-large']
                        ); ?>
                        <span class="help-block"><?php
                            echo TEXT_COPYRIGHT_NAME_TOOLTIP ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_LANGUAGE"><?php
                        echo TEXT_LANGUAGE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[APP_LANGUAGE]',
                            app_get_languages_choices(),
                            CFG_APP_LANGUAGE,
                            ['class' => 'form-control input-medium']
                        ); ?>
                        <?php
                        echo tooltip_text(TEXT_APP_LANGUAGE_TIP) ?>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_SKIN"><?php
                        echo TEXT_SKIN ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[APP_SKIN]',
                            app_get_skins_choices(),
                            CFG_APP_SKIN,
                            ['class' => 'form-control input-medium']
                        ); ?>
                        <span class="help-block"><?php
                            echo TEXT_SKIN_TOOLTIP ?></span>
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
                    <label class="col-md-3 control-label" for="CFG_APP_TIMEZONE"><?php
                        echo TEXT_APPLICATION_TIMEZONE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[APP_TIMEZONE]',
                            $timezone_list,
                            CFG_APP_TIMEZONE,
                            ['class' => 'form-control input-large']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_TIMEZONE"><?php
                        echo ' <a href="https://en.wikipedia.org/wiki/Coordinated_Universal_Time" target="_blank">UTC</a>' ?></label>
                    <div class="col-md-9">
                        <p class="form-control-static"><?php
                            echo date('P'); ?></p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_ROWS_PER_PAGE"><?php
                        echo TEXT_ROWS_PER_PAGE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'CFG[APP_ROWS_PER_PAGE]',
                            CFG_APP_ROWS_PER_PAGE,
                            ['class' => 'form-control input-small required number']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_DATE_FORMAT"><?php
                        echo TEXT_DATE_FORMAT ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'CFG[APP_DATE_FORMAT]',
                            CFG_APP_DATE_FORMAT,
                            ['class' => 'form-control input-small required']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_DATETIME_FORMAT"><?php
                        echo TEXT_DATETIME_FORMAT ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'CFG[APP_DATETIME_FORMAT]',
                            CFG_APP_DATETIME_FORMAT,
                            ['class' => 'form-control input-small required']
                        ) ?>
                        <?php
                        echo '<span class="help-block">' . TEXT_DATE_FORMAT_INFO . '</span>'; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_APP_NUMBER_FORMAT"><?php
                        echo tooltip_icon(TEXT_NUMBER_FORMAT_INFO) . TEXT_NUMBER_FORMAT ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'CFG[APP_NUMBER_FORMAT]',
                            CFG_APP_NUMBER_FORMAT,
                            ['class' => 'form-control input-small input-masked', 'data-mask' => '9/~/~']
                        ) ?>
                        <?php
                        echo '<span class="help-block">' . TEXT_NUMBER_FORMAT_INFO_NOTE . '</span>'; ?>
                    </div>
                </div>

                <?php
                $days_array = explode(',', str_replace('"', '', TEXT_DATEPICKER_DAYS));
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
                    <label class="col-md-3 control-label" for="CFG_APP_FIRST_DAY_OF_WEEK"><?php
                        echo TEXT_FIRST_DAY_OF_WEEK ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[APP_FIRST_DAY_OF_WEEK]',
                            $days_list,
                            CFG_APP_FIRST_DAY_OF_WEEK,
                            ['class' => 'form-control input-medium required']
                        ) ?>
                        <?php
                        echo '<span class="help-block">' . TEXT_FIRST_DAY_OF_WEEK_INFO . '</span>'; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_DROP_DOWN_MENU_ON_HOVER"><?php
                        echo TEXT_DROP_DOWN_MENU_ON_HOVER ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[DROP_DOWN_MENU_ON_HOVER]',
                            $default_selector,
                            CFG_DROP_DOWN_MENU_ON_HOVER,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_DISABLE_CHECK_FOR_UPDATES"><?php
                        echo TEXT_DISABLE_CHECK_FOR_UPDATES ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[DISABLE_CHECK_FOR_UPDATES]',
                            $default_selector,
                            CFG_DISABLE_CHECK_FOR_UPDATES,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_HIDE_POWERED_BY_TEXT"><?php
                        echo TEXT_HIDE . ' "' . TEXT_POWERED_BY . ' KeruyCRM"' ?></label>
                    <div class="col-md-9">
                        <?php
                        echo(is_ext_installed() ? select_tag(
                            'CFG[HIDE_POWERED_BY_TEXT]',
                            $default_selector,
                            CFG_HIDE_POWERED_BY_TEXT,
                            ['class' => 'form-control input-small']
                        ) : '<p class="form-control-static">' . TEXT_EXTENSION_REQUIRED_URL . '</p>'); ?>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <?php
    echo submit_tag(TEXT_BUTTON_SAVE) ?>

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