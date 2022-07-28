<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php
echo ajax_modal_template_header(TEXT_FORM_WIZARD) ?>

<?php
$entities_id = _GET('entities_id');
$cfg = new entities_cfg($_GET['entities_id']);

$default_selector = ['1' => TEXT_YES, '0' => TEXT_NO];
?>

<?php
echo form_tag(
    'fields_form',
    url_for(
        'entities/entities_configuration',
        'action=save&redirect_to=entities/forms&entities_id=' . $_GET['entities_id']
    ),
    ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']
) ?>
<div class="modal-body ajax-modal-width-790">
    <div class="form-body">

        <p><?php
            echo TEXT_FORM_WIZARD_INFO ?></p>

        <div class="form-group">
            <label class="col-md-4 control-label"><?php
                echo TEXT_IS_ACTIVE; ?></label>
            <div class="col-md-8">
                <?php
                echo select_tag(
                    'cfg[is_form_wizard]',
                    $default_selector,
                    $cfg->get('is_form_wizard', 0),
                    ['class' => 'form-control input-small']
                ); ?>
            </div>
        </div>

        <div class="form-group" form_display_rules="cfg_is_form_wizard:1">
            <label class="col-md-4 control-label"><?php
                echo TEXT_DISPLAY_PROGRESS_BAR; ?></label>
            <div class="col-md-8">
                <?php
                echo select_tag(
                    'cfg[is_form_wizard_progress_bar]',
                    $default_selector,
                    $cfg->get('is_form_wizard_progress_bar', 0),
                    ['class' => 'form-control input-small']
                ); ?>
            </div>
        </div>


    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form> 