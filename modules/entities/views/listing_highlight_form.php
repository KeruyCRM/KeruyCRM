<?php

$obj = [];

$obj = (isset($_GET['id']) ? db_find('app_listing_highlight_rules', _GET('id')) : db_show_columns(
    'app_listing_highlight_rules'
));

if (!isset($_GET['id'])) {
    $obj['is_active'] = 1;

    $sort_order_query = db_query(
        "select (max(sort_order)+1) as sort_order from app_listing_highlight_rules where entities_id=" . _GET(
            'entities_id'
        )
    );
    if ($sort_order = db_fetch_array($sort_order_query)) {
        $obj['sort_order'] = $sort_order['sort_order'];
    }
}

?>

<?php
echo ajax_modal_template_header(TEXT_HEADING_REPORTS_FILTER_INFO) ?>

<?php
echo form_tag(
    'modal_form',
    url_for(
        'entities/listing_highlight',
        'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '') . '&entities_id=' . $_GET['entities_id']
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">


        <div class="form-group">
            <label class="col-md-3 control-label" for="is_active"><?php
                echo TEXT_IS_ACTIVE ?></label>
            <div class="col-md-9">
                <div class="checkbox-list"><label class="checkbox-inline"><?php
                        echo input_checkbox_tag('is_active', '1', ['checked' => $obj['is_active']]) ?></label></div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="fields_id"><?php
                echo TEXT_SELECT_FIELD ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'fields_id',
                    listing_highlight::get_fields_choices($_GET['entities_id']),
                    $obj['fields_id'],
                    [
                        'class' => 'form-control input-xlarge chosen-select required',
                        'onChange' => 'load_fields_values(this.value)'
                    ]
                ) ?>
            </div>
        </div>

        <div id="fields_values"></div>

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

        <div class="form-group">
            <label class="col-md-3 control-label" for="sort_order"><?php
                echo TEXT_SORT_ORDER ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-small']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="sort_order"><?php
                echo TEXT_NOTE ?></label>
            <div class="col-md-9">
                <?php
                echo textarea_tag('notes', $obj['notes'], ['class' => 'form-control textarea-small']) ?>
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
            ignore: '',
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });

        load_fields_values($('#fields_id').val());
    });


    function load_fields_values(fields_id) {
        if (fields_id > 0) {
            $('#fields_values').html('<div class="ajax-loading"></div>');

            $('#fields_values').load('<?php echo url_for(
                "entities/listing_highlight",
                'action=get_field_value&entities_id=' . _GET('entities_id')
            )?>', {fields_id: fields_id, id: '<?php echo $obj["id"] ?>'}, function (response, status, xhr) {
                if (status == "error") {
                    $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
                } else {
                    appHandleUniform();
                }
            });
        }
    }

</script>  

    
 
