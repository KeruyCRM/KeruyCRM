<?php

$parent_id = isset($_GET['parent_id']) ? _GET('parent_id') : 0;

echo ajax_modal_template_header(
    TEXT_SORT_VALUES . ($parent_id > 0 ? ' (' . entities::get_name_by_id($parent_id) . ')' : '')
) ?>

<?php
echo form_tag(
    'choices_form',
    url_for('forms_tabs/groups', 'action=sort_redirect&entities_id=' . _GET('entities_id')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">

        <?php

        $entities_id = _GET('entities_id');

        $html = '<ul class="sortable" id="group_0">';
        $rows_query = db_query(
            "select id, name from app_forms_tabs where is_folder=0 and parent_id=0 and entities_id={$entities_id} order by sort_order, name"
        );
        while ($rows = db_fetch_array($rows_query)) {
            $html .= '<li id="form_tab_' . $rows['id'] . '">' . $rows['name'] . '</li>';
        }
        $html .= '</ul>';


        $html .= '<ol class="sortable sortable_groups" id="groups_list">';
        $groups_query = db_query(
            "select * from app_forms_tabs where is_folder=1 and entities_id={$entities_id} order by sort_order, name"
        );
        while ($groups = db_fetch_array($groups_query)) {
            $html .= '<li id="group_' . $groups['id'] . '"  style="cursor:default; margin-bottom: 15px;"> <b class="sortable_group_heading" style="cursor:move">' . $groups['name'] . '</b>';

            $html .= '<ul class="sortable" id="group_' . $groups['id'] . '">';
            $rows_query = db_query(
                "select id, name from app_forms_tabs where parent_id={$groups['id']} order by sort_order, name"
            );
            while ($rows = db_fetch_array($rows_query)) {
                $html .= '<li id="form_tab_' . $rows['id'] . '">' . $rows['name'] . '</li>';
            }
            $html .= '</ul>';

            $html .= '</li>';
        }
        $html .= '</ol>';

        echo $html;


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
                $.ajax({
                    type: "POST",
                    url: '<?php echo url_for("forms_tabs/groups", "action=sort&entities_id=" . _GET('entities_id')) ?>',
                    data: data
                });
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
                    url: '<?php echo url_for(
                        "forms_tabs/groups",
                        "action=sort_groups&entities_id=" . _GET('entities_id')
                    ) ?>',
                    data: data
                });
            }
        });

    });
</script>
