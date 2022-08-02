<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_SORT_VALUES) ?>

<?= \Helpers\Html::form_tag(
    'choices_form',
    \Helpers\Urls::url_for('main/entities/entities_groups/sort'),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">
        <div class="dd" id="choices_sort">
            <ol class="dd-list">
                <?php
                foreach (\Models\Main\Entities_groups::get_choices() as $id => $name) {
                    if ($id > 0) {
                        echo '<li class="dd-item" data-id="' . $id . '"><div class="dd-handle">' . $name . '</div></i>';
                    }
                }
                ?>
            </ol>
        </div>

    </div>
</div>
<?= \Helpers\Html::input_hidden_tag('choices_sorted') ?>
<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#choices_sort').nestable({
            group: 1,
            maxDepth: 1
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