<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<h3 class="page-title"><?= \K::$fw->TEXT_MENU_USERS_REGISTRATION ?></h3>

<p><?= \K::$fw->TEXT_PUBLIC_REGISTRATION_TIP ?></p>

<?= \Helpers\Html::form_tag(
    'cfg',
    \Helpers\Urls::url_for('main/configuration/save'),
    ['class' => 'form-horizontal']
) ?>
<?= \Helpers\Html::input_hidden_tag('redirect_to', 'main/configuration/public_users_registration') ?>
<div class="form-body">
    <div class="tabbable tabbable-custom">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#public_registration"
                                  data-toggle="tab"><?= \K::$fw->TEXT_PUBLIC_REGISTRATION ?></a></li>
            <li><a href="#user_activation" data-toggle="tab"><?= \K::$fw->TEXT_USER_ACTIVATION ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="public_registration">

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_USE_PUBLIC_REGISTRATION"><?= \K::$fw->TEXT_USE_PUBLIC_REGISTRATION ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[USE_PUBLIC_REGISTRATION]',
                            \K::$fw->default_selector,
                            \K::$fw->CFG_USE_PUBLIC_REGISTRATION,
                            ['class' => 'form-control input-small']
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_PUBLIC_REGISTRATION_USER_GROUP"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_PUBLIC_REGISTRATION_USER_GROUP . ' ' . \K::$fw->TEXT_PUBLIC_REGISTRATION_USER_GROUP_MULTIPLE
                        ) . \K::$fw->TEXT_USERS_GROUPS ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[PUBLIC_REGISTRATION_USER_GROUP][]',
                            \Models\Main\Access_groups::get_choices(false),
                            \K::$fw->CFG_PUBLIC_REGISTRATION_USER_GROUP,
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                        ); ?>
                    </div>
                </div>

                <?php
                if (\K::$fw->CFG_ENABLE_MULTIPLE_ACCESS_GROUPS): ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label"
                               for="CFG_USE_PUBLIC_REGISTRATION_MULTIPLE_USER_GROUPS"><?= \K::$fw->TEXT_ASSIGN_USER_TO_MULTIPLE_GROUPS ?></label>
                        <div class="col-md-9">
                            <?= \Helpers\Html::select_tag(
                                'CFG[USE_PUBLIC_REGISTRATION_MULTIPLE_USER_GROUPS]',
                                \K::$fw->default_selector,
                                \K::$fw->CFG_USE_PUBLIC_REGISTRATION_MULTIPLE_USER_GROUPS,
                                ['class' => 'form-control input-small']
                            ); ?>
                        </div>
                    </div>
                <?php
                endif ?>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_PUBLIC_REGISTRATION_PAGE_HEADING"><?= \K::$fw->TEXT_LOGIN_PAGE_HEADING ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag(
                            'CFG[PUBLIC_REGISTRATION_PAGE_HEADING]',
                            \K::$fw->CFG_PUBLIC_REGISTRATION_PAGE_HEADING,
                            ['class' => 'form-control input-xlarge']
                        ); ?>
                        <?= \Helpers\App::tooltip_text(
                            \K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->TEXT_REGISTRATION_NEW_USER
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_PUBLIC_REGISTRATION_PAGE_CONTENT"><?= \K::$fw->TEXT_LOGIN_PAGE_CONTENT ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::textarea_tag(
                            'CFG[PUBLIC_REGISTRATION_PAGE_CONTENT]',
                            \K::$fw->CFG_PUBLIC_REGISTRATION_PAGE_CONTENT,
                            ['class' => 'form-control editor input-xlarge', 'rows' => 3]
                        ); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_REGISTRATION_BUTTON_TITLE"><?= \K::$fw->TEXT_REGISTRATION_BUTTON_TITLE ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::input_tag(
                            'CFG[REGISTRATION_BUTTON_TITLE]',
                            \K::$fw->CFG_REGISTRATION_BUTTON_TITLE,
                            ['class' => 'form-control input-medium']
                        ); ?>
                        <?php
                        echo \Helpers\App::tooltip_text(
                            \K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->TEXT_BUTTON_REGISTRATION
                        ) ?>
                    </div>
                </div>
                <?php

                $html = '
  			<div class="form-group">
			  	<label class="col-md-3 control-label" for="hidden_fields">' . \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_HIDDEN_FIELDS_TIP
                    ) . \K::$fw->TEXT_HIDDEN_FIELDS . '</label>
			    <div class="col-md-9">' . \Helpers\Html::select_tag(
                        'CFG[PUBLIC_REGISTRATION_HIDDEN_FIELDS][]',
                        \K::$fw->choices,
                        \K::$fw->CFG_PUBLIC_REGISTRATION_HIDDEN_FIELDS,
                        ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                    ) . '
			    </div>
			  </div>';

                echo $html;
                ?>
                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_REGISTRATION_NOTIFICATION_USERS"><?= \K::$fw->TEXT_SEND_NOTIFICATION ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[REGISTRATION_NOTIFICATION_USERS][]',
                            \K::$fw->choices2,
                            \K::$fw->CFG_REGISTRATION_NOTIFICATION_USERS,
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                        ); ?>
                        <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_REGISTRATION_SEND_NOTIFICATION_INFO) ?>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_PUBLIC_REGISTRATION_USER_AGREEMENT"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_EXT_PB_USER_AGREEMENT_TEXT_INFO
                        ) . \K::$fw->TEXT_EXT_PB_USER_AGREEMENT_TEXT ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::textarea_tag(
                            'CFG[PUBLIC_REGISTRATION_USER_AGREEMENT]',
                            \K::$fw->CFG_PUBLIC_REGISTRATION_USER_AGREEMENT,
                            ['class' => 'form-control']
                        ) ?>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="user_activation">

                <?php
                $choices = [];
                $choices['automatic'] = \K::$fw->TEXT_AUTOMATIC;
                $choices['email'] = \K::$fw->TEXT_BY_EMAIL;
                $choices['manually'] = \K::$fw->TEXT_MANUALLY;
                ?>
                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="CFG_PUBLIC_REGISTRATION_USER_ACTIVATION"><?= \K::$fw->TEXT_USER_ACTIVATION ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'CFG[PUBLIC_REGISTRATION_USER_ACTIVATION]',
                            $choices,
                            \K::$fw->CFG_PUBLIC_REGISTRATION_USER_ACTIVATION,
                            ['class' => 'form-control input-large', 'onChange' => 'show_user_activation_tip()']
                        ); ?>

                        <div class="user-activation-tip"
                             id="user_activation_automatic_tip"><?= \Helpers\App::tooltip_text(
                                \K::$fw->TEXT_USER_ACTIVATION_AUTOMATIC_TIP
                            ) ?></div>
                        <div class="user-activation-tip"
                        " id="user_activation_email_tip"><?= \Helpers\App::tooltip_text(
                            \K::$fw->TEXT_USER_ACTIVATION_BY_EMAIL_TIP
                        ) ?>
                    </div>
                    <div class="user-activation-tip"
                    " id="user_activation_manually_tip"><?= \Helpers\App::tooltip_text(
                        \K::$fw->TEXT_USER_ACTIVATION_MANUALLY_TIP
                    ) ?>
                </div>
            </div>
        </div>

        <div class="user-activation-seciton" id="user_activation_manually_section">
            <h3 class="form-section"><?= \K::$fw->TEXT_REGISTRATION_SUCCESS_PAGE ?></h3>

            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="CFG_REGISTRATION_SUCCESS_PAGE_HEADING"><?= \K::$fw->TEXT_HEADING ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::input_tag(
                        'CFG[REGISTRATION_SUCCESS_PAGE_HEADING]',
                        \K::$fw->CFG_REGISTRATION_SUCCESS_PAGE_HEADING,
                        ['class' => 'form-control input-xlarge']
                    ); ?>
                    <?= \Helpers\App::tooltip_text(
                        \K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->TEXT_REGISTRATION_SUCCESS_PAGE_HEADING
                    ) ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="CFG_REGISTRATION_SUCCESS_PAGE_DESCRIPTION"><?= \K::$fw->TEXT_DESCRIPTION ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::textarea_tag(
                        'CFG[REGISTRATION_SUCCESS_PAGE_DESCRIPTION]',
                        \K::$fw->CFG_REGISTRATION_SUCCESS_PAGE_DESCRIPTION,
                        ['class' => 'form-control editor input-xlarge']
                    ); ?>
                </div>
            </div>

            <h3 class="form-section"><?= \K::$fw->TEXT_EMAIL_ABOUT_USER_ACTIVATION ?></h3>

            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="CFG_USER_ACTIVATION_EMAIL_SUBJECT"><?= \K::$fw->TEXT_EMAIL_SUBJECT ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::input_tag(
                        'CFG[USER_ACTIVATION_EMAIL_SUBJECT]',
                        \K::$fw->CFG_USER_ACTIVATION_EMAIL_SUBJECT,
                        ['class' => 'form-control input-xlarge']
                    ); ?>
                    <?= \Helpers\App::tooltip_text(
                        \K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->TEXT_USER_ACTIVATION_EMAIL_SUBJECT
                    ) ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="CFG_USER_ACTIVATION_EMAIL_BODY"><?= \K::$fw->TEXT_DESCRIPTION ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::textarea_tag(
                        'CFG[USER_ACTIVATION_EMAIL_BODY]',
                        \K::$fw->CFG_USER_ACTIVATION_EMAIL_BODY,
                        ['class' => 'form-control editor input-xlarge']
                    ); ?>
                </div>
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