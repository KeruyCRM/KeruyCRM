<h3 class="page-title"><?= \K::$fw->TEXT_HEADING_MAINTENANCE_MODE ?></h3>

<?= \Helpers\Html::form_tag(
    'cfg',
    \Helpers\Urls::url_for('main/configuration/save'),
    ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']
) ?>
<?= \Helpers\Html::input_hidden_tag('redirect_to', 'main/configuration/maintenance_mode') ?>
<div class="form-body">

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_MAINTENANCE_MODE"><?= \K::$fw->TEXT_MAINTENANCE_MODE ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[MAINTENANCE_MODE]',
                \K::$fw->default_selector,
                \K::$fw->CFG_MAINTENANCE_MODE,
                ['class' => 'form-control input-small']
            ); ?>
            <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_MAINTENANCE_MODE_NOTE) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"
               for="CFG_MAINTENANCE_ALLOW_LOGIN_FOR_USERS"><?= \K::$fw->TEXT_ALLOW_LOGIN_FOR_USERS ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[MAINTENANCE_ALLOW_LOGIN_FOR_USERS][]',
                \K::$fw->choices,
                \K::$fw->CFG_MAINTENANCE_ALLOW_LOGIN_FOR_USERS,
                ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"
               for="CFG_MAINTENANCE_MESSAGE_HEADING"><?= \K::$fw->TEXT_MESSAGE_HEADING ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[MAINTENANCE_MESSAGE_HEADING]',
                \K::$fw->CFG_MAINTENANCE_MESSAGE_HEADING,
                ['class' => 'form-control input-large']
            ); ?>
            <?= \Helpers\App::tooltip_text(
                \K::$fw->TEXT_DEFAULT . ': "' . \K::$fw->TEXT_MAINTENANCE_MESSAGE_HEADING . '"'
            ) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"
               for="CFG_MAINTENANCE_MESSAGE_CONTENT"><?= \K::$fw->TEXT_MESSAGE_CONTENT ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::textarea_tag(
                'CFG[MAINTENANCE_MESSAGE_CONTENT]',
                \K::$fw->CFG_MAINTENANCE_MESSAGE_CONTENT,
                ['class' => 'form-control input-xlarge', 'rows' => 3]
            ); ?>
            <?= \Helpers\App::tooltip_text(
                \K::$fw->TEXT_DEFAULT . ': "' . \K::$fw->TEXT_MAINTENANCE_MESSAGE_CONTENT . '"'
            ) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"
               for="APP_LOGIN_MAINTENANCE_BACKGROUND"><?= \K::$fw->TEXT_LOGIN_PAGE_BACKGROUND ?></label>
        <div class="col-md-9">
            <?php
            echo \Helpers\Html::input_file_tag(
                    'APP_LOGIN_MAINTENANCE_BACKGROUND',
                    [
                        'accept' => \Tools\FieldsTypes\Fieldtype_attachments::get_accept_types_by_extensions(
                            'gif,jpg,png'
                        )
                    ]
                ) . \Helpers\Html::input_hidden_tag(
                    'CFG[APP_LOGIN_MAINTENANCE_BACKGROUND]',
                    \K::$fw->CFG_APP_LOGIN_MAINTENANCE_BACKGROUND
                );
            if (is_file(\K::$fw->DIR_FS_UPLOADS . '/' . \K::$fw->CFG_APP_LOGIN_MAINTENANCE_BACKGROUND)) {
                echo '<span class="help-block">' . \K::$fw->CFG_APP_LOGIN_MAINTENANCE_BACKGROUND . '<label class="checkbox">' . \Helpers\Html::input_checkbox_tag(
                        'delete_login_maintenance_background'
                    ) . ' ' . \K::$fw->TEXT_DELETE . '</label></span>';
            }

            echo \Helpers\App::tooltip_text(\K::$fw->TEXT_LOGIN_PAGE_BACKGROUND_INFO);
            ?>
        </div>
    </div>

    <?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>

</div>
</form>