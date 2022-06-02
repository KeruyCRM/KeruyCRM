<h3 class="page-title"><?php
    echo TEXT_MENU_USERS_REGISTRATION ?></h3>

<p><?php
    echo TEXT_PUBLIC_REGISTRATION_TIP ?></p>

<?php
echo form_tag(
    'cfg',
    url_for('configuration/save', 'redirect_to=configuration/public_users_registration'),
    ['class' => 'form-horizontal']
) ?>
<div class="form-body">


    <div class="tabbable tabbable-custom">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#public_registration" data-toggle="tab"><?php
                    echo TEXT_PUBLIC_REGISTRATION ?></a></li>
            <li><a href="#user_activation" data-toggle="tab"><?php
                    echo TEXT_USER_ACTIVATION ?></a></li>
        </ul>


        <div class="tab-content">
            <div class="tab-pane fade active in" id="public_registration">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_USE_PUBLIC_REGISTRATION"><?php
                        echo TEXT_USE_PUBLIC_REGISTRATION ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[USE_PUBLIC_REGISTRATION]',
                            $default_selector,
                            CFG_USE_PUBLIC_REGISTRATION,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_PUBLIC_REGISTRATION_USER_GROUP"><?php
                        echo tooltip_icon(
                                TEXT_PUBLIC_REGISTRATION_USER_GROUP . ' ' . TEXT_PUBLIC_REGISTRATION_USER_GROUP_MULTIPLE
                            ) . TEXT_USERS_GROUPS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[PUBLIC_REGISTRATION_USER_GROUP][]',
                            access_groups::get_choices(false),
                            CFG_PUBLIC_REGISTRATION_USER_GROUP,
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                        ); ?>
                    </div>
                </div>

                <?php
                if (CFG_ENABLE_MULTIPLE_ACCESS_GROUPS): ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label"
                               for="CFG_USE_PUBLIC_REGISTRATION_MULTIPLE_USER_GROUPS"><?php
                            echo TEXT_ASSIGN_USER_TO_MULTIPLE_GROUPS ?></label>
                        <div class="col-md-9">
                            <?php
                            echo select_tag(
                                'CFG[USE_PUBLIC_REGISTRATION_MULTIPLE_USER_GROUPS]',
                                $default_selector,
                                CFG_USE_PUBLIC_REGISTRATION_MULTIPLE_USER_GROUPS,
                                ['class' => 'form-control input-small']
                            ); ?>
                        </div>
                    </div>
                <?php
                endif ?>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_PUBLIC_REGISTRATION_PAGE_HEADING"><?php
                        echo TEXT_LOGIN_PAGE_HEADING ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'CFG[PUBLIC_REGISTRATION_PAGE_HEADING]',
                            CFG_PUBLIC_REGISTRATION_PAGE_HEADING,
                            ['class' => 'form-control input-xlarge']
                        ); ?>
                        <?php
                        echo tooltip_text(TEXT_DEFAULT . ': ' . TEXT_REGISTRATION_NEW_USER) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_PUBLIC_REGISTRATION_PAGE_CONTENT"><?php
                        echo TEXT_LOGIN_PAGE_CONTENT ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag(
                            'CFG[PUBLIC_REGISTRATION_PAGE_CONTENT]',
                            CFG_PUBLIC_REGISTRATION_PAGE_CONTENT,
                            ['class' => 'form-control editor input-xlarge', 'rows' => 3]
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_REGISTRATION_BUTTON_TITLE"><?php
                        echo TEXT_REGISTRATION_BUTTON_TITLE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag(
                            'CFG[REGISTRATION_BUTTON_TITLE]',
                            CFG_REGISTRATION_BUTTON_TITLE,
                            ['class' => 'form-control input-medium']
                        ); ?>
                        <?php
                        echo tooltip_text(TEXT_DEFAULT . ': ' . TEXT_BUTTON_REGISTRATCION) ?>
                    </div>
                </div>
                <?php
                $choices = ['' => TEXT_NONE];
                $fields_query = db_query(
                    "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_type_list_excluded_in_form(
                    ) . ',' . str_replace(
                        "'fieldtype_user_photo',",
                        '',
                        fields_types::get_users_types_list()
                    ) . ") and f.entities_id='1' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
                );
                while ($fields = db_fetch_array($fields_query)) {
                    $choices[$fields['tab_name']][$fields['id']] = fields_types::get_option(
                        $fields['type'],
                        'name',
                        $fields['name']
                    );
                }

                $html = '
  					
  			<div class="form-group">
			  	<label class="col-md-3 control-label" for="hidden_fields">' . tooltip_icon(
                        TEXT_HIDEN_FIELDS_IN_FORM
                    ) . TEXT_HIDEN_FIELDS . '</label>
			    <div class="col-md-9">' . select_tag(
                        'CFG[PUBLIC_REGISTRATION_HIDDEN_FIELDS][]',
                        $choices,
                        CFG_PUBLIC_REGISTRATION_HIDDEN_FIELDS,
                        ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                    ) . '
			    </div>
			  </div>';

                echo $html;


                $choices = ['' => TEXT_NONE];
                $users_query = db_query(
                    "select u.* from app_entity_1 u where u.field_6=0 order by u.field_8, u.field_7"
                );
                while ($users = db_fetch_array($users_query)) {
                    $choices[$users['id']] = $app_users_cache[$users['id']]['name'];
                }
                ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_REGISTRATION_NOTIFICATION_USERS"><?php
                        echo TEXT_SEND_NOTIFICATION ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[REGISTRATION_NOTIFICATION_USERS][]',
                            $choices,
                            CFG_REGISTRATION_NOTIFICATION_USERS,
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                        ); ?>
                        <?php
                        echo tooltip_text(TEXT_REGISTRATION_SEND_NOTIFICATION_INFO) ?>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_PUBLIC_REGISTRATION_USER_AGREEMENT"><?php
                        echo tooltip_icon(
                                TEXT_EXT_PB_USER_AGREEMENT_TEXT_INFO
                            ) . TEXT_EXT_PB_USER_AGREEMENT_TEXT ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag(
                            'CFG[PUBLIC_REGISTRATION_USER_AGREEMENT]',
                            CFG_PUBLIC_REGISTRATION_USER_AGREEMENT,
                            ['class' => 'form-control']
                        ) ?>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="user_activation">

                <?php
                $choices = [];
                $choices['automatic'] = TEXT_AUTOMATIC;
                $choices['email'] = TEXT_BY_EMAIL;
                $choices['manually'] = TEXT_MANUALLY;
                ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="CFG_PUBLIC_REGISTRATION_USER_ACTIVATION"><?php
                        echo TEXT_USER_ACTIVATION ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'CFG[PUBLIC_REGISTRATION_USER_ACTIVATION]',
                            $choices,
                            CFG_PUBLIC_REGISTRATION_USER_ACTIVATION,
                            ['class' => 'form-control input-large', 'onChange' => 'show_user_activation_tip()']
                        ); ?>

                        <div class="user-activation-tip" id="user_activation_automatic_tip"><?php
                            echo tooltip_text(TEXT_USER_ACTIVATION_AUTOMATIC_TIP) ?></div>
                        <div class="user-activation-tip"
                        " id="user_activation_email_tip"><?php
                        echo tooltip_text(TEXT_USER_ACTIVATION_BY_EMAIL_TIP) ?></div>
                    <div class="user-activation-tip"
                    " id="user_activation_manually_tip"><?php
                    echo tooltip_text(TEXT_USER_ACTIVATION_MANUALLY_TIP) ?></div>
            </div>
        </div>

        <div class="user-activation-seciton" id="user_activation_manually_section">
            <h3 class="form-section"><?php
                echo TEXT_REGISTRATION_SUCCESS_PAGE ?></h3>

            <div class="form-group">
                <label class="col-md-3 control-label" for="CFG_REGISTRATION_SUCCESS_PAGE_HEADING"><?php
                    echo TEXT_HEADING ?></label>
                <div class="col-md-9">
                    <?php
                    echo input_tag(
                        'CFG[REGISTRATION_SUCCESS_PAGE_HEADING]',
                        CFG_REGISTRATION_SUCCESS_PAGE_HEADING,
                        ['class' => 'form-control input-xlarge']
                    ); ?>
                    <?php
                    echo tooltip_text(TEXT_DEFAULT . ': ' . TEXT_REGISTRATION_SUCCESS_PAGE_HEADING) ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label" for="CFG_REGISTRATION_SUCCESS_PAGE_DESCRIPTION"><?php
                    echo TEXT_DESCRIPTION ?></label>
                <div class="col-md-9">
                    <?php
                    echo textarea_tag(
                        'CFG[REGISTRATION_SUCCESS_PAGE_DESCRIPTION]',
                        CFG_REGISTRATION_SUCCESS_PAGE_DESCRIPTION,
                        ['class' => 'form-control editor input-xlarge']
                    ); ?>
                </div>
            </div>

            <h3 class="form-section"><?php
                echo TEXT_EMAIL_ABOUT_USER_ACTIVATION ?></h3>

            <div class="form-group">
                <label class="col-md-3 control-label" for="CFG_USER_ACTIVATION_EMAIL_SUBJECT"><?php
                    echo TEXT_EMAIL_SUBJECT ?></label>
                <div class="col-md-9">
                    <?php
                    echo input_tag(
                        'CFG[USER_ACTIVATION_EMAIL_SUBJECT]',
                        CFG_USER_ACTIVATION_EMAIL_SUBJECT,
                        ['class' => 'form-control input-xlarge']
                    ); ?>
                    <?php
                    echo tooltip_text(TEXT_DEFAULT . ': ' . TEXT_USER_ACTIVATION_EMAIL_SUBJECT) ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label" for="CFG_USER_ACTIVATION_EMAIL_BODY"><?php
                    echo TEXT_DESCRIPTION ?></label>
                <div class="col-md-9">
                    <?php
                    echo textarea_tag(
                        'CFG[USER_ACTIVATION_EMAIL_BODY]',
                        CFG_USER_ACTIVATION_EMAIL_BODY,
                        ['class' => 'form-control editor input-xlarge']
                    ); ?>
                </div>
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
        show_user_activation_tip()
    })

    function show_user_activation_tip() {
        type = $("#CFG_PUBLIC_REGISTRATION_USER_ACTIVATION").val()

        $('.user-activation-tip').hide();
        $('#user_activation_' + type + '_tip').show();

        $('.user-activation-seciton').hide();
        $('#user_activation_' + type + '_section').show();
    }


</script>