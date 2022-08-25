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
        $sql_data = [
            'is_active' => (isset($_POST['is_active']) ? true : false),
            'fields_id' => _POST('fields_id'),
            'fields_values' => (is_array($_POST['fields_values']) ? implode(
                ',',
                $_POST['fields_values']
            ) : $_POST['fields_values']),
            'entities_id' => _get::int('entities_id'),
            'bg_color' => $_POST['bg_color'],
            'sort_order' => $_POST['sort_order'],
            'notes' => $_POST['notes'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_listing_highlight_rules', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_listing_highlight_rules', $sql_data);
        }

        redirect_to('entities/listing_types', 'entities_id=' . _GET('entities_id'));
    }

    public function delete()
    {
        db_delete_row('app_listing_highlight_rules', _GET('id'));
        redirect_to('entities/listing_types', 'entities_id=' . _GET('entities_id'));
    }

    public function get_field_value()
    {
        $obj = (isset($_POST['id']) ? db_find('app_listing_highlight_rules', _POST('id')) : db_show_columns(
            'app_listing_highlight_rules'
        ));

        $html = '';
        $field_query = db_query("select id,name,type,configuration from app_fields where id=" . _POST('fields_id'));
        if ($field_info = db_fetch_array($field_query)) {
            $values_html_heading = TEXT_VALUES;
            $values_html = '';

            $cfg = new fields_types_cfg($field_info['configuration']);

            switch (listing_highlight::get_field_type_key($field_info['type'])) {
                case 'boolean':
                    $choices = [
                        'true' => TEXT_BOOLEAN_TRUE,
                        'false' => TEXT_BOOLEAN_FALSE,
                    ];
                    $values_html = select_tag(
                        'fields_values[]',
                        $choices,
                        $obj['fields_values'],
                        ['class' => 'form-control chosen-select required']
                    );
                    break;
                case 'choices':
                    if ($cfg->get('use_global_list') > 0) {
                        $choices = global_lists::get_choices($cfg->get('use_global_list'), false);
                    } else {
                        $choices = fields_choices::get_choices($field_info['id'], false);
                    }

                    $values_html = select_tag(
                        'fields_values[]',
                        $choices,
                        $obj['fields_values'],
                        ['class' => 'form-control chosen-select required', 'multiple' => 'multiple']
                    );
                    break;
                case 'entities':
                    $parent_entity_item_is_the_same = false;
                    $choices = fieldtype_entity::get_choices(
                        $field_info,
                        ['parent_entity_item_id' => 0],
                        '',
                        $parent_entity_item_is_the_same
                    );

                    $values_html = select_tag(
                        'fields_values[]',
                        $choices,
                        $obj['fields_values'],
                        ['class' => 'form-control chosen-select required', 'multiple' => 'multiple']
                    );

                    break;
                case 'dates':
                    $values_html_heading = TEXT_FILTER_BY_DAYS;
                    $values_html = input_tag(
                            'fields_values',
                            $obj['fields_values'],
                            ['class' => 'form-control required']
                        ) . tooltip_text(TEXT_FILTER_BY_DAYS_TOOLTIP);
                    break;
                case 'numeric':
                    $values_html_heading = tooltip_icon(TEXT_FILTERS_NUMERIC_FIELDS_TOOLTIP) . TEXT_VALUES;
                    $values_html = input_tag(
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