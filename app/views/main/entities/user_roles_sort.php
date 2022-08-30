<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_SORT_VALUES) ?>

<?= \Helpers\Html::form_tag(
    'choices_form',
    \Helpers\Urls::url_for(
        'main/entities/user_roles/sort',
        'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">
        <div class="dd" id="choices_sort">
            <?php
            if (count(\K::$fw->choices_query) > 0) {
                $html = '<ol class="dd-list">';

                //while ($v = db_fetch_array(\K::$fw->choices_query)) {
                foreach (\K::$fw->choices_query as $v) {
                    $v = $v->cast();

                    $html .= '<li class="dd-item" data-id="' . $v['id'] . '"><div class="dd-handle">' . $v['name'] . '</div></li>';
                }

                $html .= '</ol>';

                echo $html;
            }
            ?>
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