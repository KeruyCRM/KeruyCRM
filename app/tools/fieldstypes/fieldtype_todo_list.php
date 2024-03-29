<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_todo_list
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_TODO_LIST_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_ALLOW_SEARCH_TIP
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
            'title' => \K::$fw->TEXT_USE_COMMENTS,
            'name' => 'use_comments',
            'type' => 'dropdown',
            'params' => ['class' => 'form-control input-large'],
            'choices' => [
                '' => '',
                'auto' => \K::$fw->TEXT_AUTO_ADD_COMMENT,
                'form' => \K::$fw->TEXT_OPEN_COMMENT_FORM
            ],
            'tooltip_icon' => \K::$fw->TEXT_FIELDTYPE_TODO_LIST_USE_COMMENTS_INFO
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_FOR_SUCCESSFUL_CHECK,
            'name' => 'text_check',
            'type' => 'input',
            'params' => ['class' => 'form-control input-large']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_FOR_UNCHECK,
            'name' => 'text_unckeck',
            'type' => 'input',
            'params' => ['class' => 'form-control input-large']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_HIDE_CHECKBOXES_IF_NO_ACCESS,
            'name' => 'hide_checkboxes',
            'type' => 'checkbox'
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = \Models\Main\Fields_types::parse_configuration($field['configuration']);

        $attributes = [
            'rows' => '3',
            'class' => 'form-control ' . $cfg['width'] . ($field['is_heading'] == 1 ? ' autofocus' : '') . ' fieldtype_todo_list field_' . $field['id'] . ($field['is_required'] == 1 ? ' required noSpace' : '')
        ];

        return \Helpers\Html::textarea_tag(
            'fields[' . $field['id'] . ']',
            str_replace(['&lt;', '&gt;'], ['<', '>'], $obj['field_' . $field['id']]),
            $attributes
        );
    }

    public function process($options)
    {
        return str_replace(['<', '>'], ['&lt;', '&gt;'], $options['value']);
    }

    public function output($options)
    {
        $html_listing = '';
        if (strlen($options['value'])) {
            foreach (preg_split('/\r\n|\r|\n/', $options['value']) as $key => $value) {
                if (substr($value, 0, 1) == '*') {
                    $value = '<strike>' . substr($value, 1) . '</strike>';
                }

                $html_listing .= '<div>' . $value . '</div>';
            }
        }

        if (isset($options['is_export'])) {
            return ((!isset($options['is_print']) and !isset($options['is_email'])) ? str_replace(['&lt;', '&gt;'],
                ['<', '>'],
                $options['value']) : $html_listing);
        } else {
            $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

            //get default filed access cfg
            $fields_access_schema = \Models\Main\Users\Users::get_fields_access_schema(
                $options['field']['entities_id'],
                \K::$fw->app_user['group_id']
            );

            //get field access rules
            $access_rules = new \Models\Main\Access_rules($options['field']['entities_id'], $options['item']);
            $fields_access_schema += $access_rules->get_fields_view_only_access();

            $hide_checkboxes = (isset($fields_access_schema[$options['field']['id']]) and $cfg->get(
                    'hide_checkboxes'
                ) == 1 ? true : false);

            if (isset($options['is_listing']) or isset($options['is_email']) or $hide_checkboxes) {
                return $html_listing;
            } else {
                $html = '';
                if (strlen($options['value'])) {
                    $html = '<table class="todo-list">';
                    foreach (preg_split('/\r\n|\r|\n/', $options['value']) as $key => $value) {
                        $is_checked = '';

                        if (substr($value, 0, 1) == '*') {
                            $is_checked = 'checked';
                            $value = substr($value, 1);
                        }

                        $html .= '
  							<tr>
  								<td>' . \Helpers\Html::input_checkbox_tag(
                                'todo_list_' . $options['field']['id'] . '_' . $key,
                                $key,
                                ['class' => 'todo-list-item-' . $options['field']['id'], 'checked' => $is_checked]
                            ) . '</td>
  								<td><label class="todo_list_' . $options['field']['id'] . '_' . $key . (strlen(
                                $is_checked
                            ) ? ' checked' : '') . '" for="todo_list_' . $options['field']['id'] . '_' . $key . '">' . $value . '</label></td>
  							</tr>';
                    }
                    $html .= '</table>';

                    //prepare ajax complete code
                    $js_done = '';

                    switch ($cfg->get('use_comments')) {
                        case 'auto':
                            $js_done = '
  									load_comments_listing("items_comments_listing",1,"");
  								';
                            break;
                        case 'form':
                            $js_done = '
  								description = (is_checked==1 ? \'' . addslashes(
                                    $cfg->get('text_check')
                                ) . '\':\'' . addslashes($cfg->get('text_unckeck')) . '\')+" "+checked_text;  								
  								open_dialog(\'' . \Helpers\Urls::url_for(
                                    'main/items/comments_form',
                                    'path=' . $options['path']
                                ) . '&description=\'+encodeURIComponent(description));  								
  								';
                            break;
                    }

                    $js_function_name = 'todo_list_action_' . $options['field']['id'];

                    $html .= '
  					<script>
  						function ' . $js_function_name . '()
  						{
  							//todo list
							  $(".todo-list-item-' . $options['field']['id'] . '").change(function(){
							  	list_id = $(this).val();
  								
  								var checked_text = $(".todo_list_' . $options['field']['id'] . '_"+list_id).html();
  										  								  								
							  	if($(this).prop("checked"))
							  	{	
							  		$(".todo_list_' . $options['field']['id'] . '_"+list_id).addClass("checked")
  									var is_checked = 1  									
							  	}
							  	else
							  	{
							  		$(".todo_list_' . $options['field']['id'] . '_"+list_id).removeClass("checked")
  									var is_checked = 0  									
							  	}
  								
  								$.ajax({
  									method:"POST",
  									url:"' . \Helpers\Urls::url_for(
                            'main/items/todo_list/update',
                            'path=' . $options['path']
                        ) . '",
  									data:{field_id:' . $options['field']['id'] . ',list_id:list_id,is_checked:is_checked}
  								}).done(function(data){
  									' . $js_done . '	
  								})
							  })
  						}
  								
  						$(function(){
  							' . $js_function_name . '()	
  						})		
  					</script>
  						';
                }
                return $html;
            }
        }
    }
}