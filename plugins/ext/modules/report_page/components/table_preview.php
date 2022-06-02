<?php

$parent_block_id = _GET('block_id');
$parent_block_settings = new settings($block_info['settings']);

$html = '
    <table class="table-block-preview" border="1" cellpadding="4" style="min-width: 30%">
     <thead>
';

//extra rows
$rows_query = db_query(
    "select b.* from app_ext_report_page_blocks b where b.block_type='thead' and b.report_id = " . $report_page['id'] . " and b.parent_id = " . $parent_block_id . " order by b.sort_order, b.id"
);
while ($rows = db_fetch_array($rows_query)) {
    $blocks_query = db_query(
        "select b.* from app_ext_report_page_blocks b where b.report_id = " . $report_page['id'] . " and b.parent_id = " . $rows['id'] . " order by b.sort_order, b.id"
    );

    if (db_num_rows($blocks_query)) {
        $html .= '<tr>';

        while ($blocks = db_fetch_array($blocks_query)) {
            $settings = new settings($blocks['settings']);

            $cell_value = $settings->get('heading');
            $cell_settings = $settings->get('tag_td_attributes');

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
    "select b.*,f.type as field_type, f.name as field_name, f.entities_id as field_entity_id from app_ext_report_page_blocks b left join app_fields f on b.field_id=f.id  where block_type='body_cell'  and b.parent_id = " . $parent_block_id . " order by b.sort_order, b.id"
);
while ($blocks = db_fetch_array($blocks_query)) {
    $settings = new settings($blocks['settings']);

    $cell_settings = '';

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
        "select b.*,f.type as field_type, f.name as field_name, f.entities_id as field_entity_id from app_ext_report_page_blocks b left join app_fields f on b.field_id=f.id  where block_type='body_cell'  and b.parent_id = " . $parent_block_id . " order by b.sort_order, b.id"
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
    "select b.*,f.type as field_type, f.name as field_name, f.entities_id as field_entity_id from app_ext_report_page_blocks b left join app_fields f on b.field_id=f.id  where block_type='body_cell'  and b.parent_id = " . $parent_block_id . " order by b.sort_order, b.id"
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
    "select b.* from app_ext_report_page_blocks b where b.block_type='tfoot' and b.report_id = " . $report_page['id'] . " and b.parent_id = " . $parent_block_id . " order by b.sort_order, b.id"
);
while ($rows = db_fetch_array($rows_query)) {
    $blocks_query = db_query(
        "select b.*,f.type as field_type, f.name as field_name, f.entities_id as field_entity_id from app_ext_report_page_blocks b left join app_fields f on b.field_id=f.id where b.report_id = " . $report_page['id'] . " and b.parent_id = " . $rows['id'] . " order by b.sort_order, b.id"
    );

    if (db_num_rows($blocks_query)) {
        $html .= '<tr>';

        while ($blocks = db_fetch_array($blocks_query)) {
            $settings = new settings($blocks['settings']);

            $cell_value = $settings->get('heading');
            $cell_settings = $settings->get('tag_td_attributes');

            switch ($settings->get('value_type')) {
                case 'field':
                    $cell_value = fields_types::get_option($blocks['field_type'], 'name', $blocks['field_name']);
                    break;
                case 'php_code':
                    $cell_value = !strlen($cell_value) ? TEXT_PHP_CODE : $cell_value;
                    break;
            }

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