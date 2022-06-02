<ul class="page-breadcrumb breadcrumb">
    <li><?php
        echo link_to(TEXT_EXT_EXPORT_SELECTED, url_for('ext/export_selected/templates')) ?><i
                class="fa fa-angle-right"></i></li>
    <li><?php
        echo $template_info['entities_name'] ?><i class="fa fa-angle-right"></i></li>
    <li><?php
        echo $template_info['name'] ?><i class="fa fa-angle-right"></i></li>
    <li><?php
        echo TEXT_TABLE ?></li>
</ul>

<p><?php
    echo TEXT_EXT_EXPORT_TEMPLATES_TABLE_BLOCK_TIP ?></p>

<?php
$block_type = 'thead';
require(component_path('ext/export_selected/extra_rows'))
?>


<h3 class="page-title"><?php
    echo TEXT_COLUMNS ?></h3>

<?php
echo button_tag(
    TEXT_BUTTON_ADD,
    url_for('ext/export_selected/table_blocks_form', 'templates_id=' . $template_info['id'])
) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_ENTITY ?></th>
            <th width="100%"><?php
                echo TEXT_FIELD ?></th>
            <th><?php
                echo TEXT_HEADING ?></th>
            <th><?php
                echo TEXT_WIDHT ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>

        <?php

        $blocks_query = db_query(
            "select b.*, f.name, f.entities_id, f.type as field_type,f.configuration as field_configuration from app_ext_export_selected_blocks b, app_fields f, app_entities e where  block_type='body_cell' and b.fields_id=f.id and b.templates_id = " . $template_info['id'] . " and b.parent_id=0  and f.entities_id=e.id order by b.sort_order, b.id",
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
                                'ext/export_selected/table_blocks_delete',
                                'id=' . $blocks['id'] . '&templates_id=' . $template_info['id']
                            )
                        ) . ' ' . button_icon_edit(
                            url_for(
                                'ext/export_selected/table_blocks_form',
                                'id=' . $blocks['id'] . '&templates_id=' . $template_info['id']
                            )
                        ) ?></td>
                <td><?php
                    echo $app_entities_cache[$blocks['entities_id']]['name'] ?></td>
                <td><?php
                    $cfg = new fields_types_cfg($blocks['field_configuration']);

                    $field_name = fields_types::get_option($blocks['field_type'], 'name', $blocks['name']);

                    echo $field_name;
                    ?>
                </td>
                <td><?php
                    echo $settings->get('heading') ?></td>
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
$block_type = 'tfoot';
require(component_path('ext/export_selected/extra_rows'))
?>

<?php
echo '<a href="' . url_for(
        'ext/export_selected/templates',
        'templates_id=' . $template_info['id']
    ) . '" class="btn btn-default"><i class="fa fa-angle-left" aria-hidden="true"></i> ' . TEXT_BUTTON_BACK . '</a>'; ?>

<?php
require(component_path('ext/export_selected/table_preview')) ?>