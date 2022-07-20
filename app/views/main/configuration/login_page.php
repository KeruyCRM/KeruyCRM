<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<h3 class="page-title"><?= \K::$fw->TEXT_HEADING_LOGIN_PAGE_CONFIGURATION ?></h3>

<?= \Helpers\Html::form_tag(
    'cfg',
    \Helpers\Urls::url_for('main/configuration/save'),
    ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']
) ?>
<?= \Helpers\Html::input_hidden_tag('redirect_to', 'main/configuration/login_page') ?>
<div class="form-body">

    <div class="form-group">
        <label class="col-md-3 control-label"
               for="CFG_LOGIN_PAGE_HEADING"><?= \K::$fw->TEXT_LOGIN_PAGE_HEADING ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[LOGIN_PAGE_HEADING]',
                \K::$fw->CFG_LOGIN_PAGE_HEADING,
                ['class' => 'form-control input-large']
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"
               for="CFG_LOGIN_PAGE_CONTENT"><?= \K::$fw->TEXT_LOGIN_PAGE_CONTENT ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::textarea_tag(
                'CFG[LOGIN_PAGE_CONTENT]',
                \K::$fw->CFG_LOGIN_PAGE_CONTENT,
                ['class' => 'form-control input-xlarge editor', 'rows' => 3]
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"
               for="APP_LOGIN_PAGE_BACKGROUND"><?= \K::$fw->TEXT_LOGIN_PAGE_BACKGROUND ?></label>
        <div class="col-md-9">
            <?php
            echo \Helpers\Html::input_file_tag(
                    'APP_LOGIN_PAGE_BACKGROUND',
                    [
                        'accept' => \Tools\FieldsTypes\Fieldtype_attachments::get_accept_types_by_extensions(
                            'gif,jpg,png'
                        )
                    ]
                ) . \Helpers\Html::input_hidden_tag(
                    'CFG[APP_LOGIN_PAGE_BACKGROUND]',
                    \K::$fw->CFG_APP_LOGIN_PAGE_BACKGROUND
                );
            if (is_file(\K::$fw->DIR_FS_UPLOADS . '/' . \K::$fw->CFG_APP_LOGIN_PAGE_BACKGROUND)) {
                echo '<span class="help-block">' . \K::$fw->CFG_APP_LOGIN_PAGE_BACKGROUND . '<label class="checkbox">' . \Helpers\Html::input_checkbox_tag(
                        'delete_login_page_background'
                    ) . ' ' . \K::$fw->TEXT_DELETE . '</label></span>';
            }

            echo \Helpers\App::tooltip_text(\K::$fw->TEXT_LOGIN_PAGE_BACKGROUND_INFO);
            ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"
               for="CFG_LOGIN_PAGE_HIDE_REMEMBER_ME"><?= \K::$fw->TEXT_HIDE . ' "' . \K::$fw->TEXT_REMEMBER_ME . '"' ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[LOGIN_PAGE_HIDE_REMEMBER_ME]',
                \K::$fw->default_selector,
                \K::$fw->CFG_LOGIN_PAGE_HIDE_REMEMBER_ME,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <?php
    if (\Helpers\App::is_ext_installed()) {
        $modules = new \Tools\Modules('digital_signature');
        $choices = $modules->get_active_modules();
        ?>
        <div class="form-group">
            <label class="col-md-3 control-label"
                   for="CFG_LOGIN_DIGITAL_SIGNATURE_MODULE"><?= \K::$fw->TEXT_DIGITAL_SIGNATURE_LOGIN ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'CFG[LOGIN_DIGITAL_SIGNATURE_MODULE]',
                    ['' => ''] + $choices,
                    \K::$fw->CFG_LOGIN_DIGITAL_SIGNATURE_MODULE,
                    ['class' => 'form-control input-large']
                ); ?>
                <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_DIGITAL_SIGNATURE_LOGIN_INFO) ?>
            </div>
        </div>
        <?php
    } ?>

    <?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>

</div>
</form>