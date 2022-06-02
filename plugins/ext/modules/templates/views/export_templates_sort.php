<?php
echo ajax_modal_template_header(TEXT_SORT_ORDER) ?>

<?php
echo form_tag('templates_filter', url_for('ext/templates/export_templates')) ?>

<div class="modal-body">

    <ul id="templates" class="sortable">
        <?php
        $templates_query = db_query(
            "select ep.*, e.name as entities_name from app_ext_export_templates ep, app_entities e where e.id=ep.entities_id  and ep.entities_id='" . db_input(
                $export_templates_filter
            ) . "' order by e.id, ep.sort_order, ep.name"
        );
        while ($templates = db_fetch_array($templates_query)) {
            echo '<li id="template_' . $templates['id'] . '">' . $templates['name'] . '</li>';
        }
        ?>
    </ul>
</div>

<?php
echo ajax_modal_template_footer() ?>

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
                $.ajax({
                    type: "POST",
                    url: '<?php echo url_for("ext/templates/export_templates", "action=sort_templates")?>',
                    data: data
                });
            }
        });
    });
</script>