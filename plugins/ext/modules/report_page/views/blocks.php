<ul class="page-breadcrumb breadcrumb">
    <li><?php
        echo link_to(TEXT_EXT_REPORT_DESIGNER, url_for('ext/report_page/reports')) ?><i class="fa fa-angle-right"></i>
    </li>
    <li><?php
        echo $report_page['name'] ?><i class="fa fa-angle-right"></i></li>
    <li><?php
        echo TEXT_EXT_HTML_BLOCKS ?></li>
</ul>


<?php
echo button_tag(TEXT_BUTTON_ADD, url_for('ext/report_page/blocks_form', 'report_id=' . $report_page['id'])) ?>&nbsp;

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_INSERT ?></th>
            <th><?php
                echo TEXT_TYPE ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>

        <?php
        $blocks_query = db_query(
            "select b.*,f.type as field_type, f.name as field_name, f.entities_id as field_entity_id from app_ext_report_page_blocks b left join app_fields f on b.field_id=f.id  where b.report_id={$report_page['id']} and b.block_type in ('field','table','php','html') order by b.sort_order, b.id"
        );

        if (db_num_rows($blocks_query) == 0) {
            echo '<tr><td colspan="6">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($blocks = db_fetch_array($blocks_query)) {
            $block_settings = new settings($blocks['settings']);
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for(
                                'ext/report_page/blocks_delete',
                                'id=' . $blocks['id'] . '&report_id=' . $report_page['id']
                            )
                        ) . ' ' . button_icon_edit(
                            url_for(
                                'ext/report_page/blocks_form',
                                'id=' . $blocks['id'] . '&report_id=' . $report_page['id']
                            )
                        ) ?></td>
                <td><?php
                    echo '<input value="${' . $blocks['id'] . '}" readonly="readonly" class="form-control input-small select-all">' ?></td>
                <td><?php
                    echo report_page\blocks::get_name($blocks['block_type']) ?></td>
                <td><?php
                    if ($blocks['field_id'] > 0) {
                        $name = $app_entities_cache[$blocks['field_entity_id']]['name'] . ': ' . fields_types::get_option(
                                $blocks['field_type'],
                                'name',
                                $blocks['field_name']
                            );
                    } else {
                        $name = $blocks['name'];
                    }

                    if (in_array($block_settings->get('display_us'), ['table', 'tree_table'])) {
                        $name = link_to(
                            $name,
                            url_for('ext/report_page/blocks_entity_table', 'block_id=' . $blocks['id'])
                        );
                    }

                    $name .= report_page\blocks::get_filters_link($blocks);

                    echo $name;
                    ?>
                </td>
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
        'ext/report_page/reports'
    ) . '" class="btn btn-default"><i class="fa fa-angle-left" aria-hidden="true"></i> ' . TEXT_BUTTON_BACK . '</a>'; ?>