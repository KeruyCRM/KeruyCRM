<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php

$parent_id = isset($_GET['parent_id']) ? _GET('parent_id') : 0;

echo ajax_modal_template_header(
    TEXT_SORT_VALUES . ($parent_id > 0 ? ' (' . entities::get_name_by_id($parent_id) . ')' : '')
) ?>

<?php
echo form_tag('choices_form', url_for('entities/entities'), ['class' => 'form-horizontal']) ?>
<div class="modal-body">
    <div class="form-body">

        <?php

        if ($parent_id > 0) {
            $html = '<ul class="sortable" id="entities_list_0">';
            $entities_query = db_query(
                "select id, name from app_entities where parent_id={$parent_id} order by sort_order, name"
            );
            while ($entities = db_fetch_array($entities_query)) {
                $html .= '<li id="entity_' . $entities['id'] . '">' . $entities['name'] . '</li>';
            }

            $html .= '</ul>';

            echo $html;
        } else {
            $html = '<ul class="sortable" id="entities_list_0">';
            $entities_query = db_query(
                "select id, name from app_entities where parent_id=0 and group_id=0 order by sort_order, name"
            );
            while ($entities = db_fetch_array($entities_query)) {
                $html .= '<li id="entity_' . $entities['id'] . '">' . $entities['name'] . '</li>';
            }
            $html .= '</ul>';


            $html .= '<ol class="sortable sortable_groups" id="groups_list">';
            $groups_query = db_query(
                "select * from app_entities_groups " . ($entities_filter > 0 ? " where id={$entities_filter}" : "") . " order by sort_order, name"
            );
            while ($groups = db_fetch_array($groups_query)) {
                $html .= '<li id="group_' . $groups['id'] . '"  style="cursor:default; margin-bottom: 15px;"> <b class="sortable_group_heading" style="cursor:move">' . $groups['name'] . '</b>';

                $html .= '<ul class="sortable" id="entities_list_' . $groups['id'] . '">';
                $entities_query = db_query(
                    "select id, name from app_entities where parent_id=0 and group_id={$groups['id']} order by sort_order, name"
                );
                while ($entities = db_fetch_array($entities_query)) {
                    $html .= '<li id="entity_' . $entities['id'] . '">' . $entities['name'] . '</li>';
                }
                $html .= '</ul>';

                $html .= '</li>';
            }
            $html .= '</ol>';

            echo $html;
        }

        ?>


    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

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
                $.ajax({type: "POST", url: '<?php echo url_for("entities/entities", "action=sort") ?>', data: data});
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
                    url: '<?php echo url_for("entities/entities", "action=sort_groups") ?>',
                    data: data
                });
            }
        });

    });
</script>
