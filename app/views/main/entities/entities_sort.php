<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(
    \K::$fw->TEXT_SORT_VALUES . (\K::$fw->parent_id > 0 ? ' (' . \Models\Main\Entities::get_name_by_id(
            \K::$fw->parent_id
        ) . ')' : '')
) ?>

<?= \Helpers\Html::form_tag(
    'choices_form',
    \Helpers\Urls::url_for('main/entities/entities'),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">
        <?php
        $html = '<ul class="sortable" id="entities_list_0">';
        foreach (\K::$fw->entities_query as $entities) {
            $entities = $entities->cast();

            $html .= '<li id="entity_' . $entities['id'] . '">' . $entities['name'] . '</li>';
        }
        $html .= '</ul>';
        if (\K::$fw->parent_id == 0) {
            $html .= '<ol class="sortable sortable_groups" id="groups_list">';

            //while ($groups = db_fetch_array($groups_query)) {
            foreach (\K::$fw->groups_query as $groups) {
                $groups = $groups->cast();

                $html .= '<li id="group_' . $groups['id'] . '"  style="cursor:default; margin-bottom: 15px;"> <b class="sortable_group_heading" style="cursor:move">' . $groups['name'] . '</b>';

                $html .= '<ul class="sortable" id="entities_list_' . $groups['id'] . '">';
                /*$entities_query = db_query(
                    "select id, name from app_entities where parent_id=0 and group_id={$groups['id']} order by sort_order, name"
                );*/
                $entities_query = \K::model()->db_fetch('app_entities', [
                    'parent_id = 0 and group_id = ?',
                    $groups['id']
                ], ['order' => 'sort_order, name'], 'id,name');

                //while ($entities = db_fetch_array($entities_query)) {
                foreach ($entities_query as $entities) {
                    $entities = $entities->cast();

                    $html .= '<li id="entity_' . $entities['id'] . '">' . $entities['name'] . '</li>';
                }
                $html .= '</ul>';

                $html .= '</li>';
            }
            $html .= '</ol>';
        }
        echo $html;

        ?>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        //sortable fields
        $("ul.sortable").sortable({
            connectWith: "ul",
            update: function (event, ui) {
                data = '';
                $("ul.sortable").each(function () {
                    data = data + '&' + $(this).attr('id') + '=' + $(this).sortable("toArray")
                });

                data = data.slice(1)
                $.ajax({type: "POST", url: '<?= \Helpers\Urls::url_for('main/entities/entities/sort') ?>', data: data});
            }
        });

        //sortable tabs
        $("ol.sortable_groups").sortable({
            handle: '.sortable_group_heading',
            update: function (event, ui) {

                data = '';
                $("ol.sortable_groups").each(function () {
                    data = data + '&' + $(this).attr('id') + '=' + $(this).sortable("toArray")
                });
                data = data.slice(1)
                $.ajax({
                    type: "POST",
                    url: '<?= \Helpers\Urls::url_for('main/entities/entities/sort_groups') ?>',
                    data: data
                });
            }
        });
    });
</script>