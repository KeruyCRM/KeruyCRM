<ul class="page-breadcrumb breadcrumb">
    <li><?php
        echo link_to(TEXT_EXT_EXPORT_TEMPLATES, url_for('ext/templates/export_templates')) ?><i
                class="fa fa-angle-right"></i></li>
    <li><?php
        echo $template_info['entities_name'] ?><i class="fa fa-angle-right"></i></li>
    <li><?php
        echo link_to(
            $template_info['name'],
            url_for('ext/templates_docx/blocks', 'templates_id=' . $template_info['id'])
        ) ?><i class="fa fa-angle-right"></i></li>
    <li><?php
        echo $parent_block['field_name'] ?><i class="fa fa-angle-right"></i></li>
    <li><?php
        echo TEXT_EXT_TABLE_LIST ?></li>
</ul>

<p><?php
    echo TEXT_EXT_EXPORT_TEMPLATES_TABLE_LIST_BLOCK_TIP ?></p>

<h3 class="page-title"><?php
    echo TEXT_COLUMNS ?></h3>

<?php
echo button_tag(
    TEXT_BUTTON_ADD,
    url_for(
        'ext/templates_docx/table_list_blocks_form',
        'templates_id=' . $template_info['id'] . '&parent_block_id=' . $parent_block['id']
    )
) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_HEADING ?></th>
            <th width="100%"><?php
                echo TEXT_FIELD ?></th>
            <th><?php
                echo TEXT_WIDHT ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>

        <?php

        $blocks_query = db_query(
            "select b.* from app_ext_items_export_templates_blocks b where b.block_type='table_list_cell' and b.templates_id = " . $template_info['id'] . " and b.parent_id = " . $parent_block['id'] . " order by b.sort_order, b.id",
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
                                'ext/templates_docx/table_list_blocks_delete',
                                'id=' . $blocks['id'] . '&templates_id=' . $template_info['id'] . '&parent_block_id=' . $parent_block['id']
                            )
                        ) . ' ' . button_icon_edit(
                            url_for(
                                'ext/templates_docx/table_list_blocks_form',
                                'id=' . $blocks['id'] . '&templates_id=' . $template_info['id'] . '&parent_block_id=' . $parent_block['id']
                            )
                        ) ?></td>
                <td><?php
                    echo $settings->get('heading') ?></td>
                <td><?php

                    if (is_array($settings->get('fields')) and count($settings->get('fields'))) {
                        $choices = [];
                        $fields_query = db_query(
                            "select * from app_fields where id in (" . implode(
                                ',',
                                $settings->get('fields')
                            ) . ") order by field(id," . implode(',', $settings->get('fields')) . ")"
                        );
                        while ($fields = db_fetch_array($fields_query)) {
                            $choices[] = fields_types::get_option($fields['type'], 'name', $fields['name']);
                        }

                        echo implode('<br>', $choices);
                    };
                    ?>
                </td>
                <td><?php
                    echo $settings->get('cell_width') ?></td>
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
        'ext/templates_docx/blocks',
        'templates_id=' . $template_info['id']
    ) . '" class="btn btn-default"><i class="fa fa-angle-left" aria-hidden="true"></i> ' . TEXT_BUTTON_BACK . '</a>'; ?>

