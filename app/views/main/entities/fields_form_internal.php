<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_HEADING_FIELD_INFO) ?>

<?= \Helpers\Html::form_tag(
    'fields_form',
    \Helpers\Urls::url_for(
        'main/entities/fields/save_internal',
        (isset(\K::$fw->GET['id']) ? '&id=' . \K::$fw->GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body ajax-modal-width-790">
        <?= \Helpers\Html::input_hidden_tag('entities_id', \K::$fw->GET['entities_id']) ?>
        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?= \K::$fw->TEXT_TYPE ?></label>
            <div class="col-md-9">
                <p class="form-control-static"><?= \Models\Main\Fields_types::get_title(\K::$fw->obj['type']) ?></p>
                <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_INTERNAL_FIELD_NOTE) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?= \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_FIELD_NAME_INFO
                ) . \K::$fw->TEXT_NAME ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag('name', \K::$fw->obj['name'], ['class' => 'form-control input-large']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="short_name"><?= \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_FIELD_SHORT_NAME_INFO
                ) . \K::$fw->TEXT_SHORT_NAME ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'short_name',
                    \K::$fw->obj['short_name'],
                    ['class' => 'form-control input-small']
                ) ?>
            </div>
        </div>
        <?php
        if (!in_array(\K::$fw->obj['type'], ['fieldtype_parent_item_id', 'fieldtype_date_updated'])): ?>
            <div class="form-group" id="is-heading-container">
                <label class="col-md-3 control-label" for="is_heading"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_IS_HEADING_INFO
                    ) . \K::$fw->TEXT_IS_HEADING ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list"><label class="checkbox-inline"><?= \Helpers\Html::input_checkbox_tag(
                                'is_heading',
                                '1',
                                ['checked' => \K::$fw->obj['is_heading']]
                            ) ?></label>
                    </div>
                </div>
            </div>
        <?php
        endif ?>
        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo \K::$fw->TEXT_SORT_ORDER ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'sort_order',
                    \K::$fw->obj['sort_order'],
                    ['class' => 'form-control input-xsmall']
                ) ?>
            </div>
        </div>
        <div id="fields_types_configuration"></div>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        fields_types_configuration('<?php echo \K::$fw->obj['type'] ?>');
    });

    function fields_types_configuration(field_type) {
        $('#fields_types_configuration').html('<div class="ajax-loading"></div>');
        $('#fields_types_configuration').load('<?= \Helpers\Urls::url_for(
            "main/entities/fields_configuration"
        )?>', {
            field_type: field_type,
            id: '<?= \K::$fw->obj["id"] ?>',
            entities_id: '<?= \K::$fw->GET["entities_id"]?>'
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