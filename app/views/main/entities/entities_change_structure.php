<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_CHANGE_STRUCTURE) ?>

<?= \Helpers\Html::form_tag(
    'entities_form',
    \Helpers\Urls::url_for(
        'main/entities/entities_change_structure/save',
        (isset(\K::$fw->GET['id']) ? '&id=' . \K::$fw->GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <p><?= \K::$fw->TEXT_CHANGE_STRUCTURE_INFO; ?></p>
    <div class="form-body">
        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?= \K::$fw->TEXT_MOVE_ENTITY ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'entities_id',
                    ['' => \K::$fw->TEXT_SELECT_ENTITY] + \Models\Main\Entities::get_choices(),
                    '',
                    ['class' => 'form-control input-xlarge required']
                ) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?= \K::$fw->TEXT_ENTITY_MOVE_TO ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'move_to_entities_id',
                    \Models\Main\Entities::get_choices_with_empty(\K::$fw->TEXT_TOP_LEVEL),
                    '',
                    ['class' => 'form-control input-xlarge required', 'onChange' => 'get_parent_items_list()']
                ) ?>
            </div>
        </div>
        <div id="parent_items_list"></div>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#entities_form').validate({
            ignore: '',
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });
    });

    function get_parent_items_list() {
        $('#parent_items_list').html('<div class="ajax-loading"></div>');

        $('#parent_items_list').load('<?= \Helpers\Urls::url_for(
            'main/entities/entities_change_structure/get_parent_items'
        )?>', {entities_id: $('#move_to_entities_id').val()}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();
            }
        });
    }
</script>