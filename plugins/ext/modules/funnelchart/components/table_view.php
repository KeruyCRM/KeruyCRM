<?php

$totals = ['count' => 0];

//field fields heading
$thead = '';
if (strlen($reports['sum_by_field'])) {
    foreach (explode(',', $reports['sum_by_field']) as $id) {
        $field = db_find('app_fields', $id);
        $thead .= '<th>' . $field['name'] . '</th>';

        $totals['field_' . $id] = 0;
    }
}

//build table heading
$field = db_find('app_fields', $reports['group_by_field']);
$html = '	
	<div class="table-scrollable">	
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>' . $field['name'] . '</th>
					<th>' . TEXT_EXT_TOTAL . '</th>
					<th>' . TEXT_EXT_CONVERSION . '</th>
					<th>' . TEXT_EXT_FUNNEL . '</th>
					<th>' . tooltip_icon(TEXT_EXT_CONVERSION_STAGE_INFO, 'left') . TEXT_EXT_CONVERSION_STAGE . '</th>
					' . $thead . '
				</tr>
			</thead>
			<tbody>';

//build table body
foreach ($funnel_info_choices as $choices_id => $value) {
    //Hide zero values
    if ($value['count'] == 0 and $reports['hide_zero_values'] == 1) {
        continue;
    }

    //total count
    $totals['count'] += $value['count'];

    //conversion
    $conversion = ($value['count'] > 0 ? floor($value['count'] / $count_items * 100) : 0);

    //stage conversion
    if (!isset($previous_value)) {
        $previous_value = $value['count'];
        $conversion_stage = '';
    } else {
        if ($value['count'] > 0 and $previous_value > 0) {
            $conversion_stage = floor(($value['count'] / $previous_value * 100)) . '%';
        } else {
            $conversion_stage = '0%';
        }

        $previous_value = $value['count'];
    }

    //fields values and totals
    $tbody = '';
    if (strlen($reports['sum_by_field'])) {
        foreach (explode(',', $reports['sum_by_field']) as $id) {
            $tbody .= '<td>' . fieldtype_input_numeric::number_format(
                    $value['field_' . $id],
                    $app_fields_cache[$reports['entities_id']][$id]['configuration']
                ) . '</td>';

            $totals['field_' . $id] += $value['field_' . $id];
        }
    }

    $color = funnelchart::get_color_by_choice_id($choices_id, $reports['colors']);

    $background = ((isset($choices_backgrounds[$choices_id]) and !strlen(
            $color
        )) ? 'background: ' . $choices_backgrounds[$choices_id]['background'] : '');

    if (strlen($color)) {
        $background = 'background: ' . $color;
    }

    $html .= '
			<tr>
				<td><a href="#" onclick="return funnelchart_items_listin(\'' . addslashes(
            $funnel_choices[$choices_id]
        ) . '\',\'' . $reports['group_by_field'] . ':' . $choices_id . '\')">' . $funnel_choices[$choices_id] . '</a></td>
				<td>' . $value['count'] . '</td>
				<td>' . $conversion . '%</td>
				<td style="width: 300px; padding: 0;"> 
						<div class="funnel-table-bar" style="width: ' . $conversion . '%;' . $background . '"></div> 
				</td>
				<td>' . $conversion_stage . '</td>
				' . $tbody . '
			</tr>
			';
}

$tfoot = '';
if (strlen($reports['sum_by_field'])) {
    foreach (explode(',', $reports['sum_by_field']) as $id) {
        $tfoot .= '<td>' . fieldtype_input_numeric::number_format(
                $totals['field_' . $id],
                $app_fields_cache[$reports['entities_id']][$id]['configuration']
            ) . '</td>';
    }
}

$html .= '
		</tbody>
		<tfoot>
			<tr>
				<th></th>
				<th>' . $totals['count'] . '</th>
				<th></th>
				<th></th>
				<th></th>	
				' . $tfoot . '		
			</tr>
		</tfoot>
		</table>
	</div>					
		';

echo $html;