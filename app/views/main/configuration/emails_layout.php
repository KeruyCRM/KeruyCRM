<h3 class="page-title"><?= \K::$fw->TEXT_EMAILS_LAYOUT ?></h3>

<p><?= \K::$fw->TEXT_EMAILS_LAYOUT_INFO ?></p>

<?= \Helpers\Html::form_tag('cfg', Helpers\Urls::url_for('main/configuration/save'), ['class' => 'form-horizontal']) ?>
<?= \Helpers\Html::input_hidden_tag('redirect_to', 'main/configuration/emails_layout') ?>
<div class="form-body">
    <div class="form-group">
        <label class="col-md-2 control-label"><?= \K::$fw->TEXT_TOGGLE_ON ?></label>
        <div class="col-md-10">
            <?= \Helpers\Html::select_tag(
                'CFG[USE_EMAIL_HTML_LAYOUT]',
                \K::$fw->default_selector,
                \K::$fw->CFG_USE_EMAIL_HTML_LAYOUT,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-2 control-label"><?= \K::$fw->TEXT_CUSTOM_HTML ?></label>
        <div class="col-md-10">
            <?= \Helpers\Html::textarea_tag('CFG[EMAIL_HTML_LAYOUT]', \K::$fw->CFG_EMAIL_HTML_LAYOUT, ['class' => 'form-control code-mirror']
            ); ?>
        </div>
    </div>

    <?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>

</div>
</form>


<?= \Helpers\App::app_include_codemirror(['htmlmixed']) ?>

<script>
    $('.code-mirror').each(function () {
        var editor = CodeMirror.fromTextArea(document.getElementById($(this).attr('id')), {
            lineNumbers: true,
            autofocus: true,
            height: 600,
            lineWrapping: true,
            matchBrackets: true,
            extraKeys: {
                "F11": function (cm) {
                    cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                },
                "Esc": function (cm) {
                    if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                },
            }
        }).setSize(null, 600);

    })
</script>