<ul class="page-breadcrumb breadcrumb">
    <li><?php
        echo link_to(TEXT_EXT_EXPORT_TEMPLATES, url_for('ext/templates/export_templates')) ?><i
                class="fa fa-angle-right"></i></li>
    <li><?php
        echo $template_info['entities_name'] ?><i class="fa fa-angle-right"></i></li>
    <li><?php
        echo $template_info['name'] ?></li>
</ul>

<p><?php
    echo TEXT_EXT_EXPORT_TEMPLATES_TIP ?></p>

<?php
echo export_templates::get_available_fields_for_all_entities($template_info['entities_id']);
?>

<?php
echo form_tag(
    'export_templates_form',
    url_for('ext/templates/export_templates', 'action=save_description&id=' . $_GET['id']),
    ['class' => 'form-horizontal']
) ?>

<div class="row">
    <div class="col-md-12">
        <?php
        echo textarea_tag('export_templates_description', $template_info['description']) ?>

        <br>

        <?php
        echo submit_tag(TEXT_BUTTON_SAVE) . ' ' . button_tag(
                TEXT_BUTTON_CANCEL,
                url_for('ext/templates/export_templates'),
                false,
                ['class' => 'btn btn-default']
            ) ?>
        <i class="fa fa-check" style="display:none" aria-hidden="true"></i>
        <div class="fa fa-spinner fa-spin primary-modal-action-loading"></div>

        <br>

    </div>
</div>

</form>

<?php
if ($template_info['type'] == 'html_code') { ?>

    <?php
    echo app_include_codemirror(['xml', 'javascript', 'css', 'htmlmixed']) ?>

    <link rel="stylesheet" href="js/codemirror/addon/hint/show-hint.css">
    <script src="js/codemirror/addon/hint/show-hint.js"></script>
    <script src="js/codemirror/addon/hint/html-hint.js"></script>
    <script src="js/codemirror/addon/hint/xml-hint.js"></script>

    <script>

        var myCodeMirror = CodeMirror.fromTextArea(document.getElementById("export_templates_description"), {
            mode: "text/html",
            lineNumbers: true,
            autofocus: true,
            lineWrapping: true,
            matchBrackets: true,
            height: 600,
            extraKeys: {
                "F11": function (cm) {
                    cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                },
                "Esc": function (cm) {
                    if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                },
                "Ctrl-S": function (cm) {
                    $('.primary-modal-action-loading').css('visibility', 'visible')

                    cm.save();

                    $.ajax({
                        method: "POST",
                        url: $('#export_templates_form').attr('action'),
                        data: $('#export_templates_form').serializeArray()
                    }).done(function () {
                        $('.primary-modal-action-loading').css('visibility', 'hidden')
                        $('#export_templates_form .fa-check').show().fadeOut();
                    })
                },
            }
        });

        $(function () {

            myCodeMirror.setSize(null, 600)

            $('.insert_to_template_description').click(function () {
                html = $(this).html().trim();

                codeMirrorInsertText(myCodeMirror, html)

            })
        })

    </script>

<?php
} elseif ($template_info['type'] == 'label') { ?>

    <script>
        $(function () {

            CKEDITOR.config.baseFloatZIndex = 20000;

            CKEDITOR.replace('export_templates_description', {
                height: 450,
                startupFocus: true,
                language: app_language_short_code,
                toolbar: (app_language_text_direction == 'rtl' ? 'RtlFull' : 'Full'),
                contentsCss: '<?php echo url_for(
                    'ext/templates/export_templates_configure',
                    'action=get_css&id=' . $template_info['id']
                ) ?>',
            });


            $('.insert_to_template_description').click(function () {
                html = $(this).html().trim();
                CKEDITOR.instances.export_templates_description.insertText(html);
            })
        })
    </script>

<?php
} else { ?>

    <script>
        $(function () {

            use_editor_full('export_templates_description', true)

            $('.insert_to_template_description').click(function () {
                html = $(this).html().trim();
                CKEDITOR.instances.export_templates_description.insertText(html);
            })
        })
    </script>

<?php
} ?>
