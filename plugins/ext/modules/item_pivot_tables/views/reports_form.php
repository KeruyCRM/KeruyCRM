<?php
echo ajax_modal_template_header(TEXT_EXT_PIVOTREPORTS) ?>

<?php
echo form_tag(
    'reports_form',
    url_for('ext/item_pivot_tables/reports', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">


        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#listing_configuration" data-toggle="tab"><?php
                    echo TEXT_NAV_LISTING_CONFIG ?></a></li>
        </ul>


        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_info">

                <div class="form-group">
                    <label class="col-md-4 control-label" for="name"><?php
                        echo TEXT_NAME ?></label>
                    <div class="col-md-8">
                        <?php
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
                    </div>
                </div>

                <?php
                $choices = [];
                $choices['left_column'] = TEXT_LEFT_COLUMN;
                $choices['right_column'] = TEXT_RIGHT_COLUMN;
                ?>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="position"><?php
                        echo TEXT_POSITION ?> </label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'position',
                            $choices,
                            $obj['position'],
                            ['class' => 'form-control input-medium required']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="entities_id"><?php
                        echo TEXT_REPORT_ENTITY ?> </label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'entities_id',
                            ['' => ''] + entities::get_choices(),
                            $obj['entities_id'],
                            ['class' => 'form-control input-xlarge required']
                        ) ?>
                        <?php
                        echo tooltip_text(TEXT_EXT_ITEM_PIVOT_TABLES_ENTITY_1_TIP) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="related_entities_id"><?php
                        echo TEXT_EXT_SELECT_DATA_FROM ?> </label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'related_entities_id',
                            ['' => ''] + entities::get_choices(),
                            $obj['related_entities_id'],
                            [
                                'class' => 'form-control input-xlarge required',
                                'onChange' => 'load_related_entity_fields()'
                            ]
                        ) ?>
                        <?php
                        echo tooltip_text(TEXT_EXT_ITEM_PIVOT_TABLES_ENTITY_2_TIP) ?>
                    </div>
                </div>

                <div id="related_entity_fields"></div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="sort_order"><?php
                        echo TEXT_SORT_ORDER ?></label>
                    <div class="col-md-8">
                        <?php
                        echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-small']) ?>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-4 control-label" for="allowed_groups"><?php
                        echo tooltip_icon(TEXT_EXT_USERS_GROUPS_INFO) . TEXT_EXT_USERS_GROUPS ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'allowed_groups[]',
                            access_groups::get_choices(),
                            $obj['allowed_groups'],
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                        ) ?>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="listing_configuration">

                <div class="form-group">
                    <label class="col-md-4 control-label" for="rows_per_page"><?php
                        echo TEXT_ROWS_PER_PAGE ?></label>
                    <div class="col-md-8">
                        <?php
                        echo input_tag('rows_per_page', $obj['rows_per_page'], ['class' => 'form-control input-small']
                        ) ?>
                    </div>
                </div>

                <div id="fields_in_listing"></div>

            </div>

        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {

        $('#reports_form').validate({
            ignore: '',
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });

        load_related_entity_fields()
    });

    function load_related_entity_fields() {
        $('#related_entity_fields').html('<div class="ajax-loading"></div>');

        $('#related_entity_fields').load('<?php echo url_for(
            "ext/item_pivot_tables/reports",
            "action=related_entity_fields"
        )?>', {
            entities_id: $("#related_entities_id").val(),
            id: '<?php echo $obj["id"] ?>'
        }, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                load_fields_in_listing();

                $('#related_entities_fields').change(function () {
                    load_fields_in_listing();
                })
            }
        });

        function load_fields_in_listing() {
            $('#fields_in_listing').html('<div class="ajax-loading"></div>');

            $('#fields_in_listing').load('<?php echo url_for(
                "ext/item_pivot_tables/reports",
                "action=fields_in_listing"
            )?>', {
                related_entities_fields: $("#related_entities_fields").val(),
                id: '<?php echo $obj["id"] ?>'
            }, function (response, status, xhr) {
                if (status == "error") {
                    $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
                } else {
                    appHandleUniform();
                }
            });
        }
    }
</script>   