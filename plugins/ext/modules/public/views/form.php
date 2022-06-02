<script>
    if (isIframe()) {
        document.write('<link href="<?php echo url_for(
            'ext/public/form',
            'action=get_css&id=' . $public_form['id']
        ) ?>" rel="stylesheet" type="text/css" />');
    }
</script>

<h3 class="form-title"><?php
    echo(strlen($public_form['page_title']) > 0 ? $public_form['page_title'] : $public_form['name']) ?></h3>

<?php
echo(strlen(
    $public_form['description']
) > 0 ? '<p class="public-form-text">' . $public_form['description'] . '</p>' : '') ?>

<div class="items-form-conteiner">
    <?php
    $is_new_item = true;
    $app_items_form_name = 'public_form';

    ?>

    <?php
    echo form_tag(
        $app_items_form_name,
        url_for('ext/public/form', 'action=save&id=' . $public_form['id']),
        ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']
    ) ?>

    <?php
    $entity_info = db_find('app_entities', $current_entity_id);
    if ($entity_info['parent_id'] > 0) {
        $parent_entity_info = db_find('app_entities', $entity_info['parent_id']);
        $parent_entity_cfg = entities::get_cfg($entity_info['parent_id']);

        $parent_item_id = (isset($_GET['parent_item_id']) ? strip_tags(
            $_GET['parent_item_id']
        ) : $public_form['parent_item_id']);

        if ($public_form['hide_parent_item'] == 1 and $parent_item_id > 0) {
            echo input_hidden_tag('parent_item_id', $parent_item_id);
        } else {
            ?>
            <div class="form-group form-group-parent-item-id">
                <label class="col-md-3 control-label" for="entities_id"><?php
                    echo(strlen(
                        $parent_entity_cfg['listing_heading']
                    ) > 0 ? $parent_entity_cfg['listing_heading'] : $parent_entity_info['name']) ?></label>
                <div class="col-md-9">
                    <?php
                    echo select_tag(
                        'parent_item_id',
                        items::get_choices($entity_info['parent_id'], true),
                        $parent_item_id,
                        ['class' => 'form-control chosen-select required']
                    ) ?>
                </div>
            </div>
            <?php
        }
    }

    $obj = db_show_columns('app_entity_' . $current_entity_id);

    $parent_entity_item_id = ((isset($parent_item_id) and $public_form['hide_parent_item'] == 1) ? $parent_item_id : 0);

    $fields_access_schema = users::get_fields_access_schema($current_entity_id, $app_user['group_id']);

    $count_tabs = db_count('app_forms_tabs', $current_entity_id, "entities_id");

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
            $where_sql = (strlen(
                $public_form['hidden_fields']
            ) ? " and f.id not in (" . $public_form['hidden_fields'] . ")" : '');
            $fields_query = db_query(
                "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_type_list_excluded_in_form(
                ) . ") and  f.entities_id='" . db_input(
                    $current_entity_id
                ) . "' {$where_sql} and f.forms_tabs_id=t.id and f.forms_tabs_id='" . db_input(
                    $tabs['id']
                ) . "'  and length(f.forms_rows_position)=0  order by t.sort_order, t.name, f.sort_order, f.name"
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

                //check post fields
                if (isset($_POST['fields'][$v['id']])) {
                    $obj['field_' . $v['id']] = (is_array($_POST['fields'][$v['id']]) ? implode(
                        ',',
                        $_POST['fields'][$v['id']]
                    ) : $_POST['fields'][$v['id']]);
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


                $count_fields++;
            }

            //handle rows
            $forms_rows = new forms_rows($current_entity_id, $tabs['id']);
            $forms_rows->fields_access_schema = $fields_access_schema;
            $forms_rows->obj = $obj;
            $forms_rows->is_new_item = $is_new_item;
            $forms_rows->parent_entity_item_id = $parent_entity_item_id;
            $forms_rows->hidden_fields = $public_form['hidden_fields'];
            $html_tab_content[$tabs['id']] .= $forms_rows_html = $forms_rows->render();

            $html_tab_content[$tabs['id']] .= '</div>';

            //if there is no fields for this tab then remove content from array
            if ($count_fields == 0 and !strlen($forms_rows_html)) {
                unset($html_tab_content[$tabs['id']]);
            }

            $count_tabs++;
        }

        //render nav-tabs
        $html = '<ul class="nav nav-tabs" id="form_tabs"> ' . forms_tabs::render_tabs_nav($current_entity_id) . '</ul>';

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
        $where_sql = (strlen(
            $public_form['hidden_fields']
        ) ? " and f.id not in (" . $public_form['hidden_fields'] . ")" : '');
        $fields_query = db_query(
            "select f.* from app_fields f where f.type not in (" . fields_types::get_type_list_excluded_in_form(
            ) . ") and  f.entities_id='" . db_input(
                $current_entity_id
            ) . "' {$where_sql}  and length(f.forms_rows_position)=0 order by f.sort_order, f.name"
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

            //check post fields
            if (isset($_POST['fields'][$v['id']])) {
                $obj['field_' . $v['id']] = (is_array($_POST['fields'][$v['id']]) ? implode(
                    ',',
                    $_POST['fields'][$v['id']]
                ) : $_POST['fields'][$v['id']]);
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
                    ['parent_entity_item_id' => $parent_entity_item_id, 'form' => 'item', 'is_new_item' => $is_new_item]
                );
            } else {
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

            $count_fields++;
        }

        //handle rows
        $forms_rows = new forms_rows($current_entity_id, $tabs['id']);
        $forms_rows->fields_access_schema = $fields_access_schema;
        $forms_rows->obj = $obj;
        $forms_rows->is_new_item = $is_new_item;
        $forms_rows->parent_entity_item_id = $parent_entity_item_id;
        $forms_rows->hidden_fields = $public_form['hidden_fields'];
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

    <?php

    if (strlen($public_form['user_agreement'])) {
        echo '
	   <div class="form-group form-group-single-checkbox public-form-text">				
	     <label class="col-md-12 control-label">' . input_checkbox_tag('user_agreement', '1', ['class' => 'required']
            ) . ' ' . $public_form['user_agreement'] . '</label>	               		
	     <label for="user_agreement" class="col-md-12 control-label error"></label>
            </div>		            		
	  ';
    }

    $forms_wizard = new forms_wizard($app_items_form_name, $current_entity_id, $entity_cfg);

    $button_save_title = (strlen(
        $public_form['button_save_title']
    ) > 0 ? $public_form['button_save_title'] : TEXT_BUTTON_SEND);

    if ($forms_wizard->is_active()) {
        echo $forms_wizard->ajax_modal_template_footer($button_save_title);
    } else {
        echo '
        <div class="modal-footer">
          <div id="form-error-container"></div>
            <div class="fa fa-spinner fa-spin primary-modal-action-loading"></div>
            <button type="submit" class="btn btn-primary btn-primary-modal-action">' . $button_save_title . '</button>    	
        </div>';
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

<script>
    $(function () {
        $('.public-form-text a').attr('target', '_new');
    })

    <?php echo $public_form['form_js'] ?>
</script>

<?php
echo forms_fields_rules::hidden_form_fields($current_entity_id, false) ?>

