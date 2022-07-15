<?php

namespace Tools\FieldsTypes;

class Fieldtype_time
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_TIME];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $choices = [
            'calendar' => \K::$fw->TEXT_CALENDAR,
            'input' => \K::$fw->TEXT_FIELDTYPE_INPUT_TITLE,
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DISPLAY_AS,
            'name' => 'display_as',
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_SUM_IN_COMMENTS,
            'name' => 'sum_in_comments',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_SUM_IN_COMMENTS_INFO
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        if (strlen($obj['field_' . $field['id']]) > 0 and $obj['field_' . $field['id']] != 0) {
            $value = $obj['field_' . $field['id']];
            $hours = floor($value / 60);
            $minutes = $value - ($hours * 60);

            $hours = ($hours < 10 ? '0' : '') . $hours;
            $minutes = ($minutes < 10 ? '0' : '') . $minutes;

            $value = $hours . ":" . $minutes;
        } else {
            $hours = '';
            $minutes = '';
            $value = '';
        }

        if ($cfg->get('sum_in_comments') == 1 and $params['form'] != 'comment') {
            return '<p class="form-control-static">' . $value . '</p>' . input_hidden_tag(
                    'fields[' . $field['id'] . ']',
                    $value
                );
        }

        if ($cfg->get('display_as') == 'calendar') {
            $attributes = [
                'class' => 'form-control fieldtype_time field_' . $field['id'] . ($field['is_required'] == 1 ? ' required noSpace' : ''),
                'readonly' => 'readonly'
            ];

            $html = '
      <div class="input-group input-small date timepicker-field-' . $field['id'] . '" >' .
                input_tag('fields[' . $field['id'] . ']', $value, $attributes) .
                '<span class="input-group-btn">
          <button class="btn btn-default date-set" type="button"><i class="fa fa-calendar"></i></button>
        </span>
      </div>
    	' . fields_types::custom_error_handler($field['id']) . '              	
    			
    	<script>    		
	    	$(".timepicker-field-' . $field['id'] . '").datetimepicker({
		        autoclose: true,
		        isRTL: App.isRTL(),
		        format: "hh:ii",        
		        pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left"),
		        clearBtn: true,
		        startView:1,
		        maxView:1,	    			
		    }).on("changeDate", function(ev){
		    	$("#fields_' . $field['id'] . '").removeClass("error");
		      $("#fields_' . $field['id'] . '-error").html("").attr("style","");
				});		
    	</script>		
      ';
        } else {
            $html = '
                    <div class="fieldtype-time-input">        
                        <div class="input-group input-small">						    
                        ' . input_tag(
                    'fields[' . $field['id'] . ']_hours',
                    $hours,
                    ['class' => 'form-control field_hm_' . $field['id'], 'placeholder' => '00', 'maxlength' => 3]
                ) . '
                            <span class="input-group-addon" style="padding-left:0; padding-right: 0">:</span>
                        ' . input_tag(
                    'fields[' . $field['id'] . ']_minutes',
                    $minutes,
                    ['class' => 'form-control field_hm_' . $field['id'], 'placeholder' => '00', 'maxlength' => 2]
                ) . '                        
                        </div>    																		
                        ' . input_hidden_tag(
                    'fields[' . $field['id'] . ']',
                    $value,
                    ['class' => 'field_' . $field['id'] . ' ' . ($field['is_required'] == 1 ? ' required' : '')]
                ) . '    
                    </div>        
                                
    					
    			<script>
    				$(".field_hm_' . $field['id'] . '").keyup(function(){
                                    var length = 0;
                                    let parent_obj = $(this).parents(".fieldtype-time-input")
                                    
                                    $(".field_hm_' . $field['id'] . '",parent_obj).each(function(){
                                        length = length+$(this).val().length;
                                    })	

                                    if(length==0)
                                    {
                                        $(".field_' . $field['id'] . '",parent_obj).val("")
                                    }
                                    else
                                    {
                                        $(".field_' . $field['id'] . '",parent_obj).val( $("#fields_' . $field['id'] . '_hours",parent_obj).val() + ":" + $("#fields_' . $field['id'] . '_minutes",parent_obj).val() ).removeClass("error");
                                        $(".error",parent_obj).html("").attr("style","");
                                    }
    				})
    			</script>';
        }

        return $html;
    }

    public function process($options)
    {
        $value = db_prepare_input($options['value']);

        if (strlen($value)) {
            $value = explode(':', $value);

            if (count($value) == 2) {
                $hours = (int)$value[0];
                $minutes = (int)$value[1];

                return ($hours * 60) + $minutes;
            } else {
                return $value[0];
            }
        } else {
            return 0;
        }

        return db_prepare_input($options['value']);
    }

    public function output($options)
    {
        if ($options['value'] > 0) {
            $value = $options['value'];
            $hours = floor($value / 60);
            $minutes = $value - ($hours * 60);

            return ($hours < 10 ? '0' : '') . $hours . ":" . ($minutes < 10 ? '0' : '') . $minutes;
        } else {
            return '';
        }

        return $options['value'];
    }

    public static function get_fields_sum_in_comments($entity_id, $item_id, $field_id)
    {
        $history_query = db_query(
            "select sum(fields_value+0) as total from app_comments_history where fields_id='" . $field_id . "' and comments_id in (select id from app_comments where entities_id='" . db_input(
                $entity_id
            ) . "' and items_id='" . db_input($item_id) . "')"
        );
        $history = db_fetch_array($history_query);

        return $history['total'];
    }
}