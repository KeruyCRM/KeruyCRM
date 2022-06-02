<?php
echo ajax_modal_template_header(TEXT_RESET_SORTING) ?>

<?php
echo form_tag(
    'choices_form',
    url_for('global_lists/choices', 'action=sort_reset&lists_id=' . $_GET['lists_id']),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">
        <?php
        echo TEXT_VALUES_WILL_SORTED_BY_NAME ?>
    </div>
</div>

<?php
echo ajax_modal_template_footer(TEXT_BUTTON_CONTINUE) ?>

</form> 
