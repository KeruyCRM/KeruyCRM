<?php
echo ajax_modal_template_header(TEXT_CHANGE_PARENT_ITEM) ?>


<?php
echo form_tag('modal_form', url_for('items/change_parent', 'path=' . $app_path . '&action=change_parent')) ?>
<div class="modal-body ajax-modal-width-790">

    <div class="dd" id="choices_sort">
        <?php
        echo select_entities_tag(
            'parent_id',
            [],
            '',
            ['entities_id' => $current_entity_id, 'is_tree_view' => true, 'parent_item_id' => $parent_entity_item_id]
        ) ?>
    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>
</form>

<script>
    $(function () {
        $('#modal_form').validate({ignore: ''})
    })
</script>
