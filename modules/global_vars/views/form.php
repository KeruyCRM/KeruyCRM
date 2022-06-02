<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for('global_vars/vars', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<?php
echo input_hidden_tag('is_folder', $obj['is_folder']) ?>
<div class="modal-body">
    <div class="form-body">

        <?php
        if (db_count('app_global_vars', '1', 'is_folder')): ?>
            <div class="form-group">
                <label class="col-md-3 control-label" for="name"><?php
                    echo TEXT_PARENT ?></label>
                <div class="col-md-9">
                    <?php
                    echo select_tag(
                        'parent_id',
                        global_vars::get_folder_choices(),
                        $obj['parent_id'],
                        ['class' => 'form-control input-large']
                    ) ?>
                </div>
            </div>
        <?php
        endif; ?>


        <?php
        if ($obj['is_folder']) { ?>

            <div class="form-group">
                <label class="col-md-3 control-label" for="name"><?php
                    echo TEXT_NAME ?></label>
                <div class="col-md-9">
                    <?php
                    echo input_tag('folder_name', $obj['name'], ['class' => 'form-control input-large required']) ?>
                </div>
            </div>

        <?php
        }else{ ?>

            <div class="form-group">
                <label class="col-md-3 control-label" for="name"><?php
                    echo TEXT_NAME ?></label>
                <div class="col-md-9">
                    <div class="input-group input-xlarge">
                        <span class="input-group-addon">VAR_</span>
                        <?php
                        echo input_tag(
                            'name',
                            $obj['name'],
                            ['class' => 'form-control input-xlarge required', 'data_id' => (int)$obj['id']]
                        ) ?>
                    </div>

                    <label id="name-error" class="error" for="name"></label>
                </div>
            </div>
            <script>
                jQuery(function ($) {
                    $("#name").inputmask({
                        mask: "A{1,60}",
                        greedy: false,
                        clearIncomplete: true,
                        definitions: {
                            'A': {
                                validator: "[0-9A-Za-z_]",
                                casing: "upper"
                            }
                        }
                    });
                });
            </script>

            <div class="form-group">
                <label class="col-md-3 control-label" for="name"><?php
                    echo TEXT_VALUE ?></label>
                <div class="col-md-9">
                    <?php
                    echo input_tag('value', $obj['value'], ['class' => 'form-control input-xlarge required']) ?>
                </div>
            </div>

        <?php
        } ?>

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_ADMINISTRATOR_NOTE ?></label>
            <div class="col-md-9">
                <?php
                echo textarea_tag('notes', $obj['notes'], ['class' => 'form-control ']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="sort_order"><?php
                echo TEXT_SORT_ORDER ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-xsmall']) ?>
            </div>
        </div>

    </div>
</div>
<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#configuration_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            },
            rules: {
                name: {
                    required: true,
                    remote: url_for('global_vars/vars', 'action=check_name') + '&id=' + $('#name').attr('data_id')
                }
            }
        });
    });
</script>         