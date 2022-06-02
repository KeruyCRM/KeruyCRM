<?php
echo ajax_modal_template_header(TEXT_EXT_PIVOTREPORTS) ?>

<?php
echo form_tag(
    'reports_form',
    url_for('ext/pivotreports/reports', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">


        <ul class="nav nav-tabs">
            <li class="active"><a href="#general_info" data-toggle="tab"><?php
                    echo TEXT_GENERAL_INFO ?></a></li>
            <li><a href="#access" data-toggle="tab"><?php
                    echo TEXT_ACCESS ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="general_info">


                <div class="form-group">
                    <label class="col-md-4 control-label" for="type"><?php
                        echo TEXT_REPORT_ENTITY ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'entities_id',
                            entities::get_choices(),
                            $obj['entities_id'],
                            ['class' => 'form-control input-large required']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="name"><?php
                        echo TEXT_NAME ?></label>
                    <div class="col-md-8">
                        <?php
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="cfg_numer_format"><?php
                        echo tooltip_icon(TEXT_EXT_PIVOTREPORTS_NUMBER_FORMAT_INFO) . TEXT_NUMBER_FORMAT ?></label>
                    <div class="col-md-8">
                        <?php
                        echo input_tag(
                            'cfg_numer_format',
                            $obj['cfg_numer_format'],
                            ['class' => 'form-control input-small input-masked', 'data-mask' => '9/~/~']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="name"><?php
                        echo TEXT_SORT_ORDER ?></label>
                    <div class="col-md-8">
                        <?php
                        echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-small']) ?>
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" id="access">
                <p><?php
                    echo TEXT_EXT_USERS_GROUPS_INFO ?></p>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="allowed_groups"><?php
                        echo TEXT_EXT_USERS_GROUPS ?></label>
                    <div class="col-md-8">
                        <?php
                        echo select_tag(
                            'allowed_groups[]',
                            access_groups::get_choices(false),
                            $obj['allowed_groups'],
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="allow_edit"><?php
                        echo tooltip_icon(TEXT_EXT_ALLOW_EDIT_REPORT_TIP) . TEXT_EXT_ALLOW_EDIT_REPORT ?></label>
                    <div class="col-md-8">
                        <p class="form-control-static"><?php
                            echo input_checkbox_tag(
                                'allow_edit',
                                1,
                                ['class' => 'form-control input-small', 'checked' => $obj['allow_edit']]
                            ) ?></p>
                    </div>
                </div>

            </div>
        </div>


    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#reports_form').validate();

        $(".input-masked").each(function () {
            $.mask.definitions["~"] = "[,. ]";
            $(this).mask($(this).attr("data-mask"));
        })
    });
</script>   