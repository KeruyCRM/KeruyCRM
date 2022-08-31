<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<div class="items-form-conteiner">
    <?php

    $header_menu_button = '';

    //add templates menu in header
    if (is_ext_installed()) {
        $header_menu_button = entities_templates::render_modal_header_menu($current_entity_id);
    }

    $heading_msg = '';

    if (isset($_GET['save_success_msg'])) {
        $heading_msg = ' <span class="label label-info heading-msg">' . TEXT_DATA_SAVED . '</span>
            <script>$(".heading-msg").delay(1000).fadeOut()</script>
         ';
    }

    echo ajax_modal_template_header(
        $header_menu_button . (strlen($entity_cfg->get('window_heading')) > 0 ? $entity_cfg->get(
            'window_heading'
        ) : TEXT_INFO) . $heading_msg
    );

    $is_new_item = (!isset($_GET['id']) ? true : false);

    $app_items_form_name = (isset($_GET['is_submodal']) ? 'sub_items_form' : 'items_form');
    ?>

    <?php
    $form_url = url_for('items/', 'action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : ''));
    echo form_tag($app_items_form_name, $form_url, ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal'])
    ?>
    <div class="modal-body <?php
    echo $entity_cfg->get('window_width') ?>">
        <div class="form-body">

            <?php
            echo input_hidden_tag('path', $_GET['path']) ?>
            <?php
            echo input_hidden_tag('redirect_to', $app_redirect_to) ?>
            <?php
            echo input_hidden_tag('parent_item_id', $parent_entity_item_id) ?>
            <?php
            echo input_hidden_tag('parent_id', (isset($_GET['parent_id']) ? _GET('parent_id') : 0)) ?>
            <?php
            if (isset($_GET['related'])) echo input_hidden_tag('related', $_GET['related']) ?>
            <?php
            if (isset($_GET['gotopage'])) echo input_hidden_tag(
                'gotopage[' . key($_GET['gotopage']) . ']',
                current($_GET['gotopage'])
            ) ?>
            <?php
            if (isset($_GET['mail_groups_id'])) echo input_hidden_tag('mail_groups_id', $_GET['mail_groups_id']) ?>

            <?php
            $html_user_password = '
          <div class="form-group">
          	<label class="col-md-3 control-label" for="password"><span class="required-label">*</span>' . TEXT_FIELDTYPE_USER_PASSWORD_TITLE . '</label>
            <div class="col-md-9">	
          	  ' . input_password_tag('password', ['class' => 'form-control input-medium', 'autocomplete' => 'off']) . '
              ' . tooltip_text(TEXT_FIELDTYPE_USER_PASSWORD_TOOLTIP) . '
            </div>			
          </div>        
        ';


            $fields_access_schema = users::get_fields_access_schema($current_entity_id, $app_user['group_id']);

            //check fields access rules for item
            if (isset($_GET['id'])) {
                $access_rules = new access_rules($current_entity_id, $obj);
                $fields_access_schema += $access_rules->get_fields_view_only_access();
            }

            $count_tabs = db_count('app_forms_tabs', $current_entity_id, "entities_id");

            if ($count_tabs > 1) {
                $count_tabs = 0;

                //put tags content html in array
                $html_tab_content = [];

                $tabs_tree = forms_tabs::get_tree($current_entity_id);
                foreach ($tabs_tree as $tabs) {
                    $html_tab_content[$tabs['id']] = '
        <div class="tab-pane fade" id="form_tab_' . $tabs['id'] . '">
      ' . (strlen($tabs['description']) ? '<p>' . $tabs['description'] . '</p>' : '');

                    $count_fields = 0;
                    $fields_query = db_query(
                        "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_type_list_excluded_in_form(
                        ) . ") and  f.entities_id='" . db_input(
                            $current_entity_id
                        ) . "' and f.forms_tabs_id=t.id and f.forms_tabs_id='" . db_input(
                            $tabs['id']
                        ) . "'  and length(f.forms_rows_position)=0 order by t.sort_order, t.name, f.sort_order, f.name"
                    );
                    while ($v = db_fetch_array($fields_query)) {
                        //check field access
                        if (isset($fields_access_schema[$v['id']])) {
                            continue;
                        }

                        //handle params from GET
                        if (isset($_GET['fields'][$v['id']])) {
                            $obj['field_' . $v['id']] = db_prepare_input($_GET['fields'][$v['id']]);
                        }

                        if ($v['type'] == 'fieldtype_section') {
                            $html_tab_content[$tabs['id']] .= '<div class="form-group-' . $v['id'] . '">' . fields_types::render(
                                    $v['type'],
                                    $v,
                                    $obj,
                                    ['count_fields' => $count_fields]
                                ) . '</div>';
                        } elseif ($v['type'] == 'fieldtype_dropdown_multilevel') {
                            $html_tab_content[$tabs['id']] .= fields_types::render(
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

                            $html_tab_content[$tabs['id']] .= '
	          <div class="form-group form-group-' . $v['id'] . ' form-group-' . $v['type'] . '">
	          	<label class="col-md-3 control-label" for="fields_' . $v['id'] . '">' .
                                ($v['is_required'] == 1 ? '<span class="required-label">*</span>' : '') .
                                ($v['tooltip_display_as'] == 'icon' ? tooltip_icon($v['tooltip']) : '') .
                                fields_types::get_option($v['type'], 'name', $v['name']) .
                                '</label>
	            <div class="col-md-9">	
	          	  <div id="fields_' . $v['id'] . '_rendered_value">' . fields_types::render(
                                    $v['type'],
                                    $v,
                                    $obj,
                                    [
                                        'parent_entity_item_id' => $parent_entity_item_id,
                                        'form' => 'item',
                                        'is_new_item' => $is_new_item
                                    ]
                                ) . '</div>
	              ' . ($v['tooltip_display_as'] != 'icon' ? tooltip_text($v['tooltip']) : '') . '
	            </div>			
	          </div>        
	        ';
                        }

                        //including user password field for new user form
                        if ($v['type'] == 'fieldtype_user_username' and !isset($_GET['id'])) {
                            $html_tab_content[$tabs['id']] .= $html_user_password;
                        }

                        $count_fields++;
                    }

                    //handle rows
                    $forms_rows = new forms_rows($current_entity_id, $tabs['id']);
                    $forms_rows->fields_access_schema = $fields_access_schema;
                    $forms_rows->obj = $obj;
                    $forms_rows->is_new_item = $is_new_item;
                    $forms_rows->parent_entity_item_id = $parent_entity_item_id;
                    $html_tab_content[$tabs['id']] .= $forms_rows_html = $forms_rows->render();

                    $html_tab_content[$tabs['id']] .= '</div>';

                    //if there is no fields for this tab then remove content from array
                    if ($count_fields == 0 and !strlen($forms_rows_html)) {
                        unset($html_tab_content[$tabs['id']]);
                    }

                    $count_tabs++;
                }

                //render nav-tabs
                $html = '<ul class="nav nav-tabs" id="form_tabs"> ' . forms_tabs::render_tabs_nav(
                        $current_entity_id
                    ) . '</ul>';

                $html .= '<div class="tab-content">';

                //build tabs content
                $count = 0;
                foreach ($html_tab_content as $tab_id => $content) {
                    $html .= ($count == 0 ? str_replace(
                        'tab-pane fade',
                        'tab-pane fade active in',
                        $content
                    ) : $content);
                    $count++;
                }

                $html .= '</div>';
            } else {
                $count_fields = 0;
                $html = '';

                $tabs_query = db_fetch_all(
                    'app_forms_tabs',
                    "entities_id='" . db_input($current_entity_id) . "' order by  sort_order, name"
                );
                $tabs = db_fetch_array($tabs_query);

                if (strlen($tabs['description'])) {
                    $html .= '<p>' . $tabs['description'] . '</p>';
                }


                $fields_query = db_query(
                    "select f.* from app_fields f where f.type not in (" . fields_types::get_type_list_excluded_in_form(
                    ) . ") and  f.entities_id='" . db_input(
                        $current_entity_id
                    ) . "' and length(f.forms_rows_position)=0 order by f.sort_order, f.name"
                );
                while ($v = db_fetch_array($fields_query)) {
                    //check field access
                    if (isset($fields_access_schema[$v['id']])) {
                        continue;
                    }

                    //handle params from GET
                    if (isset($_GET['fields'][$v['id']])) {
                        $obj['field_' . $v['id']] = db_prepare_input($_GET['fields'][$v['id']]);
                    }

                    if ($v['type'] == 'fieldtype_section') {
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
                            fields_types::get_option($v['type'], 'name', $v['name']) .
                            '</label>
	            <div class="col-md-9">	
	          	  <div id="fields_' . $v['id'] . '_rendered_value">' . fields_types::render(
                                $v['type'],
                                $v,
                                $obj,
                                [
                                    'parent_entity_item_id' => $parent_entity_item_id,
                                    'form' => 'item',
                                    'is_new_item' => $is_new_item
                                ]
                            ) . '</div>
	              ' . ($v['tooltip_display_as'] != 'icon' ? tooltip_text($v['tooltip']) : '') . '
	            </div>			
	          </div>        
	        ';
                    }

                    //including user password field for new user form
                    if ($v['type'] == 'fieldtype_user_username' and !isset($_GET['id'])) {
                        $html .= $html_user_password;
                    }

                    $count_fields++;
                }

                //handle rows
                $forms_rows = new forms_rows($current_entity_id, $tabs['id']);
                $forms_rows->fields_access_schema = $fields_access_schema;
                $forms_rows->obj = $obj;
                $forms_rows->is_new_item = $is_new_item;
                $forms_rows->parent_entity_item_id = $parent_entity_item_id;
                $html .= $forms_rows->render();
            }

            echo $html;


            //render templates fields values
            if (is_ext_installed()) {
                echo entities_templates::render_fields_values($current_entity_id);
            }
            ?>
        </div>
    </div>

    <?php
    $forms_wizard = new forms_wizard($app_items_form_name, $current_entity_id, $entity_cfg);

    $extra_button = '';

    //prepare delete button for gantt report
    require(component_path('items/items_form_gantt_delete_prepare'));

    if (!isset($_GET['id']) and !isset($_GET['is_submodal']) and $entity_cfg->get(
            'redirect_after_adding'
        ) == 'form' and ($app_redirect_to == '' or substr(
                $app_redirect_to,
                0,
                7
            ) == 'report_' or $app_redirect_to == 'parent_item_info_page')) {
        $extra_button = '
                <button type="submit" class="btn btn-primary btn-save-and-add btn-primary-modal-action">' . TEXT_SAVE . ' +</button>
                <button type="button" class="btn btn-default btn-save-and-close btn-primary-modal-action">' . TEXT_SAVE_AND_CLOSE . '</button>
                <script>
                    $(".btn-save-and-close").click(function(){
                        $("#' . $app_items_form_name . '").attr("save_and_close",1)
                        $("#' . $app_items_form_name . '").submit();
                    })
                    $(".btn-save-and-add").click(function(){
                        $("#' . $app_items_form_name . '").attr("save_and_close",0)
                    })
                </script>';


        echo ajax_modal_template_footer('hide-save-button', $extra_button);
    } elseif ($forms_wizard->is_active() and !isset($_GET['id']) and !isset($_GET['is_submodal'])) {
        echo $forms_wizard->ajax_modal_template_footer();
    } else {
        echo ajax_modal_template_footer(false, $extra_button);
    }

    //check ruels for hidden fields by access
    if (isset($_GET['id'])) {
        echo forms_fields_rules::prepare_hidden_fields($current_entity_id, $obj, $fields_access_schema);
    }
    ?>

    </form>
</div>

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