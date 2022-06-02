<h3 class="page-title"><?php
    echo TEXT_EMAILS_LAYOUT ?></h3>

<p><?php
    echo TEXT_EMAILS_LAYOUT_INFO ?></p>

<?php
echo form_tag('cfg', url_for('configuration/save'), ['class' => 'form-horizontal']) ?>
<?php
echo input_hidden_tag('redirect_to', 'configuration/emails_layout') ?>
<div class="form-body">


    <div class="form-group">
        <label class="col-md-2 control-label"><?php
            echo TEXT_TOGGLE_ON ?></label>
        <div class="col-md-10">
            <?php
            echo select_tag(
                'CFG[USE_EMAIL_HTML_LAYOUT]',
                $default_selector,
                CFG_USE_EMAIL_HTML_LAYOUT,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-2 control-label"><?php
            echo TEXT_CUSTOM_HTML ?></label>
        <div class="col-md-10">
            <?php
            echo textarea_tag('CFG[EMAIL_HTML_LAYOUT]', CFG_EMAIL_HTML_LAYOUT, ['class' => 'form-control code-mirror']
            ); ?>
        </div>
    </div>

    <?php
    echo submit_tag(TEXT_BUTTON_SAVE) ?>

</div>
</form>


<?php
echo app_include_codemirror(['css']) ?>

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
