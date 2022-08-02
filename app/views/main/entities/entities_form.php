<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_HEADING_ENTITY_INFO) ?>

<?= \Helpers\Html::form_tag(
    'entities_form',
    \Helpers\Urls::url_for(
        'main/entities/entities/save',
        (isset(\K::$fw->GET['id']) ? 'id=' . \K::$fw->GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">
        <?php
        if (isset(\K::$fw->GET['parent_id'])) echo \Helpers\Html::input_hidden_tag(
            'parent_id',
            \K::$fw->GET['parent_id']
        ) ?>

        <?php
        if (!isset(\K::$fw->GET['parent_id']) and (int)\K::$fw->obj['parent_id'] == 0) {
            $choices = \Models\Main\Entities_groups::get_choices();

            if (count($choices)) {
                echo '
            <div class="form-group">
                <label class="col-md-3 control-label" for="name">' . \K::$fw->TEXT_GROUP . '</label>
                <div class="col-md-9">	
                      ' . \Helpers\Html::select_tag(
                        'group_id',
                        $choices,
                        \K::$fw->obj['group_id'],
                        ['class' => 'form-control input-large']
                    ) . '
                </div>			
          </div>  
            ';
            }
        }
        ?>
        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?= \K::$fw->TEXT_NAME ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'name',
                    \K::$fw->obj['name'],
                    ['class' => 'form-control input-large required']
                ) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="sort_order"><?= \K::$fw->TEXT_SORT_ORDER ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'sort_order',
                    \K::$fw->obj['sort_order'],
                    ['class' => 'form-control input-small required number']
                ) ?>
            </div>
        </div>
        <?php
        if (isset(\K::$fw->GET['parent_id']) or \K::$fw->obj['parent_id'] > 0): ?>
            <div class="form-group">
                <label class="col-md-3 control-label" for="display_in_menu"><?= \K::$fw->TEXT_DISPLAY_IN_MENU ?></label>
                <div class="col-md-9">
                    <p class="form-control-static"><?= \Helpers\Html::input_checkbox_tag(
                            'display_in_menu',
                            1,
                            ['class' => 'form-control', 'checked' => \K::$fw->obj['display_in_menu']]
                        ) ?></p>
                </div>
            </div>
        <?php
        endif ?>
        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?= \K::$fw->TEXT_ADMINISTRATOR_NOTE ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::textarea_tag('notes', \K::$fw->obj['notes'], ['class' => 'form-control']) ?>
            </div>
        </div>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#entities_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });
    });
</script>