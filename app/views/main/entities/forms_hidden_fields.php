<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_HIDDEN_FIELDS_TIP) ?>

<?= \Helpers\Html::form_tag(
    'fields_form',
    \Helpers\Urls::url_for('main/entities/forms_hidden_fields/save', 'entities_id=' . \K::$fw->GET['entities_id']),
    ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']
) ?>
<div class="modal-body ajax-modal-width-790">
    <div class="form-body">
        <p><?= \K::$fw->TEXT_HIDDEN_FIELDS_IN_FORM_TIP ?></p>
        <div class="form-group">
            <div class="col-md-12">
                <?= \Helpers\Html::select_tag(
                    'hidden_form_fields[]',
                    \K::$fw->choices,
                    \K::$fw->cfg->get('hidden_form_fields'),
                    ['class' => 'form-control chosen-select', 'multiple' => 'multiple']
                ) ?>
            </div>
        </div>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>