<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Listing_highlight extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        \Helpers\Urls::redirect_to('main/dashboard');
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['entities_id'])) {
            $sql_data = [
                'is_active' => isset(\K::$fw->POST['is_active']),
                'fields_id' => \K::$fw->POST['fields_id'],
                'fields_values' => (is_array(\K::$fw->POST['fields_values']) ? implode(
                    ',',
                    \K::$fw->POST['fields_values']
                ) : \K::$fw->POST['fields_values']),
                'entities_id' => \K::$fw->GET['entities_id'],
                'bg_color' => \K::$fw->POST['bg_color'],
                'sort_order' => \K::$fw->POST['sort_order'],
                'notes' => \K::$fw->POST['notes'],
            ];

            /*if (isset(\K::$fw->GET['id'])) {
                db_perform(
                    'app_listing_highlight_rules',
                    $sql_data,
                    'update',
                    "id='" . db_input(\K::$fw->GET['id']) . "'"
                );
            } else {
                db_perform('app_listing_highlight_rules', $sql_data);
            }*/

            \K::model()->db_perform('app_listing_highlight_rules', $sql_data, [
                'id = ?',
                \K::$fw->GET['id']
            ]);

            \Helpers\Urls::redirect_to('main/entities/listing_types', 'entities_id=' . \K::$fw->GET['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['id']) and isset(\K::$fw->GET['entities_id'])) {
            \K::model()->db_delete_row('app_listing_highlight_rules', \K::$fw->GET['id']);

            \Helpers\Urls::redirect_to('main/entities/listing_types', 'entities_id=' . \K::$fw->GET['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function get_field_value()
    {
        /*$obj = (isset(\K::$fw->POST['id']) ? db_find(
            'app_listing_highlight_rules',
            \K::$fw->POST['id']
        ) : db_show_columns(
            'app_listing_highlight_rules'
        ));*/

        $obj = \K::model()->db_find('app_listing_highlight_rules', \K::$fw->POST['id']);

        $html = '';
        /*$field_query = db_query(
            "select id,name,type,configuration from app_fields where id=" . \K::$fw->POST['fields_id']
        );**/

        $field_info = \K::model()->db_fetch_one('app_fields', [
            'id = ?',
            \K::$fw->POST['fields_id']
        ], [], 'id,name,type,configuration');

        if ($field_info) {
            $values_html_heading = \K::$fw->TEXT_VALUES;
            $values_html = '';

            $cfg = new \Models\Main\Fields_types_cfg($field_info['configuration']);

            switch (\Models\Main\Items\Listing_highlight::get_field_type_key($field_info['type'])) {
                case 'boolean':
                    $choices = [
                        'true' => \K::$fw->TEXT_BOOLEAN_TRUE,
                        'false' => \K::$fw->TEXT_BOOLEAN_FALSE,
                    ];

                    $values_html = \Helpers\Html::select_tag(
                        'fields_values[]',
                        $choices,
                        $obj['fields_values'],
                        ['class' => 'form-control chosen-select required']
                    );
                    break;
                case 'choices':
                    if ($cfg->get('use_global_list') > 0) {
                        $choices = \Models\Main\Global_lists::get_choices($cfg->get('use_global_list'), false);
                    } else {
                        $choices = \Models\Main\Fields_choices::get_choices($field_info['id'], false);
                    }

                    $values_html = \Helpers\Html::select_tag(
                        'fields_values[]',
                        $choices,
                        $obj['fields_values'],
                        ['class' => 'form-control chosen-select required', 'multiple' => 'multiple']
                    );
                    break;
                case 'entities':
                    $parent_entity_item_is_the_same = false;
                    $choices = \Tools\FieldsTypes\Fieldtype_entity::get_choices(
                        $field_info,
                        ['parent_entity_item_id' => 0],
                        '',
                        $parent_entity_item_is_the_same
                    );

                    $values_html = \Helpers\Html::select_tag(
                        'fields_values[]',
                        $choices,
                        $obj['fields_values'],
                        ['class' => 'form-control chosen-select required', 'multiple' => 'multiple']
                    );

                    break;
                case 'dates':
                    $values_html_heading = \K::$fw->TEXT_FILTER_BY_DAYS;
                    $values_html = \Helpers\Html::input_tag(
                            'fields_values',
                            $obj['fields_values'],
                            ['class' => 'form-control required']
                        ) . \Helpers\App::tooltip_text(\K::$fw->TEXT_FILTER_BY_DAYS_TOOLTIP);
                    break;
                case 'numeric':
                    $values_html_heading = \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_FILTERS_NUMERIC_FIELDS_TOOLTIP
                        ) . \K::$fw->TEXT_VALUES;
                    $values_html = \Helpers\Html::input_tag(
                        'fields_values',
                        $obj['fields_values'],
                        ['class' => 'form-control required']
                    );
                    break;
            }

            $html = '
              <div class="form-group">
              	<label class="col-md-3 control-label" for="fields_id">' . $values_html_heading . '</label>
                <div class="col-md-9">	
              	  ' . $values_html . '
                </div>			
              </div>
           ';
        }

        echo $html;
    }
}