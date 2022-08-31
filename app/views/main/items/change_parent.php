<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_CHANGE_PARENT_ITEM) ?>

<?= \Helpers\Html::form_tag(
    'modal_form',
    \Helpers\Urls::url_for('main/items/change_parent/change_parent', 'path=' . \K::$fw->app_path)
) ?>
<div class="modal-body ajax-modal-width-790">
    <div class="dd" id="choices_sort">
        <?= \Helpers\Html::select_entities_tag(
            'parent_id',
            [],
            '',
            [
                'entities_id' => \K::$fw->current_entity_id,
                'is_tree_view' => true,
                'parent_item_id' => \K::$fw->parent_entity_item_id
            ]
        ) ?>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#modal_form').validate({ignore: ''})
    })
</script>