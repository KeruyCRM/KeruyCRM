<?php
echo ajax_modal_template_header(TEXT_SORT_VALUES) ?>

<?php
echo form_tag(
    'choices_form',
    url_for('forms_fields_rules/rules', 'action=sort&entities_id=' . $_GET['entities_id']),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body">

        <div class="dd" id="choices_sort">
            <ol class="dd-list">
                <?php
                $form_fields_query = db_query(
                    "select r.*, f.name, f.type, f.id as fields_id, f.configuration from app_forms_fields_rules r, app_fields f where r.fields_id=f.id and r.entities_id='" . _get::int(
                        'entities_id'
                    ) . "' order by r.sort_order, f.name"
                );
                while ($v = db_fetch_array($form_fields_query)) {
                    echo '
        <li class="dd-item" data-id="' . $v['id'] . '">
            <div class="dd-handle" style="height: auto;">' . $v['name'] . '<br><small>' . forms_fields_rules::get_chocies_values_by_field_type(
                            $v,
                            ', '
                        ) . '</small></div>
        </li>';
                }
                ?>
            </ol>
        </div>

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
