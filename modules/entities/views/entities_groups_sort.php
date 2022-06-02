<?php
echo ajax_modal_template_header(TEXT_SORT_VALUES) ?>

<?php
echo form_tag('choices_form', url_for('entities/entities_groups', 'action=sort'), ['class' => 'form-horizontal']) ?>
<div class="modal-body">
    <div class="form-body">

        <div class="dd" id="choices_sort">
            <ol class="dd-list">
                <?php
                foreach (entities_groups::get_choices() as $id => $name) {
                    if ($id > 0) {
                        echo '<li class="dd-item" data-id="' . $id . '"><div class="dd-handle">' . $name . '</div></i>';
                    }
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
