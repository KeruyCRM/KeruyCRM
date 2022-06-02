<h3 class="page-title"><?php
    echo TEXT_HEADING_MY_ACCOUNT ?></h3>

<div class="portlet">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-reorder"></i><?php
            echo TEXT_DETAILS ?>
        </div>
    </div>
    <div class="portlet-body form paretn-items-form">


        <?php
        $is_new_item = false;
        $app_items_form_name = 'account_form';
        $_GET['id'] = $app_user['id'];
        $current_path = 1;
        $_GET['path'] = 1;
        echo form_tag(
            'account_form',
            url_for('users/account', 'action=update'),
            ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']
        )
        ?>
        <div class="form-body">

            <?php

            $excluded_fileds_types = "'fieldtype_user_accessgroups','fieldtype_user_status','fieldtype_user_skin','fieldtype_text_pattern'";

            if (CFG_ALLOW_CHANGE_USERNAME == 0) {
                $excluded_fileds_types .= ",'fieldtype_user_username'";
            }

            $count_tabs = db_count('app_forms_tabs', $current_entity_id, "entities_id");


            $html_cfg = '
		   <div class="form-group">
        	<label class="col-md-3 control-label" for="cfg_disable_notification">' . tooltip_icon(
                    TEXT_DISABLE_NOTIFICATIONS_INFO
                ) . TEXT_DISABLE_EMAIL_NOTIFICATIONS . '</label>
          <div class="col-md-9">	
        	  <p class="form-control-static">' . input_checkbox_tag(
                    'cfg[disable_notification]',
                    1,
                    ['checked' => $app_users_cfg->get('disable_notification')]
                ) . '</p>               
          </div>			
       </div>
  		<div class="form-group">
        	<label class="col-md-3 control-label" for="cfg_disable_internal_notification">' . tooltip_icon(
                    TEXT_DISABLE_INTERNAL_NOTIFICATIONS_INFO
                ) . TEXT_DISABLE_INTERNAL_NOTIFICATIONS . '</label>
          <div class="col-md-9">	
        	  <p class="form-control-static">' . input_checkbox_tag(
                    'cfg[disable_internal_notification]',
                    1,
                    ['checked' => $app_users_cfg->get('disable_internal_notification')]
                ) . '</p>               
          </div>			
       </div>
       <div class="form-group">
        	<label class="col-md-3 control-label" for="cfg_disable_highlight_unread">' . tooltip_icon(
                    TEXT_DISABLE_HIGHLIGH_UNREAD_INFO
                ) . TEXT_DISABLE_HIGHLIGH_UNREAD . '</label>
          <div class="col-md-9">	
        	  <p class="form-control-static">' . input_checkbox_tag(
                    'cfg[disable_highlight_unread]',
                    1,
                    ['checked' => $app_users_cfg->get('disable_highlight_unread')]
                ) . '</p>               
          </div>			
       </div> 	  		
				  		
		';

            $fields_access_schema = users::get_fields_access_schema($current_entity_id, $app_user['group_id']);

            $html = '';

            if ($count_tabs > 1) {
                $count = 0;

                $html = '<ul class="nav nav-tabs" id="form_tabs"> ' . forms_tabs::render_tabs_nav(
                        $current_entity_id
                    ) . '</ul>';

                $html .= '<div class="tab-content">';
                $count = 0;

                $tabs_tree = forms_tabs::get_tree($current_entity_id);
                foreach ($tabs_tree as $tabs) {
                    $html .= '
        <div class="tab-pane ' . ($count == 0 ? 'active' : '') . '" id="form_tab_' . $tabs['id'] . '">
          ' . (strlen($tabs['description']) ? '<p>' . $tabs['description'] . '</p>' : '');

                    $count_fields = 0;
                    $fields_query = db_query(
                        "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_type_list_excluded_in_form(
                        ) . "," . $excluded_fileds_types . ") and  f.entities_id='" . db_input(
                            $current_entity_id
                        ) . "' and f.forms_tabs_id=t.id and f.forms_tabs_id='" . db_input(
                            $tabs['id']
                        ) . "' and length(f.forms_rows_position)=0 order by t.sort_order, t.name, f.sort_order, f.name"
                    );
                    while ($v = db_fetch_array($fields_query)) {
                        if ($v['type'] == 'fieldtype_user_language' and count(app_get_languages_choices()) == 1) {
                            $html .= input_hidden_tag('fields[' . $v['id'] . ']', CFG_APP_LANGUAGE);
                            continue;
                        }

                        if (isset($fields_access_schema[$v['id']])) {
                            if ($fields_access_schema[$v['id']] == 'hide') {
                                continue;
                            } elseif ($fields_access_schema[$v['id']] == 'view' and strlen($obj['field_' . $v['id']])) {
                                $output_options = [
                                    'class' => $v['type'],
                                    'value' => $obj['field_' . $v['id']],
                                    'field' => $v,
                                    'item' => $obj,
                                    'path' => $current_entity_id . '-' . $app_user['id']
                                ];

                                $output = fields_types::output($output_options);

                                if (strlen($output)) {
                                    $html .= '
			          <div class="form-group form-group-' . $v['id'] . '">
			          	<label class="col-md-3 control-label" for="fields_' . $v['id'] . '">' . fields_types::get_option(
                                            $v['type'],
                                            'name',
                                            $v['name']
                                        ) . '</label>
			            <div class="col-md-9">
			          	  <p class="form-control-static">' . $output . '</p>
			            </div>
			          </div>
		        	';
                                }
                            }
                        } elseif ($v['type'] == 'fieldtype_section') {
                            $html .= '<div class="form-group-' . $v['id'] . '">' . fields_types::render(
                                    $v['type'],
                                    $v,
                                    $obj,
                                    ['count_fields' => $count_fields]
                                ) . '</div>';
                        } elseif ($v['type'] == 'fieldtype_dropdown_multilevel') {
                            $html .= fields_types::render(
                                $v['type'],
                                $v,
                                $obj,
                                [
                                    'parent_entity_item_id' => $parent_entity_item_id,
                                    'form' => 'item',
                                    'is_new_item' => $is_new_item
                                ]
                            );
                        } else {
                            $v['is_required'] = (in_array(
                                $v['type'],
                                [
                                    'fieldtype_user_firstname',
                                    'fieldtype_user_lastname',
                                    'fieldtype_user_username',
                                    'fieldtype_user_email'
                                ]
                            ) ? 1 : $v['is_required']);

                            $html .= '
	        <div class="form-group form-group-' . $v['id'] . ' form-group-' . $v['type'] . '">
	            <label class="col-md-3 control-label" for="fields_' . $v['id'] . '">' .
                                ($v['is_required'] == 1 ? '<span class="required-label">*</span>' : '') .
                                ($v['tooltip_display_as'] == 'icon' ? tooltip_icon($v['tooltip']) : '') .
                                fields_types::get_option($v['type'], 'name', $v['name']) . '</label>
	            <div class="col-md-9">	
	          	' . fields_types::render(
                                    $v['type'],
                                    $v,
                                    $obj,
                                    [
                                        'is_new_item' => false,
                                        'parent_entity_item_id' => $obj['parent_item_id'],
                                        'form' => 'item'
                                    ]
                                )
                                . ($v['tooltip_display_as'] != 'icon' ? tooltip_text($v['tooltip']) : '') . '
	            </div>			
	         </div>
	        ';
                        }

                        $count_fields++;
                    }

                    //handle rows
                    $forms_rows = new forms_rows($current_entity_id, $tabs['id']);
                    $forms_rows->fields_access_schema = $fields_access_schema;
                    $forms_rows->obj = $obj;
                    $forms_rows->is_new_item = $is_new_item;
                    $forms_rows->parent_entity_item_id = $obj['parent_item_id'];
                    $forms_rows->excluded_fileds_types = $excluded_fileds_types;
                    $html .= $forms_rows->render();

                    if ($count == 0) {
                        $html .= $html_cfg;
                    }

                    $html .= '</div>';

                    $count++;
                }

                $html .= '</div>';
            } else {
                $tabs_query = db_fetch_all(
                    'app_forms_tabs',
                    "entities_id='" . db_input($current_entity_id) . "' order by  sort_order, name"
                );
                $tabs = db_fetch_array($tabs_query);

                $count_fields = 0;
                $fields_query = db_query(
                    "select f.* from app_fields f where f.type not in (" . fields_types::get_type_list_excluded_in_form(
                    ) . "," . $excluded_fileds_types . ") and  f.entities_id='" . db_input(
                        $current_entity_id
                    ) . "' and length(f.forms_rows_position)=0 order by f.sort_order, f.name"
                );
                while ($v = db_fetch_array($fields_query)) {
                    if ($v['type'] == 'fieldtype_user_language' and count(app_get_languages_choices()) == 1) {
                        $html .= input_hidden_tag('fields[' . $v['id'] . ']', CFG_APP_LANGUAGE);
                        continue;
                    }

                    if (isset($fields_access_schema[$v['id']])) {
                        if ($fields_access_schema[$v['id']] == 'hide') {
                            continue;
                        } elseif ($fields_access_schema[$v['id']] == 'view' and strlen($obj['field_' . $v['id']])) {
                            $output_options = [
                                'class' => $v['type'],
                                'value' => $obj['field_' . $v['id']],
                                'field' => $v,
                                'item' => $obj,
                                'path' => $current_entity_id . '-' . $app_user['id']
                            ];

                            $output = fields_types::output($output_options);

                            if (strlen($output)) {
                                $html .= '
		          <div class="form-group form-group-' . $v['id'] . '">
		          	<label class="col-md-3 control-label" for="fields_' . $v['id'] . '">' . fields_types::get_option(
                                        $v['type'],
                                        'name',
                                        $v['name']
                                    ) . '</label>
		            <div class="col-md-9">
		          	  <p class="form-control-static">' . $output . '</p>
		            </div>
		          </div>
		        ';
                            }
                        }
                    } elseif ($v['type'] == 'fieldtype_section') {
                        $html .= '<div class="form-group-' . $v['id'] . '">' . fields_types::render(
                                $v['type'],
                                $v,
                                $obj,
                                ['count_fields' => $count_fields]
                            ) . '</div>';
                    } elseif ($v['type'] == 'fieldtype_dropdown_multilevel') {
                        $html .= fields_types::render(
                            $v['type'],
                            $v,
                            $obj,
                            [
                                'parent_entity_item_id' => $parent_entity_item_id,
                                'form' => 'item',
                                'is_new_item' => $is_new_item
                            ]
                        );
                    } else {
                        $v['is_required'] = (in_array(
                            $v['type'],
                            [
                                'fieldtype_user_firstname',
                                'fieldtype_user_lastname',
                                'fieldtype_user_username',
                                'fieldtype_user_email'
                            ]
                        ) ? 1 : $v['is_required']);

                        $html .= '
	        <div class="form-group form-group-' . $v['id'] . '">
                    <label class="col-md-3 control-label" for="fields_' . $v['id'] . '">' .
                            ($v['is_required'] == 1 ? '<span class="required-label">*</span>' : '') .
                            ($v['tooltip_display_as'] == 'icon' ? tooltip_icon($v['tooltip']) : '') .
                            fields_types::get_option($v['type'], 'name', $v['name']) . '</label>
                        
                    <div class="col-md-9">	
	        	' . fields_types::render(
                                $v['type'],
                                $v,
                                $obj,
                                [
                                    'is_new_item' => false,
                                    'parent_entity_item_id' => $obj['parent_item_id'],
                                    'form' => 'item'
                                ]
                            )
                            . ($v['tooltip_display_as'] != 'icon' ? tooltip_text($v['tooltip']) : '') . '
                    </div>			
	        </div>
	      ';
                    }

                    $count_fields++;
                }

                //handle rows
                $forms_rows = new forms_rows($current_entity_id, $tabs['id']);
                $forms_rows->fields_access_schema = $fields_access_schema;
                $forms_rows->obj = $obj;
                $forms_rows->is_new_item = $is_new_item;
                $forms_rows->parent_entity_item_id = $obj['parent_item_id'];
                $forms_rows->excluded_fileds_types = $excluded_fileds_types;
                $html .= $forms_rows->render();

                $html .= $html_cfg;
            }

            echo $html;

            //check ruels for hidden fields by access
            echo forms_fields_rules::prepare_hidden_fields($current_entity_id, $obj, $fields_access_schema);

            ?>

            <div id="form-error-container"></div>

        </div>

        <div class="form-actions fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-offset-3 col-md-9">
                        <?php
                        echo submit_tag(TEXT_BUTTON_SAVE, ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
        </div>

        <?php
        //hidden user group value to handle fileds displays rules.
        echo '<input type="hidden" value="' . $app_user['group_id'] . '" id="field_6" class="field_6">';
        ?>

        </form>

    </div>
</div>

<style>
    .bg-color-value {
        display: inline-block;
    }
</style>

<?php
if (is_ext_installed()) {
    $smart_input = new smart_input($current_entity_id);
    echo $smart_input->render();
}
?>

<?php
require(component_path('items/items_form.js')); ?>

<?php
echo forms_fields_rules::hidden_form_fields($current_entity_id) ?>

<script>
    $(function () {

        //validate user photo
        $("#fields_10").rules("add", {
            required: false,
            extension: "gif|jpeg|jpg|png"
        });

        $('#form_tabs a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        })

    });
</script>