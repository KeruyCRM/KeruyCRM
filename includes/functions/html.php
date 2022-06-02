<?php

function tag_attributes_to_html($default, $attributes = [])
{
    $attributes = array_merge($default, $attributes);

    return implode(
        '',
        array_map('tag_attributes_to_html_callback', array_keys($attributes), array_values($attributes))
    );
}

function tag_attributes_to_html_callback($k, $v)
{
    return false === $v || null === $v || ('' === $v && 'value' != $k) ? '' : sprintf(
        ' %s="%s"',
        $k,
        htmlspecialchars((string)$v, ENT_QUOTES)
    );
}

function generate_id_from_name($name)
{
    // check to see if we have an array variable for a field name
    if (strstr($name, '[')) {
        $name = str_replace(['[]', '][', '[', ']'], ['', '_', '_', ''], $name);
    }

    // remove illegal characters
    $name = preg_replace(['/^[^A-Za-z]+/', '/[^A-Za-z0-9\:_\.\-]/'], ['', '_'], $name);

    return $name;
}

function form_tag($name, $action, $attributes = [])
{
    global $app_session_token;

    $default = ['name' => $name, 'id' => generate_id_from_name($name), 'method' => 'post'];

    return '<form action="' . $action . '" ' . tag_attributes_to_html($default, $attributes) . '> ' . input_hidden_tag(
            'form_session_token',
            $app_session_token
        );
}

function input_tag($name = '', $value = '', $attributes = [])
{
    $default = ['name' => $name, 'id' => generate_id_from_name($name), 'value' => $value, 'type' => 'text'];

    return '<input ' . tag_attributes_to_html($default, $attributes) . '>';
}

function submit_tag($value = '', $attributes = [])
{
    $attributes = array_merge(['type' => 'submit', 'class' => 'btn btn-primary'], $attributes);

    return input_tag('', $value, $attributes);
}

function input_password_tag($name, $attributes = [])
{
    $attributes = array_merge($attributes, ['type' => 'password']);

    return input_tag($name, '', $attributes);
}

function input_file_tag($name, $attributes = [])
{
    $attributes = array_merge($attributes, ['type' => 'file']);

    return input_tag($name, '', $attributes);
}

function input_hidden_tag($name, $value = '', $attributes = [])
{
    $attributes = array_merge($attributes, ['type' => 'hidden']);

    return input_tag($name, $value, $attributes);
}

function input_checkbox_tag($name, $value = '1', $attributes = [])
{
    $attributes = array_merge($attributes, ['type' => 'checkbox']);

    if (isset($attributes['checked'])) {
        if (is_numeric($attributes['checked'])) {
            $attributes['checked'] = (bool)$attributes['checked'];
        }
    }

    return input_tag($name, $value, $attributes);
}

function input_radiobox_tag($name, $value = '1', $attributes = [])
{
    $attributes = array_merge($attributes, ['type' => 'radio']);

    if (isset($attributes['checked'])) {
        if (is_numeric($attributes['checked'])) {
            $attributes['checked'] = (bool)$attributes['checked'];
        }
    }

    return input_tag($name, $value, $attributes);
}

function select_tag($name, $choices = [], $value = '', $attributes = [])
{
    $default = ['name' => $name, 'id' => generate_id_from_name($name)];

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
            $html .= '<option ' . (in_array($k, $value) ? 'selected' : '') . ' value="' . $k . '">' . htmlspecialchars(
                    (string)$v,
                    ENT_QUOTES
                ) . '</option>';
        }
    }

    return '<select ' . tag_attributes_to_html($default, $attributes) . '>' . $html . '</select>';
}

function select_tag_with_color($name, $choices = [], $value = '', $attributes = [])
{
    $default = ['name' => $name, 'id' => generate_id_from_name($name)];

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

    return '<select ' . tag_attributes_to_html($default, $attributes) . '>' . $html . '</select>';
}

function select_checkboxes_tag($name, $choices = [], $value = '', $attributes = [])
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

                $attributes['id'] = generate_id_from_name($name . '[' . $kk . ']');

                $html .= '<div><label>' . input_checkbox_tag(
                        $name . '[]',
                        $kk,
                        $attributes
                    ) . ' ' . $vv . '</label></div>';
            }
        } else {
            if (strlen($value) == 0) {
                $attributes['checked'] = false;
            } elseif (in_array($k, explode(',', $value))) {
                $attributes['checked'] = true;
            } else {
                $attributes['checked'] = false;
            }

            $attributes['id'] = generate_id_from_name($name . '[' . $k . ']');

            $html .= '<div><label>' . input_checkbox_tag($name . '[]', $k, $attributes) . ' ' . $v . '</label></div>';
        }
    }

    return '<div class="select_checkboxes_tag">' . $html . '</div> <label for="' . $name . '[]" class="error"></label>';
}

function select_radioboxes_tag($name, $choices = [], $value = '', $attributes = [])
{
    $html = '';

    foreach ($choices as $k => $v) {
        if (in_array($k, explode(',', $value))) {
            $attributes['checked'] = true;
        } else {
            $attributes['checked'] = false;
        }

        $attributes['id'] = generate_id_from_name($name . '[' . $k . ']');

        $html .= '<div><label>' . input_radiobox_tag($name, $k, $attributes) . ' ' . $v . '</label></div>';
    }

    return '<div class="select_checkboxes_tag">' . $html . '</div>';
}

function select_checkboxes_ul_tag($name, $choices = [], $value = '', $attributes = [])
{
    $html = '';

    foreach ($choices as $k => $v) {
        $attributes['checked'] = (strlen($value) and in_array($k, explode(',', $value))) ? true : false;

        $attributes['id'] = generate_id_from_name($name . '[' . $k . ']');

        $html .= '<li><label>' . input_checkbox_tag($name . '[]', $k, $attributes) . ' ' . $v . '</></li>';
    }

    return '<ul class="list-unstyled checkboxes ' . ($attributes['ul-class'] ?? '') . '">' . $html . '</li>';
}

function select_checkboxes_ul_color_tag($name, $choices = [], $value = '', $attributes = [])
{
    $html = '';

    foreach ($choices as $k => $v) {
        $title = $v['name'] ?? '';
        $color = $v['color'] ?? '';

        $attributes['checked'] = (strlen($value) and in_array($k, explode(',', $value))) ? true : false;

        $attributes['id'] = generate_id_from_name($name . '[' . $k . ']');


        $html .= '<li><label style="color: ' . $color . '">' . input_checkbox_tag(
                $name . '[]',
                $k,
                $attributes
            ) . ' ' . $title . '</></li>';
    }

    return '<ul class="list-unstyled checkboxes ' . ($attributes['ul-class'] ?? '') . '">' . $html . '</li>';
}

function select_radioboxes_ul_tag($name, $choices = [], $value = '', $attributes = [])
{
    $html = '';

    foreach ($choices as $k => $v) {
        $attributes['checked'] = (strlen($value) and in_array($k, explode(',', $value))) ? true : false;

        $attributes['id'] = generate_id_from_name($name . '[' . $k . ']');

        $html .= '<li><label>' . input_radiobox_tag($name, $k, $attributes) . ' ' . $v . '</></li>';
    }

    return '<ul class="list-unstyled list-radioboxes ' . ($attributes['ul-class'] ?? '') . '">' . $html . '</li>';
}

function select_radioboxes_button($name, $choices = [], $value = '', $attributes = [])
{
    $html = '';

    foreach ($choices as $k => $v) {
        if (in_array($k, explode(',', $value))) {
            $attributes['checked'] = true;
        } else {
            $attributes['checked'] = false;
        }

        $attributes['id'] = generate_id_from_name($name . '[' . $k . ']');
        $attributes['class'] = 'toggle';

        $html .= '<label class="btn btn-default ' . ($attributes['checked'] ? 'active' : '') . '">' . input_radiobox_tag(
                $name,
                $k,
                $attributes
            ) . ' ' . $v . '</label>';
    }

    return '<div class="btn-group" data-toggle="buttons">' . $html . '</div>';
}

function select_checkboxes_button($name, $choices = [], $value = '', $attributes = [])
{
    $html = '';

    if (is_array($value)) {
        $value = implode(',', $value);
    }

    foreach ($choices as $k => $v) {
        if (strlen($value) == 0) {
            $attributes['checked'] = false;
        } elseif (in_array($k, explode(',', $value))) {
            $attributes['checked'] = true;
        } else {
            $attributes['checked'] = false;
        }

        $attributes['id'] = generate_id_from_name($name . '[' . $k . ']');
        $attributes['class'] = 'toggle';

        $html .= '<label class="btn btn-default ' . ($attributes['checked'] ? 'active' : '') . '">' . input_checkbox_tag(
                $name . '[]',
                $k,
                $attributes
            ) . ' ' . $v . '</label>';
    }

    return '<div class="btn-group" data-toggle="buttons">' . $html . '</div> <label for="' . $name . '[]" class="error"></label>';
}

function textarea_tag($name, $value = '', $attributes = [])
{
    $default = ['name' => $name, 'id' => generate_id_from_name($name), 'wrap' => 'soft'];

    return '<textarea ' . tag_attributes_to_html($default, $attributes) . '>' . htmlspecialchars(
            (string)$value,
            ENT_NOQUOTES,
            'UTF-8'
        ) . '</textarea>';
}

function button_tag($value, $url, $is_dialog = true, $attributes = [], $left_icon = '', $right_icon = '')
{
    $default = ['class' => 'btn btn-primary', 'type' => 'button'];

    if (strlen($left_icon) > 0) {
        $left_icon = app_render_icon($left_icon) . ' ';
    }
    if (strlen($right_icon) > 0) {
        $right_icon = ' ' . app_render_icon($right_icon);
    }

    return '<button ' . ($is_dialog ? 'onClick="open_dialog(\'' . $url . '\'); return false;"' : (strlen(
            $url
        ) > 0 ? 'onClick="location.href=\'' . $url . '\'"' : '')) . ' ' . tag_attributes_to_html(
            $default,
            $attributes
        ) . '>' . $left_icon . $value . $right_icon . '</button>';
}

function button_icon($title, $class, $url, $is_dialog = true, $attributes = [])
{
    $default = [
        'title' => $title,
        'class' => 'btn btn-default btn-xs purple'
    ];

    if ($is_dialog) {
        return '<a ' . tag_attributes_to_html(
                $default,
                $attributes
            ) . '  href="#" onClick="open_dialog(\'' . $url . '\'); return false;"><i class="' . $class . '"></i></a>';
    } else {
        if (isset($attributes['confirm']) and is_string($attributes['confirm'])) {
            $attributes['onclick'] = "return confirm('" . addslashes($attributes['confirm']) . "')";
        }

        return '<a ' . tag_attributes_to_html(
                $default,
                $attributes
            ) . '  href="' . $url . '"><i class="' . $class . '"></i></a>';
    }
}

function button_icon_delete($url, $is_dialog = true, $attributes = [])
{
    return button_icon(TEXT_BUTTON_DELETE, 'fa fa-trash-o', $url, $is_dialog, $attributes);
}

function button_icon_edit($url, $is_dialog = true)
{
    return button_icon(TEXT_BUTTON_EDIT, 'fa fa-edit', $url, $is_dialog);
}

function image_tag($path, $attributes = [])
{
    $default = ['border' => '0'];

    return '<img src="' . $path . '" ' . tag_attributes_to_html($default, $attributes) . '>';
}

function select_button_tag($choices = [], $value = '', $btn_class = 'btn-default')
{
    $html = '
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

    return $html;
}

function input_color($name, $color = '')
{
    $html = '
          <div class="input-group input-small color colorpicker-default" data-color="' . (strlen(
            $color
        ) ? $color : '#cccccc') . '" >
                  <span class="input-group-btn"><button class="btn btn-default" type="button">&nbsp;</button></span>
                  ' . input_tag($name, $color, ['class' => 'form-control', 'style' => 'width: 95px']) . '                    
          </div>
         ';

    return $html;
}

function select_entities_tag($name, $choices = [], $value = '', $attributes = [])
{
    $html = select_tag($name, $choices, $value, $attributes);

    $url = url_for(
        'items/select2_entities',
        'action=select_items&entity_id=' . $attributes['entities_id'] . '&path=' . $attributes['entities_id']
    );

    if (isset($attributes['parent_item_id'])) {
        $url .= '&parent_item_id=' . $attributes['parent_item_id'];
    }

    $field_id = generate_id_from_name($name);

    $is_tree_view = (isset($attributes['is_tree_view']) and $attributes['is_tree_view'] == 1) ? 1 : 0;

    $html .= '
  	<script>
			$(function(){	
  			
  			$("#' . $field_id . '").select2({		      
				    width: "100%",		      
				    dropdownParent: $("#ajax-modal"),
				    "language":{
				      "noResults" : function () { return "' . addslashes(TEXT_NO_RESULTS_FOUND) . '"; },
				  		"searching" : function () { return "' . addslashes(TEXT_SEARCHING) . '"; },
				  		"errorLoading" : function () { return "' . addslashes(TEXT_RESULTS_COULD_NOT_BE_LOADED) . '"; },
				  		"loadingMore" : function () { return "' . addslashes(TEXT_LOADING_MORE_RESULTS) . '"; }		    				
				    },	
                                    allowClear: true,
                                    placeholder: \'' . addslashes(TEXT_PLEASE_SELECT_ITEMS) . '\',
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
