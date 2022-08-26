<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_SORT_VALUES) ?>

<?= \Helpers\Html::form_tag(
    'choices_form',
    \Helpers\Urls::url_for(
        'main/entities/listing_sections/sort',
        'entities_id=' . \K::$fw->GET['entities_id'] . '&listing_types_id=' . \K::$fw->GET['listing_types_id']
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">
        <div class="dd" id="choices_sort">
            <ol class="dd-list">
                <?php
                //while ($v = db_fetch_array($filters_query)) {
                foreach (\K::$fw->filters_query as $v) {
                    $v = $v->cast();

                    $title = '';

                    if (strlen($v['name'])) {
                        $title = $v['name'];
                    } elseif (strlen($v['fields'])) {
                        $choices = [];
                        /*$fields_query = db_query(
                            "select * from app_fields where id in (" . $v['fields'] . ") order by field(id," . $v['fields'] . ")"
                        );*/

                        $fields_query = \K::model()->db_fetch('app_fields', [
                            'id in (' . $v['fields'] . ')'
                        ], ['order' => 'field(id,' . $v['fields'] . ')'], 'type,name');

                        //while ($fields = db_fetch_array($fields_query)) {
                        foreach ($fields_query as $fields) {
                            $fields = $fields->cast();

                            $choices[] = \Models\Main\Fields_types::get_option(
                                $fields['type'],
                                'name',
                                $fields['name']
                            );
                        }

                        $title = implode(', ', $choices);
                    }

                    echo '<li class="dd-item" data-id="' . $v['id'] . '"><div class="dd-handle">' . $title . '</div></li>';
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
            group: 0,
            maxDepth: 1,
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