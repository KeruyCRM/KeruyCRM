<?php
echo ajax_modal_template_header((isset($_GET['id']) ? TEXT_HEADING_EDIT_FORM_TAB : TEXT_HEADING_NEW_FORM_TAB)) ?>

<?php
echo form_tag(
    'forms_form',
    url_for('entities/comments_form', 'action=save_tab' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">

        <?php
        echo input_hidden_tag('entities_id', $_GET['entities_id']) ?>

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_NAME ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#forms_form').validate();
    });

</script>   