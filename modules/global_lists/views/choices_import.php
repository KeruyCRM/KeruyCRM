<?php
echo ajax_modal_template_header(TEXT_BUTTON_IMPORT) ?>

<?php
echo form_tag(
    'choices_import',
    url_for('global_lists/choices', 'action=import&lists_id=' . $_GET['lists_id']),
    ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']
) ?>
<div class="modal-body">
    <div class="form-body">

        <div class="form-group">
            <label class="col-md-4 control-label" for="filename"><?php
                echo TEXT_FILENAME ?></label>
            <div class="col-md-8">
                <?php
                echo input_file_tag('filename', ['class' => 'form-control required']) ?>
                <span class="help-block">*.xls, *.xlsx</span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="import_column"><?php
                echo tooltip_icon(TEXT_COLUMNS_IMPORT_INFO) . TEXT_COLUMNS_IMPORT ?></label>
            <div class="col-md-8">
                <?php
                echo input_tag('import_columns', '1', ['class' => 'form-control input-xsmall required']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="import_first_row"><?php
                echo TEXT_IMPORT_FIRST_ROW ?></label>
            <div class="col-md-8">
                <p class="form-control-static"><?php
                    echo input_checkbox_tag('import_first_row', 1) ?></p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="sort_like_file"><?php
                echo TEXT_SORT_LIKE_FILE ?></label>
            <div class="col-md-8">
                <p class="form-control-static"><?php
                    echo input_checkbox_tag('sort_like_file', 1) ?></p>
            </div>
        </div>

    </div>
</div>
<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {

        $('#choices_import').validate({
            rules: {
                filename: {
                    required: true,
                    extension: "xls|xlsx"
                }

            }
        });

    });

</script> 