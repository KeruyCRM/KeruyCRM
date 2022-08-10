<?php

namespace Tools\Pages;

class Dashboard_pages
{
    public $has_pages;

    public function __construct()
    {
        $this->has_pages = false;
    }

    public static function get_color_choices()
    {
        return [
            'default' => \K::$fw->TEXT_DEFAULT,
            'warning' => \K::$fw->TEXT_ALERT_WARNING,
            'danger' => \K::$fw->TEXT_ALERT_DANGER,
            'success' => \K::$fw->TEXT_ALERT_SUCCESS,
            'info' => \K::$fw->TEXT_ALERT_INFO,
        ];
    }

    public static function get_color_by_name($name)
    {
        $types = self::get_color_choices();

        return (isset($types[$name]) ? $types[$name] : '');
    }

    public function render_info_blocks()
    {
        $html_sections = '';

        $sections_choices = [];
        $sections_choices[] = ['id' => 0, 'grid' => 4, 'name' => \K::$fw->app_user['name']];
        //$sections_query = db_query("select * from app_dashboard_pages_sections order by sort_order, name");
        $sections_query = \K::model()->db_fetch(
            'app_dashboard_pages_sections',
            [],
            ['order' => 'sort_order,name'],
            'id,grid,name'
        );

        //while ($sections = db_fetch_array($sections_query)) {
        foreach ($sections_query as $sections) {
            $sections_choices[] = ['id' => $sections['id'], 'grid' => $sections['grid'], 'name' => $sections['name']];
        }

        foreach ($sections_choices as $section) {
            $html = '';
            $item = '';
            /*$pages_query = db_query(
                "select * from app_dashboard_pages where sections_id='" . $section['id'] . "' and type='info_block' and find_in_set(" . \K::$fw->app_user['group_id'] . ", users_groups) and is_active=1 order by sort_order, name"
            );*/

            $pages_query = \K::model()->db_fetch('app_dashboard_pages', [
                'sections_id = ? and type = ? and find_in_set( ? , users_groups) and is_active = 1',
                $section['id'],
                'info_block',
                \K::$fw->app_user['group_id']
            ], ['order' => 'sort_order,name']);

            if (count($pages_query)) {
                $item = \K::model()->db_query_exec_one(
                    "select e.* " .
                    \Tools\FieldsTypes\Fieldtype_formula::prepare_query_select(
                        1,
                        ''
                    ) . " from app_entity_1 e where e.id = ? and e.field_5 = 1", [\K::$fw->app_user['id']]
                );
                /*if (isset($item_query[0])) {
                    $item = $item_query[0];
                }*/
            }

            $count = 1;

            //while ($pages = db_fetch_array($pages_query)) {
            foreach ($pages_query as $pages) {
                $pages = $pages->cast();

                $fields_html = '';
                $count_fields = 0;

                if (strlen($pages['users_fields'])) {
                    $fields_access_schema = \Models\Main\Users\Users::get_fields_access_schema(
                        1,
                        \K::$fw->app_user['group_id']
                    );

                    $fields_html = '<table class="table">';

                    /*$fields_query = db_query(
                        "select id, type, name, configuration, entities_id from app_fields where id in (" . $pages['users_fields'] . ") order by field(id," . $pages['users_fields'] . ")"
                    );*/

                    $fields_query = \K::model()->db_fetch(
                        'app_fields',
                        [
                            'id in (?)',
                            $pages['users_fields']
                        ],
                        ['order' => 'field(id,' . $pages['users_fields'] . ')'],
                        'id,type,name,configuration,entities_id'
                    );

                    //while ($field = db_fetch_array($fields_query)) {
                    foreach ($fields_query as $field) {
                        $field = $field->cast();
                        //prepare field value
                        $value =  \Models\Main\Items\Items::prepare_field_value_by_type($field, $item);

                        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

                        //hide if empty
                        if (($cfg->get('hide_field_if_empty') == 1 and strlen($value) == 0) or ($cfg->get(
                                    'hide_field_if_empty'
                                ) == 1 and in_array(
                                    $field['type'],
                                    [
                                        'fieldtype_dropdown',
                                        'fieldtype_radioboxes',
                                        'fieldtype_created_by',
                                        'fieldtype_input_date',
                                        'fieldtype_input_datetime'
                                    ]
                                ) and $value == 0)) {
                            continue;
                        }

                        //hide if date updated empty
                        if ($field['type'] == 'fieldtype_date_updated' and $value == 0) {
                            continue;
                        }

                        //check field access
                        if (isset($fields_access_schema[$field['id']])) {
                            if ($fields_access_schema[$field['id']] == 'hide') {
                                continue;
                            }
                        }

                        if ($cfg->get('hide_field_if_empty') == 1 and \Models\Main\Fields_types::is_empty_value(
                                $value,
                                $field['type']
                            )) {
                            continue;
                        }

                        $output_options = [
                            'class' => $field['type'],
                            'value' => $value,
                            'field' => $field,
                            'item' => $item,
                            'is_listing' => true,
                            'display_user_photo' => true,
                            'path' => '1-' . \K::$fw->app_user['id']
                        ];

                        $fields_html .= '
								<tr>
									<th>' . ($field_name = \Models\Main\Fields_types::get_option(
                                $field['type'],
                                'name',
                                $field['name']
                            )) . '</th>
									<td>' . ($field_value = \Models\Main\Fields_types::output($output_options)) . '</td>
								<tr>
								';

                        $count_fields++;
                    }

                    $fields_html .= '</table>';
                }

                //get count col
                switch ($section['grid']) {
                    case '6':
                        $count_col = 2;
                        break;
                    case '4':
                        $count_col = 3;
                        break;
                    case '3':
                        $count_col = 4;
                        break;
                    default:
                        $count_col = 3;
                        break;
                }

                if ($count_fields == 1 and !strlen($pages['description']) and !strlen($pages['name'])) {
                    $html .= '
					<div class="col-md-' . $section['grid'] . '">
						<div class="stats-overview stat-block stats-' . $pages['color'] . '">
						 	<table width="100%">
								<tr>
							' . (strlen(
                            $pages['icon']
                        ) ? '<td width="32"><div class="icon">' . \Helpers\App::app_render_icon(
                                $pages['icon']
                            ) . '</div></td>' : '') . '
									<td>
										
										<div class="display stat ok huge">
											<div class="percent float-left">
												' . $field_value . '
											</div>
										</div>
										
										<br>
												
										<div class="details">
											<div class="title">
												 ' . $field_name . '
											</div>
											<div class="numbers">
					
											</div>
										</div>
									</td>
								</tr>
							</table>
							
						</div>
					</div>			
                    ';

                    if ($count / $count_col == floor($count / $count_col)) {
                        $html .= '</div><div class="row users-info-blocks">';
                    }

                    $count++;
                } elseif ($count_fields > 0 or strlen($pages['description'])) {
                    if (strlen($pages['description'])) {
                        $text_pattern = new \Tools\FieldsTypes\Fieldtype_text_pattern();
                        $pages['description'] = $text_pattern->output_singe_text(
                            $pages['description'],
                            1,
                            \K::$fw->app_user['fields']
                        );
                    }

                    $html .= '
							<div class="col-md-' . $section['grid'] . '">
							<div class="panel panel-' . $pages['color'] . '">
							  ' . (strlen($pages['name']) ? '<div class="panel-heading">' . (strlen(
                                $pages['icon']
                            ) ? \Helpers\App::app_render_icon(
                                    $pages['icon']
                                ) . ' ' : '') . $pages['name'] . '</div>' : '') . '
							  <div class="panel-body">
							    ' . (strlen($pages['description']) ? '<p>' . $pages['description'] . '</p>' : '') . '
							    ' . $fields_html . '		
							  </div>
							</div>
							</div>
							';

                    if ($count / $count_col == floor($count / $count_col)) {
                        $html .= '</div><div class="row users-info-blocks">';
                    }

                    $count++;
                }
            }

            if (strlen($html)) {
                $html_sections .= '
						<h3 class="page-title">' . str_replace(
                        '[user_name]',
                        \K::$fw->app_user['name'],
                        $section['name']
                    ) . '</h3>
						<div class="row users-info-blocks users-info-blocks-content">' . $html . '</div>		
						';

                $this->has_pages = true;
            }
        }

        return $html_sections;
    }

    public function render_info_pages()
    {
        $html = '';

        /*$pages_query = db_query(
            "select * from app_dashboard_pages where type='page' and find_in_set(" . \K::$fw->app_user['group_id'] . ", users_groups) and is_active=1 order by sort_order, name"
        );*/

        $pages_query = \K::model()->db_fetch('app_dashboard_pages', [
            'type = ? and FIND_IN_SET( ? ,users_groups) and is_active = 1',
            'page',
            \K::$fw->app_user['group_id']
        ], ['order' => 'sort_order,name']);

        //while ($pages = db_fetch_array($pages_query)) {
        foreach ($pages_query as $pages) {
            $pages = $pages->cast();

            if ($pages['color'] == 'default') {
                $html .= '
						<h3 class="page-title">' . (strlen($pages['icon']) ? \Helpers\App::app_render_icon(
                            $pages['icon']
                        ) . ' ' : '') . $pages['name'] . '</h3>
						<p>' . $pages['description'] . '</p>
						';
            } else {
                $html .= '
						<div class="alert alert-' . $pages['color'] . '">
							<h3 class="page-title">' . (strlen($pages['icon']) ? \Helpers\App::app_render_icon(
                            $pages['icon']
                        ) . ' ' : '') . $pages['name'] . '</h3>
							<p>' . $pages['description'] . '</p>
						</div>		
						';
            }
        }

        if (strlen($html)) {
            $this->has_pages = true;
        }

        return $html;
    }

    public static function get_section_grid_choices()
    {
        $choices = [];
        $choices[6] = '2 ' . \K::$fw->TEXT_COLUMNS;
        $choices[4] = '3 ' . \K::$fw->TEXT_COLUMNS;
        $choices[3] = '4 ' . \K::$fw->TEXT_COLUMNS;

        return $choices;
    }

    public static function get_section_grid_name($v)
    {
        $choices = self::get_section_grid_choices();

        return ($choices[$v] ?? '');
    }

    public static function get_section_choices()
    {
        $choices = [];
        $choices[] = \K::$fw->TEXT_DEFAULT;
        $sections_query = db_query("select * from app_dashboard_pages_sections order by sort_order, name");
        while ($sections = db_fetch_array($sections_query)) {
            $choices[$sections['id']] = $sections['name'];
        }

        return $choices;
    }
}