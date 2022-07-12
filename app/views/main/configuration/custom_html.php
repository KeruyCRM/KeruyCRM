<h3 class="page-title"><?= \K::$fw->TEXT_CUSTOM_HTML ?></h3>

<p><?= \K::$fw->TEXT_CUSTOM_HTML_INFO ?></p>

<?= \Helpers\Html::form_tag(
    'cfg',
    \Helpers\Urls::url_for('main/configuration/save', 'redirect_to=main/configuration/custom_html'),
    ['class' => 'form-horizontal']
) ?>
<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label"><?= htmlspecialchars(\K::$fw->TEXT_ADD_CODE_END_OF_HEAD) ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::textarea_tag('CFG[CUSTOM_HTML_HEAD]', \K::$fw->CFG_CUSTOM_HTML_HEAD, ['class' => 'form-control code-mirror']
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"><?= htmlspecialchars(\K::$fw->TEXT_ADD_CODE_BEFORE_BODY) ?></label>
        <div class="col-md-9">
            <?= \Helpers\Html::textarea_tag('CFG[CUSTOM_HTML_BODY]', \K::$fw->CFG_CUSTOM_HTML_BODY, ['class' => 'form-control code-mirror']
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
            height: 300,
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
        }).setSize(null, 300);

    })

</script>