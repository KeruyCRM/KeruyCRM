<?php

namespace Helpers;

class Html
{
    public static function tag_attributes_to_html($default, $attributes = [])
    {
        $attributes = array_merge($default, $attributes);

        return implode(
            '',
            array_map('self::tag_attributes_to_html_callback', array_keys($attributes), array_values($attributes))
        );
    }

    public static function tag_attributes_to_html_callback($k, $v)
    {
        return false === $v || null === $v || ('' === $v && 'value' != $k) ? '' : sprintf(
            ' %s="%s"',
            $k,
            htmlspecialchars((string)$v, ENT_QUOTES)
        );
    }

    public static function generate_id_from_name($name)
    {
        // check to see if we have an array variable for a field name
        if (strstr($name, '[')) {
            $name = str_replace(['[]', '][', '[', ']'], ['', '_', '_', ''], $name);
        }

        // remove illegal characters
        $name = preg_replace(['/^[^A-Za-z]+/', '/[^A-Za-z0-9\:_\.\-]/'], ['', '_'], $name);

        return $name;
    }

    public static function form_tag($name, $action, $attributes = [])
    {
        //global $app_session_token;

        $default = ['name' => $name, 'id' => self::generate_id_from_name($name), 'method' => 'post'];

        return '<form action="' . $action . '" ' . self::tag_attributes_to_html(
                $default,
                $attributes
            ) . '> ' . self::input_hidden_tag(
                'form_session_token',
                \K::$fw->app_session_token
            );
    }

    public static function input_tag($name = '', $value = '', $attributes = [])
    {
        $default = ['name' => $name, 'id' => self::generate_id_from_name($name), 'value' => $value, 'type' => 'text'];

        return '<input ' . self::tag_attributes_to_html($default, $attributes) . '>';
    }

    public static function submit_tag($value = '', $attributes = [])
    {
        $attributes = array_merge(['type' => 'submit', 'class' => 'btn btn-primary'], $attributes);

        return self::input_tag('', $value, $attributes);
    }

    public static function input_password_tag($name, $attributes = [])
    {
        $attributes = array_merge($attributes, ['type' => 'password']);

        return self::input_tag($name, '', $attributes);
    }

    public static function input_file_tag($name, $attributes = [])
    {
        $attributes = array_merge($attributes, ['type' => 'file']);

        return self::input_tag($name, '', $attributes);
    }

    public static function input_hidden_tag($name, $value = '', $attributes = [])
    {
        $attributes = array_merge($attributes, ['type' => 'hidden']);

        return self::input_tag($name, $value, $attributes);
    }

    public static function input_checkbox_tag($name, $value = '1', $attributes = [])
    {
        $attributes = array_merge($attributes, ['type' => 'checkbox']);

        if (isset($attributes['checked'])) {
            if (is_numeric($attributes['checked'])) {
                $attributes['checked'] = (bool)$attributes['checked'];
            }
        }

        return self::input_tag($name, $value, $attributes);
    }

    public static function input_radiobox_tag($name, $value = '1', $attributes = [])
    {
        $attributes = array_merge($attributes, ['type' => 'radio']);

        if (isset($attributes['checked'])) {
            if (is_numeric($attributes['checked'])) {
                $attributes['checked'] = (bool)$attributes['checked'];
            }
        }

        return self::input_tag($name, $value, $attributes);
    }

    public static function select_tag($name, $choices = [], $value = '', $attributes = [])
    {
        $default = ['name' => $name, 'id' => self::generate_id_from_name($name)];

        $html = '';

        if (!is_array($value)) {
            $value = (strlen($value) ? explode(',', $value) : []);
        }

        foreach ($choices as $k => $v) {
            if (is_array($v)) {
                $html_optgroup = '';
                foreach ($v as $kk => $vv) {
                    $html_optgroup .= '<option ' . (in_array(
                            $kk,
                            $value
                        ) ? 'selected' : '') . ' value="' . $kk . '">' . htmlspecialchars(
                            (string)$vv,
                            ENT_QUOTES
                        ) . '</option>';
                }

                $html .= '<optgroup label="' . htmlspecialchars(
                        (string)$k,
                        ENT_QUOTES
                    ) . '">' . $html_optgroup . '</optgroup>';
            } else {
                $html .= '<option ' . (in_array(
                        $k,
                        $value
                    ) ? 'selected' : '') . ' value="' . $k . '">' . htmlspecialchars(
                        (string)$v,
                        ENT_QUOTES
                    ) . '</option>';
            }
        }

        return '<select ' . self::tag_attributes_to_html($default, $attributes) . '>' . $html . '</select>';
    }

    public static function select_tag_with_color($name, $choices = [], $value = '', $attributes = [])
    {
        $default = ['name' => $name, 'id' => self::generate_id_from_name($name)];

        $html = '';

        if (!is_array($value)) {
            $value = (strlen($value) ? explode(',', $value) : []);
        }

        foreach ($choices as $k => $v) {
            $name = $v['name'] ?? '';
            $color = $v['color'] ?? '';

            $html .= '<option ' . (in_array(
                    $k,
                    $value
                ) ? 'selected' : '') . ' value="' . $k . '" class="' . $color . '">' . htmlspecialchars(
                    (string)$name,
                    ENT_QUOTES
                ) . '</option>';
        }

        return '<select ' . self::tag_attributes_to_html($default, $attributes) . '>' . $html . '</select>';
    }

    public static function select_checkboxes_tag($name, $choices = [], $value = '', $attributes = [])
    {
        $html = '';

        foreach ($choices as $k => $v) {
            if (is_array($v)) {
                $html .= '<div><strong>' . $k . '</strong></div>';

                foreach ($v as $kk => $vv) {
                    if (in_array($kk, explode(',', $value))) {
                        $attributes['checked'] = true;
                    } else {
                        $attributes['checked'] = false;
                    }

                    $attributes['id'] = self::generate_id_from_name($name . '[' . $kk . ']');

                    $html .= '<div><label>' . self::input_checkbox_tag(
                            $name . '[]',
                            $kk,
                            $attributes
                        ) . ' ' . $vv . '</label></div>';
                }
            } else {
                $attributes = self::getAttributes($value, $attributes, $k, $name);

                $html .= '<div><label>' . self::input_checkbox_tag(
                        $name . '[]',
                        $k,
                        $attributes
                    ) . ' ' . $v . '</label></div>';
            }
        }

        return '<div class="select_checkboxes_tag">' . $html . '</div> <label for="' . $name . '[]" class="error"></label>';
    }

    public static function select_radioboxes_tag($name, $choices = [], $value = '', $attributes = [])
    {
        $html = '';

        foreach ($choices as $k => $v) {
            if (in_array($k, explode(',', $value))) {
                $attributes['checked'] = true;
            } else {
                $attributes['checked'] = false;
            }

            $attributes['id'] = self::generate_id_from_name($name . '[' . $k . ']');

            $html .= '<div><label>' . self::input_radiobox_tag($name, $k, $attributes) . ' ' . $v . '</label></div>';
        }

        return '<div class="select_checkboxes_tag">' . $html . '</div>';
    }

    public static function select_checkboxes_ul_tag($name, $choices = [], $value = '', $attributes = [])
    {
        $html = '';

        foreach ($choices as $k => $v) {
            $attributes['checked'] = (strlen($value) and in_array($k, explode(',', $value))) ? true : false;

            $attributes['id'] = self::generate_id_from_name($name . '[' . $k . ']');

            $html .= '<li><label>' . self::input_checkbox_tag($name . '[]', $k, $attributes) . ' ' . $v . '</></li>';
        }

        return '<ul class="list-unstyled checkboxes ' . ($attributes['ul-class'] ?? '') . '">' . $html . '</li>';
    }

    public static function select_checkboxes_ul_color_tag($name, $choices = [], $value = '', $attributes = [])
    {
        $html = '';

        foreach ($choices as $k => $v) {
            $title = $v['name'] ?? '';
            $color = $v['color'] ?? '';

            $attributes['checked'] = (strlen($value) and in_array($k, explode(',', $value))) ? true : false;

            $attributes['id'] = self::generate_id_from_name($name . '[' . $k . ']');

            $html .= '<li><label style="color: ' . $color . '">' . self::input_checkbox_tag(
                    $name . '[]',
                    $k,
                    $attributes
                ) . ' ' . $title . '</></li>';
        }

        return '<ul class="list-unstyled checkboxes ' . ($attributes['ul-class'] ?? '') . '">' . $html . '</li>';
    }

    public static function select_radioboxes_ul_tag($name, $choices = [], $value = '', $attributes = [])
    {
        $html = '';

        foreach ($choices as $k => $v) {
            $attributes['checked'] = (strlen($value) and in_array($k, explode(',', $value))) ? true : false;

            $attributes['id'] = self::generate_id_from_name($name . '[' . $k . ']');

            $html .= '<li><label>' . self::input_radiobox_tag($name, $k, $attributes) . ' ' . $v . '</></li>';
        }

        return '<ul class="list-unstyled list-radioboxes ' . ($attributes['ul-class'] ?? '') . '">' . $html . '</li>';
    }

    public static function select_radioboxes_button($name, $choices = [], $value = '', $attributes = [])
    {
        $html = '';

        foreach ($choices as $k => $v) {
            if (in_array($k, explode(',', $value))) {
                $attributes['checked'] = true;
            } else {
                $attributes['checked'] = false;
            }

            $attributes['id'] = self::generate_id_from_name($name . '[' . $k . ']');
            $attributes['class'] = 'toggle';

            $html .= '<label class="btn btn-default ' . ($attributes['checked'] ? 'active' : '') . '">' . self::input_radiobox_tag(
                    $name,
                    $k,
                    $attributes
                ) . ' ' . $v . '</label>';
        }

        return '<div class="btn-group" data-toggle="buttons">' . $html . '</div>';
    }

    public static function select_checkboxes_button($name, $choices = [], $value = '', $attributes = [])
    {
        $html = '';

        if (is_array($value)) {
            $value = implode(',', $value);
        }

        foreach ($choices as $k => $v) {
            $attributes = self::getAttributes($value, $attributes, $k, $name);
            $attributes['class'] = 'toggle';

            $html .= '<label class="btn btn-default ' . ($attributes['checked'] ? 'active' : '') . '">' . self::input_checkbox_tag(
                    $name . '[]',
                    $k,
                    $attributes
                ) . ' ' . $v . '</label>';
        }

        return '<div class="btn-group" data-toggle="buttons">' . $html . '</div> <label for="' . $name . '[]" class="error"></label>';
    }

    public static function textarea_tag($name, $value = '', $attributes = [])
    {
        $default = ['name' => $name, 'id' => self::generate_id_from_name($name), 'wrap' => 'soft'];

        return '<textarea ' . self::tag_attributes_to_html($default, $attributes) . '>' . htmlspecialchars(
                (string)$value,
                ENT_NOQUOTES,
                'UTF-8'
            ) . '</textarea>';
    }

    public static function button_tag(
        $value,
        $url,
        $is_dialog = true,
        $attributes = [],
        $left_icon = '',
        $right_icon = ''
    ) {
        $default = ['class' => 'btn btn-primary', 'type' => 'button'];

        if (strlen($left_icon) > 0) {
            $left_icon = \Helpers\App::app_render_icon($left_icon) . ' ';
        }
        if (strlen($right_icon) > 0) {
            $right_icon = ' ' . \Helpers\App::app_render_icon($right_icon);
        }

        return '<button ' . ($is_dialog ? 'onClick="open_dialog(\'' . $url . '\'); return false;"' : (strlen(
                $url
            ) > 0 ? 'onClick="location.href=\'' . $url . '\'"' : '')) . ' ' . self::tag_attributes_to_html(
                $default,
                $attributes
            ) . '>' . $left_icon . $value . $right_icon . '</button>';
    }

    public static function button_icon($title, $class, $url, $is_dialog = true, $attributes = [])
    {
        $default = [
            'title' => $title,
            'class' => 'btn btn-default btn-xs purple'
        ];

        if ($is_dialog) {
            return '<a ' . self::tag_attributes_to_html(
                    $default,
                    $attributes
                ) . '  href="#" onClick="open_dialog(\'' . $url . '\'); return false;"><i class="' . $class . '"></i></a>';
        } else {
            if (isset($attributes['confirm']) and is_string($attributes['confirm'])) {
                $attributes['onclick'] = "return confirm('" . addslashes($attributes['confirm']) . "')";
            }

            return '<a ' . self::tag_attributes_to_html(
                    $default,
                    $attributes
                ) . '  href="' . $url . '"><i class="' . $class . '"></i></a>';
        }
    }

    public static function button_icon_delete($url, $is_dialog = true, $attributes = [])
    {
        return self::button_icon(\Base::instance()->TEXT_BUTTON_DELETE, 'fa fa-trash-o', $url, $is_dialog, $attributes);
    }

    public static function button_icon_edit($url, $is_dialog = true)
    {
        return self::button_icon(\Base::instance()->TEXT_BUTTON_EDIT, 'fa fa-edit', $url, $is_dialog);
    }

    public static function image_tag($path, $attributes = [])
    {
        $default = ['border' => '0'];

        return '<img src="' . $path . '" ' . self::tag_attributes_to_html($default, $attributes) . '>';
    }

    public static function select_button_tag($choices = [], $value = '', $btn_class = 'btn-default')
    {
        return '
    <div class="btn-group">
			<button type="button" class="btn ' . $btn_class . '">' . $value . '</button>
			<button type="button" class="btn ' . $btn_class . ' dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"><i class="fa fa-angle-down"></i></button>
			<ul class="dropdown-menu" role="menu">
				<li>
				' . implode('</li><li>', $choices) . '
				</li>
			</ul>
		</div>
    ';
    }

    public static function input_color($name, $color = '')
    {
        return '
          <div class="input-group input-small color colorpicker-default" data-color="' . (strlen(
                $color
            ) ? $color : '#cccccc') . '" >
                  <span class="input-group-btn"><button class="btn btn-default" type="button">&nbsp;</button></span>
                  ' . self::input_tag($name, $color, ['class' => 'form-control', 'style' => 'width: 95px']) . '                    
          </div>
         ';
    }

    public static function select_entities_tag($name, $choices = [], $value = '', $attributes = [])
    {
        $html = self::select_tag($name, $choices, $value, $attributes);

        $url = url_for(
            'items/select2_entities',
            'action=select_items&entity_id=' . $attributes['entities_id'] . '&path=' . $attributes['entities_id']
        );

        if (isset($attributes['parent_item_id'])) {
            $url .= '&parent_item_id=' . $attributes['parent_item_id'];
        }

        $field_id = self::generate_id_from_name($name);

        $is_tree_view = (isset($attributes['is_tree_view']) and $attributes['is_tree_view'] == 1) ? 1 : 0;

        $html .= '
  	<script>
			$(function(){	
  			
  			$("#' . $field_id . '").select2({		      
				    width: "100%",		      
				    dropdownParent: $("#ajax-modal"),
				    "language":{
				      "noResults" : function () { return "' . addslashes(\Base::instance()->TEXT_NO_RESULTS_FOUND) . '"; },
				  		"searching" : function () { return "' . addslashes(\Base::instance()->TEXT_SEARCHING) . '"; },
				  		"errorLoading" : function () { return "' . addslashes(
                \Base::instance()->TEXT_RESULTS_COULD_NOT_BE_LOADED
            ) . '"; },
				  		"loadingMore" : function () { return "' . addslashes(
                \Base::instance()->TEXT_LOADING_MORE_RESULTS
            ) . '"; }		    				
				    },	
                                    allowClear: true,
                                    placeholder: \'' . addslashes(\Base::instance()->TEXT_PLEASE_SELECT_ITEMS) . '\',
				    ajax: {
				  		url: "' . $url . '",
				  		dataType: "json",                                                
				  		data: function (params) {
					      var query = {
					        search: params.term,
					        page: params.page || 1,
                                                is_tree_view: ' . $is_tree_view . '
					      }
					
					      // Query parameters will be ?search=[term]&page=[page]
					      return query;
					    },        				        				
				  	},        				
						templateResult: function (d) { return $(d.html); },      		        			
					});
				
				  $("#' . $field_id . '").change(function (e) {
						$("#' . $field_id . '-error").remove();
					});
								
				})
			</script>
  			';

        return $html;
    }

    public static function getAlerts()
    {
        $alerts = '';

        $messages = \K::flash()->getMessages();
        foreach ($messages as $message) {
            switch ($message['status']) {
                case 'error':
                    $class = 'alert-danger';
                    break;
                case 'warning':
                    $class = 'alert-warning';
                    break;
                case 'success':
                    $class = 'alert-success';
                    break;
                default:
                    $class = 'alert-info';
                    break;
            }

            $alerts .= '<div class="alert ' . $class . '"><button type="button" class="close" data-dismiss="alert">&times;</button>' . $message['text'] . '</div>' . PHP_EOL;
        }

        return $alerts;
    }

    private static function getAttributes($value, $attributes, $k, $name)
    {
        $attributes['checked'] = false;
        if (strlen($value) == 0) {
            $attributes['checked'] = false;
        } elseif (in_array($k, explode(',', $value))) {
            $attributes['checked'] = true;
        }

        $attributes['id'] = self::generate_id_from_name($name . '[' . $k . ']');
        return $attributes;
    }
}
