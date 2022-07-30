<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_SORT) ?>

<?= \Helpers\Html::form_tag('menu', \Helpers\Urls::url_for('main/entities/menu')) ?>

<div class="modal-body">
    <div class="cfg_forms_fields">
        <ul id="sort_items" class="sortable">
            <?php
            //while ($v = db_fetch_array($groups_query)) {
            foreach (\K::$fw->groups_query as $v) {
                $v = $v->cast();

                echo '
    <li id="item_' . $v['id'] . '"><div>' . $v['name'] . '</div></li>
  ';
            }
            ?>
        </ul>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $("ul.sortable").sortable({
            connectWith: "ul",
            update: function (event, ui) {
                data = '';
                $("ul.sortable").each(function () {
                    data = data + '&' + $(this).attr('id') + '=' + $(this).sortable("toArray")
                });
                data = data.slice(1)
                $.ajax({type: "POST", url: '<?= \Helpers\Urls::url_for('main/entities/menu/sort') ?>', data: data});
            }
        });
    });
</script>