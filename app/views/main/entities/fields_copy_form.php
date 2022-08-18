<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_COPY_FIELDS) ?>

<?= \Helpers\Html::form_tag(
    'form-copy-to',
    \Helpers\Urls::url_for('main/entities/fields/copy_selected', 'entities_id=' . \K::$fw->GET['entities_id']),
    ['class' => 'form-horizontal']
) ?>
<?= \Helpers\Html::input_hidden_tag('selected_fields') ?>
<div class="modal-body">
    <div id="modal-body-content">
        <div class="form-group">
            <label class="col-md-4 control-label" for="type"><?= \K::$fw->TEXT_SELECT_ENTITY ?></label>
            <div class="col-md-8">
                <?= \Helpers\Html::select_tag(
                    'copy_to_entities_id',
                    \Models\Main\Entities::get_choices(),
                    \K::$fw->GET['entities_id'],
                    ['class' => 'form-control']
                ) ?>
            </div>
        </div>
        <div id="entities_form_tabs"></div>
    </div>
</div>
<?= \Helpers\App::ajax_modal_template_footer(\K::$fw->TEXT_COPY) ?>

</form>

<script>
    $(function () {
        if ($('.fields_checkbox:checked').length == 0) {
            $('#modal-body-content').html('<?= \K::$fw->TEXT_PLEASE_SELECT_FIELDS ?>')
            $('.btn-primary-modal-action').hide()
        } else {
            selected_fields_list = $('.fields_checkbox:checked').serialize().replace(/fields%5B%5D=/g, '').replace(/&/g, ',');
            $('#selected_fields').val(selected_fields_list);

            get_entities_form_tabs();
        }

        $('#copy_to_entities_id').change(function () {
            get_entities_form_tabs();
        })

        $('#form-copy-to').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });
    })

    function get_entities_form_tabs() {
        $('#entities_form_tabs').load('<?= \Helpers\Urls::url_for(
            'main/entities/fields/get_entities_form_tabs'
        ) ?>', {entities_id: $('#copy_to_entities_id').val()})
    }
</script>