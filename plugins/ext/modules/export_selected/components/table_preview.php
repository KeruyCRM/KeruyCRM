<?php

$parent_block_id = 0;
$parent_block_settings = new settings($template_info['settings']);

$header_bg_color = (strlen($parent_block_settings->get('header_color')) ? $parent_block_settings->get(
    'header_color'
) : $parent_block_settings->get('table_color'));
$table_bg_color = $parent_block_settings->get('table_color');

$html = '
    <table class="table-block-preview" border="1" cellpadding="4" style="min-width: 30%">
     <thead>
';

//extra rows
$rows_query = db_query(
    "select b.* from app_ext_export_selected_blocks b where b.block_type='thead' and b.templates_id = " . $template_info['id'] . " and b.parent_id = " . $parent_block_id . " order by b.sort_order, b.id"
);
while ($rows = db_fetch_array($rows_query)) {
    $blocks_query = db_query(
        "select b.* from app_ext_export_selected_blocks b where b.templates_id = " . $template_info['id'] . " and b.parent_id = " . $rows['id'] . " order by b.sort_order, b.id"
    );

    if (db_num_rows($blocks_query)) {
        $html .= '<tr>';

        while ($blocks = db_fetch_array($blocks_query)) {
            $settings = new settings($blocks['settings']);

            //get field value if field is selected            
            $cell_value = $settings->get('heading');

            $cell_settings = 'style="text-align: ' . $settings->get('heading_alignment') . '" ' . (strlen(
                    $settings->get('colspan')
                ) ? 'colspan="' . $settings->get('colspan') . '"' : '');

            $html .= '<td ' . $cell_settings . '>' . $cell_value . '</td>';
        }

        $html .= '</tr>';
    }
}


//thead
$html .= '<tr>';

if ($parent_block_settings->get('line_numbering') == 1) {
    $html .= '<td style="text-align: center">' . $parent_block_settings->get('line_numbering_heading') . '</td>';
}

$blocks_query = db_query(
    "select b.*, f.name, f.entities_id, f.type as field_type,f.configuration as field_configuration from app_ext_export_selected_blocks b, app_fields f, app_entities e where  block_type='body_cell' and b.fields_id=f.id and b.templates_id = " . $template_info['id'] . " and b.parent_id = " . $parent_block_id . " and f.entities_id=e.id order by b.sort_order, b.id",
    false
);
while ($blocks = db_fetch_array($blocks_query)) {
    $settings = new settings($blocks['settings']);

    $cell_settings = 'style="text-align: ' . $settings->get('heading_alignment') . '" ' . (strlen(
            $settings->get('colspan')
        ) ? 'colspan="' . $settings->get('colspan') . '"' : '');

    $cell_name = (strlen($settings->get('heading')) ? $settings->get('heading') : fields_types::get_option(
        $blocks['field_type'],
        'name',
        $blocks['name']
    ));

    $html .= '<td ' . $cell_settings . '>' . $cell_name . '</td>';
}
$html .= '</tr>';


$html .= '
    </thead>
    <tbody>    
';

//column numbering
if ($parent_block_settings->get('column_numbering') == 1) {
    $html .= '<tr>';

    $count = 1;

    if ($parent_block_settings->get('line_numbering') == 1) {
        $html .= '<td style="text-align: center">' . $count . '</td>';
        $count++;
    }

    $blocks_query = db_query(
        "select b.*, f.name, f.entities_id, f.type as field_type,f.configuration as field_configuration from app_ext_items_export_templates_blocks b, app_fields f, app_entities e where  block_type='body_cell' and b.fields_id=f.id and b.templates_id = " . $template_info['id'] . " and b.parent_id = " . $parent_block_id . " and f.entities_id=e.id order by b.sort_order, b.id",
        false
    );
    while ($blocks = db_fetch_array($blocks_query)) {
        $html .= '<td style="text-align: center">' . $count . '</td>';
        $count++;
    }
    $html .= '</tr>';
}

//item
$html .= '<tr>';

if ($parent_block_settings->get('line_numbering') == 1) {
    $html .= '<td style="text-align: center">1</td>';
}

$blocks_query = db_query(
    "select b.*, f.name, f.entities_id, f.type as field_type,f.configuration as field_configuration from app_ext_export_selected_blocks b, app_fields f, app_entities e where  block_type='body_cell' and b.fields_id=f.id and b.templates_id = " . $template_info['id'] . " and b.parent_id = " . $parent_block_id . " and f.entities_id=e.id order by b.sort_order, b.id",
    false
);
while ($blocks = db_fetch_array($blocks_query)) {
    $settings = new settings($blocks['settings']);

    $html .= '<td style="text-align: ' . $settings->get('heading_alignment') . '" >*</td>';
}
$html .= '</tr>';


$html .= '
    </tbody>
    <tfoot>
';


//extra rows
$rows_query = db_query(
    "select b.* from app_ext_export_selected_blocks b where b.block_type='tfoot' and b.templates_id = " . $template_info['id'] . " and b.parent_id = " . $parent_block_id . " order by b.sort_order, b.id"
);
while ($rows = db_fetch_array($rows_query)) {
    $blocks_query = db_query(
        "select b.* from app_ext_export_selected_blocks b where b.templates_id = " . $template_info['id'] . " and b.parent_id = " . $rows['id'] . " order by b.sort_order, b.id"
    );

    if (db_num_rows($blocks_query)) {
        $html .= '<tr>';

        while ($blocks = db_fetch_array($blocks_query)) {
            $settings = new settings($blocks['settings']);

            //get field value if field is selected
            $cell_value = ($blocks['fields_id'] > 0 ? '*' : $settings->get('heading'));

            $cell_settings = 'style="text-align: ' . $settings->get('heading_alignment') . '" ' . (strlen(
                    $settings->get('colspan')
                ) ? 'colspan="' . $settings->get('colspan') . '"' : '');

            $html .= '<td ' . $cell_settings . '>' . $cell_value . '</td>';
        }

        $html .= '</tr>';
    }
}


$html .= '
    </tfoot>
    </table>
';
?>


<div class="panel panel-default margin-top-20">
    <div class="panel-heading">
        <h3 class="panel-title"><?php
            echo TEXT_EXT_PREVIEW ?></h3>
    </div>
    <div class="panel-body">
        <?php
        echo $html ?>
    </div>
</div>