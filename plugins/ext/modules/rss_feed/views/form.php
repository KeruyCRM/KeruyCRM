<?php
echo ajax_modal_template_header(TEXT_EXT_IPAGE) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for('ext/rss_feed/feeds', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<?php
echo input_hidden_tag('is_menu', '0') ?>
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
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_NAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('name', $obj['name'], ['class' => 'form-control input-xlarge required']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="type"><?php
                        echo TEXT_TYPE ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'type',
                            rss_feed::get_type_choices(),
                            $obj['type'],
                            ['class' => 'form-control input-large required']
                        ) ?>
                    </div>
                </div>

                <div id="feed_settgins"></div>

                <div id="entity_settgins"></div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="sort_order"><?php
                        echo TEXT_SORT_ORDER ?></label>
                    <div class="col-md-9">
                        <?php
                        echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-xsmall']) ?>
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" id="access">

                <p><?php
                    echo TEXT_EXT_IPAGES_USERS_GROUPS_INFO ?></p>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="allowed_groups"><?php
                        echo TEXT_EXT_USERS_GROUPS ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'users_groups[]',
                            access_groups::get_choices(),
                            $obj['users_groups'],
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => true]
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="allowed_groups"><?php
                        echo TEXT_ASSIGNED_TO ?></label>
                    <div class="col-md-9">
                        <?php
                        echo select_tag(
                            'assigned_to[]',
                            users::get_choices(),
                            $obj['assigned_to'],
                            ['class' => 'form-control input-xlarge chosen-select', 'multiple' => true]
                        ) ?>
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
        $('#configuration_form').validate({
            ignore: '',
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });

        load_feed_settings();

        $('#type').change(function () {
            load_feed_settings();
        })

    });

    function load_feed_settings() {
        $('#feed_settgins').load('<?php echo url_for(
            'ext/rss_feed/feeds',
            'action=settings'
        ) ?>', {type: $('#type').val(), id:<?php echo $_GET['id'] ?? 0 ?>}, function () {
            load_entity_settings()
        })
    }

    function load_entity_settings() {
        $('#entity_settgins').load('<?php echo url_for(
            'ext/rss_feed/feeds',
            'action=entity_settings'
        ) ?>', {
            type: $('#type').val(),
            entities_id: $('#entities_id').val(),
            id:<?php echo $_GET['id'] ?? 0 ?>}, function () {
            appHandleUniform();
        })
    }
</script>   