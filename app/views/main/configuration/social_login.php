<h3 class="page-title"><?= \K::$fw->TEXT_SOCIAL_LOGIN ?></h3>

<p><?= \K::$fw->TEXT_SOCIAL_LOGIN_INFO ?></p>

<?= \Helpers\Html::form_tag(
    'cfg',
    \Helpers\Urls::url_for('main/configuration/save'),
    ['class' => 'form-horizontal']
) ?>
<?= \Helpers\Html::input_hidden_tag('redirect_to', 'main/configuration/social_login') ?>
<div class="form-body">
    <?php
    $choices = [
        '0' => \K::$fw->TEXT_NO,
        '1' => \K::$fw->TEXT_YES,
        '2' => \K::$fw->TEXT_ENABLE_SOCIAL_LOGIN_ONLY,
    ];
    ?>
    <div class="form-group">
        <label class="col-md-3 control-label"><?= \K::$fw->TEXT_ENABLE_SOCIAL_LOGIN ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[ENABLE_SOCIAL_LOGIN]',
                $choices,
                \K::$fw->CFG_ENABLE_SOCIAL_LOGIN,
                ['class' => 'form-control input-xlarge']
            ); ?>
        </div>
    </div>

    <?php
    $choices = [
        '' => \K::$fw->TEXT_NO,
        'autocreate' => \K::$fw->TEXT_CREATE_USER_AUTOMATICALLY,
        'public_registration' => \K::$fw->TEXT_REDIRECT_TO_PUBLIC_REGISTRATION,
    ];
    ?>

    <div class="form-group">
        <label class="col-md-3 control-label"><?= \K::$fw->TEXT_CREATE_USER ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[SOCIAL_LOGIN_CREATE_USER]',
                $choices,
                \K::$fw->CFG_SOCIAL_LOGIN_CREATE_USER,
                ['class' => 'form-control input-xlarge']
            ); ?>
        </div>
    </div>

    <div class="form-group" form_display_rules="CFG_SOCIAL_LOGIN_CREATE_USER:autocreate">
        <label class="col-md-3 control-label"><?= \Helpers\App::tooltip_icon(
                \K::$fw->TEXT_PUBLIC_REGISTRATION_USER_GROUP
            ) . \K::$fw->TEXT_USERS_GROUPS ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[SOCIAL_LOGIN_USER_GROUP][]',
                \Models\Main\Access_groups::get_choices(false),
                \K::$fw->CFG_SOCIAL_LOGIN_USER_GROUP,
                ['class' => 'form-control input-xlarge chosen-select required', 'multiple' => 'multiple']
            ); ?>
        </div>
    </div>

    <h3 class="form-section"><?= \K::$fw->TEXT_SELECT_SOCIAL_NETWORKS ?></h3>

    <div class="form-group">
        <label class="col-md-3 control-label"><i class="fa fa-vk" aria-hidden="true"></i> <?= \K::$fw->TEXT_VKONTAKTE ?>
        </label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[ENABLE_VKONTAKTE_LOGIN]',
                \K::$fw->default_selector,
                \K::$fw->CFG_ENABLE_VKONTAKTE_LOGIN,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div form_display_rules="CFG_ENABLE_VKONTAKTE_LOGIN:1">
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_APP_ID ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[VKONTAKTE_APP_ID]',
                    \K::$fw->CFG_VKONTAKTE_APP_ID,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_SECRET_KEY ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[VKONTAKTE_SECRET_KEY]',
                    \K::$fw->CFG_VKONTAKTE_SECRET_KEY,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_REDIRECT_URI ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'redirect_uri',
                    \Helpers\Urls::url_for('main/social_login/vkontakte'),
                    ['class' => 'form-control input-xlarge select-all', 'readonly' => 'readonly']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_BUTTON_TITLE ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[VKONTAKTE_BUTTON_TITLE]',
                    \K::$fw->CFG_VKONTAKTE_BUTTON_TITLE,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
    </div>

    <div class="form-section"></div>

    <div class="form-group">
        <label class="col-md-3 control-label"><i class="lab la-yandex"></i> <?= \K::$fw->TEXT_YANDEX ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[ENABLE_YANDEX_LOGIN]',
                \K::$fw->default_selector,
                \K::$fw->CFG_ENABLE_YANDEX_LOGIN,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div form_display_rules="CFG_ENABLE_YANDEX_LOGIN:1">
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_APP_ID ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[YANDEX_APP_ID]',
                    \K::$fw->CFG_YANDEX_APP_ID,
                    ['class' => 'form-control input-large']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_SECRET_KEY ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[YANDEX_SECRET_KEY]',
                    \K::$fw->CFG_YANDEX_SECRET_KEY,
                    ['class' => 'form-control input-large']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_REDIRECT_URI ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'redirect_uri',
                    \Helpers\Urls::url_for('main/social_login/yandex'),
                    ['class' => 'form-control input-xlarge select-all', 'readonly' => 'readonly']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_BUTTON_TITLE ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[YANDEX_BUTTON_TITLE]',
                    \K::$fw->CFG_YANDEX_BUTTON_TITLE,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
    </div>

    <div class="form-section"></div>

    <div class="form-group">
        <label class="col-md-3 control-label"><i class="fa fa-google"
                                                 aria-hidden="true"></i> <?= \K::$fw->TEXT_GOOGLE ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[ENABLE_GOOGLE_LOGIN]',
                \K::$fw->default_selector,
                \K::$fw->CFG_ENABLE_GOOGLE_LOGIN,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div form_display_rules="CFG_ENABLE_GOOGLE_LOGIN:1">
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_APP_ID ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[GOOGLE_APP_ID]',
                    \K::$fw->CFG_GOOGLE_APP_ID,
                    ['class' => 'form-control input-xlarge']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_SECRET_KEY ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[GOOGLE_SECRET_KEY]',
                    \K::$fw->CFG_GOOGLE_SECRET_KEY,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_REDIRECT_URI ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'redirect_uri',
                    \Helpers\Urls::url_for('main/social_login/google'),
                    ['class' => 'form-control input-xlarge select-all', 'readonly' => 'readonly']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_BUTTON_TITLE ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[GOOGLE_BUTTON_TITLE]',
                    \K::$fw->CFG_GOOGLE_BUTTON_TITLE,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
    </div>


    <div class="form-section"></div>

    <div class="form-group">
        <label class="col-md-3 control-label"><i class="fa fa-facebook"
                                                 aria-hidden="true"></i> <?= \K::$fw->TEXT_FACEBOOK ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[ENABLE_FACEBOOK_LOGIN]',
                \K::$fw->default_selector,
                \K::$fw->CFG_ENABLE_FACEBOOK_LOGIN,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div form_display_rules="CFG_ENABLE_FACEBOOK_LOGIN:1">
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_APP_ID ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[FACEBOOK_APP_ID]',
                    \K::$fw->CFG_FACEBOOK_APP_ID,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_SECRET_KEY ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[FACEBOOK_SECRET_KEY]',
                    \K::$fw->CFG_FACEBOOK_SECRET_KEY,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_REDIRECT_URI ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'redirect_uri',
                    \Helpers\Urls::url_for('main/social_login/facebook'),
                    ['class' => 'form-control input-xlarge select-all', 'readonly' => 'readonly']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_BUTTON_TITLE ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[FACEBOOK_BUTTON_TITLE]',
                    \K::$fw->CFG_FACEBOOK_BUTTON_TITLE,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
    </div>

    <div class="form-section"></div>

    <div class="form-group">
        <label class="col-md-3 control-label"><i class="fa fa-linkedin"
                                                 aria-hidden="true"></i> <?= \K::$fw->TEXT_LINKEDIN ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[ENABLE_LINKEDIN_LOGIN]',
                \K::$fw->default_selector,
                \K::$fw->CFG_ENABLE_LINKEDIN_LOGIN,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div form_display_rules="CFG_ENABLE_LINKEDIN_LOGIN:1">
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_APP_ID ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[LINKEDIN_APP_ID]',
                    \K::$fw->CFG_LINKEDIN_APP_ID,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_SECRET_KEY ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[LINKEDIN_SECRET_KEY]',
                    \K::$fw->CFG_LINKEDIN_SECRET_KEY,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_REDIRECT_URI ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'redirect_uri',
                    \Helpers\Urls::url_for('main/social_login/linkedin'),
                    ['class' => 'form-control input-xlarge select-all', 'readonly' => 'readonly']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_BUTTON_TITLE ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[LINKEDIN_BUTTON_TITLE]',
                    \K::$fw->CFG_LINKEDIN_BUTTON_TITLE,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
    </div>


    <div class="form-section"></div>

    <div class="form-group">
        <label class="col-md-3 control-label"><i class="fa fa-steam" aria-hidden="true"></i> <?= \K::$fw->TEXT_STEAM ?>
        </label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[ENABLE_STEAM_LOGIN]',
                \K::$fw->default_selector,
                \K::$fw->CFG_ENABLE_STEAM_LOGIN,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div form_display_rules="CFG_ENABLE_STEAM_LOGIN:1">
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_API_KEY ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[STEAM_API_KEY]',
                    \K::$fw->CFG_STEAM_API_KEY,
                    ['class' => 'form-control input-large']
                ); ?>
                <p><?= \K::$fw->TEXT_MORE_INFO . ': <a href="https://steamcommunity.com/dev/apikey" target="_blank">https://steamcommunity.com/dev/apikey</a>' ?></p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_DOMAIN ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'redirect_uri',
                    \K::$fw->HOST,
                    ['class' => 'form-control input-large select-all', 'readonly' => 'readonly']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_BUTTON_TITLE ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[STEAM_BUTTON_TITLE]',
                    \K::$fw->CFG_STEAM_BUTTON_TITLE,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
    </div>


    <div class="form-section"></div>

    <div class="form-group">
        <label class="col-md-3 control-label"><i class="fa fa-twitter"
                                                 aria-hidden="true"></i> <?= \K::$fw->TEXT_TWITTER ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[ENABLE_TWITTER_LOGIN]',
                \K::$fw->default_selector,
                \K::$fw->CFG_ENABLE_TWITTER_LOGIN,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div form_display_rules="CFG_ENABLE_TWITTER_LOGIN:1">
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_APP_ID ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[TWITTER_APP_ID]',
                    \K::$fw->CFG_TWITTER_APP_ID,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_SECRET_KEY ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[TWITTER_SECRET_KEY]',
                    \K::$fw->CFG_TWITTER_SECRET_KEY,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_REDIRECT_URI ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'redirect_uri',
                    \Helpers\Urls::url_for('main/social_login/twitter'),
                    ['class' => 'form-control input-xlarge select-all', 'readonly' => 'readonly']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= \K::$fw->TEXT_BUTTON_TITLE ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'CFG[TWITTER_BUTTON_TITLE]',
                    \K::$fw->CFG_TWITTER_BUTTON_TITLE,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
    </div>

    <?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>

</div>
</form>

<script>
    $(function () {
        $('#cfg').validate({ignore: '.ignore-validation'});
    });
</script> 