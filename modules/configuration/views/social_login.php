<h3 class="page-title"><?php
    echo TEXT_SOCIAL_LOGIN ?></h3>

<p><?php
    echo TEXT_SOCIAL_LOGIN_INFO ?></p>

<?php
echo form_tag(
    'cfg',
    url_for('configuration/save', 'redirect_to=configuration/social_login'),
    ['class' => 'form-horizontal']
) ?>
<div class="form-body">


    <?php
    $choices = [
        '0' => TEXT_NO,
        '1' => TEXT_YES,
        '2' => TEXT_ENABLE_SOCIAL_LOGIN_ONLY,
    ];
    ?>
    <div class="form-group">
        <label class="col-md-3 control-label"><?php
            echo TEXT_ENABLE_SOCIAL_LOGIN ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'CFG[ENABLE_SOCIAL_LOGIN]',
                $choices,
                CFG_ENABLE_SOCIAL_LOGIN,
                ['class' => 'form-control input-xlarge']
            ); ?>
        </div>
    </div>

    <?php
    $choices = [
        '' => TEXT_NO,
        'autocreate' => TEXT_CREATE_USER_AUTOMATICALLY,
        'public_registration' => TEXT_REDIRECT_TO_PUBLIC_REGISTRATION,
    ];
    ?>

    <div class="form-group">
        <label class="col-md-3 control-label"><?php
            echo TEXT_CREATE_USER ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'CFG[SOCAL_LOGIN_CREATE_USER]',
                $choices,
                CFG_SOCIAL_LOGIN_CREATE_USER,
                ['class' => 'form-control input-xlarge']
            ); ?>
        </div>
    </div>

    <div class="form-group" form_display_rules="CFG_SOCIAL_LOGIN_CREATE_USER:autocreate">
        <label class="col-md-3 control-label"><?php
            echo tooltip_icon(TEXT_PUBLIC_REGISTRATION_USER_GROUP) . TEXT_USERS_GROUPS ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'CFG[SOCAL_LOGIN_USER_GROUP][]',
                access_groups::get_choices(false),
                CFG_SOCIAL_LOGIN_USER_GROUP,
                ['class' => 'form-control input-xlarge chosen-select required', 'multiple' => 'multiple']
            ); ?>
        </div>
    </div>

    <h3 class="form-section"><?php
        echo TEXT_SELECT_SOCIAL_NETWORKS ?></h3>

    <div class="form-group">
        <label class="col-md-3 control-label"><i class="fa fa-vk" aria-hidden="true"></i> <?php
            echo TEXT_VKONTAKTE ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'CFG[ENABLE_VKONTAKTE_LOGIN]',
                $default_selector,
                CFG_ENABLE_VKONTAKTE_LOGIN,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div form_display_rules="CFG_ENABLE_VKONTAKTE_LOGIN:1">
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_APP_ID ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('CFG[VKONTAKTE_APP_ID]', CFG_VKONTAKTE_APP_ID, ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_SECRET_KEY ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag(
                    'CFG[VKONTAKTE_SECRET_KEY]',
                    CFG_VKONTAKTE_SECRET_KEY,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_REDIRECT_URI ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag(
                    'redirect_uri',
                    url_for('social_login/vkontakte'),
                    ['class' => 'form-control input-xlarge select-all', 'readonly' => 'readonly']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_BUTTON_TITLE ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag(
                    'CFG[VKONTAKTE_BUTTON_TITLE]',
                    CFG_VKONTAKTE_BUTTON_TITLE,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
    </div>

    <div class="form-section"></div>

    <div class="form-group">
        <label class="col-md-3 control-label"><i class="lab la-yandex"></i> <?php
            echo TEXT_YANDEX ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'CFG[ENABLE_YANDEX_LOGIN]',
                $default_selector,
                CFG_ENABLE_YANDEX_LOGIN,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div form_display_rules="CFG_ENABLE_YANDEX_LOGIN:1">
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_APP_ID ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('CFG[YANDEX_APP_ID]', CFG_YANDEX_APP_ID, ['class' => 'form-control input-large']); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_SECRET_KEY ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('CFG[YANDEX_SECRET_KEY]', CFG_YANDEX_SECRET_KEY, ['class' => 'form-control input-large']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_REDIRECT_URI ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag(
                    'redirect_uri',
                    url_for('social_login/yandex'),
                    ['class' => 'form-control input-xlarge select-all', 'readonly' => 'readonly']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_BUTTON_TITLE ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag(
                    'CFG[YANDEX_BUTTON_TITLE]',
                    CFG_YANDEX_BUTTON_TITLE,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
    </div>

    <div class="form-section"></div>

    <div class="form-group">
        <label class="col-md-3 control-label"><i class="fa fa-google" aria-hidden="true"></i> <?php
            echo TEXT_GOOGLE ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'CFG[ENABLE_GOOGLE_LOGIN]',
                $default_selector,
                CFG_ENABLE_GOOGLE_LOGIN,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div form_display_rules="CFG_ENABLE_GOOGLE_LOGIN:1">
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_APP_ID ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('CFG[GOOGLE_APP_ID]', CFG_GOOGLE_APP_ID, ['class' => 'form-control input-xlarge']); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_SECRET_KEY ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('CFG[GOOGLE_SECRET_KEY]', CFG_GOOGLE_SECRET_KEY, ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_REDIRECT_URI ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag(
                    'redirect_uri',
                    url_for('social_login/google'),
                    ['class' => 'form-control input-xlarge select-all', 'readonly' => 'readonly']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_BUTTON_TITLE ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag(
                    'CFG[GOOGLE_BUTTON_TITLE]',
                    CFG_GOOGLE_BUTTON_TITLE,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
    </div>


    <div class="form-section"></div>

    <div class="form-group">
        <label class="col-md-3 control-label"><i class="fa fa-facebook" aria-hidden="true"></i> <?php
            echo TEXT_FACEBOOK ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'CFG[ENABLE_FACEBOOK_LOGIN]',
                $default_selector,
                CFG_ENABLE_FACEBOOK_LOGIN,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div form_display_rules="CFG_ENABLE_FACEBOOK_LOGIN:1">
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_APP_ID ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('CFG[FACEBOOK_APP_ID]', CFG_FACEBOOK_APP_ID, ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_SECRET_KEY ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag(
                    'CFG[FACEBOOK_SECRET_KEY]',
                    CFG_FACEBOOK_SECRET_KEY,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_REDIRECT_URI ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag(
                    'redirect_uri',
                    url_for('social_login/facebook'),
                    ['class' => 'form-control input-xlarge select-all', 'readonly' => 'readonly']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_BUTTON_TITLE ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag(
                    'CFG[FACEBOOK_BUTTON_TITLE]',
                    CFG_FACEBOOK_BUTTON_TITLE,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
    </div>

    <div class="form-section"></div>

    <div class="form-group">
        <label class="col-md-3 control-label"><i class="fa fa-linkedin" aria-hidden="true"></i> <?php
            echo TEXT_LINKEDIN ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'CFG[ENABLE_LINKEDIN_LOGIN]',
                $default_selector,
                CFG_ENABLE_LINKEDIN_LOGIN,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div form_display_rules="CFG_ENABLE_LINKEDIN_LOGIN:1">
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_APP_ID ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('CFG[LINKEDIN_APP_ID]', CFG_LINKEDIN_APP_ID, ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_SECRET_KEY ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag(
                    'CFG[LINKEDIN_SECRET_KEY]',
                    CFG_LINKEDIN_SECRET_KEY,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_REDIRECT_URI ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag(
                    'redirect_uri',
                    url_for('social_login/linkedin'),
                    ['class' => 'form-control input-xlarge select-all', 'readonly' => 'readonly']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_BUTTON_TITLE ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag(
                    'CFG[LINKEDIN_BUTTON_TITLE]',
                    CFG_LINKEDIN_BUTTON_TITLE,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
    </div>


    <div class="form-section"></div>

    <div class="form-group">
        <label class="col-md-3 control-label"><i class="fa fa-steam" aria-hidden="true"></i> <?php
            echo TEXT_STEAM ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'CFG[ENABLE_STEAM_LOGIN]',
                $default_selector,
                CFG_ENABLE_STEAM_LOGIN,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div form_display_rules="CFG_ENABLE_STEAM_LOGIN:1">
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_API_KEY ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('CFG[STEAM_API_KEY]', CFG_STEAM_API_KEY, ['class' => 'form-control input-large']); ?>
                <p><?php
                    echo TEXT_MORE_INFO . ': <a href="https://steamcommunity.com/dev/apikey" target="_blank">https://steamcommunity.com/dev/apikey</a>' ?></p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_DOMAIN ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag(
                    'redirect_uri',
                    $_SERVER['SERVER_NAME'],
                    ['class' => 'form-control input-large select-all', 'readonly' => 'readonly']
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_BUTTON_TITLE ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag(
                    'CFG[STEAM_BUTTON_TITLE]',
                    CFG_STEAM_BUTTON_TITLE,
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>
    </div>


    <!--div class="form-section"></div>
    
    <div class="form-group">
        <label class="col-md-3 control-label"><i class="fa fa-twitter" aria-hidden="true"></i> <?php
    echo TEXT_TWITTER ?></label>
        <div class="col-md-9">	
            <?php
    echo select_tag(
        'CFG[ENABLE_TWITTER_LOGIN]',
        $default_selector,
        CFG_ENABLE_TWITTER_LOGIN,
        ['class' => 'form-control input-small']
    ); ?>
        </div>			
    </div>
    
<div form_display_rules="CFG_ENABLE_TWITTER_LOGIN:1">
    <div class="form-group">
        <label class="col-md-3 control-label"><?php
    echo TEXT_APP_ID ?></label>
        <div class="col-md-9">	
            <?php
    echo input_tag('CFG[TWITTER_APP_ID]', CFG_TWITTER_APP_ID, ['class' => 'form-control input-medium']); ?>
        </div>			
    </div>    
    <div class="form-group">
        <label class="col-md-3 control-label"><?php
    echo TEXT_SECRET_KEY ?></label>
        <div class="col-md-9">	
            <?php
    echo input_tag('CFG[TWITTER_SECRET_KEY]', CFG_TWITTER_SECRET_KEY, ['class' => 'form-control input-medium']); ?>
        </div>			
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label"><?php
    echo TEXT_REDIRECT_URI ?></label>
        <div class="col-md-9">	
            <?php
    echo input_tag(
        'redirect_uri',
        url_for('social_login/twitter'),
        ['class' => 'form-control input-xlarge select-all', 'readonly' => 'readonly']
    ); ?>
        </div>			
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label"><?php
    echo TEXT_BUTTON_TITLE ?></label>
        <div class="col-md-9">	
            <?php
    echo input_tag('CFG[TWITTER_BUTTON_TITLE]', CFG_TWITTER_BUTTON_TITLE, ['class' => 'form-control input-medium']); ?>
        </div>			
    </div>
</div-->

    <?php
    echo submit_tag(TEXT_BUTTON_SAVE) ?>

</div>
</form>

<script>
    $(function () {
        $('#cfg').validate({ignore: '.ignore-validation'});
    });
</script> 