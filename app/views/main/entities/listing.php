<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>

<h3 class="page-title"><?= \Helpers\Urls::link_to(
        \K::$fw->TEXT_NAV_LISTING_CONFIG,
        \Helpers\Urls::url_for('main/entities/listing_types', 'entities_id=' . \K::$fw->GET['entities_id'])
    ) ?> <i class="fa fa-angle-right"></i> <?= \K::$fw->TEXT_TABLE ?></h3>

<ul class="nav nav-tabs">
    <li class="active"><a href="#fields_in_listing_tab" data-toggle="tab"><?= \K::$fw->TEXT_FIELDS_IN_LISTING ?></a>
    </li>
    <li><a href="#settings_tab" data-toggle="tab"><?= \K::$fw->TEXT_SETTINGS ?></a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade active in" id="fields_in_listing_tab">
        <div><?= \K::$fw->TEXT_LISTING_CFG_INFO ?></div>
        <div class="row">
            <div class="col-md-8">
                <table style="width: 100%;">
                    <tr>
                        <td valign="top" width="50%">
                            <fieldset>
                                <legend><?= \K::$fw->TEXT_FIELDS_IN_LISTING ?></legend>
                                <div class="cfg_listing">
                                    <ul id="fields_in_listing" class="sortable">
                                        <?php
                                        //while ($v = db_fetch_array($fields_query)) {
                                        foreach (\K::$fw->fields_query as $v) {
                                            echo '<li id="form_fields_' . $v['id'] . '"><div>' . \Models\Main\Fields_types::get_option(
                                                    $v['type'],
                                                    'name',
                                                    $v['name']
                                                ) . '</div></li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </fieldset>
                        </td>
                        <td style="padding-left: 25px;" valign="top">
                            <fieldset>
                                <legend><?= \K::$fw->TEXT_FIELDS_EXCLUDED_FROM_LISTING ?></legend>
                                <div class="cfg_listing">
                                    <ul id="fields_excluded_from_listing" class="sortable">
                                        <?php
                                        //while ($v = db_fetch_array($fields_query)) {
                                        foreach (\K::$fw->fields_query2 as $v) {
                                            echo '<li id="form_fields_' . $v['id'] . '"><div>' . \Models\Main\Fields_types::get_option(
                                                    $v['type'],
                                                    'name',
                                                    $v['name']
                                                ) . '</div></li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </fieldset>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="settings_tab">
        <div class="row">
            <form class="form-horizontal" role="form">
                <div class="form-group">
                    <label class="col-md-3 control-label"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_IS_HEADING_INFO
                        ) . \K::$fw->TEXT_HEADING ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'heading_field_id',
                            \K::$fw->choices,
                            \Models\Main\Fields::get_heading_id(\K::$fw->GET["entities_id"]),
                            ['class' => 'form-control input-large']
                        ) ?>
                        <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_SELECT_HEADING_FIELD) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="number_fixed_field_in_listing"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_HEADING_WIDTH_BASED_CONTENT_INFO
                        ) . \K::$fw->TEXT_HEADING_WIDTH_BASED_CONTENT ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'heading_width_based_content',
                            ['1' => \K::$fw->TEXT_YES, '0' => \K::$fw->TEXT_NO],
                            (int)\K::$fw->cfg->get('heading_width_based_content'),
                            ['class' => 'form-control input-small']
                        ) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="number_fixed_field_in_listing"><?= \K::$fw->TEXT_CHANGE_COL_WIDTH_IN_LISTING ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'change_col_width_in_listing',
                            ['1' => \K::$fw->TEXT_YES, '0' => \K::$fw->TEXT_NO],
                            (int)\K::$fw->cfg->get('change_col_width_in_listing'),
                            ['class' => 'form-control input-small']
                        ) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="editable_fields_in_listing"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_EDITABLE_FIELDS_IN_LISTING_INFO
                        ) . \K::$fw->TEXT_EDITABLE_FIELDS_IN_LISTING ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'editable_fields_in_listing',
                            ['1' => \K::$fw->TEXT_YES, '0' => \K::$fw->TEXT_NO],
                            (int)\K::$fw->cfg->get('editable_fields_in_listing'),
                            ['class' => 'form-control input-small']
                        ) ?>
                    </div>
                </div>
            </form>
        </div>
        <br><br>
        <div class="row">
            <div class="col-md-8">
                <legend><?= \K::$fw->TEXT_LISTING_HORIZONTAL_SCROLL ?></legend>
                <div><?= \K::$fw->TEXT_LISTING_HORIZONTAL_SCROLL_INFO ?></div>
                <div>
                    <form class="form-inline" role="form">
                        <div class="form-group">
                            <label for="number_fixed_field_in_listing"><?= \K::$fw->TEXT_NUMBER_FIXED_FIELD ?></label>
                            <?= \Helpers\Html::input_tag(
                                'number_fixed_field_in_listing',
                                (int)\K::$fw->cfg->get('number_fixed_field_in_listing'),
                                ['class' => 'form-control input-xsmall']
                            ) ?>
                        </div>
                    </form>
                </div>
                <div><?= \K::$fw->TEXT_NUMBER_FIXED_FIELD_INFO ?></div>
            </div>
        </div>
    </div>
</div>

<hr>

<?= \Helpers\Urls::link_to(
    \K::$fw->TEXT_BUTTON_BACK,
    \Helpers\Urls::url_for('main/entities/listing_types', 'entities_id=' . \K::$fw->GET['entities_id']),
    ['class' => 'btn btn-default']
) ?>

<script>
    $(function () {
        $("ul.sortable").sortable({
            connectWith: "ul",
            update: function (event, ui) {
                data = '';
                $("ul.sortable").each(function () {
                    data = data + '&' + $(this).attr('id') + '=' + $(this).sortable("toArray")
                });
                data = data.slice(1)
                $.ajax({
                    type: "POST",
                    url: '<?= \Helpers\Urls::url_for(
                        'ain/entities/fields/sort_fields',
                        'entities_id=' . \K::$fw->GET["entities_id"]
                    ) ?>',
                    data: data
                });
            }
        });

        $("#heading_width_based_content").change(function () {
            $.ajax({
                type: "POST",
                url: '<?= \Helpers\Urls::url_for(
                    'main/entities/fields/set_heading_field_width',
                    'entities_id=' . \K::$fw->GET["entities_id"]
                ) ?>',
                data: {heading_width_based_content: $(this).val()}
            });
        })

        $("#change_col_width_in_listing").change(function () {
            $.ajax({
                type: "POST",
                url: '<?= \Helpers\Urls::url_for(
                    'main/entities/fields/set_change_col_width_in_listing',
                    'entities_id=' . \K::$fw->GET["entities_id"]
                ) ?>',
                data: {change_col_width_in_listing: $(this).val()}
            });
        })

        $("#editable_fields_in_listing").change(function () {
            $.ajax({
                type: "POST",
                url: '<?= \Helpers\Urls::url_for(
                    'main/entities/fields/editable_fields_in_listing',
                    'entities_id=' . \K::$fw->GET["entities_id"]
                ) ?>',
                data: {editable_fields_in_listing: $(this).val()}
            });
        })

        $("#number_fixed_field_in_listing").keyup(function () {
            $.ajax({
                type: "POST",
                url: '<?= \Helpers\Urls::url_for(
                    'main/entities/fields/set_number_fixed_field_in_listing',
                    'entities_id=' . \K::$fw->GET["entities_id"]
                ) ?>',
                data: {number_fields: $(this).val()}
            });
        })

        $("#heading_field_id").change(function () {
            $.ajax({
                type: "POST",
                url: '<?= \Helpers\Urls::url_for(
                    'main/entities/fields/set_heading_field_id',
                    'entities_id=' . \K::$fw->GET["entities_id"]
                ) ?>',
                data: {heading_field_id: $(this).val()}
            });
        })
    });
</script>