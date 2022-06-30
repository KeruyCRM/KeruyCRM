<?php
echo ajax_modal_template_header(TEXT_EXT_CALENDAR_REPORTS) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for(
        'ext/pivot_calendars/entities',
        'action=save&calendars_id=' . _get::int('calendars_id') . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body ajax-modal-width-790">


        <div class="form-group">
            <label class="col-md-3 control-label" for="type"><?php
                echo TEXT_REPORT_ENTITY ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'entities_id',
                    entities::get_choices(),
                    $obj['entities_id'],
                    [
                        'class' => 'form-control input-large required',
                        'onChange' => 'ext_get_entities_fields(this.value)'
                    ]
                ) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="bg_color"><?php
                echo TEXT_BACKGROUND_COLOR ?></label>
            <div class="col-md-9">
                <div class="input-group input-small color colorpicker-default" data-color="<?php
                echo(strlen($obj['bg_color']) > 0 ? $obj['bg_color'] : '#ff0000') ?>">
                    <?php
                    echo input_tag('bg_color', $obj['bg_color'], ['class' => 'form-control input-small']) ?>
                    <span class="input-group-btn">
	  				<button class="btn btn-default" type="button">&nbsp;</button>
	  			</span>
                </div>
            </div>
        </div>

        <div id="reports_entities_fields"></div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>

    $(function () {
        $('#configuration_form').validate();

        ext_get_entities_fields($('#entities_id').val());

    });

    function ext_get_entities_fields(entities_id) {
        $('#reports_entities_fields').html('<div class="ajax-loading"></div>');

        $('#reports_entities_fields').load('<?php echo url_for(
            "ext/pivot_calendars/entities",
            "action=get_entities_fields&calendars_id=" . _get::int('calendars_id')
        )?>', {entities_id: entities_id, id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
            }
        });


    }


</script>   