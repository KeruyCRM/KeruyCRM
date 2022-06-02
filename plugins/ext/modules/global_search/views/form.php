<?php
echo ajax_modal_template_header(TEXT_ENTITY) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for('ext/global_search/entities', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body ajax-modal-width-790">
    <div class="form-body">

        <div class="form-group">
            <label class="col-md-4 control-label" for="entities_id"><?php
                echo TEXT_ENTITY ?></label>
            <div class="col-md-8">
                <?php
                echo select_tag(
                    'entities_id',
                    entities::get_choices(),
                    $obj['entities_id'],
                    ['class' => 'form-control input-xlarge required']
                ) ?>
            </div>
        </div>

        <div id="entities_fields"></div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="sort_order"><?php
                echo TEXT_SORT_ORDER ?></label>
            <div class="col-md-8">
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
            }
        });
        ;

        $('#entities_id').change(function () {
            ext_get_entities_fields();
        })

        ext_get_entities_fields();

    });

    function ext_get_entities_fields() {
        entities_id = $('#entities_id').val()

        $('#entities_fields').html('<div class="ajax-loading"></div>');

        $('#entities_fields').load('<?php echo url_for(
            "ext/global_search/entities",
            "action=get_entities_fields"
        )?>', {entities_id: entities_id, id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();
            }
        });
    }


</script>  