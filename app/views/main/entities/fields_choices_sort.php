<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_SORT_VALUES) ?>

<?= \Helpers\Html::form_tag(
    'choices_form',
    \Helpers\Urls::url_for(
        'main/entities/fields_choices/sort',
        'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
    ),
    ['class' => 'form-horizontal']
) ?>
<?php
$parent_id = \K::$fw->GET['parent_id'] ?? 0;
echo \Helpers\Html::input_hidden_tag('parent_id', $parent_id);
?>
<div class="modal-body">
    <div class="form-body">
        <div class="dd" id="choices_sort">
            <?= \Models\Main\Fields_choices::get_html_tree(\K::$fw->GET['fields_id'], $parent_id) ?>
        </div>
    </div>
</div>
<?= \Helpers\Html::input_hidden_tag('choices_sorted') ?>

<?= \Helpers\App::ajax_modal_template_footer() ?>

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