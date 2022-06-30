<?php
echo ajax_modal_template_header(TEXT_EXT_HEADING_TEMPLATE_INFO) ?>

<?php
echo form_tag(
    'modal_form',
    url_for('ext/templates/import_templates', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body">


        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#configuration" data-toggle="tab"><?php
                    echo TEXT_SETTINGS ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_info">

                <div class="form-group">
                    <label class="col-md-3 control-label" for="is_active"><?php
                        echo TEXT_IS_ACTIVE ?></label>
                    <div class="col-md-9">
                        <p class="form-control-static"><?php
                            echo input_checkbox_tag(
                                'is_active',
                                $obj['is_active'],
                                ['checked' => ($obj['is_active'] == 1 ? 'checked' : '')]
                            ) ?></p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="entities_id"><?php
                        echo TEXT_ENTITY ?></label>
                    <div class="col-md-9"><?php
                        echo select_tag(
                            'entities_id',
                            entities::get_choices(),
                            $obj['entities_id'],
                            ['class' => 'form-control input-large required']
                        ) ?>
                    </div>
                </div>

                <div id="sub_entities_list"></div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_NAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="users_groups"><?php
                        echo TEXT_USERS_GROUPS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'users_groups[]',
                            access_groups::get_choices(),
                            $obj['users_groups'],
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                        ) ?>
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

            <div class="tab-pane fade" id="configuration">

                <div id="fields_configuration"></div>

            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {

        $('#modal_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });


        load_entities_list();

        $('#entities_id').change(function () {
            load_entities_list();
        })

    });


    function load_entities_list() {
        $('#sub_entities_list').html('<div class="ajax-loading"></div>');

        $('#sub_entities_list').load('<?php echo url_for(
            "ext/templates/import_templates",
            "action=get_subentities"
        )?>', {id: '<?php echo $obj["id"] ?>', entities_id: $('#entities_id').val()}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();

                load_fields_configuration();

                jQuery(window).resize();
            }
        });
    }

    function load_fields_configuration() {
        $('#fields_configuration').html('<div class="ajax-loading"></div>');

        $('#fields_configuration').load('<?php echo url_for(
            "ext/templates/import_templates",
            "action=fields_configuration"
        )?>', {
            id: '<?php echo $obj["id"] ?>',
            entities_id: $('#entities_id').val(),
            multilevel_import: $('#multilevel_import').val()
        }, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();

                jQuery(window).resize();
            }
        });
    }

</script>  