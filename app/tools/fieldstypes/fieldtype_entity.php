<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_entity
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_ENTITY_TITLE];
    }

    public function get_configuration($params = [])
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_SELECT_ENTITY,
            'name' => 'entity_id',
            'tooltip' => \K::$fw->TEXT_FIELDTYPE_ENTITY_SELECT_ENTITY_TOOLTIP,
            'type' => 'dropdown',
            'choices' => \Models\Main\Entities::get_choices(),
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DISPLAY_USERS_AS,
            'name' => 'display_as',
            'tooltip' => \K::$fw->TEXT_DISPLAY_USERS_AS_TOOLTIP,
            'type' => 'dropdown',
            'choices' => [
                'dropdown' => \K::$fw->TEXT_DISPLAY_USERS_AS_DROPDOWN,
                'checkboxes' => \K::$fw->TEXT_DISPLAY_USERS_AS_CHECKBOXES,
                'dropdown_multiple' => \K::$fw->TEXT_DISPLAY_USERS_AS_DROPDOWN_MULTIPLE
            ],
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DEFAULT_TEXT,
            'name' => 'default_text',
            'type' => 'input',
            'tooltip' => \K::$fw->TEXT_DEFAULT_TEXT_INFO,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_WIDTH,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => [
                'input-small' => \K::$fw->TEXT_INPUT_SMALL,
                'input-medium' => \K::$fw->TEXT_INPUT_MEDIUM,
                'input-large' => \K::$fw->TEXT_INPUT_LARGE,
                'input-xlarge' => \K::$fw->TEXT_INPUT_XLARGE
            ],
            'tooltip' => \K::$fw->TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DISPLAY_ONLY_ASSIGNED_RECORDS,
            'tooltip_icon' => \K::$fw->TEXT_DISPLAY_ONLY_ASSIGNED_RECORDS_INFO,
            'name' => 'display_assigned_records_only',
            'type' => 'checkbox'
        ];

        $cfg[] = ['title' => \K::$fw->TEXT_HIDE_PLUS_BUTTON, 'name' => 'hide_plus_button', 'type' => 'checkbox'];

        $cfg[] = [
            'title' => \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_DISPLAY_NAME_AS_LINK_INFO
                ) . \K::$fw->TEXT_DISPLAY_NAME_AS_LINK,
            'name' => 'display_as_link',
            'type' => 'checkbox'
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[] = ['name' => 'fields_in_popup', 'type' => 'hidden'];

        return $cfg;
    }

    public static function prepare_parents_sql(
        $parent_entity_item_id,
        $entity_id,
        $field_entity_id,
        $listing_sql_query = '',
        $previous_prefix = 'e'
    ) {
        //set prefix for current entity
        $prefix = 'e' . (int)$entity_id;

        //get entity info
        $entity_info = \K::model()->db_find('app_entities', $entity_id);

        //if parent is 0 then it means we did not find $field_entity_id in this tree branch
        //and we don't have to check parents so that is why we return empty query
        if ($entity_info['parent_id'] == 0) {
            return '';
        }

        //check parents the same
        if ($entity_info['parent_id'] == $field_entity_id) {
            $listing_sql_query .= " and {$previous_prefix}.parent_item_id in (select {$prefix}.id from app_entity_" . (int)$entity_id . " {$prefix} where {$prefix}.parent_item_id = " . (int)$parent_entity_item_id . ")";
        } //if parents not the same then wer include sub-query
        else {
            $listing_sql_query .= " and {$previous_prefix}.parent_item_id in (select {$prefix}.id from app_entity_" . (int)$entity_id . " {$prefix} where {$prefix}.id > 0 " . self::prepare_parents_sql(
                    $parent_entity_item_id,
                    $entity_info['parent_id'],
                    $field_entity_id,
                    $listing_sql_query,
                    $prefix
                ) . ")";
        }

        return $listing_sql_query;
    }

    public static function get_choices($field, $params = [], $value = '', &$parent_entity_item_is_the_same = false)
    {
        $parent_entity_item_id = (int)$params['parent_entity_item_id'];

        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $entity_info = \K::model()->db_find('app_entities', $cfg->get('entity_id'));
        $field_entity_info = \K::model()->db_find('app_entities', $field['entities_id']);

        $choices = [];

        //add empty value if displays as dropdown and field is not required
        if ($cfg->get('display_as') == 'dropdown') {
            $choices[''] = (strlen($cfg->get('default_text')) ? $cfg->get('default_text') : \K::$fw->TEXT_NONE);
        }

        $listing_sql_query = 'e.id>0 ';
        $listing_sql_query_order = '';
        $listing_sql_query_join = '';
        $listing_sql_query_having = '';
        $listing_sql_select = '';

        $parent_entity_item_is_the_same = false;

        //if parent entity is the same then select records from parent items only
        if ($parent_entity_item_id > 0 and $entity_info['parent_id'] > 0 and $entity_info['parent_id'] == $field_entity_info['parent_id']) {
            $parent_entity_item_is_the_same = true;

            $listing_sql_query .= " and e.parent_item_id = " . $parent_entity_item_id;
        } //if parent is different then check level branch
        elseif ($parent_entity_item_id > 0 and $entity_info['parent_id'] > 0 and $entity_info['parent_id'] != $field_entity_info['parent_id']) {
            $listing_sql_query = $listing_sql_query . self::prepare_parents_sql(
                    $parent_entity_item_id,
                    $entity_info['parent_id'],
                    $field_entity_info['parent_id']
                );
        }

        if ($cfg->get('display_assigned_records_only') == 1) {
            $listing_sql_query = \Models\Main\Items\Items::add_access_query($cfg->get('entity_id'), $listing_sql_query);
        } else {
            //add visibility access query
            $listing_sql_query .= \Models\Main\Users\Records_visibility::add_access_query($cfg->get('entity_id'));
        }

        /*$default_reports_query = db_query(
            "select * from app_reports where entities_id='" . db_input(
                $cfg->get('entity_id')
            ) . "' and reports_type='entityfield" . $field['id'] . "'"
        );*/

        $default_reports = \K::model()->db_fetch_one('app_reports', [
            'entities_id = ? and reports_type = ?',
            $cfg->get('entity_id'),
            'entityfield' . (int)$field['id']
        ], [], 'id,listing_order_fields');

        if ($default_reports) {
            $listing_sql_select = \Tools\FieldsTypes\Fieldtype_formula::prepare_query_select(
                $cfg->get('entity_id'),
                ''
            );

            $listing_sql_query = \Models\Main\Reports\Reports::add_filters_query(
                $default_reports['id'],
                $listing_sql_query
            );

            //prepare having query for formula fields
            if (isset(\K::$fw->sql_query_having[$cfg->get('entity_id')])) {
                $listing_sql_query_having = \Models\Main\Reports\Reports::prepare_filters_having_query(
                    \K::$fw->sql_query_having[$cfg->get('entity_id')]
                );
            }

            $info = \Models\Main\Reports\Reports::add_order_query(
                $default_reports['listing_order_fields'],
                $cfg->get('entity_id')
            );
            $listing_sql_query_order .= $info['listing_sql_query'];
            $listing_sql_query_join .= $info['listing_sql_query_join'];
        } else {
            $listing_sql_query_order .= " order by e.id";
        }

        //if exist value then include it in query  	
        if (strlen($value)) {
            $listing_sql_query = "(" . $listing_sql_query . ") or e.id in (" . $value . ") ";
        }

        $field_heading_id = 0;
        /*$fields_query = db_query(
            "select f.* from app_fields f where f.is_heading=1 and  f.entities_id='" . db_input(
                $cfg->get('entity_id')
            ) . "'"
        );*/

        $fields = \K::model()->db_fetch_one('app_fields', [
            'is_heading = 1 and entities_id = ?',
            $cfg->get('entity_id')
        ], [], 'id');

        if ($fields) {
            $field_heading_id = $fields['id'];
        }

        /*$listing_sql = "select  e.* " . $listing_sql_select . " from app_entity_" . $cfg->get(
                'entity_id'
            ) . " e " . $listing_sql_query_join . " where " . $listing_sql_query . $listing_sql_query_having . $listing_sql_query_order;
        $items_query = db_query($listing_sql, false);*/

        $items_query = \K::model()->db_query_exec(
            "select  e.* " . $listing_sql_select . " from app_entity_" . (int)$cfg->get(
                'entity_id'
            ) . " e " . $listing_sql_query_join . " where " . $listing_sql_query . $listing_sql_query_having . $listing_sql_query_order
        );

        //while ($item = db_fetch_array($items_query)) {
        foreach ($items_query as $item) {
            if ($cfg->get('entity_id') == 1) {
                $choices[$item['id']] = \K::$fw->app_users_cache[$item['id']]['name'];
            } elseif ($field_heading_id > 0) {
                //add parent item name if exist
                $parent_name = '';
                if ($entity_info['parent_id'] > 0 and $entity_info['parent_id'] != $field_entity_info['parent_id']) {
                    $parent_name = \Models\Main\Items\Items::get_heading_field(
                            $entity_info['parent_id'],
                            $item['parent_item_id']
                        ) . ' > ';
                }

                $choices[$item['id']] = $parent_name . \Models\Main\Items\Items::get_heading_field_value(
                        $field_heading_id,
                        $item
                    );
            } else {
                $choices[$item['id']] = $item['id'];
            }
        }

        return $choices;
    }

    public function render($field, $obj, $params = [])
    {
        $parent_entity_item_id = $params['parent_entity_item_id'];

        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $entity_info = \K::model()->db_find('app_entities', $cfg->get('entity_id'));

        //set value
        $value = (strlen($obj['field_' . $field['id']]) ? $obj['field_' . $field['id']] : '');

        $parent_entity_item_is_the_same = false;

        $choices = self::get_choices($field, $params, $value, $parent_entity_item_is_the_same);

        //prepare button add
        //TODO \K::$fw->app_layout != 'public_layout.php'
        $button_add_html = '';
        if ($cfg->get(
                'hide_plus_button'
            ) != 1 and isset(\K::$fw->current_path_array) and \K::$fw->app_action != 'account' and \K::$fw->app_action != 'comments_form' and \K::$fw->app_action != 'processes' and \K::$fw->app_layout != 'public_layout.php' and \Models\Main\Users\Users::has_access_to_entity(
                $cfg->get('entity_id'),
                'create'
            ) and !isset(\K::$fw->GET['is_submodal']) and ($entity_info['parent_id'] == 0 or ($entity_info['parent_id'] > 0 and $parent_entity_item_is_the_same))) {
            $url_params = 'is_submodal=true&redirect_to=parent_modal&refresh_field=' . $field['id'];

            if ($entity_info['parent_id'] == 0) {
                $url_params .= '&path=' . $cfg->get('entity_id');
            } else {
                $path_array = \K::$fw->current_path_array;
                unset($path_array[count($path_array) - 1]);

                $url_params .= '&path=' . implode('/', $path_array) . '/' . $cfg->get('entity_id');
            }

            $submodal_url = \Helpers\Urls::url_for('main/items/form', $url_params);

            $button_add_html = '<button type="button" class="btn btn-default btn-submodal-open btn-submodal-open-chosen" data-parent-entity-item-id="' . $parent_entity_item_id . '" data-field-id="' . $field['id'] . '" data-submodal-url="' . $submodal_url . '"><i class="fa fa-plus" aria-hidden="true"></i></button>';
        }

        if ($cfg->get('display_as') == 'dropdown') {
            $attributes = [
                'class' => 'form-control chosen-select ' . $cfg->get(
                        'width'
                    ) . ' field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')
            ];

            if (strlen($button_add_html)) {
                $html = '
                <div class="dropdown-with-plus-btn ' . $cfg->get('width') . '">
                    <div class="left">' . \Helpers\Html::select_tag(
                        'fields[' . $field['id'] . ']',
                        $choices,
                        $value,
                        $attributes
                    ) . '</div>
                    <div class="right">' . $button_add_html . '</div>
                 </div>';
            } else {
                $html = \Helpers\Html::select_tag('fields[' . $field['id'] . ']', $choices, $value, $attributes);
            }

            $html .= \Models\Main\Fields_types::custom_error_handler($field['id']);

            return $html;
        } elseif ($cfg->get('display_as') == 'checkboxes') {
            $attributes = ['class' => 'field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')];

            return '<div class="checkboxes_list ' . ($field['is_required'] == 1 ? ' required' : '') . '">' . \Helpers\Html::select_checkboxes_tag(
                    'fields[' . $field['id'] . ']',
                    $choices,
                    $value,
                    $attributes
                ) . '</div>';
        } elseif ($cfg->get('display_as') == 'dropdown_multiple') {
            $attributes = [
                'class' => 'form-control chosen-select ' . $cfg->get(
                        'width'
                    ) . ' field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : ''),
                'multiple' => 'multiple',
                'data-placeholder' => (strlen($cfg->get('default_text')) ? $cfg->get(
                    'default_text'
                ) : \K::$fw->TEXT_SELECT_SOME_VALUES)
            ];

            if (strlen($button_add_html)) {
                $html = '
                <div class="dropdown-with-plus-btn ' . $cfg->get('width') . '">
                    <div class="left">' . \Helpers\Html::select_tag(
                        'fields[' . $field['id'] . '][]',
                        $choices,
                        explode(',', $value),
                        $attributes
                    ) . '</div>
                    <div class="right">' . $button_add_html . '</div>
                 </div>';
            } else {
                $html = \Helpers\Html::select_tag(
                    'fields[' . $field['id'] . '][]',
                    $choices,
                    explode(',', $value),
                    $attributes
                );
            }

            $html .= \Models\Main\Fields_types::custom_error_handler($field['id']);

            return $html;
        }
    }

    public function process($options)
    {
        return (is_array($options['value']) ? implode(',', $options['value']) : $options['value']);
    }

    public function output($options)
    {
        global $app_user;

        if (strlen($options['value']) == 0) {
            return '';
        }

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        //prepare sql if not export
        $items_info_formula_sql = '';
        if (!isset($options['is_export'])) {
            $fields_access_schema = \Models\Main\Users\Users::get_fields_access_schema(
                $cfg->get('entity_id'),
                $app_user['group_id']
            );

            $fields_in_listing = \Models\Main\Fields::get_heading_id($cfg->get('entity_id')) . (strlen(
                    $cfg->get('fields_in_popup')
                ) ? ',' . $cfg->get('fields_in_popup') : '');
            $items_info_formula_sql = \Tools\FieldsTypes\Fieldtype_formula::prepare_query_select(
                $cfg->get('entity_id'),
                '',
                false,
                ['fields_in_listing' => $fields_in_listing]
            );
        }

        $output = [];
        $exp = explode(',', $options['value']);
        foreach ($exp as $item_id) {
            /*$items_info_sql = "select e.* {$items_info_formula_sql} from app_entity_" . $cfg->get(
                     'entity_id'
                 ) . " e where e.id='" . db_input($item_id) . "'";
             $items_query = db_query($items_info_sql);*/

            //TODO Add cache?
            $item = \K::model()->db_query_exec_one(
                "select e.* {$items_info_formula_sql} from app_entity_" . (int)$cfg->get(
                    'entity_id'
                ) . " e where e.id = ?",
                $item_id
            );

            if ($item) {
                $name = \Models\Main\Items\Items::get_heading_field($cfg->get('entity_id'), $item['id']);

                //get fields in popup in not export
                if (!isset($options['is_export'])) {
                    $fields_in_popup = \Models\Main\Fields::get_items_fields_data_by_id(
                        $item,
                        $cfg->get('fields_in_popup'),
                        $cfg->get('entity_id'),
                        $fields_access_schema
                    );

                    if (count($fields_in_popup) > 0) {
                        $popup_html = \Helpers\App::app_render_fields_popup_html($fields_in_popup);

                        $name = '<span ' . $popup_html . '>' . $name . '</span>';
                    }

                    if ($cfg->get('display_as_link') == 1) {
                        $path_info = \Models\Main\Items\Items::get_path_info($cfg->get('entity_id'), $item['id']);

                        $name = '<a href="' . \Helpers\Urls::url_for(
                                'main/items/info',
                                'path=' . $path_info['full_path']
                            ) . '">' . $name . '</a>';
                    }
                }

                $output[] = $name;
            }
        }

        if (isset($options['is_export'])) {
            return implode(', ', $output);
        } else {
            return implode('<br>', $output);
        }
    }

    public function reports_query($options)
    {
        return \Models\Main\Reports\Reports::getReportsQueryValues($options);
    }
}