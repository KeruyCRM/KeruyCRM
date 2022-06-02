<?php
echo ajax_modal_template_header(TEXT_HEADING_ENTITY_IFNO) ?>

<?php
echo form_tag(
    'entities_form',
    url_for('entities/', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">

        <?php
        if (isset($_GET['parent_id'])) echo input_hidden_tag('parent_id', $_GET['parent_id']) ?>

        <?php
        if (!isset($_GET['parent_id']) and (int)$obj['parent_id'] == 0) {
            $choices = entities_groups::get_choices();

            if (count($choices)) {
                echo '
            <div class="form-group">
                <label class="col-md-3 control-label" for="name">' . TEXT_GROUP . '</label>
                <div class="col-md-9">	
                      ' . select_tag('group_id', $choices, $obj['group_id'], ['class' => 'form-control input-large']) . '
                </div>			
          </div>  
            ';
            }
        }
        ?>

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_NAME ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="sort_order"><?php
                echo TEXT_SORT_ORDER ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-small required number']
                ) ?>
            </div>
        </div>

        <?php
        if (isset($_GET['parent_id']) or $obj['parent_id'] > 0): ?>
            <div class="form-group">
                <label class="col-md-3 control-label" for="display_in_menu"><?php
                    echo TEXT_DISPLAY_IN_MENU ?></label>
                <div class="col-md-9">
                    <p class="form-control-static"><?php
                        echo input_checkbox_tag(
                            'display_in_menu',
                            1,
                            ['class' => 'form-control', 'checked' => $obj['display_in_menu']]
                        ) ?></p>
                </div>
            </div>
        <?php
        endif ?>


        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_ADMINISTRATOR_NOTE ?></label>
            <div class="col-md-9">
                <?php
                echo textarea_tag('notes', $obj['notes'], ['class' => 'form-control']) ?>
            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#entities_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });
    });

</script>   
    
 
