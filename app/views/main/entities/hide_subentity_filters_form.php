<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_HEADING_REPORTS_FILTER_INFO) ?>

<?= \Helpers\Html::form_tag(
    'reports_filters',
    \Helpers\Urls::url_for(
        'main/entities/hide_subentity_filters/save',
        'reports_id=' . \K::$fw->GET['reports_id'] . (isset(\K::$fw->GET['id']) ? '&id=' . \K::$fw->GET['id'] : '') . '&entities_id=' . \K::$fw->GET['entities_id']
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">
        <div class="form-group">
            <label class="col-md-3 control-label" for="fields_id"><?= \K::$fw->TEXT_SELECT_FIELD ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'fields_id',
                    \Models\Main\Fields::get_filters_choices(\K::$fw->reports_info['entities_id'], false),
                    \K::$fw->obj['fields_id'],
                    ['class' => 'form-control chosen-select required', 'onChange' => 'load_filters_options(this.value)']
                ) ?>
            </div>
        </div>
        <div id="filters_options"></div>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#reports_filters').validate();

        load_filters_options($('#fields_id').val());
    });

    function load_filters_options(fields_id) {
        if (fields_id > 0) {
            $('#filters_options').html('<div class="ajax-loading"></div>');
            $('#filters_options').load('<?= \Helpers\Urls::url_for('main/reports/filters_options')?>', {
                fields_id: fields_id,
                id: '<?= \K::$fw->obj["id"] ?>'
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