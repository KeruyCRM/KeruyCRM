<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>

    <h3 class="page-title"><?= \K::$fw->TEXT_NAV_LISTING_FILTERS_CONFIG ?></h3>

    <p><?= \K::$fw->TEXT_LISTING_FILTERS_CFG_INFO ?></p>

<?= \Helpers\Html::button_tag(
    \K::$fw->TEXT_BUTTON_ADD_NEW_REPORT_FILTER,
    \Helpers\Urls::url_for(
        'main/entities/listing_filters_form',
        'reports_id=' . \K::$fw->reports_info['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
    )
) . ' ' . \Helpers\Html::button_tag(
    \K::$fw->TEXT_BUTTON_CONFIGURE_SORTING,
    \Helpers\Urls::url_for(
        'main/reports/sorting',
        'reports_id=' . \K::$fw->reports_info['id'] . '&redirect_to=listng_filters'
    )
) ?>

    <div class="table-scrollable">
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th><?= \K::$fw->TEXT_ACTION ?></th>
                <th width="100%"><?= \K::$fw->TEXT_FIELD ?></th>
                <th><?= \K::$fw->TEXT_FILTERS_CONDITION ?></th>
                <th><?= \K::$fw->TEXT_VALUES ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (\K::$fw->reports_filters_count == 0) {
                echo '<tr><td colspan="4">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td></tr>';
            } ?>
            <?php
            //while ($v = db_fetch_array($filters_query)):
            foreach (\K::$fw->filters_query as $v):
                ?>
                <tr>
                    <td style="white-space: nowrap;"><?= \Helpers\Html::button_icon_delete(
                            \Helpers\Urls::url_for(
                                'main/entities/listing_filters_delete',
                                'id=' . $v['id'] . '&reports_id=' . \K::$fw->reports_info['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) . ' ' . \Helpers\Html::button_icon_edit(
                            \Helpers\Urls::url_for(
                                'main/entities/listing_filters_form',
                                'id=' . $v['id'] . '&reports_id=' . \K::$fw->reports_info['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) ?></td>
                    <td><?= \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']) ?></td>
                    <td><?= \Models\Main\Reports\Reports::get_condition_name_by_key($v['filters_condition']) ?></td>
                    <td class="nowrap"><?= \Models\Main\Reports\Reports::render_filters_values(
                            $v['fields_id'],
                            $v['filters_values'],
                            '<br>',
                            $v['filters_condition']
                        ) ?></td>
                </tr>
            <?php
            endforeach; ?>
            </tbody>
        </table>
    </div>

<?= \Helpers\Urls::link_to(
    \K::$fw->TEXT_BUTTON_BACK,
    \Helpers\Urls::url_for('main/filters_panels/panels', 'entities_id=' . \K::$fw->GET['entities_id']),
    ['class' => 'btn btn-default']
) ?>