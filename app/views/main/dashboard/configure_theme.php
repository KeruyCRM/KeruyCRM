<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_CONFIGURE_THEME) ?>

<?= \Helpers\Html::form_tag('dashboard', \Helpers\Urls::url_for('main/dashboard'), ['class' => 'form-horizontal']) ?>
<div class="modal-body">

    <div class="form-group">
        <label class="col-md-4 "><?= \K::$fw->TEXT_SIDEBAR ?></label>
        <div class="col-md-8">
            <?= \Helpers\Html::select_tag(
                'sidebar-option',
                ['default' => \K::$fw->TEXT_DEFAULT, 'fixed' => \K::$fw->TEXT_SIDEBAR_FIXED],
                (\K::app_users_cfg()->get('sidebar-option') == 'page-sidebar-fixed' ? 'fixed' : 'default'),
                [
                    'class' => 'sidebar-option form-control input-medium',
                    'onChange' => "set_user_cfg('sidebar-option',this.value)"
                ]
            ) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-4 "><?= \K::$fw->TEXT_SIDEBAR_POSITION ?></label>
        <div class="col-md-8">
            <?= \Helpers\Html::select_tag(
                'sidebar-pos-option',
                ['left' => \K::$fw->TEXT_SIDEBAR_POS_LEFT, 'right' => \K::$fw->TEXT_SIDEBAR_POS_RIGHT],
                (\K::app_users_cfg()->get('sidebar-pos-option') == 'page-sidebar-reversed' ? 'right' : 'left'),
                [
                    'class' => 'sidebar-pos-option form-control input-medium',
                    'onChange' => "set_user_cfg('sidebar-pos-option',this.value)"
                ]
            ) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-4 "><?= \K::$fw->TEXT_SCALE ?></label>
        <div class="col-md-8">
            <?= \Helpers\Html::select_tag(
                'page-scale-option',
                ['default' => \K::$fw->TEXT_DEFAULT, 'reduced' => \K::$fw->TEXT_SCALE_REDUCED],
                (\K::app_users_cfg()->get('page-scale-option') == 'page-scale-reduced' ? 'reduced' : 'default'),
                [
                    'class' => 'scale-option  form-control input-medium',
                    'onChange' => "set_user_cfg('page-scale-option',this.value)"
                ]
            ) ?>
        </div>
    </div>

</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>