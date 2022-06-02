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
        echo fields_types::get_option($parent_block['field_type'], 'name', $parent_block['field_name']) ?><i
                class="fa fa-angle-right"></i></li>
    <li><?php
        echo TEXT_EXT_INFO_BLOCKS ?></li>
</ul>

<p><?php
    echo TEXT_EXT_EXPORT_TEMPLATES_ENTITY_BLOCK_TIP ?></p>

<?php
echo button_tag(
    TEXT_BUTTON_ADD,
    url_for(
        'ext/templates_docx/entity_blocks_form',
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
                echo TEXT_INSERT ?></th>
            <th><?php
                echo TEXT_ENTITY ?></th>
            <th width="100%"><?php
                echo TEXT_FIELD ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>

        <?php

        $blocks_query = db_query(
            "select b.*, f.name, f.entities_id, f.type as field_type,f.configuration as field_configuration from app_ext_items_export_templates_blocks b, app_fields f, app_entities e where  block_type='entity' and b.fields_id=f.id and b.templates_id = " . $template_info['id'] . " and b.parent_id = " . $parent_block['id'] . " and f.entities_id=e.id order by b.sort_order, b.id",
            false
        );

        if (db_num_rows($blocks_query) == 0) {
            echo '<tr><td colspan="6">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($blocks = db_fetch_array($blocks_query)) {
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for(
                                'ext/templates_docx/entity_blocks_delete',
                                'id=' . $blocks['id'] . '&templates_id=' . $template_info['id'] . '&parent_block_id=' . $parent_block['id']
                            )
                        ) . ' ' . button_icon_edit(
                            url_for(
                                'ext/templates_docx/entity_blocks_form',
                                'id=' . $blocks['id'] . '&templates_id=' . $template_info['id'] . '&parent_block_id=' . $parent_block['id']
                            )
                        ) ?></td>
                <td><?php
                    echo '<input value="${' . $blocks['id'] . '}" readonly="readonly" class="form-control input-small select-all">' ?></td>
                <td><?php
                    echo $app_entities_cache[$blocks['entities_id']]['name'] ?></td>
                <td><?php
                    $cfg = new fields_types_cfg($blocks['field_configuration']);

                    $field_name = fields_types::get_option($blocks['field_type'], 'name', $blocks['name']);

                    echo $field_name;
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
        'ext/templates_docx/blocks',
        'templates_id=' . $template_info['id']
    ) . '" class="btn btn-default"><i class="fa fa-angle-left" aria-hidden="true"></i> ' . TEXT_BUTTON_BACK . '</a>'; ?>