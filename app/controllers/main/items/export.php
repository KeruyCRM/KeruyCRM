<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Export extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();

        if (!\Models\Main\Users\Users::has_access('export_selected')) {
            \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
        }
    }

    public function index()
    {
        if (!isset(\K::$fw->app_selected_items[\K::$fw->GET['reports_id']])) {
            \K::$fw->app_selected_items[\K::$fw->GET['reports_id']] = [];
        }

        if (count(\K::$fw->app_selected_items[\K::$fw->GET['reports_id']]) > 0) {
            \K::$fw->fields_access_schema = \Models\Main\Users\Users::get_fields_access_schema(
                \K::$fw->current_entity_id,
                \K::$fw->app_user['group_id']
            );

            $attachments_fields = [];
            $typeIn = \K::model()->quoteToString(\Models\Main\Fields_types::get_attachments_types());

            $fields_query = \K::model()->db_query_exec(
                "select f.* from app_fields f, app_forms_tabs t where f.forms_tabs_id = t.id and f.type in (" . $typeIn . ") and f.entities_id = ? order by t.sort_order, f.sort_order, f.name",
                \K::$fw->current_entity_id,
                'app_field,app_forms_tabs'
            );

            //while ($v = db_fetch_array($fields_query)) {
            foreach ($fields_query as $v) {
                if (isset($fields_access_schema[$v['id']])) {
                    if ($fields_access_schema[$v['id']] == 'hide') {
                        continue;
                    }
                }

                $attachments_fields[] = '<div><label>' . \Helpers\Html::input_checkbox_tag(
                        'fields[]',
                        $v['id'],
                        [
                            'id' => 'fields_' . $v['id'],
                            'class' => 'export_fields export_fields_' . $v['id'],
                            'checked' => 'checked'
                        ]
                    ) . ' ' . \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']) . '</label></div>';
            }

            \K::$fw->attachments_fields = $attachments_fields;

            /*\K::$fw->tabs_query = db_fetch_all(
                'app_forms_tabs',
                "entities_id='" . db_input(\K::$fw->current_entity_id) . "' order by  sort_order, name"
            );*/

            \K::$fw->tabs_query = \K::model()->db_fetch('app_forms_tabs', [
                'entities_id = ?',
                \K::$fw->current_entity_id
            ], ['order' => 'sort_order,name']);

            \K::$fw->current_entity_info = \K::model()->db_find('app_entities', \K::$fw->current_entity_id);

            \K::$fw->choices = ['xlsx' => '.xlsx', 'csv' => '.csv', 'txt' => '.txt'];

            \K::$fw->count_selected_text = sprintf(
                \K::$fw->TEXT_SELECTED_RECORDS,
                count(\K::$fw->app_selected_items[\K::$fw->GET['reports_id']])
            );
        }

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'export.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }

    public function delete_templates()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->POST['id'])) {
            \K::model()->db_delete_row('app_items_export_templates', \K::$fw->POST['id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function set_default_templates()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->POST['id'])) {
            /*db_query(
                "update app_items_export_templates set is_default=0 where users_id='" . db_input(
                    \K::$fw->app_user['id']
                ) . "' and entities_id='" . db_input(\K::$fw->current_entity_id) . "'"
            );*/
            /*db_query(
                "update app_items_export_templates set is_default=1 where users_id='" . db_input(
                    \K::$fw->app_user['id']
                ) . "' and id='" . \K::$fw->POST['id'] . "'"
            );*/

            \K::model()->begin();

            \K::model()->db_update('app_items_export_templates', ['is_default' => 0], [
                'users_id = ? and entities_id = ?',
                \K::$fw->app_user['id'],
                \K::$fw->current_entity_id
            ]);

            //set default for current row
            \K::model()->db_update('app_items_export_templates', ['is_default' => 1], [
                'users_id = ? and id = ?',
                \K::$fw->app_user['id'],
                \K::$fw->POST['id']
            ]);

            \K::model()->commit();
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function get_default_templates()
    {
        /*$templates_info_query = db_query(
            "select * from app_items_export_templates where entities_id='" . db_input(
                \K::$fw->current_entity_id
            ) . "' and users_id='" . db_input(\K::$fw->app_user['id']) . "' and is_default=1"
        );*/

        $templates_info = \K::model()->db_fetch_one('app_items_export_templates', [
            'entities_id = ? and users_id = ? and is_default = 1',
            \K::$fw->current_entity_id,
            \K::$fw->app_user['id']
        ], [], 'id,name,templates_fields');

        if ($templates_info) {
            echo \Helpers\App::app_json_encode(
                [$templates_info['templates_fields'], $templates_info['id'], $templates_info['name']]
            );
        }
    }

    public function update_templates_fields()
    {
        if (\K::$fw->VERB == 'POST') {
            $sql_data_array = [
                'templates_fields' => \K::$fw->POST['fields_list'],
            ];

            \K::model()->db_update('app_items_export_templates', $sql_data_array, [
                    'users_id = ? and id = ?',
                    \K::$fw->app_user['id'],
                    \K::$fw->POST['id']
                ]
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function update_templates_name()
    {
        if (\K::$fw->VERB == 'POST') {
            $sql_data_array = [
                'name' => \K::model()->db_prepare_input(\K::$fw->POST['name']),
            ];

            \K::model()->db_update('app_items_export_templates', $sql_data_array, [
                    'users_id = ? and id = ?',
                    \K::$fw->app_user['id'],
                    \K::$fw->POST['id']
                ]
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function save_templates()
    {
        if (\K::$fw->VERB == 'POST') {
            $templates_name = \K::model()->db_prepare_input(\K::$fw->POST['templates_name']);
            $export_fields_list = \K::model()->db_prepare_input(\K::$fw->POST['export_fields_list']);

            /*$check_query = db_query(
                "select count(*) as total from app_items_export_templates where entities_id='" . db_input(
                    \K::$fw->current_entity_id
                ) . "' and users_id='" . db_input(\K::$fw->app_user['id']) . "' and name='" . db_input(
                    $templates_name
                ) . "'"
            );
            $check = db_fetch_array($check_query);*/

            $check = \K::model()->db_fetch_one('app_items_export_templates', [
                'entities_id = ? and users_id = ? and name = ?',
                \K::$fw->current_entity_id,
                \K::$fw->app_user['id'],
                $templates_name
            ], [], null, ['total' => 'count(*)']);

            /*$count_query = db_query(
                "select count(*) as total from app_items_export_templates where entities_id='" . db_input(
                    \K::$fw->current_entity_id
                ) . "' and users_id='" . db_input(\K::$fw->app_user['id']) . "'"
            );
            $count = db_fetch_array($count_query);*/

            if ($check['total'] == 0) {
                $count = \K::model()->db_fetch_one('app_items_export_templates', [
                    'entities_id = ? and users_id = ?',
                    \K::$fw->current_entity_id,
                    \K::$fw->app_user['id']
                ], [], null, ['total' => 'count(*)']);

                $sql_data_array = [
                    'name' => $templates_name,
                    'templates_fields' => $export_fields_list,
                    'entities_id' => \K::$fw->current_entity_id,
                    'users_id' => \K::$fw->app_user['id'],
                    'is_default' => ($count['total'] == 0 ? 1 : 0)
                ];

                \K::model()->db_perform('app_items_export_templates', $sql_data_array);
            } else {
                echo sprintf(\K::$fw->TEXT_TEMPLATE_ALREADY_EXIST, $templates_name);
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function get_templates_button()
    {
        $html_list = '';

        /*$check_query = db_query(
            "select count(*) as total from app_items_export_templates where entities_id='" . db_input(
                \K::$fw->current_entity_id
            ) . "' and users_id='" . db_input(\K::$fw->app_user['id']) . "'"
        );
        $check = db_fetch_array($check_query);*/

        $templates_query = \K::model()->db_fetch('app_items_export_templates', [
            'entities_id = ? and users_id = ?',
            \K::$fw->current_entity_id,
            \K::$fw->app_user['id']
        ], [], 'id,name,templates_fields');

        if (count($templates_query)) {
            /*$templates_query = db_query(
                "select * from app_items_export_templates where entities_id='" . db_input(
                    \K::$fw->current_entity_id
                ) . "' and users_id='" . db_input(\K::$fw->app_user['id']) . "'"
            );*/

            //while ($templates = db_fetch_array($templates_query)) {
            foreach ($templates_query as $templates) {
                $templates = $templates->cast();

                $html_list .= '<li><a href="javascript: use_items_export_template(\'' . $templates['templates_fields'] . '\',' . $templates['id'] . ',\'' . addslashes(
                        $templates['name']
                    ) . '\')">' . $templates['name'] . '</a></li>';
            }
        } else {
            $html_list .= '<li><a href="#"><i>' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</i></a></li>';
        }

        $html_list .= '<li class="divider"></li><li><a href="javascript: open_my_templates_tab()">' . \K::$fw->TEXT_ADD_NEW_TEMPLATE . '</a></li>';

        $html = '
			<div class="btn-group">
				<button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown">' . \K::$fw->TEXT_SELECT_TEMPLATE . ' <i class="fa fa-angle-down"></i></button>
				<ul class="dropdown-menu" role="menu">
					' . $html_list . '
				</ul>
			</div>
			
			<script>
				$(\'[data-hover="dropdown"]\').dropdownHover();
			</script>				
		  ';

        echo $html;
    }

    public function get_templates()
    {
        $html = '
				<table class="table">
					<tr>
							<th>' . \K::$fw->TEXT_NAME . '</th>							
							<th style="text-align: center;">' . \K::$fw->TEXT_IS_DEFAULT . '</th>
							<th></th>
					</tr>';

        /*$check_query = db_query(
            "select count(*) as total from app_items_export_templates where entities_id='" . db_input(
                \K::$fw->current_entity_id
            ) . "' and users_id='" . db_input(\K::$fw->app_user['id']) . "'"
        );
        $check = db_fetch_array($check_query);*/

        $templates_query = \K::model()->db_fetch('app_items_export_templates', [
            'entities_id = ? and users_id = ?',
            \K::$fw->current_entity_id,
            \K::$fw->app_user['id']
        ], [], 'id,name,is_default');

        if (count($templates_query)) {
            /*$templates_query = db_query(
                "select * from app_items_export_templates where entities_id='" . db_input(
                    \K::$fw->current_entity_id
                ) . "' and users_id='" . db_input(\K::$fw->app_user['id']) . "'"
            );*/
            //while ($templates = db_fetch_array($templates_query)) {
            foreach ($templates_query as $templates) {
                $templates = $templates->cast();

                $html .= '
						<tr class="templates-row-' . $templates['id'] . '">
							<td style="padding-right: 15px;">' . \Helpers\Html::input_tag(
                        'export_template_name[]',
                        $templates['name'],
                        [
                            'class' => 'form-control',
                            'onKeyUp' => 'update_items_export_templates_name(' . $templates['id'] . ',this.value)'
                        ]
                    ) . '</td>							
							<td align="center">' . \Helpers\Html::input_radiobox_tag(
                        'is_default_template',
                        1,
                        ['checked' => $templates['is_default'], 'data-id' => $templates['id']]
                    ) . '</td>
							<td><button onClick="delete_items_export_templates(' . $templates['id'] . ')" class="btn btn-default" type="button"><i class="fa fa-trash-o" title="' . addslashes(
                        \K::$fw->TEXT_BUTTON_DELETE
                    ) . '"></i></button></td>
						</tr>
					';
            }
        } else {
            $html .= '
					<tr>
						<td colspan="4">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td>
					</tr>
				';
        }

        $html .= '</table>';

        echo $html;
    }

    public function print()
    {
        if (\K::$fw->VERB == 'POST') {
            if (!isset(\K::$fw->app_selected_items[\K::$fw->POST['reports_id']])) {
                \K::$fw->app_selected_items[\K::$fw->POST['reports_id']] = [];
            }

            if (count(
                    \K::$fw->app_selected_items[\K::$fw->POST['reports_id']]
                ) > 0 and isset(\K::$fw->POST['fields'])) {
                $current_entity_info = \K::model()->db_find('app_entities', \K::$fw->current_entity_id);

                $listing_fields = [];
                $export = '					
					<table class="table table-bordered" style="width: auto">
						<thead>
					';

                $idIn = \K::model()->quoteToString(\K::$fw->POST['fields'], \PDO::PARAM_INT);

                //adding reserved fields
                $fields_query = \K::model()->db_query_exec(
                    "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in ('fieldtype_action') and f.id in (" . $idIn . ") and f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
                    \K::$fw->current_entity_id,
                    'app_fields,app_forms_tabs'
                );

                //while ($fields = db_fetch_array($fields_query)) {
                foreach ($fields_query as $fields) {
                    if ($fields['type'] == 'fieldtype_dropdown_multilevel') {
                        $export .= \Tools\FieldsTypes\Fieldtype_dropdown_multilevel::output_listing_heading($fields);
                    } else {
                        $export .= '<th><div>' . \Models\Main\Fields_types::get_option(
                                $fields['type'],
                                'name',
                                $fields['name']
                            ) . '</div></th>';
                    }

                    $listing_fields[] = $fields;
                }

                //adding item url
                if (isset(\K::$fw->POST['export_url'])) {
                    $export .= '<th><div>' . \K::$fw->TEXT_URL_HEADING . '</div></th>';
                }

                $export .= '
				    </tr>
				  </thead>
				  <tbody>
				';

                $items_query = $this->_getDb_query();

                //while ($item = db_fetch_array($items_query)) {
                foreach ($items_query as $item) {
                    $export .= '<tr>';
                    $row = [];

                    $path_info_in_report = [];

                    if ($current_entity_info['parent_id'] > 0) {
                        $path_info_in_report = \Models\Main\Items\Items::get_path_info(
                            \K::$fw->current_entity_id,
                            $item['id']
                        );
                    }

                    foreach ($listing_fields as $field) {
                        $value = \Models\Main\Items\Items::prepare_field_value_by_type($field, $item);

                        $output_options = [
                            'class' => $field['type'],
                            'value' => $value,
                            'field' => $field,
                            'item' => $item,
                            'is_export' => true,
                            'is_print' => true,
                            'reports_id' => \K::$fw->POST['reports_id'],
                            'path' => ($path_info_in_report['full_path'] ?? \K::$fw->current_path),
                            'path_info' => $path_info_in_report
                        ];

                        if ($field['type'] == 'fieldtype_dropdown_multilevel') {
                            $export .= \Tools\FieldsTypes\Fieldtype_dropdown_multilevel::output_listing(
                                $output_options
                            );
                        } else {
                            $export .= '
    							<td>' . \Models\Main\Fields_types::output($output_options) . '</td>
    							';
                        }
                    }

                    if (isset(\K::$fw->POST['export_url'])) {
                        $export .= '
							<td>' . \Helpers\Urls::url_for(
                                'main/items/info',
                                'path=' . ($path_info_in_report['full_path'] ?? \K::$fw->current_path . '-' . $item['id'])
                            ) . '</td>
							';
                    }

                    $export .= '</tr>';
                }

                $export .= '
			  </tbody>';

                $export .= '
			    </table>					
					';

                $html = '
<!DOCTYPE html>
<html lang=' . \K::$fw->TEXT_APP_LANGUAGE_SHORT_CODE . '" dir="' . \K::$fw->TEXT_APP_LANGUAGE_TEXT_DIRECTION . '">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <link href="' . \K::$fw->DOMAIN . 'template/plugins/font-awesome/css/font-awesome.min.css?v=4.7.0" rel="stylesheet" type="text/css"/>
            <link href="' . \K::$fw->DOMAIN . 'template/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
            <link href="' . \K::$fw->DOMAIN . 'template/css/style-conquer.css?v=2" rel="stylesheet" type="text/css"/>
            <link href="' . \K::$fw->DOMAIN . 'template/css/style.css?v=2" rel="stylesheet" type="text/css"/>
            <link href="' . \K::$fw->DOMAIN . 'template/css/style-responsive.css?v=2" rel="stylesheet" type="text/css"/>
            <link href="' . \K::$fw->DOMAIN . 'template/css/plugins.css" rel="stylesheet" type="text/css"/>
            <link rel="stylesheet" type="text/css" href="' . \K::$fw->DOMAIN . 'css/default.css?v=' . \K::$fw->PROJECT_VERSION . '"/>
            <script src="' . \K::$fw->DOMAIN . 'template/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
    </head>
    <body>
        ' . $export . '
        <script src="' . \K::$fw->DOMAIN . 'template/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script>
            window.print();
        </script>		
    </body>
</html>';

                echo $html;
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function export()
    {
        if (\K::$fw->VERB == 'POST') {
            if (!isset(\K::$fw->app_selected_items[\K::$fw->POST['reports_id']])) {
                \K::$fw->app_selected_items[\K::$fw->POST['reports_id']] = [];
            }

            if (count(
                    \K::$fw->app_selected_items[\K::$fw->POST['reports_id']]
                ) > 0 and isset(\K::$fw->POST['fields'])) {
                $current_entity_info = \K::model()->db_find('app_entities', \K::$fw->current_entity_id);

                $listing_fields = [];
                $export = [];
                $heading = [];

                $idIn = \K::model()->quoteToString(\K::$fw->POST['fields'], \PDO::PARAM_INT);

                //adding reserved fields
                $fields_query = \K::model()->db_query_exec(
                    "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in ('fieldtype_action') and f.id in (" . $idIn . ") and f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
                    \K::$fw->current_entity_id,
                    'app_fields,app_forms_tabs'
                );

                //while ($fields = db_fetch_array($fields_query)) {
                foreach ($fields_query as $fields) {
                    if ($fields['type'] == 'fieldtype_dropdown_multilevel') {
                        $heading = array_merge(
                            $heading,
                            \Tools\FieldsTypes\Fieldtype_dropdown_multilevel::output_listing_heading($fields, true)
                        );
                    } else {
                        $heading[] = \Models\Main\Fields_types::get_option($fields['type'], 'name', $fields['name']);
                    }

                    $listing_fields[] = $fields;
                }

                //adding item url
                if (isset(\K::$fw->POST['export_url'])) {
                    $heading[] = \K::$fw->TEXT_URL_HEADING;
                }

                $export[] = $heading;

                $items_query = $this->_getDb_query();

                //while ($item = db_fetch_array($items_query)) {
                foreach ($items_query as $item) {
                    $row = [];

                    $path_info_in_report = [];

                    if ($current_entity_info['parent_id'] > 0) {
                        $path_info_in_report = \Models\Main\Items\Items::get_path_info(
                            \K::$fw->current_entity_id,
                            $item['id']
                        );
                    }

                    foreach ($listing_fields as $field) {
                        //prepare field value
                        $value = \Models\Main\Items\Items::prepare_field_value_by_type($field, $item);

                        $output_options = [
                            'class' => $field['type'],
                            'value' => $value,
                            'field' => $field,
                            'item' => $item,
                            'is_export' => true,
                            'reports_id' => \K::$fw->POST['reports_id'],
                            'path' => ($path_info_in_report['full_path'] ?? \K::$fw->current_path),
                            'path_info' => $path_info_in_report
                        ];

                        if ($field['type'] == 'fieldtype_dropdown_multilevel') {
                            $row = array_merge(
                                $row,
                                \Tools\FieldsTypes\Fieldtype_dropdown_multilevel::output_listing($output_options, true)
                            );
                        } elseif (in_array($field['type'], ['fieldtype_textarea_wysiwyg', 'fieldtype_textarea'])) {
                            $row[] = trim(\Models\Main\Fields_types::output($output_options));
                        } else {
                            $row[] = trim(strip_tags(\Models\Main\Fields_types::output($output_options)));
                        }
                    }

                    if (isset(\K::$fw->POST['export_url'])) {
                        $row[] = \Helpers\Urls::url_for(
                            'main/items/info',
                            'path=' . ($path_info_in_report['full_path'] ?? \K::$fw->current_path . '-' . $item['id'])
                        );
                    }

                    $export[] = $row;
                }

                //xlsx export
                $items_export = new \Models\Main\Items\Items_export(\K::$fw->POST['filename']);
                $items_export->xlsx_from_array($export);
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function export_csv()
    {
        if (\K::$fw->VERB == 'POST') {
            if (!isset(\K::$fw->app_selected_items[\K::$fw->POST['reports_id']])) {
                \K::$fw->app_selected_items[\K::$fw->POST['reports_id']] = [];
            }

            if (count(
                    \K::$fw->app_selected_items[\K::$fw->POST['reports_id']]
                ) > 0 and isset(\K::$fw->POST['fields'])) {
                $current_entity_info = \K::model()->db_find('app_entities', \K::$fw->current_entity_id);

                $separator = "\t";
                $listing_fields = [];
                $heading = [];

                $filename = str_replace(' ', '_', trim(\K::$fw->POST['filename']));

                $file_extension = \K::$fw->POST['file_extension'];

                //start export
                if ($file_extension == 'csv') {
                    header("Content-type: Application/octet-stream");
                    header("Content-disposition: attachment; filename=" . $filename . ".csv");
                } else {
                    header("Content-type: text/plain");
                    header("Content-disposition: attachment; filename=" . $filename . ".txt");
                }

                header("Pragma: no-cache");
                header("Expires: 0");

                $idIn = \K::model()->quoteToString(\K::$fw->POST['fields']);

                //adding reserved fields
                $fields_query = \K::model()->db_query_exec(
                    "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in ('fieldtype_action') and f.id in (" . $idIn . ") and f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
                    \K::$fw->current_entity_id,
                    'app_fields,app_forms_tabs'
                );

                //while ($fields = db_fetch_array($fields_query)) {
                foreach ($fields_query as $fields) {
                    if ($fields['type'] == 'fieldtype_dropdown_multilevel') {
                        $heading = array_merge(
                            $heading,
                            \Tools\FieldsTypes\Fieldtype_dropdown_multilevel::output_listing_heading($fields, true)
                        );
                    } else {
                        $heading[] = str_replace(["\n\r", "\r", "\n", $separator],
                            ' ',
                            \Models\Main\Fields_types::get_option($fields['type'], 'name', $fields['name']));
                    }

                    $listing_fields[] = $fields;
                }

                //adding item url
                if (isset(\K::$fw->POST['export_url'])) {
                    $heading[] = \K::$fw->TEXT_URL_HEADING;
                }

                //output heading
                $content = implode($separator, $heading) . "\n";

                if ($file_extension == 'csv') {
                    echo chr(0xFF) . chr(0xFE) . mb_convert_encoding($content, 'UTF-16LE', 'UTF-8');
                } else {
                    echo $content;
                }

                $items_query = $this->_getDb_query();
                //while ($item = db_fetch_array($items_query)) {
                foreach ($items_query as $item) {
                    $row = [];

                    $path_info_in_report = [];

                    if ($current_entity_info['parent_id'] > 0) {
                        $path_info_in_report = \Models\Main\Items\Items::get_path_info(
                            \K::$fw->current_entity_id,
                            $item['id']
                        );
                    }

                    foreach ($listing_fields as $field) {
                        //prepare field value
                        $value = \Models\Main\Items\Items::prepare_field_value_by_type($field, $item);

                        $output_options = [
                            'class' => $field['type'],
                            'value' => $value,
                            'field' => $field,
                            'item' => $item,
                            'is_export' => true,
                            'reports_id' => \K::$fw->POST['reports_id'],
                            'path' => ($path_info_in_report['full_path'] ?? \K::$fw->current_path),
                            'path_info' => $path_info_in_report
                        ];

                        if ($field['type'] == 'fieldtype_dropdown_multilevel') {
                            $row = array_merge(
                                $row,
                                \Tools\FieldsTypes\Fieldtype_dropdown_multilevel::output_listing($output_options, true)
                            );
                        } elseif (in_array($field['type'], ['fieldtype_textarea_wysiwyg', 'fieldtype_textarea'])) {
                            $row[] = str_replace(["\n\r", "\r", "\n", $separator],
                                ' ',
                                trim(\Models\Main\Fields_types::output($output_options)));
                        } else {
                            $row[] = str_replace(["\n\r", "\r", "\n", $separator],
                                ' ',
                                trim(strip_tags(\Models\Main\Fields_types::output($output_options))));
                        }
                    }

                    if (isset(\K::$fw->POST['export_url'])) {
                        $row[] = \Helpers\Urls::url_for(
                            'main/items/info',
                            'path=' . ($path_info_in_report['full_path'] ?? \K::$fw->current_path . '-' . $item['id'])
                        );
                    }

                    //output row
                    $content = implode($separator, $row) . "\n";
                    if ($file_extension == 'csv') {
                        echo chr(0xFF) . chr(0xFE) . mb_convert_encoding($content, 'UTF-16LE', 'UTF-8');
                    } else {
                        echo $content;
                    }
                }
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    private function _getDb_query()
    {
        $selected_items = \K::model()->quoteToString(
            \K::$fw->app_selected_items[\K::$fw->POST['reports_id']],
            \PDO::PARAM_INT
        );

        //prepare formulas query
        $listing_sql_query_select = \Tools\FieldsTypes\Fieldtype_formula::prepare_query_select(
            \K::$fw->current_entity_id,
            '',
            false,
            ['fields_in_listing' => implode(',', \K::$fw->POST['fields'])]
        );

        $listing_sql = "select e.* " . $listing_sql_query_select . " from app_entity_" . (int)\K::$fw->current_entity_id . " e where e.id in (" . $selected_items . ") order by field(id," . $selected_items . ")";
        return \K::model()->db_query_exec($listing_sql);
    }
}