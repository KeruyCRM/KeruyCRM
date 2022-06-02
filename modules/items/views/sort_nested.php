<?php
echo ajax_modal_template_header(TEXT_SORT) ?>


<?php
echo form_tag(
    'choices_form',
    url_for('items/sort_nested', 'path=' . $app_path . '&action=sort&redirect_to=' . $app_redirect_to)
) ?>
<div class="modal-body ajax-modal-width-790">
    <p class="form-section" style="margin:0 0 15px 0"><?php
        echo items::get_heading_field($current_entity_id, $current_item_id) ?></p>

    <div class="dd" id="choices_sort">
        <?php
        echo tree_table::get_html_tree($current_entity_id, $current_item_id) ?>
    </div>
</div>

<?php
echo input_hidden_tag('choices_sorted') ?>
<?php
echo ajax_modal_template_footer() ?>
</form>

<script>
    $(function () {
        $('#choices_sort').nestable({
            group: 1
        }).on('change', function (e) {
            output = $(this).nestable('serialize');

            if (window.JSON) {
                output = window.JSON.stringify(output);
                $('#choices_sorted').val(output);
            } else {
                alert('JSON browser support required!');
            }
        })
    })

</script>
