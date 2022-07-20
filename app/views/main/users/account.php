<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<h3 class="page-title"><?= \K::$fw->TEXT_HEADING_MY_ACCOUNT ?></h3>

<div class="portlet">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-reorder"></i><?= \K::$fw->TEXT_DETAILS ?>
        </div>
    </div>
    <div class="portlet-body form paretn-items-form">


        <?php
        $is_new_item = false;
        \K::$fw->app_items_form_name = 'account_form';
        \K::$fw->GET['id'] = \K::$fw->app_user['id'];
        \K::$fw->current_path = 1;
        \K::$fw->GET['path'] = 1;
        echo \Helpers\Html::form_tag(
            'account_form',
            \Helpers\Urls::url_for('main/users/account/update'),
            ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']
        )
        ?>
        <div class="form-body">

            <?php

            $excluded_fileds_types = "'fieldtype_user_accessgroups','fieldtype_user_status','fieldtype_user_skin','fieldtype_text_pattern'";

            if (\K::$fw->CFG_ALLOW_CHANGE_USERNAME == 0) {
                $excluded_fileds_types .= ",'fieldtype_user_username'";
            }

            $count_tabs = \K::model()->db_count('app_forms_tabs', \K::$fw->current_entity_id, 'entities_id');

            $html_cfg = '
		   <div class="form-group">
        	<label class="col-md-3 control-label" for="cfg_disable_notification">' . \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_DISABLE_NOTIFICATIONS_INFO
                ) . \K::$fw->TEXT_DISABLE_EMAIL_NOTIFICATIONS . '</label>
          <div class="col-md-9">	
        	  <p class="form-control-static">' . \Helpers\Html::input_checkbox_tag(
                    'cfg[disable_notification]',
                    1,
                    ['checked' => \K::app_users_cfg()->get('disable_notification')]
                ) . '</p>               
          </div>			
       </div>
  		<div class="form-group">
        	<label class="col-md-3 control-label" for="cfg_disable_internal_notification">' . \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_DISABLE_INTERNAL_NOTIFICATIONS_INFO
                ) . \K::$fw->TEXT_DISABLE_INTERNAL_NOTIFICATIONS . '</label>
          <div class="col-md-9">	
        	  <p class="form-control-static">' . \Helpers\Html::input_checkbox_tag(
                    'cfg[disable_internal_notification]',
                    1,
                    ['checked' => \K::app_users_cfg()->get('disable_internal_notification')]
                ) . '</p>               
          </div>			
       </div>
       <div class="form-group">
        	<label class="col-md-3 control-label" for="cfg_disable_highlight_unread">' . \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_DISABLE_HIGHLIGHT_UNREAD_INFO
                ) . \K::$fw->TEXT_DISABLE_HIGHLIGHT_UNREAD . '</label>
          <div class="col-md-9">	
        	  <p class="form-control-static">' . \Helpers\Html::input_checkbox_tag(
                    'cfg[disable_highlight_unread]',
                    1,
                    ['checked' => \K::app_users_cfg()->get('disable_highlight_unread')]
                ) . '</p>               
          </div>			
       </div> 	  		
				  		
		';

            $fields_access_schema = \Models\Main\Users\Users::get_fields_access_schema(
                \K::$fw->current_entity_id,
                \K::$fw->app_user['group_id']
            );

            $html = '';

            if ($count_tabs > 1) {
                $count = 0;

                $html = '<ul class="nav nav-tabs" id="form_tabs"> ' . \Models\Main\Forms_tabs::render_tabs_nav(
                        \K::$fw->current_entity_id
                    ) . '</ul>';

                $html .= '<div class="tab-content">';
                $count = 0;

                $tabs_tree = \Models\Main\Forms_tabs::get_tree(\K::$fw->current_entity_id);
                foreach ($tabs_tree as $tabs) {
                    $html .= '
        <div class="tab-pane ' . ($count == 0 ? 'active' : '') . '" id="form_tab_' . $tabs['id'] . '">
          ' . (strlen($tabs['description']) ? '<p>' . $tabs['description'] . '</p>' : '');

                    $count_fields = 0;
                    $fields_query = \K::model()->db_query_exec(
                        "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . \Models\Main\Fields_types::get_type_list_excluded_in_form(
                        ) . "," . $excluded_fileds_types . ") and f.entities_id = ? and f.forms_tabs_id = t.id and f.forms_tabs_id = ? and length(f.forms_rows_position) = 0 order by t.sort_order, t.name, f.sort_order, f.name",
                        [
                            \K::$fw->current_entity_id,
                            $tabs['id']
                        ]
                    );

                    //while ($v = db_fetch_array($fields_query)) {
                    foreach ($fields_query as $v) {
                        if ($v['type'] == 'fieldtype_user_language' and count(
                                \Helpers\App::app_get_languages_choices()
                            ) == 1) {
                            $html .= \Helpers\Html::input_hidden_tag(
                                'fields[' . $v['id'] . ']',
                                \K::$fw->CFG_APP_LANGUAGE
                            );
                            continue;
                        }

                        if (isset($fields_access_schema[$v['id']])) {
                            if ($fields_access_schema[$v['id']] == 'hide') {
                                continue;
                            } elseif ($fields_access_schema[$v['id']] == 'view' and strlen(
                                    \K::$fw->obj['field_' . $v['id']]
                                )) {
                                $output_options = [
                                    'class' => $v['type'],
                                    'value' => \K::$fw->obj['field_' . $v['id']],
                                    'field' => $v,
                                    'item' => \K::$fw->obj,
                                    'path' => \K::$fw->current_entity_id . '-' . \K::$fw->app_user['id']
                                ];

                                $output = \Models\Main\Fields_types::output($output_options);

                                if (strlen($output)) {
                                    $html .= '
			          <div class="form-group form-group-' . $v['id'] . '">
			          	<label class="col-md-3 control-label" for="fields_' . $v['id'] . '">' . \Models\Main\Fields_types::get_option(
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
                            $html .= '<div class="form-group-' . $v['id'] . '">' . \Models\Main\Fields_types::render(
                                    $v['type'],
                                    $v,
                                    \K::$fw->obj,
                                    ['count_fields' => $count_fields]
                                ) . '</div>';
                        } elseif ($v['type'] == 'fieldtype_dropdown_multilevel') {
                            $html .= \Models\Main\Fields_types::render(
                                $v['type'],
                                $v,
                                \K::$fw->obj,
                                [
                                    'parent_entity_item_id' => \K::$fw->parent_entity_item_id,
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
                                ($v['tooltip_display_as'] == 'icon' ? \Helpers\App::tooltip_icon($v['tooltip']) : '') .
                                \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']) . '</label>
	            <div class="col-md-9">	
	          	' . \Models\Main\Fields_types::render(
                                    $v['type'],
                                    $v,
                                    \K::$fw->obj,
                                    [
                                        'is_new_item' => false,
                                        'parent_entity_item_id' => \K::$fw->obj['parent_item_id'],
                                        'form' => 'item'
                                    ]
                                )
                                . ($v['tooltip_display_as'] != 'icon' ? \Helpers\App::tooltip_text(
                                    $v['tooltip']
                                ) : '') . '
	            </div>			
	         </div>
	        ';
                        }

                        $count_fields++;
                    }

                    //handle rows
                    $forms_rows = new \Models\Main\Forms_rows(\K::$fw->current_entity_id, $tabs['id']);
                    $forms_rows->fields_access_schema = $fields_access_schema;
                    $forms_rows->obj = \K::$fw->obj;
                    $forms_rows->is_new_item = $is_new_item;
                    $forms_rows->parent_entity_item_id = \K::$fw->obj['parent_item_id'];
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
                /*$tabs_query = db_fetch_all(
                    'app_forms_tabs',
                    "entities_id='" . db_input(\K::$fw->current_entity_id) . "' order by  sort_order, name"
                );

                $tabs = db_fetch_array($tabs_query);*/

                $tabs = \K::model()->db_fetch_one('app_forms_tabs', [
                    'entities_id = ?',
                    \K::$fw->current_entity_id
                ], ['order' => 'sort_order,name']);

                $count_fields = 0;
                /*$fields_query = db_query(
                    "select f.* from app_fields f where f.type not in (" . fields_types::get_type_list_excluded_in_form(
                    ) . "," . $excluded_fileds_types . ") and  f.entities_id='" . db_input(
                        \K::$fw->current_entity_id
                    ) . "' and length(f.forms_rows_position)=0 order by f.sort_order, f.name"
                );*/

                $fields_query = \K::model()->db_fetch('app_fields', [
                    'type not in (' . \Models\Main\Fields_types::get_type_list_excluded_in_form(
                    ) . "," . $excluded_fileds_types . ') and entities_id = ? and length(forms_rows_position) = 0',
                    \K::$fw->current_entity_id
                ], ['order' => 'sort_order,name']);

                //while ($v = db_fetch_array($fields_query)) {
                foreach ($fields_query as $v) {
                    $v = $v->cast();

                    if ($v['type'] == 'fieldtype_user_language' and count(
                            \Helpers\App::app_get_languages_choices()
                        ) == 1) {
                        $html .= \Helpers\Html::input_hidden_tag('fields[' . $v['id'] . ']', \K::$fw->CFG_APP_LANGUAGE);
                        continue;
                    }

                    if (isset($fields_access_schema[$v['id']])) {
                        if ($fields_access_schema[$v['id']] == 'hide') {
                            continue;
                        } elseif ($fields_access_schema[$v['id']] == 'view' and strlen(
                                \K::$fw->obj['field_' . $v['id']]
                            )) {
                            $output_options = [
                                'class' => $v['type'],
                                'value' => \K::$fw->obj['field_' . $v['id']],
                                'field' => $v,
                                'item' => \K::$fw->obj,
                                'path' => \K::$fw->current_entity_id . '-' . \K::$fw->app_user['id']
                            ];

                            $output = \Models\Main\Fields_types::output($output_options);

                            if (strlen($output)) {
                                $html .= '
		          <div class="form-group form-group-' . $v['id'] . '">
		          	<label class="col-md-3 control-label" for="fields_' . $v['id'] . '">' . \Models\Main\Fields_types::get_option(
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
                        $html .= '<div class="form-group-' . $v['id'] . '">' . \Models\Main\Fields_types::render(
                                $v['type'],
                                $v,
                                \K::$fw->obj,
                                ['count_fields' => $count_fields]
                            ) . '</div>';
                    } elseif ($v['type'] == 'fieldtype_dropdown_multilevel') {
                        $html .= \Models\Main\Fields_types::render(
                            $v['type'],
                            $v,
                            \K::$fw->obj,
                            [
                                'parent_entity_item_id' => \K::$fw->parent_entity_item_id,
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
                            ($v['tooltip_display_as'] == 'icon' ? \Helpers\App::tooltip_icon($v['tooltip']) : '') .
                            \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']) . '</label>
                        
                    <div class="col-md-9">	
	        	' . \Models\Main\Fields_types::render(
                                $v['type'],
                                $v,
                                \K::$fw->obj,
                                [
                                    'is_new_item' => false,
                                    'parent_entity_item_id' => \K::$fw->obj['parent_item_id'],
                                    'form' => 'item'
                                ]
                            )
                            . ($v['tooltip_display_as'] != 'icon' ? \Helpers\App::tooltip_text($v['tooltip']) : '') . '
                    </div>			
	        </div>
	      ';
                    }

                    $count_fields++;
                }

                //handle rows
                $forms_rows = new \Models\Main\Forms_rows(\K::$fw->current_entity_id, $tabs['id']);
                $forms_rows->fields_access_schema = $fields_access_schema;
                $forms_rows->obj = \K::$fw->obj;
                $forms_rows->is_new_item = $is_new_item;
                $forms_rows->parent_entity_item_id = \K::$fw->obj['parent_item_id'];
                $forms_rows->excluded_fileds_types = $excluded_fileds_types;
                $html .= $forms_rows->render();

                $html .= $html_cfg;
            }

            echo $html;

            //check rules for hidden fields by access
            echo \Models\Main\Forms_fields_rules::prepare_hidden_fields(
                \K::$fw->current_entity_id,
                \K::$fw->obj,
                $fields_access_schema
            );

            ?>

            <div id="form-error-container"></div>

        </div>

        <div class="form-actions fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-offset-3 col-md-9">
                        <?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE, ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
        </div>

        <?php
        //hidden user group value to handle fileds displays rules.
        echo '<input type="hidden" value="' . \K::$fw->app_user['group_id'] . '" id="field_6" class="field_6">';
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
if (\Helpers\App::is_ext_installed()) {
    $smart_input = new smart_input(\K::$fw->current_entity_id);
    echo $smart_input->render();
}
?>

<?= \K::view()->render(\Helpers\Urls::component_path('main/items/items_form.js')); ?>

<?= \Models\Main\Forms_fields_rules::hidden_form_fields(\K::$fw->current_entity_id) ?>

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