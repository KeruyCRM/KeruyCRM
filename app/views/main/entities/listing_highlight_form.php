<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_HEADING_REPORTS_FILTER_INFO) ?>

<?= \Helpers\Html::form_tag(
    'modal_form',
    \Helpers\Urls::url_for(
        'main/entities/listing_highlight/save',
        (isset(\K::$fw->GET['id']) ? '&id=' . \K::$fw->GET['id'] : '') . '&entities_id=' . \K::$fw->GET['entities_id']
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">
        <div class="form-group">
            <label class="col-md-3 control-label" for="is_active"><?= \K::$fw->TEXT_IS_ACTIVE ?></label>
            <div class="col-md-9">
                <div class="checkbox-list"><label class="checkbox-inline"><?= \Helpers\Html::input_checkbox_tag(
                            'is_active',
                            '1',
                            ['checked' => \K::$fw->obj['is_active']]
                        ) ?></label></div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="fields_id"><?= \K::$fw->TEXT_SELECT_FIELD ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'fields_id',
                    \Models\Main\Items\Listing_highlight::get_fields_choices(\K::$fw->GET['entities_id']),
                    \K::$fw->obj['fields_id'],
                    [
                        'class' => 'form-control input-xlarge chosen-select required',
                        'onChange' => 'load_fields_values(this.value)'
                    ]
                ) ?>
            </div>
        </div>
        <div id="fields_values"></div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="bg_color"><?= \K::$fw->TEXT_BACKGROUND_COLOR ?></label>
            <div class="col-md-9">
                <div class="input-group input-small color colorpicker-default"
                     data-color="<?= (strlen(\K::$fw->obj['bg_color']) > 0 ? \K::$fw->obj['bg_color'] : '#ff0000') ?>">
                    <?= \Helpers\Html::input_tag(
                        'bg_color',
                        \K::$fw->obj['bg_color'],
                        ['class' => 'form-control input-small']
                    ) ?>
                    <span class="input-group-btn">
  				        <button class="btn btn-default" type="button">&nbsp;</button>
  			        </span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="sort_order"><?= \K::$fw->TEXT_SORT_ORDER ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'sort_order',
                    \K::$fw->obj['sort_order'],
                    ['class' => 'form-control input-small']
                ) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="sort_order"><?= \K::$fw->TEXT_NOTE ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::textarea_tag(
                    'notes',
                    \K::$fw->obj['notes'],
                    ['class' => 'form-control textarea-small']
                ) ?>
            </div>
        </div>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

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

            $('#fields_values').load('<?= \Helpers\Urls::url_for(
                'main/entities/listing_highlight/get_field_value',
                'entities_id=' . \K::$fw->GET['entities_id']
            )?>', {fields_id: fields_id, id: '<?= \K::$fw->obj["id"] ?>'}, function (response, status, xhr) {
                if (status == "error") {
                    $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
                } else {
                    appHandleUniform();
                }
            });
        }
    }
</script>