<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_RESET_SORTING) ?>

<?= \Helpers\Html::form_tag(
    'choices_form',
    \Helpers\Urls::url_for(
        'main/entities/fields_choices/sort_reset',
        'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">
        <?= \K::$fw->TEXT_VALUES_WILL_SORTED_BY_NAME ?>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer(\K::$fw->TEXT_BUTTON_CONTINUE) ?>

</form>