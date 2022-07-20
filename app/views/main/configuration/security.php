<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<h3 class="page-title"><?= \K::$fw->TEXT_HEADING_SECURITY_CONFIGURATION ?></h3>

<?= \Helpers\Html::form_tag('cfg', \Helpers\Urls::url_for('main/configuration/save'), ['class' => 'form-horizontal']) ?>
<?= \Helpers\Html::input_hidden_tag('redirect_to', 'main/configuration/security') ?>
<div class="form-body">

    <h3 class="form-section">Google reCAPTCHA v2 <a href="https://www.google.com/recaptcha/intro/index.html"
                                                    target="_blank"><i class="fa fa-external-link"
                                                                       aria-hidden="true"></i></a></h3>
    <p><?= \K::$fw->TEXT_RECAPTCHA_INFO ?></p>
    <p><?= \K::$fw->TEXT_RECAPTCHA_HOW_ENABLE ?></p>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES"><?= \K::$fw->TEXT_STATUS ?></label>
        <div class="col-md-9">
            <p class="form-control-static"><?= \Helpers\App::app_render_status_label(
                    \Helpers\App_recaptcha::is_enabled()
                ) ?></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES"><?= \K::$fw->TEXT_RECAPTCHA_SITE_KEY ?></label>
        <div class="col-md-9">
            <p class="form-control-static"><?= \K::$fw->CFG_RECAPTCHA_KEY ?></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES"><?= \K::$fw->TEXT_RECAPTCHA_SECRET_KEY ?></label>
        <div class="col-md-9">
            <p class="form-control-static"><?= \K::$fw->CFG_RECAPTCHA_SECRET_KEY ?></p>
        </div>
    </div>

    <h3 class="form-section"><?= \K::$fw->TEXT_RESTRICTED_COUNTRIES ?></h3>

    <p><?= \K::$fw->TEXT_RESTRICTED_COUNTRIES_INFO ?></p>
    <p><?= \K::$fw->TEXT_RESTRICTED_COUNTRIES_HOW_ENABLE ?></p>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES"><?= \K::$fw->TEXT_STATUS ?></label>
        <div class="col-md-9">
            <p class="form-control-static"><?= \Helpers\App::app_render_status_label(
                    \Helpers\App_restricted_countries::is_enabled()
                ) ?></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES"><?= \K::$fw->TEXT_ALLOWED_COUNTRIES ?></label>
        <div class="col-md-9">
            <p class="form-control-static"><?= \K::$fw->CFG_ALLOWED_COUNTRIES_LIST ?></p>
        </div>
    </div>

    <h3 class="form-section"><?= \K::$fw->TEXT_RESTRICTED_BY_IP ?></h3>

    <p><?= \K::$fw->TEXT_RESTRICTED_BY_IP_INFO ?></p>
    <p><?= \K::$fw->TEXT_RESTRICTED_BY_IP_HOW_ENABLE ?></p>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES"><?= \K::$fw->TEXT_STATUS ?></label>
        <div class="col-md-9">
            <p class="form-control-static"><?= \Helpers\App::app_render_status_label(
                    \Helpers\App_restricted_ip::is_enabled()
                ) ?></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES"><?= \K::$fw->TEXT_ALLOWED_IP ?></label>
        <div class="col-md-9">
            <p class="form-control-static"><?= \K::$fw->CFG_ALLOWED_IP_LIST ?></p>
        </div>
    </div>
</div>
</form>