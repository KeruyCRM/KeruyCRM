<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_HEADING_DELETE) ?>

<?= \Helpers\Html::form_tag(
    'delete_selected_form',
    \Helpers\Urls::url_for(
        'main/items/delete_selected/delete_selected',
        'reports_id=' . \K::$fw->GET['reports_id'] . '&path=' . \K::$fw->GET['path']
    )
) ?>

<?= \Helpers\Html::input_hidden_tag('redirect_to', \K::$fw->app_redirect_to) ?>

<?php
if (count(\K::$fw->app_selected_items[\K::$fw->GET['reports_id']]) == 0) {
    echo '
    <div class="modal-body">    
      <div>' . \K::$fw->TEXT_PLEASE_SELECT_ITEMS . '</div>
    </div>    
  ' . \Helpers\App::ajax_modal_template_footer('hide-save-button');
} else {
    ?>
    <div class="modal-body">
        <div id="modal-body-content">
            <p><?= \K::$fw->TEXT_DELETE_SELECTED_CONFIRMATION ?></p>

            <?php
            if (\Models\Main\Entities::has_subentities(\K::$fw->current_entity_id)) {
                $show_delete_confirm = false;
                //$entities_query = db_query("select id from app_entities where parent_id='" . \K::$fw->current_entity_id . "'");

                $entities_query = \K::model()->db_fetch('app_entities', [
                    'parent_id = ?',
                    \K::$fw->current_entity_id
                ], [], 'id');

                //while ($entities = db_fetch_array($entities_query)) {
                foreach ($entities_query as $entities) {
                    $entities = $entities->cast();
                    //$items_query = db_query("select id from app_entity_" . $entities['id'] . " limit 1");

                    $items = \K::model()->db_fetch_one('app_entity_' . (int)$entities['id'], [], [], 'id');

                    if ($items) {
                        $show_delete_confirm = true;
                        break;
                    }
                }

                if ($show_delete_confirm) {
                    echo '<div style="margin-top: 15px;" class="alert alert-warning">' . sprintf(
                            \K::$fw->TEXT_WARNING_ITEM_HAS_SUB_ITEM,
                            \K::$fw->app_entities_cache[\K::$fw->current_entity_id]['name']
                        ) . '</div><div class="single-checkbox"><label>' . \Helpers\Html::input_checkbox_tag(
                            'delete_confirm',
                            1,
                            ['class' => 'required']
                        ) . ' ' . \K::$fw->TEXT_CONFIRM_DELETE . '</label></div>';
                }
            }
            ?>
        </div>
    </div>
    <?php
    $count_selected_text = sprintf(
        \K::$fw->TEXT_SELECTED_RECORDS,
        count(\K::$fw->app_selected_items[\K::$fw->GET['reports_id']])
    );
    echo \Helpers\App::ajax_modal_template_footer(\K::$fw->TEXT_BUTTON_DELETE, '', $count_selected_text)
    ?>

    <?php
} ?>
</form>

<script>
    $('#delete_selected_form').validate({
        submitHandler: function (form) {
            app_prepare_modal_action_loading(form)
            form.submit();
        },
        errorPlacement: function (error, element) {
            error.insertAfter(".single-checkbox");
        }
    });
</script>