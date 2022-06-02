<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'action_from',
    url_for(
        'ext/processes/clone_subitems',
        'action=save&actions_id=' . $app_actions_info['id'] . '&process_id=' . $app_process_info['id'] . (isset($_GET['parent_id']) ? '&parent_id=' . $_GET['parent_id'] : '') . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body ajax-modal-width-790">

        <?php

        echo input_hidden_tag('parent_id', (isset($_GET['parent_id']) ? $_GET['parent_id'] : 0));

        $choices_from_entity = [];
        $choices_to_entity = [];

        if (isset($_GET['parent_id'])) {
            $rule_info = db_find('app_ext_processes_clone_subitems', _get::int('parent_id'));
            $parent_entity_from = $rule_info['from_entity_id'];
            $parent_entity_to = $rule_info['to_entity_id'];
        } else {
            $parent_entity_from = processes::get_entity_id_from_action_type($app_actions_info['type']);;
            $parent_entity_to = $app_process_info['entities_id'];
        }

        //handle colone item
        if (!isset($_GET['parent_id']) and strstr($app_actions_info['type'], 'clone_item_entity_')) {
            $entities_qeury = db_query(
                "select id,name from app_entities where id='" . processes::get_entity_id_from_action_type(
                    $app_actions_info['type']
                ) . "'"
            );
            while ($entities = db_fetch_array($entities_qeury)) {
                $choices_from_entity[$entities['id']] = $entities['name'] . ' (#' . $entities['id'] . ')';
            }

            $settigns = new settings($app_actions_info['settings']);

            $entities_qeury = db_query(
                "select id,name from app_entities where id='" . (is_array($settigns->get('clone_to_entity')) ? current(
                    $settigns->get('clone_to_entity')
                ) : 0) . "'"
            );
            while ($entities = db_fetch_array($entities_qeury)) {
                $choices_to_entity[$entities['id']] = $entities['name'] . ' (#' . $entities['id'] . ')';
            }
        } else {
            $choices_from_entity[''] = '';
            $entities_qeury = db_query(
                "select id,name from app_entities where parent_id='" . $parent_entity_from . "'"
            );
            while ($entities = db_fetch_array($entities_qeury)) {
                $choices_from_entity[$entities['id']] = $entities['name'] . ' (#' . $entities['id'] . ')';
            }

            $choices_to_entity[''] = '';
            $entities_qeury = db_query("select id,name from app_entities where parent_id='" . $parent_entity_to . "'");
            while ($entities = db_fetch_array($entities_qeury)) {
                $choices_to_entity[$entities['id']] = $entities['name'] . ' (#' . $entities['id'] . ')';
            }
        }
        ?>

        <div class="form-group">
            <label class="col-md-3 control-label" for="fields_id"><?php
                echo TEXT_EXT_FROM_ENTITY ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'from_entity_id',
                    $choices_from_entity,
                    $obj['from_entity_id'],
                    ['class' => 'form-control  required', 'onChange' => 'get_fields_schema()']
                ) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="fields_id"><?php
                echo TEXT_EXT_TO_ENTITY ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'to_entity_id',
                    $choices_to_entity,
                    $obj['to_entity_id'],
                    ['class' => 'form-control  required', 'onChange' => 'get_fields_schema()']
                ) ?>
            </div>
        </div>

        <div id="fields_schema"></div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#action_from').validate({
            ignore: '',
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true
            }
        });

        get_fields_schema();

    });

    function get_fields_schema() {
        var from_entity_id = $('#from_entity_id').val();
        var to_entity_id = $('#to_entity_id').val();

        $('#fields_schema').html('<div class="ajax-loading"></div>');

        if (from_entity_id > 0 && to_entity_id > 0) {
            $('#fields_schema').load('<?php echo url_for(
                "ext/processes/clone_subitems",
                "process_id=" . $app_process_info['id'] . "&actions_id=" . $app_actions_info['id'] . "&action=get_fields_schema"
            )?>', {
                id: '<?php echo $obj["id"] ?>',
                from_entity_id: $('#from_entity_id').val(),
                to_entity_id: $('#to_entity_id').val()
            }, function (response, status, xhr) {
                if (status == "error") {
                    $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
                } else {
                    appHandleUniform();
                }
            });
        } else {
            $('#fields_schema').html('')
        }

    }


</script>