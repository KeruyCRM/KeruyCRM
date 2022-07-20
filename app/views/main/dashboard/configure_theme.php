<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php
echo ajax_modal_template_header(TEXT_CONFIGURE_THEME) ?>

<?php
echo form_tag('dashboard', url_for('dashboard/'), ['class' => 'form-horizontal']) ?>
<div class="modal-body">

    <div class="form-group">
        <label class="col-md-4 "><?php
            echo TEXT_SIDEBAR ?></label>
        <div class="col-md-8">
            <?php
            echo select_tag(
                'sidebar-option',
                ['default' => TEXT_DEFAULT, 'fixed' => TEXT_SIDEBAR_FIXED],
                ($app_users_cfg->get('sidebar-option') == 'page-sidebar-fixed' ? 'fixed' : 'default'),
                [
                    'class' => 'sidebar-option form-control input-medium',
                    'onChange' => "set_user_cfg('sidebar-option',this.value)"
                ]
            ) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-4 "><?php
            echo TEXT_SIDEBAR_POSITION ?></label>
        <div class="col-md-8">
            <?php
            echo select_tag(
                'sidebar-pos-option',
                ['left' => TEXT_SIDEBAR_POS_LEFT, 'right' => TEXT_SIDEBAR_POS_RIGHT],
                ($app_users_cfg->get('sidebar-pos-option') == 'page-sidebar-reversed' ? 'right' : 'left'),
                [
                    'class' => 'sidebar-pos-option form-control input-medium',
                    'onChange' => "set_user_cfg('sidebar-pos-option',this.value)"
                ]
            ) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-4 "><?php
            echo TEXT_SCALE ?></label>
        <div class="col-md-8">
            <?php
            echo select_tag(
                'page-scale-option',
                ['default' => TEXT_DEFAULT, 'reduced' => TEXT_SCALE_REDUCED],
                ($app_users_cfg->get('page-scale-option') == 'page-scale-reduced' ? 'reduced' : 'default'),
                [
                    'class' => 'scale-option  form-control input-medium',
                    'onChange' => "set_user_cfg('page-scale-option',this.value)"
                ]
            ) ?>
        </div>
    </div>

</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

