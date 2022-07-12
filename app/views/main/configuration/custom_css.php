<h3 class="page-title"><?= \K::$fw->TEXT_CUSTOM_CSS ?></h3>

<p><?= \K::$fw->TEXT_CUSTOM_CSS_INFO ?></p>

<?php
$css_folder = \K::$fw->DIR_FS_CATALOG . 'css';

if (!is_writable($css_folder)) {
    echo \Helpers\App::alert_error(sprintf(\K::$fw->TEXT_ERROR_FOLDER_NOT_WRITABLE, $css_folder));
} else {
    $custom_css = '';

    if (is_file('css/custom.css')) {
        $custom_css = file_get_contents('css/custom.css');
    }

    echo \Helpers\Html::form_tag(
            'custom_css_form',
            \Helpers\Urls::url_for('main/configuration/custom_css/save')
        ) .
        \Helpers\Html::textarea_tag('custom_css', $custom_css) .
        \Helpers\App::tooltip_text(
            \K::$fw->TEXT_FILE_PATH . ': ' . \K::$fw->DIR_FS_CATALOG . 'css/custom.css'
        ) . '<br>' .
        '<div id="custom_css_submit">' . \Helpers\Html::submit_tag(\K::$fw->TEXT_SAVE) . ' 
                 <i class="fa fa-check" style="display:none" aria-hidden="true"></i>                
                 <div class="fa fa-spinner fa-spin primary-modal-action-loading"></div>                 
              </div>' .
        '</form>';
}
?>

<?= \Helpers\App::app_include_codemirror(['css']) ?>

<script>
    var editor = CodeMirror.fromTextArea(document.getElementById("custom_css"), {
        lineNumbers: true,
        autofocus: true,
        matchBrackets: true,
        height: 600,
        extraKeys: {
            "F11": function (cm) {
                cm.setOption("fullScreen", !cm.getOption("fullScreen"));
            },
            "Esc": function (cm) {
                if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
            },
            "Ctrl-S": function (instance) {
                $("#custom_css_submit .btn").click();
            },
        }
    }).setSize(null, 600);


    $("#custom_css_form").submit(function (event) {

        $('.primary-modal-action-loading').css('visibility', 'visible')

        $.ajax({
            method: 'POST',
            url: $('#custom_css_form').attr('action'),
            data: $('#custom_css_form').serializeArray()
        }).done(function (msg) {

            $('.primary-modal-action-loading').css('visibility', 'hidden')
            $('#custom_css_submit .fa-check').show().fadeOut();

        })

        event.preventDefault();
    });

</script>