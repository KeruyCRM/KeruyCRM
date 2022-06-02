<ul class="page-breadcrumb breadcrumb">
    <li><?php
        echo link_to(TEXT_EXT_REPORT_DESIGNER, url_for('ext/report_page/reports')) ?><i class="fa fa-angle-right"></i>
    </li>
    <li><?php
        echo link_to($report_page['name'], url_for('ext/report_page/blocks', 'report_id=' . $report_page['id'])) ?><i
                class="fa fa-angle-right"></i></li>
    <li><?php
        echo link_to($block_name, url_for('ext/report_page/blocks_entity_table', 'block_id=' . $block_info['id'])) ?><i
                class="fa fa-angle-right"></i></li>
    <li><?php
        echo TEXT_TABLE ?><i class="fa fa-angle-right"></i></li>
    <li><?php
        echo strtoupper($row_info['block_type']) ?><i class="fa fa-angle-right"></i></li>
    <li><?php
        echo TEXT_EXT_ROW . ' (' . abs($row_info['sort_order']) . ')' ?></li>
</ul>

<p><?php
    echo TEXT_EXT_EXPORT_TEMPLATES_TABLE_ROW_TIP ?></p>

<h3 class="page-title"><?php
    echo TEXT_COLUMNS ?></h3>

<?php
echo button_tag(
    TEXT_BUTTON_ADD,
    url_for(
        'ext/report_page/extra_rows_form',
        'report_id=' . $report_page['id'] . '&block_id=' . $block_info['id'] . '&row_id=' . $row_info['id']
    )
) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_TYPE ?></th>
            <th width="100%"><?php
                echo TEXT_HEADING ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>

        <?php

        $blocks_query = db_query(
            "select b.*,f.type as field_type, f.name as field_name, f.entities_id as field_entity_id from app_ext_report_page_blocks b left join app_fields f on b.field_id=f.id  where b.report_id = " . $report_page['id'] . " and b.parent_id = " . $row_info['id'] . " order by b.sort_order, b.id",
            false
        );

        if (db_num_rows($blocks_query) == 0) {
            echo '<tr><td colspan="6">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($blocks = db_fetch_array($blocks_query)) {
            $settings = new settings($blocks['settings']);
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for(
                                'ext/report_page/extra_rows_delete',
                                'id=' . $blocks['id'] . '&report_id=' . $report_page['id'] . '&block_id=' . $block_info['id'] . '&row_id=' . $row_info['id']
                            )
                        ) . ' ' . button_icon_edit(
                            url_for(
                                'ext/report_page/extra_rows_form',
                                'id=' . $blocks['id'] . '&report_id=' . $report_page['id'] . '&block_id=' . $block_info['id'] . '&row_id=' . $row_info['id']
                            )
                        ) ?></td>
                <td><?php

                    switch ($settings->get('value_type')) {
                        case 'field':
                            $value = TEXT_FIELD . ': ' . fields_types::get_option(
                                    $blocks['field_type'],
                                    'name',
                                    $blocks['field_name']
                                );
                            break;
                        case 'php_code':
                            $value = TEXT_PHP_CODE;
                            break;
                        default:
                            $value = TEXT_TEXT;
                            break;
                    }

                    echo $value;
                    ?>
                </td>
                <td><?php
                    echo $settings->get('heading') ?></td>
                <td><?php
                    echo $blocks['sort_order'] ?></td>
            </tr>

            <?php
        }
        ?>

        </tbody>
    </table>
</div>

<?php
echo '<a href="' . url_for(
        'ext/report_page/blocks_entity_table',
        'report_id=' . $report_page['id'] . '&block_id=' . $block_info['id']
    ) . '" class="btn btn-default"><i class="fa fa-angle-left" aria-hidden="true"></i> ' . TEXT_BUTTON_BACK . '</a>'; ?>

<?php
require(component_path('ext/report_page/table_preview')) ?>