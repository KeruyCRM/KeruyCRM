<?php
echo ajax_modal_template_header(TEXT_IMPORT_FIELDS) ?>

<?php
echo form_tag(
    'import_form',
    url_for('entities/fields', 'action=import&entities_id=' . $_GET['entities_id']),
    ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']
) ?>
<div class="modal-body">
    <div id="modal-body-content">

        <p><?php
            echo TEXT_IMPORT_FIELDS_INFO ?></p>
        <div class="form-group">
            <label class="col-md-4 control-label" for="type"><?php
                echo TEXT_FILE ?></label>
            <div class="col-md-8">
                <?php
                echo input_file_tag(
                    'filename',
                    [
                        'class' => 'form-control required',
                        'accept' => fieldtype_attachments::get_accept_types_by_extensions('xml')
                    ]
                ) ?>
                <span class="help-block">*.xml</span>
            </div>
        </div>


    </div>
</div>
<?php
echo ajax_modal_template_footer(TEXT_CONTINUE) ?>

</form>

<script>
    $(function () {
        $('#import_form').validate();

    });
</script> 