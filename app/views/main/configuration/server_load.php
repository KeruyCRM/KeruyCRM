<h3 class="page-title"><?= \K::$fw->TEXT_SERVER_LOAD ?></h3>

<p><?= \K::$fw->TEXT_SERVER_LOAD_INFO ?></p>
<p><?= \K::$fw->TEXT_CACHE_FOLDER . ': ' . \K::$fw->DIR_FS_CACHE ?></p>

<?= \Helpers\Html::form_tag(
    'cfg',
    \Helpers\Urls::url_for('main/configuration/save'),
    ['class' => 'form-horizontal']
) ?>
<?= \Helpers\Html::input_hidden_tag('redirect_to', 'main/configuration/server_load') ?>
<div class="form-body">

    <p class="form-section form-section-0"><?= \K::$fw->TEXT_REPORTS_IN_HEADER_MENU ?></p>
    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_LDAP_USE"><?= \K::$fw->TEXT_USE_CACHE ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::select_tag(
                'CFG[USE_CACHE_REPORTS_IN_HEADER]',
                \K::$fw->default_selector,
                \K::$fw->CFG_USE_CACHE_REPORTS_IN_HEADER,
                ['class' => 'form-control input-small']
            ); ?>
            <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_REPORTS_IN_HEADER_MENU_CACHE_INFO) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_LDAP_SERVER_NAME"><?= \Helpers\App::tooltip_icon(\K::$fw->TEXT_CACHE_LIVETIME_INFO) . \K::$fw->TEXT_CACHE_LIVETIME ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::input_tag(
                'CFG[CACHE_REPORTS_IN_HEADER_LIFETIME]',
                \K::$fw->CFG_CACHE_REPORTS_IN_HEADER_LIFETIME,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>

</div>
</form>