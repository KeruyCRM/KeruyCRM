<h3 class="form-title"><?php
    echo(strlen(
        CFG_PUBLIC_REGISTRATION_PAGE_HEADING
    ) > 0 ? CFG_PUBLIC_REGISTRATION_PAGE_HEADING : TEXT_REGISTRATION_NEW_USER) ?></h3>

<?php
echo(strlen(CFG_PUBLIC_REGISTRATION_PAGE_CONTENT) > 0 ? '<p>' . nl2br(
        CFG_PUBLIC_REGISTRATION_PAGE_CONTENT
    ) . '</p>' : '') ?>

<?php
$is_new_item = true;
$app_items_form_name = 'registration_form';
$excluded_fileds_types = "'fieldtype_user_status','fieldtype_user_skin','fieldtype_users','fieldtype_users_ajax'";

if (strlen(CFG_PUBLIC_REGISTRATION_USER_GROUP) != 0 and count(explode(',', CFG_PUBLIC_REGISTRATION_USER_GROUP)) == 1) {
    $excluded_fileds_types .= ",'fieldtype_user_accessgroups'";
}

$fields_where_sql = (strlen(
    CFG_PUBLIC_REGISTRATION_HIDDEN_FIELDS
) ? " and f.id not in (" . CFG_PUBLIC_REGISTRATION_HIDDEN_FIELDS . ") " : '');

?>

<div class="items-form-conteiner">

    <?php
    echo form_tag(
        $app_items_form_name,
        url_for('users/registration', 'action=save'),
        ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']
    ) ?>
    <div class="form-body">

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

        $count_tabs = db_count('app_forms_tabs', $current_entity_id, "entities_id");

        $obj = db_show_columns('app_entity_1');

        $html = '';

        if ($count_tabs > 1) {
            $count = 0;

            $count_tabs = 0;

            //put tags content html in array
            $html_tab_content = [];

            $tabs_tree = forms_tabs::get_tree($current_entity_id);
            foreach ($tabs_tree as $tabs) {
                $html_tab_content[$tabs['id']] = '
        <div class="tab-pane fade ' . ($count_tabs == 0 ? 'active in' : '') . '" id="form_tab_' . $tabs['id'] . '">
      ' . (strlen($tabs['description']) ? '<p>' . $tabs['description'] . '</p>' : '');

                $count_fields = 0;
                $fields_query = db_query(
                    "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_type_list_excluded_in_form(
                    ) . "," . $excluded_fileds_types . ") and  f.entities_id='" . db_input(
                        $current_entity_id
                    ) . "' and f.forms_tabs_id=t.id and f.forms_tabs_id='" . db_input(
                        $tabs['id']
                    ) . "' {$fields_where_sql}  and length(f.forms_rows_position)=0  order by t.sort_order, t.name, f.sort_order, f.name"
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

                    //handle params from POST
                    if (isset($_POST['fields'][$v['id']])) {
                        $obj['field_' . $v['id']] = db_prepare_input($_POST['fields'][$v['id']]);
                    }

                    if ($v['type'] == 'fieldtype_user_language') {
                        $html .= input_hidden_tag('fields[' . $v['id'] . ']', CFG_APP_LANGUAGE);
                        continue;
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
	          <div class="form-group form-group-' . $v['id'] . '">
	          	<label class="col-md-3 control-label" for="fields_' . $v['id'] . '">' .
                            ($v['is_required'] == 1 ? '<span class="required-label">*</span>' : '') .
                            ($v['tooltip_display_as'] == 'icon' ? tooltip_icon($v['tooltip']) : '') .
                            fields_types::get_option($v['type'], 'name', $v['name']) .
                            '</label>
	            <div class="col-md-9">	
	          	  ' . fields_types::render(
                                $v['type'],
                                $v,
                                $obj,
                                ['parent_entity_item_id' => 0, 'form' => 'item', 'is_new_item' => $is_new_item]
                            ) . '
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
                $forms_rows->parent_entity_item_id = 0;
                $forms_rows->excluded_fileds_types = $excluded_fileds_types;
                $forms_rows->hidden_fields = CFG_PUBLIC_REGISTRATION_HIDDEN_FIELDS;
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
            foreach ($html_tab_content as $tab_id => $content) {
                $html .= $content;
            }

            $html .= '</div>';
        } else {
            $tabs_query = db_fetch_all(
                'app_forms_tabs',
                "entities_id='" . db_input($current_entity_id) . "' order by  sort_order, name"
            );
            $tabs = db_fetch_array($tabs_query);

            $count_fields = 0;
            $html = '';
            $fields_query = db_query(
                "select f.* from app_fields f where f.type not in (" . fields_types::get_type_list_excluded_in_form(
                ) . "," . $excluded_fileds_types . ") and  f.entities_id='" . db_input(
                    $current_entity_id
                ) . "' {$fields_where_sql}  and length(f.forms_rows_position)=0  order by f.sort_order, f.name"
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

                //handle params from POST
                if (isset($_POST['fields'][$v['id']])) {
                    $obj['field_' . $v['id']] = db_prepare_input($_POST['fields'][$v['id']]);
                }

                if ($v['type'] == 'fieldtype_user_language') {
                    $html .= input_hidden_tag('fields[' . $v['id'] . ']', CFG_APP_LANGUAGE);
                    continue;
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
	          <div class="form-group form-group-' . $v['id'] . '">
	          	<label class="col-md-3 control-label" for="fields_' . $v['id'] . '">' .
                        ($v['is_required'] == 1 ? '<span class="required-label">*</span>' : '') .
                        ($v['tooltip_display_as'] == 'icon' ? tooltip_icon($v['tooltip']) : '') .
                        fields_types::get_option($v['type'], 'name', $v['name']) .
                        '</label>
	            <div class="col-md-9">	
	          	  ' . fields_types::render(
                            $v['type'],
                            $v,
                            $obj,
                            ['parent_entity_item_id' => 0, 'form' => 'item', 'is_new_item' => $is_new_item]
                        ) . '
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
            $forms_rows->parent_entity_item_id = 0;
            $forms_rows->excluded_fileds_types = $excluded_fileds_types;
            $forms_rows->hidden_fields = CFG_PUBLIC_REGISTRATION_HIDDEN_FIELDS;
            $html .= $forms_rows->render();
        }

        echo $html;

        ?>

        <?php
        if (app_recaptcha::is_enabled()): ?>
            <div class="form-group">
                <label class="col-md-3 control-label"></label>
                <div class="col-md-9">
                    <?php
                    echo app_recaptcha::render() ?>
                </div>
            </div>
        <?php
        endif ?>

    </div>

    <?php


    if (strlen(CFG_PUBLIC_REGISTRATION_USER_AGREEMENT)) {
        echo '
	   <div class="form-group form-group-single-checkbox">
	     <label class="col-md-12 control-label">' . input_checkbox_tag('user_agreement', '1', ['class' => 'required']
            ) . ' ' . CFG_PUBLIC_REGISTRATION_USER_AGREEMENT . '</label>
	     <label for="user_agreement" class="col-md-12 control-label error"></label>
		 </div>
	  ';
    }


    $forms_wizard = new forms_wizard($app_items_form_name, $current_entity_id, $entity_cfg);

    $button_title = (strlen(CFG_REGISTRATION_BUTTON_TITLE) ? CFG_REGISTRATION_BUTTON_TITLE : TEXT_BUTTON_REGISTRATCION);

    if ($forms_wizard->is_active()) {
        $html = $forms_wizard->ajax_modal_template_footer($button_title);
    } else {
        $html = '
  <div class="modal-footer">
    <div id="form-error-container"></div>    
      <div class="fa fa-spinner fa-spin primary-modal-action-loading"></div>
      <button type="submit" class="btn btn-primary btn-primary-modal-action">' . $button_title . '</button>
    	<a href="' . url_for('users/login') . '" class="btn btn-default">' . TEXT_BUTTON_BACK . '</a>
  </div>';
    }


    echo $html;
    ?>

    </form>

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

</div>