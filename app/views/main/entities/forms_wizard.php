<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_FORM_WIZARD) ?>

<?= \Helpers\Html::form_tag(
    'fields_form',
    \Helpers\Urls::url_for(
        'main/entities/entities_configuration/save',
        'redirect_to=entities/forms&entities_id=' . \K::$fw->GET['entities_id']
    ),
    ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']
) ?>
<div class="modal-body ajax-modal-width-790">
    <div class="form-body">
        <p><?= \K::$fw->TEXT_FORM_WIZARD_INFO ?></p>
        <div class="form-group">
            <label class="col-md-4 control-label"><?= \K::$fw->TEXT_IS_ACTIVE; ?></label>
            <div class="col-md-8">
                <?= \Helpers\Html::select_tag(
                    'cfg[is_form_wizard]',
                    \K::$fw->default_selector,
                    \K::$fw->cfg->get('is_form_wizard', 0),
                    ['class' => 'form-control input-small']
                ); ?>
            </div>
        </div>
        <div class="form-group" form_display_rules="cfg_is_form_wizard:1">
            <label class="col-md-4 control-label"><?= \K::$fw->TEXT_DISPLAY_PROGRESS_BAR; ?></label>
            <div class="col-md-8">
                <?= \Helpers\Html::select_tag(
                    'cfg[is_form_wizard_progress_bar]',
                    \K::$fw->default_selector,
                    \K::$fw->cfg->get('is_form_wizard_progress_bar', 0),
                    ['class' => 'form-control input-small']
                ); ?>
            </div>
        </div>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>