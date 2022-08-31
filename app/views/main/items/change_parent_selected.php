<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_CHANGE_PARENT_ITEM) ?>

<?php
if (!isset(\K::$fw->app_selected_items[\K::$fw->GET['reports_id']])) {
    \K::$fw->app_selected_items[\K::$fw->GET['reports_id']] = [];
}

if (count(\K::$fw->app_selected_items[\K::$fw->GET['reports_id']]) == 0) {
    echo '
    <div class="modal-body">    
      <div>' . \K::$fw->TEXT_PLEASE_SELECT_ITEMS . '</div>
    </div>    
  ' . \Helpers\App::ajax_modal_template_footer('hide-save-button');
} else {
    ?>

    <?= \Helpers\Html::form_tag(
        'modal_form',
        \Helpers\Urls::url_for('main/items/change_parent_selected/change_parent', 'path=' . \K::$fw->app_path)
    ) . \Helpers\Html::input_hidden_tag('reports_id', \K::$fw->GET['reports_id']) ?>
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

    <?= \Helpers\App::ajax_modal_template_footer('', '', \K::$fw->count_selected_text); ?>

    </form>

    <script>
        $(function () {
            $('#modal_form').validate({ignore: ''})
        })
    </script>
    <?php
}