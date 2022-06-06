<?php
require(component_path('entities/navigation')) ?>

<h3 class="page-title"><?php
    echo TEXT_NAV_LISTING_CONFIG ?></h3>

<p><?php
    echo TEXT_LISTING_CONFIGURATION_INFO; ?></p>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>

            <th><?php
                echo TEXT_ACTION ?></th>
            <th width="100%"><?php
                echo TEXT_TYPE ?></th>
            <th><?php
                echo TEXT_IS_ACTIVE ?></th>
            <th><?php
                echo TEXT_IS_DEFAULT ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $listing_types_query = db_query(
            "select * from app_listing_types where entities_id='" . _get::int('entities_id') . "'"
        );

        while ($v = db_fetch_array($listing_types_query)):

            switch ($v['type']) {
                case 'table':
                    $url = url_for('entities/listing', 'entities_id=' . $v['entities_id']);
                    break;
                case 'tree_table':
                    $url = '';
                    break;
                default:
                    $url = url_for(
                        'entities/listing_sections',
                        'listing_types_id=' . $v['id'] . '&entities_id=' . $v['entities_id']
                    );
                    break;
            }

            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_edit(
                        url_for(
                            'entities/listing_types_form',
                            'id=' . $v['id'] . '&entities_id=' . $_GET['entities_id']
                        )
                    ) ?></td>
                <td><?php
                    echo(strlen($url) ? link_to(
                        listing_types::get_type_title($v['type']),
                        $url
                    ) : listing_types::get_type_title($v['type'])) ?></td>
                <td><?php
                    echo $v['type'] == 'table' ? render_bool_value(1) : render_bool_value($v['is_active']) ?></td>
                <td><?php
                    echo($v['type'] != 'mobile' ? render_bool_value($v['is_default']) : '') ?></td>

            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>


<h3 class="page-title margin-top-20"><?php
    echo TEXT_HIGHLIGHT_ROW ?></h3>

<p><?php
    echo TEXT_LISTING_HIGHLIGHT_ROW_INFO; ?></p>

<?php
echo button_tag(TEXT_BUTTON_ADD, url_for('entities/listing_highlight_form', 'entities_id=' . $_GET['entities_id'])) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_ID ?></th>
            <th><?php
                echo TEXT_IS_ACTIVE ?></th>
            <th width="100%"><?php
                echo TEXT_FIELD ?></th>
            <th><?php
                echo TEXT_VALUE ?></th>
            <th><?php
                echo TEXT_COLOR ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (db_count('app_listing_highlight_rules', $_GET['entities_id'], 'entities_id') == 0) {
            echo '<tr><td colspan="7">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        } ?>
        <?php

        $fields_query = db_query(
            "select r.*, f.name, f.type, f.configuration from app_listing_highlight_rules r, app_fields f where f.id = r.fields_id and r.entities_id='" . db_input(
                $_GET['entities_id']
            ) . "' order by r.sort_order, r.id"
        );
        while ($v = db_fetch_array($fields_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for(
                                'entities/listing_highlight_delete',
                                'id=' . $v['id'] . '&entities_id=' . $_GET['entities_id']
                            )
                        )
                        . ' ' . button_icon_edit(
                            url_for(
                                'entities/listing_highlight_form',
                                'id=' . $v['id'] . '&entities_id=' . $_GET['entities_id']
                            )
                        ) ?></td>
                <td><?php
                    echo $v['id'] ?></td>
                <td><?php
                    echo render_bool_value($v['is_active']) ?></td>
                <td class="white-space-nomral"><?php
                    echo $v['name'] . tooltip_text('<i>' . $v['notes'] . '</i>') ?></td>
                <td><?php
                    echo listing_highlight::get_field_value_by_type($v, $v['fields_values']) ?></td>
                <td><?php
                    echo render_bg_color_block($v['bg_color']) ?></td>
                <td><?php
                    echo $v['sort_order'] ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>