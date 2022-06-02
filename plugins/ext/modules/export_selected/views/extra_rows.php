<ul class="page-breadcrumb breadcrumb">
    <li><?php
        echo link_to(TEXT_EXT_EXPORT_TEMPLATES, url_for('ext/export_selected/templates')) ?><i
                class="fa fa-angle-right"></i></li>
    <li><?php
        echo $template_info['entities_name'] ?><i class="fa fa-angle-right"></i></li>
    <li><?php
        echo $template_info['name'] ?><i class="fa fa-angle-right"></i></li>
    <li><?php
        echo TEXT_TABLE ?><i class="fa fa-angle-right"></i></li>
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
        'ext/export_selected/extra_rows_form',
        'templates_id=' . $template_info['id'] . '&row_id=' . $row_info['id']
    )
) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th width="100%"><?php
                echo TEXT_HEADING ?></th>
            <th><?php
                echo TEXT_VALUE ?></th>
            <th><?php
                echo TEXT_EXT_MERGE_CELLS ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>

        <?php

        $blocks_query = db_query(
            "select b.* from app_ext_export_selected_blocks b where b.templates_id = " . $template_info['id'] . " and b.parent_id = " . $row_info['id'] . " order by b.sort_order, b.id",
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
                                'ext/export_selected/extra_rows_delete',
                                'id=' . $blocks['id'] . '&templates_id=' . $template_info['id'] . '&row_id=' . $row_info['id']
                            )
                        ) . ' ' . button_icon_edit(
                            url_for(
                                'ext/export_selected/extra_rows_form',
                                'id=' . $blocks['id'] . '&templates_id=' . $template_info['id'] . '&row_id=' . $row_info['id']
                            )
                        ) ?></td>
                <td><?php
                    echo $settings->get('heading') ?></td>
                <td><?php
                    if ($blocks['fields_id'] > 0) {
                        echo fields::get_name_by_id($blocks['fields_id']);
                    }
                    ?></td>
                <td><?php
                    echo $settings->get('colspan') ?></td>
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
        'ext/export_selected/table_blocks',
        'templates_id=' . $template_info['id']
    ) . '" class="btn btn-default"><i class="fa fa-angle-left" aria-hidden="true"></i> ' . TEXT_BUTTON_BACK . '</a>'; ?>

<?php
require(component_path('ext/export_selected/table_preview')) ?>