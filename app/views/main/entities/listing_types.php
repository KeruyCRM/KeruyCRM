<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>

<h3 class="page-title"><?= \K::$fw->TEXT_NAV_LISTING_CONFIG ?></h3>

<p><?= \K::$fw->TEXT_LISTING_CONFIGURATION_INFO; ?></p>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?= \K::$fw->TEXT_ACTION ?></th>
            <th width="100%"><?= \K::$fw->TEXT_TYPE ?></th>
            <th><?= \K::$fw->TEXT_IS_ACTIVE ?></th>
            <th><?= \K::$fw->TEXT_IS_DEFAULT ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        //while ($v = db_fetch_array($listing_types_query)):
        foreach (\K::$fw->listing_types_query as $v):
            $v = $v->cast();

            switch ($v['type']) {
                case 'table':
                    $url = \Helpers\Urls::url_for('main/entities/listing', 'entities_id=' . $v['entities_id']);
                    break;
                case 'tree_table':
                    $url = '';
                    break;
                default:
                    $url = \Helpers\Urls::url_for(
                        'main/entities/listing_sections',
                        'listing_types_id=' . $v['id'] . '&entities_id=' . $v['entities_id']
                    );
                    break;
            }
            ?>
            <tr>
                <td style="white-space: nowrap;"><?= \Helpers\Html::button_icon_edit(
                        \Helpers\Urls::url_for(
                            'main/entities/listing_types_form',
                            'id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                        )
                    ) ?></td>
                <td><?= (strlen($url) ? \Helpers\Urls::link_to(
                        \Models\Main\Listing_types::get_type_title($v['type']),
                        $url
                    ) : \Models\Main\Listing_types::get_type_title($v['type'])) ?></td>
                <td><?= $v['type'] == 'table' ? \Helpers\App::render_bool_value(1) : \Helpers\App::render_bool_value(
                        $v['is_active']
                    ) ?></td>
                <td><?= ($v['type'] != 'mobile' ? \Helpers\App::render_bool_value($v['is_default']) : '') ?></td>

            </tr>
        <?php
        endforeach; ?>
        </tbody>
    </table>
</div>

<h3 class="page-title margin-top-20"><?= \K::$fw->TEXT_HIGHLIGHT_ROW ?></h3>

<p><?= \K::$fw->TEXT_LISTING_HIGHLIGHT_ROW_INFO; ?></p>

<?= \Helpers\Html::button_tag(
    \K::$fw->TEXT_BUTTON_ADD,
    \Helpers\Urls::url_for('main/entities/listing_highlight_form', 'entities_id=' . \K::$fw->GET['entities_id'])
) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?= \K::$fw->TEXT_ACTION ?></th>
            <th><?= \K::$fw->TEXT_ID ?></th>
            <th><?= \K::$fw->TEXT_IS_ACTIVE ?></th>
            <th width="100%"><?= \K::$fw->TEXT_FIELD ?></th>
            <th><?= \K::$fw->TEXT_VALUE ?></th>
            <th><?= \K::$fw->TEXT_COLOR ?></th>
            <th><?= \K::$fw->TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        //if (db_count('app_listing_highlight_rules', \K::$fw->GET['entities_id'], 'entities_id') == 0) {
        if (count(\K::$fw->fields_query) == 0) {
            echo '<tr><td colspan="7">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td></tr>';
        } ?>
        <?php
        //while ($v = db_fetch_array($fields_query)):
        foreach (\K::$fw->fields_query as $v):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?= \Helpers\Html::button_icon_delete(
                        \Helpers\Urls::url_for(
                            'main/entities/listing_highlight_delete',
                            'id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                        )
                    )
                    . ' ' . \Helpers\Html::button_icon_edit(
                        \Helpers\Urls::url_for(
                            'main/entities/listing_highlight_form',
                            'id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                        )
                    ) ?></td>
                <td><?= $v['id'] ?></td>
                <td><?= \Helpers\App::render_bool_value($v['is_active']) ?></td>
                <td class="white-space-normal"><?= $v['name'] . \Helpers\App::tooltip_text(
                        '<i>' . $v['notes'] . '</i>'
                    ) ?></td>
                <td><?= \Models\Main\Items\Listing_highlight::get_field_value_by_type($v, $v['fields_values']) ?></td>
                <td><?= \Helpers\App::render_bg_color_block($v['bg_color']) ?></td>
                <td><?= $v['sort_order'] ?></td>
            </tr>
        <?php
        endforeach; ?>
        </tbody>
    </table>
</div>