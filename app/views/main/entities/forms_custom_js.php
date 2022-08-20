<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header('JavaScript') ?>

<?= \Helpers\Html::form_tag(
    'fields_form',
    \Helpers\Urls::url_for('main/entities/forms/save_javascript', 'entities_id=' . \K::$fw->GET['entities_id']),
    ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']
) ?>
<div class="modal-body ajax-modal-width-790">
    <div class="form-body">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#javascript_in_form"
                                  data-toggle="tab"><?= \K::$fw->TEXT_JAVASCRIPT_IN_FORM ?></a></li>
            <li><a href="#onSubmit" data-toggle="tab" id="onSubmitTab"><?= 'onSubmit' ?></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="javascript_in_form">
                <p><?= \K::$fw->TEXT_JAVASCRIPT_IN_FORM_INFO ?></p>
                <div class="form-group">
                    <div class="col-md-12">
                        <?= \Helpers\Html::textarea_tag(
                            'javascript_in_from',
                            \K::$fw->cfg->get('javascript_in_from'),
                            ['class' => 'form-control']
                        ) ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade " id="onSubmit">
                <p><?= \K::$fw->TEXT_JAVASCRIPT_ONSUBMIT_FORM_INFO ?></p>
                <div class="form-group">
                    <div class="col-md-12">
                        <?= \Helpers\Html::textarea_tag(
                            'javascript_onsubmit',
                            \K::$fw->cfg->get('javascript_onsubmit'),
                            ['class' => 'form-control']
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>

<?= \Helpers\App::app_include_codemirror(['javascript']) ?>

<script>
    $('#ajax-modal').on('shown.bs.modal', function () {
        var myCodeMirror1 = CodeMirror.fromTextArea(document.getElementById('javascript_in_from'), {
            lineNumbers: true,
            autofocus: true,
            matchBrackets: true,
            lineWrapping: true,
        });
    });

    $('#onSubmitTab').click(function () {
        if (!$(this).hasClass('active-codemirror')) {
            setTimeout(function () {
                var myCodeMirror2 = CodeMirror.fromTextArea(document.getElementById('javascript_onsubmit'), {
                    lineNumbers: true,
                    matchBrackets: true,
                    lineWrapping: true,
                });
            }, 300);

            $(this).addClass('active-codemirror')
        }
    })
</script>