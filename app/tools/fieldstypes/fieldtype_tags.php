<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_tags
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_TAGS_TITLE, 'has_choices' => true];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_WIDTH,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => [
                'input-medium' => \K::$fw->TEXT_INPUT_MEDIUM,
                'input-large' => \K::$fw->TEXT_INPUT_LARGE,
                'input-xlarge' => \K::$fw->TEXT_INPUT_XLARGE
            ],
            'tooltip_icon' => \K::$fw->TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DISPLAY_AS,
            'name' => 'display_as',
            'type' => 'dropdown',
            'choices' => [
                'dropdown_multiple' => \K::$fw->TEXT_FIELDTYPE_DROPDOWN_MULTIPLE_TITLE,
                'dropdown' => \K::$fw->TEXT_FIELDTYPE_DROPDOWN_TITLE
            ],
            'default' => 'dropdown_multiple',
            'params' => ['class' => 'form-control input-xlarge']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_AUTOMATICALLY_CREATE_TAG,
            'name' => 'auto_create_tag',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_AUTOMATICALLY_CREATE_TAG_TIP
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

        $cfg[] = [
            'title' => \K::$fw->TEXT_DISPLAY_CHOICES_VALUES,
            'name' => 'display_choices_values',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_DISPLAY_CHOICES_VALUES_TIP
        ];

        //cfg global list if exist
        if (count($choices = \Models\Main\Global_lists::get_lists_choices()) > 0) {
            $cfg[] = [
                'title' => \K::$fw->TEXT_USE_GLOBAL_LIST,
                'name' => 'use_global_list',
                'type' => 'dropdown',
                'choices' => $choices,
                'tooltip' => \K::$fw->TEXT_USE_GLOBAL_LIST_TOOLTIP,
                'params' => ['class' => 'form-control input-medium']
            ];
        }

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $add_empty = ($field['is_required'] == 1 ? false : true);

        $attributes = [
            'class' => 'form-control ' . $cfg->get(
                    'width'
                ) . ' field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')
        ];

        if ($cfg->get('display_as') == 'dropdown_multiple') {
            $attributes['multiple'] = 'multiple';
            $attributes['data-placeholder'] = \K::$fw->TEXT_ENTER_VALUE;
            $add_empty = false;

            $field_name = 'fields[' . $field['id'] . '][]';
        } else {
            $field_name = 'fields[' . $field['id'] . ']';
        }

        //use global lists if exist
        if ($cfg->get('use_global_list') > 0) {
            $choices = \Models\Main\Global_lists::get_choices(
                $cfg->get('use_global_list'),
                $add_empty,
                '',
                $obj['field_' . $field['id']],
                true
            );
            $default_id = \Models\Main\Global_lists::get_choices_default_id($cfg->get('use_global_list'));
        } else {
            $choices = \Models\Main\Fields_choices::get_choices(
                $field['id'],
                $add_empty,
                '',
                $cfg->get('display_choices_values'),
                $obj['field_' . $field['id']],
                true
            );
            $default_id = \Models\Main\Fields_choices::get_default_id($field['id']);
        }

        $value = ($obj['field_' . $field['id']] > 0 ? $obj['field_' . $field['id']] : ($params['form'] == 'comment' ? '' : $default_id));

        $html = '
        <div>' . \Helpers\Html::select_tag(
                $field_name,
                $choices,
                $value,
                $attributes
            ) . '</div>' . \Models\Main\Fields_types::custom_error_handler(
                $field['id']
            );

        $tags_flag = (in_array(
            \K::$fw->app_module_path,
            [
                'items/form',
                'ext/public/form',
                'users/registration',
                'users/account',
                'items/processes',
                'subentity/form'
            ]
        ) ? 1 : 0);

        $html .= '
    	<script>	
    	$(function(){
            let is_form_row_' . $field['id'] . ' = $("#fields_' . $field['id'] . '").parents(".forms-rows").size();
                    
	    	$("#fields_' . $field['id'] . '").select2({
		      tags: ' . $tags_flag . ',
		      width: (is_form_row_' . $field['id'] . '==0 ? "' . self::get_select2_width_by_class($cfg->get('width')) . '":"100%"),		      
		      ' . (in_array(\K::$fw->app_layout, ['public_layout.php']) ? '' : 'dropdownParent: $("#ajax-modal"),') . ' 
		      ' . ($cfg->get('auto_create_tag') == '1' ? "tokenSeparators: [',', ' ']," : '') . '
		      "language":{
		        "noResults" : function () { return "' . addslashes(\K::$fw->TEXT_NO_RESULTS_FOUND) . '"; },		    				    				
		      },
		      createTag: function (params) {			            		                
				    return {
				      id: "#"+params.term,
				      text: params.term
				    }
				  }		
	    	});
		  });
    	</script>
    ';

        return $html;
    }

    public function process($options)
    {
        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        $values_id = [];

        $values_list = (is_array($options['value']) ? $options['value'] : [0 => $options['value']]);

        $forceCommit = \K::model()->forceCommit();

        foreach ($values_list as $value) {
            if (substr($value, 0, 1) == '#') {
                $value = substr($value, 1);

                if (strlen($value)) {
                    $value = \K::model()->db_prepare_input(trim($value));

                    if ($cfg->get('use_global_list') > 0) {
                        /*$check_query = db_query(
                            "select id from app_global_lists_choices where lists_id='" . $cfg->get(
                                'use_global_list'
                            ) . "' and name='" . $value . "'"
                        );*/

                        $check = \K::model()->db_fetch_one('app_global_lists_choices', [
                            'lists_id = ? and name= ?',
                            $cfg->get('use_global_list'),
                            $value
                        ], [], 'id');

                        if (!$check) {
                            $sql_data = [
                                'lists_id' => $cfg->get('use_global_list'),
                                'name' => $value,
                            ];

                            $mapper = \K::model()->db_perform('app_global_lists_choices', $sql_data);
                            $choices_id = \K::model()->db_insert_id($mapper);

                            $values_id[] = $choices_id;
                        }
                    } else {
                        /*$check_query = db_query(
                            "select id from app_fields_choices where fields_id='" . $options['field']['id'] . "' and name='" . $value . "'"
                        );*/

                        $check = \K::model()->db_fetch_one('app_fields_choices', [
                            'fields_id = ? and name = ?',
                            $options['field']['id'],
                            $value
                        ], [], 'id');

                        if (!$check) {
                            $sql_data = [
                                'fields_id' => $options['field']['id'],
                                'name' => $value,
                            ];

                            $mapper = \K::model()->db_perform('app_fields_choices', $sql_data);
                            $choices_id = \K::model()->db_insert_id($mapper);

                            $values_id[] = $choices_id;
                        }
                    }
                }
            } else {
                $values_id[] = $value;
            }
        }

        if ($forceCommit) {
            \K::model()->commit();
        }

        //update value in process option to handle $choices_values->prepare($process_options); in items.php
        \K::$fw->process_options['value'] = $values_id;

        return implode(',', $values_id);
    }

    public function output($options)
    {
        $is_export = isset($options['is_export']);

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        //render global list value
        if ($cfg->get('use_global_list') > 0) {
            return \Models\Main\Global_lists::render_value($options['value'], $is_export);
        } else {
            return \Models\Main\Fields_choices::render_value($options['value'], $is_export);
        }
    }

    public function reports_query($options)
    {
        return \Models\Main\Reports\Reports::getReportsQueryValues($options);
    }

    public static function get_select2_width_by_class($class)
    {
        if (\Helpers\App::is_mobile()) {
            return '100%';
        }

        switch ($class) {
            case 'input-small':
                $width = '120px';
                break;
            case 'input-medium':
                $width = '240px';
                break;
            case 'input-large':
                $width = '320px';
                break;
            case 'input-xlarge':
                $width = '480px';
                break;
            default:
                $width = '100%';
                break;
        }

        return $width;
    }
}