<?php

$modules = new modules($obj['type']);

$module = new $obj['module'];

echo ajax_modal_template_header($module->title)
?>

<?php
echo form_tag(
    'module_form',
    url_for('ext/modules/modules', 'action=save&id=' . _get::int('id') . '&type=' . $_GET['type']),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body ajax-modal-width-790">

        <div class="form-group">
            <label class="col-md-4 control-label" for="is_active"><?php
                echo TEXT_IS_ACTIVE ?></label>
            <div class="col-md-8">
                <p class="form-control-static"><?php
                    echo input_checkbox_tag('is_active', 1, ['checked' => $obj['is_active']]) ?></p>
            </div>
        </div>

        <?php
        echo $modules->render_configuration($module, _get::int('id')) ?>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#module_form').validate({ignore: ''});
    });
</script>   
    
 
