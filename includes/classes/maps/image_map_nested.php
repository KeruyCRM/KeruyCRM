<?php

class image_map_nested
{
    public $map_filename;
    public $field_id;

    function __construct($field_id)
    {
        $this->field_id = $field_id;
        $this->data = [];
        $this->map_filename = '';
        $this->background = false;
    }

    function set_path($path)
    {
        $this->path = $path;
        $path_info = items::parse_path($this->path);
        $this->item_id = $path_info['item_id'];
    }

    function set_filename($filename)
    {
        $this->map_filename = urldecode($filename);
    }

    function get_data()
    {
        $field_query = db_query(
            "select id, name, entities_id, configuration from app_fields where id='" . $this->field_id . "'"
        );
        if ($field = db_fetch_array($field_query)) {
            $this->data['code'] = 1;

            $this->get_map($field);

            //$this->get_regions_labels($map_info);			

            $this->get_markers($field);

            $this->get_html($field);
        }

        return json_encode($this->data);
    }

    function get_map($field)
    {
        $cfg = $this->field_cfg = new fields_types_cfg($field['configuration']);

        //use report scale        
        $this->scale = $cfg->get('scale');


        //set fields in popup
        if (is_array($cfg->get('fields_in_popup'))) {
            $this->fields_in_popup = $cfg->get('fields_in_popup');
        }

        //set background
        if (strlen($cfg->get('background'))) {
            $this->background = $cfg->get('background');
        }

        $filename = DIR_WS_UPLOADS . 'maps_nested/' . $field['id'] . '/' . $this->item_id . '/' . $this->map_filename;

        $width = $height = 0;
        if (is_file($filename)) {
            $data = getimagesize($filename);

            $width = $data[0];
            $height = $data[1];
        }

        $this->data['data']['map'] = [
            'id' => $field['id'],
            'name' => $field['name'],
            'item_id' => $this->item_id,
            'enabled' => 1,
            'showLegend' => 1,
            'zoom' => $this->scale,
            'mapImage' => [
                'width' => $width,
                'height' => $height
            ]
        ];

        $this->data['data']['regions'] = null;
        $this->data['data']['labels'] = null;
    }

    function get_markers($field)
    {
        global $sql_query_having;

        $this->data['data']['markers'] = null;

        $markers = [];


        $entity_id = $this->field_cfg->get('entity_id');

        $sql_query_select = '';
        $listing_sql_query = '';
        $select_sql_query = '';
        $listing_sql_query_having = '';
        $sql_query_having = [];


        //prepare fields sum for formulas
        $sql_query_select = fieldtype_formula::prepare_query_select($entity_id, '');

        $fiters_reports_id = reports::get_reports_id_by_type($entity_id, 'entityfield' . $field['id']);

        if ($fiters_reports_id) {
            //add filters query
            $listing_sql_query = reports::add_filters_query($fiters_reports_id, $listing_sql_query);

            //prepare having query for formula fields
            if (isset($sql_query_having[$entity_id])) {
                $listing_sql_query .= reports::prepare_filters_having_query($sql_query_having[$entity_id]);
            }
        }

        //add access query
        $listing_sql_query = items::add_access_query($entity_id, $listing_sql_query);


        $listing_sql = "select e.* " . $sql_query_select . " from app_entity_" . $entity_id . " e  where e.parent_item_id='{$this->item_id}' " . $listing_sql_query;

        $items_query = db_query($listing_sql);


        while ($items = db_fetch_array($items_query)) {
            $x = $y = 0;

            //get possition 
            $marker_info_query = db_query(
                "select * from app_image_map_markers_nested where entities_id='" . $entity_id . "' and items_id = '" . $items['id'] . "' and fields_id='" . $this->field_id . "'"
            );
            if ($marker_info = db_fetch_array($marker_info_query)) {
                $x = $marker_info['x'];
                $y = $marker_info['y'];
            }

            //prepare bakcground by choice
            $typeCssName = '';
            $icon = 'images/map_marker.png';
            if ($this->background) {
                if (isset($items['field_' . $this->background])) {
                    $typeCssName = 'type-' . $items['field_' . $this->background];

                    //prepare icon 
                    if (strlen(
                        $icon_filename = $this->field_cfg->get('icon_' . $items['field_' . $this->background])
                    )) {
                        if (is_file($icon_filepath = DIR_WS_UPLOADS . 'icons/' . $icon_filename)) {
                            $icon = $icon_filepath;
                        }
                    }
                }
            }

            $html = '
                    <div class="cfm-inner">
                            <div class="cfm-title-params">
                                    <span class="cfm-icon"><img src="' . $icon . '" width="24" height="24" /></span>
                                    <span class="cfm-title"><a href="' . url_for(
                    'items/info',
                    'path=' . $entity_id . '-' . $items['id']
                ) . '" target="_new">' . items::get_heading_field($entity_id, $items['id'], $items) . '</a></span>
                            </div>
                            ' . $this->get_fields_in_popup($items) . '												
                    </div>';

            $html = str_replace(["\r\n", "\n", "\r"], '', $html);

            //prepare marker
            $markers[] = [
                'id' => $items['id'],
                'x' => $x,
                'y' => $y,
                'typeCssName' => $typeCssName,
                'html' => $html,
            ];


            if ($this->background and isset($items['field_' . $this->background])) {
                $this->choices_in_legend[$items['field_' . $this->background]] = $items['field_' . $this->background];
            }
        }

        if (count($markers)) {
            $this->data['data']['markers'] = $markers;
        }
    }

    function get_fields_in_popup($items)
    {
        $html = '';

        $fields_in_popup = is_array($this->field_cfg->get('fields_in_popup')) ? $this->field_cfg->get(
            'fields_in_popup'
        ) : [];

        if (count($fields_in_popup)) {
            $html .= '
                <table class="cfm-params">
                        <tbody>';


            foreach ($fields_in_popup as $fields_id) {
                $field_query = db_query("select * from app_fields where id='" . $fields_id . "'");
                if ($field = db_fetch_array($field_query)) {
                    //prepare field value
                    $value = items::prepare_field_value_by_type($field, $items);

                    $output_options = [
                        'class' => $field['type'],
                        'value' => $value,
                        'field' => $field,
                        'item' => $items,
                        'is_listing' => true,
                        'path' => ''
                    ];

                    $value = trim(fields_types::output($output_options));

                    if (strlen(strip_tags($value)) > 255 and in_array(
                            $field['type'],
                            ['fieldtype_textarea_wysiwyg', 'fieldtype_textarea']
                        )) {
                        $value = substr(strip_tags($value), 0, 255) . '...';
                    }

                    if (strlen($value)) {
                        $html .= '
                                <tr>
                                        <td valign="top" style="padding-right: 7px;">' . fields_types::get_option(
                                $field['type'],
                                'name',
                                $field['name']
                            ) . '</td>
                                        <td valign="top">' . $value . '</td>
                                </tr>';
                    }
                }
            }

            $html .= '
                    </tbody>
            </table>
            ';
        }

        return $html;
    }

    function get_html($field)
    {
        $legend = '';
        $maps = '';
        $map_choices = '';

        //prepare legend
        if ($this->background) {
            $choices_query = db_query(
                "select * from app_fields_choices where fields_id = '" . db_input(
                    $this->background
                ) . "' order by sort_order, name"
            );
            while ($choices = db_fetch_array($choices_query)) {
                if (in_array($choices['id'], $this->choices_in_legend)) {
                    $legend .= '<li><div class="cfm-marker cfm-marker-type-' . $choices['id'] . '"></div>&nbsp;&nbsp;' . $choices['name'] . '</li>';
                }
            }
        }

        $map_choices = '<li class="dropdown"><a href="#" onClick="return false">' . $field['name'] . '</a></li>';

        $breadcrumb = '
				<ul class="nav navbar-nav pull-right">
					' . $map_choices . '
					' . $maps . '
				</ul>
				';

        $this->data['data']['viewHtml'] = [
            'breadcrumb' => $breadcrumb,
            'legend' => $legend,
        ];
    }

    static function save_markers()
    {
        global $app_path;

        $map_id = _get::int('map_id');

        $field_query = db_query("select configuration from app_fields where id={$map_id}");
        $field = db_fetch_array($field_query);

        $cfg = new settings($field['configuration']);

        $entity_id = $cfg->get('entity_id');

        foreach ($_POST['markers'] as $marker) {
            $sql_data = [
                'x' => $marker['x'],
                'y' => $marker['y']
            ];

            $marker_info_query = db_query(
                "select * from app_image_map_markers_nested where entities_id='" . $entity_id . "' and items_id = '" . $marker['id'] . "' and fields_id='" . $map_id . "'"
            );
            if ($marker_info = db_fetch_array($marker_info_query)) {
                db_perform('app_image_map_markers', $sql_data, 'update', "id='" . db_input($marker_info['id']) . "'");
            } else {
                $sql_data['entities_id'] = $entity_id;
                $sql_data['items_id'] = $marker['id'];
                $sql_data['fields_id'] = $map_id;

                db_perform('app_image_map_markers_nested', $sql_data);
            }
        }
    }

    static function delete_markers($entities_id, $items_id)
    {
        db_query(
            "delete from app_image_map_markers_nested where entities_id='" . $entities_id . "' and items_id='" . $items_id . "'"
        );
    }

    static function delete_by_fields_id($fields_id)
    {
        db_query("delete from app_image_map_markers_nested where fields_id={$fields_id}");
    }

}
