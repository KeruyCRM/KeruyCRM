<h3 class="page-title"><?php
    echo TEXT_EXT_EXTRA_ROWS ?></h3>

<?php
echo button_tag(
    TEXT_BUTTON_ADD,
    url_for(
        'ext/export_selected/extra_rows',
        'action=add_row&block_type=' . $block_type . '&templates_id=' . $template_info['id']
    ),
    false
) ?>


<table class="table table-striped table-bordered table-hover margin-top-10">
    <tbody>

    <?php

    $blocks_query = db_query(
        "select b.* from app_ext_export_selected_blocks b where  b.block_type='" . $block_type . "' and  b.templates_id = " . $template_info['id'] . " and b.parent_id = 0 order by b.sort_order, b.id",
        false
    );

    if (db_num_rows($blocks_query) == 0) {
        echo '<tr><td colspan="6">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
    }

    while ($blocks = db_fetch_array($blocks_query)) {
        ?>
        <tr>
            <td style="white-space: nowrap;"><?php
                echo '<a onClick="return confirm(\'' . TEXT_ARE_YOU_SURE . '\')" href="' . url_for(
                        'ext/export_selected/extra_rows',
                        'action=delete_row&id=' . $blocks['id'] . '&templates_id=' . $template_info['id']
                    ) . '" class="btn btn-default btn-xs purple"><i class="fa fa-trash-o"></i></a>' ?></td>
            <td width="100%"><?php
                echo link_to(
                    TEXT_EXT_ROW . ' (' . abs($blocks['sort_order']) . ')',
                    url_for(
                        'ext/export_selected/extra_rows',
                        'row_id=' . $blocks['id'] . '&templates_id=' . $template_info['id']
                    )
                ) ?></td>
        </tr>

        <?php
    }
    ?>

    </tbody>
</table>
