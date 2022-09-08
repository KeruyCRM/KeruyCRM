<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
    <div class="items-form-conteiner">
        <?php

        $header_menu_button = '';

        //add templates menu in header
        if (\Helpers\App::is_ext_installed()) {
            $header_menu_button = entities_templates::render_modal_header_menu(\K::$fw->current_entity_id);
        }

        $heading_msg = '';

        if (isset(\K::$fw->GET['save_success_msg'])) {
            $heading_msg = ' <span class="label label-info heading-msg">' . \K::$fw->TEXT_DATA_SAVED . '</span>
            <script>$(".heading-msg").delay(1000).fadeOut()</script>
         ';
        }

        echo \Helpers\App::ajax_modal_template_header(
            $header_menu_button . (strlen(\K::$fw->entity_cfg->get('window_heading')) > 0 ? \K::$fw->entity_cfg->get(
                'window_heading'
            ) : \K::$fw->TEXT_INFO) . $heading_msg
        );

        $is_new_item = !isset(\K::$fw->GET['id']);

        $app_items_form_name = (isset(\K::$fw->GET['is_submodal']) ? 'sub_items_form' : 'items_form');

        echo \Helpers\Html::form_tag(
            $app_items_form_name,
            \Helpers\Urls::url_for(
                'main/items/items/save',
                (isset(\K::$fw->GET['id']) ? '&id=' . \K::$fw->GET['id'] : '')
            ),
            ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']
        )
        ?>
        <div class="modal-body <?= \K::$fw->entity_cfg->get('window_width') ?>">
            <div class="form-body">
                <?= \Helpers\Html::input_hidden_tag('path', \K::$fw->GET['path']) ?>
                <?= \Helpers\Html::input_hidden_tag('redirect_to', \K::$fw->app_redirect_to) ?>
                <?= \Helpers\Html::input_hidden_tag('parent_item_id', \K::$fw->parent_entity_item_id) ?>
                <?= \Helpers\Html::input_hidden_tag('parent_id', (\K::$fw->GET['parent_id'] ?? 0)) ?>
                <?php
                if (isset(\K::$fw->GET['related'])) echo \Helpers\Html::input_hidden_tag(
                    'related',
                    \K::$fw->GET['related']
                ) ?>
                <?php
                if (isset(\K::$fw->GET['gotopage'])) {
                    echo \Helpers\Html::input_hidden_tag(
                        'gotopage[' . key(\K::$fw->GET['gotopage']) . ']',
                        current(\K::$fw->GET['gotopage'])
                    );
                }
                if (isset(\K::$fw->GET['mail_groups_id'])) {
                    echo \Helpers\Html::input_hidden_tag('mail_groups_id', \K::$fw->GET['mail_groups_id']);
                }

                $html_user_password = '
          <div class="form-group">
          	<label class="col-md-3 control-label" for="password"><span class="required-label">*</span>' . \K::$fw->TEXT_FIELDTYPE_USER_PASSWORD_TITLE . '</label>
            <div class="col-md-9">	
          	  ' . \Helpers\Html::input_password_tag(
                        'password',
                        ['class' => 'form-control input-medium', 'autocomplete' => 'off']
                    ) . '
              ' . \Helpers\App::tooltip_text(\K::$fw->TEXT_FIELDTYPE_USER_PASSWORD_TOOLTIP) . '
            </div>			
          </div>        
        ';

                $fields_access_schema = \Models\Main\Users\Users::get_fields_access_schema(
                    \K::$fw->current_entity_id,
                    \K::$fw->app_user['group_id']
                );

                //check fields access rules for item
                if (isset(\K::$fw->GET['id'])) {
                    $access_rules = new \Models\Main\Access_rules(\K::$fw->current_entity_id, \K::$fw->obj);
                    $fields_access_schema += $access_rules->get_fields_view_only_access();
                }

                $count_tabs = \K::model()->db_count('app_forms_tabs', \K::$fw->current_entity_id, "entities_id");

                if ($count_tabs > 1) {
                    $count_tabs = 0;

                    //put tags content html in array
                    $html_tab_content = [];

                    $tabs_tree = \Models\Main\Forms_tabs::get_tree(\K::$fw->current_entity_id);
                    foreach ($tabs_tree as $tabs) {
                        $html_tab_content[$tabs['id']] = '
        <div class="tab-pane fade" id="form_tab_' . $tabs['id'] . '">
      ' . (strlen($tabs['description']) ? '<p>' . $tabs['description'] . '</p>' : '');

                        $count_fields = 0;
                        $fields_query = \K::model()->db_query_exec(
                            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . \Models\Main\Fields_types::get_type_list_excluded_in_form(
                            ) . ") and f.entities_id = ? and f.forms_tabs_id = t.id and f.forms_tabs_id = ? and length(f.forms_rows_position) = 0 order by t.sort_order, t.name, f.sort_order, f.name",
                            [
                                \K::$fw->current_entity_id,
                                $tabs['id']
                            ],
                            'app_fields,app_forms_tabs'
                        );

                        //while ($v = db_fetch_array($fields_query)) {
                        foreach ($fields_query as $v) {
                            //check field access
                            if (isset($fields_access_schema[$v['id']])) {
                                continue;
                            }

                            //handle params from GET
                            if (isset(\K::$fw->GET['fields'][$v['id']])) {
                                \K::$fw->obj['field_' . $v['id']] = \K::model()->db_prepare_input(
                                    \K::$fw->GET['fields'][$v['id']]
                                );
                            }

                            if ($v['type'] == 'fieldtype_section') {
                                $html_tab_content[$tabs['id']] .= '<div class="form-group-' . $v['id'] . '">' . \Models\Main\Fields_types::render(
                                        $v['type'],
                                        $v,
                                        \K::$fw->obj,
                                        ['count_fields' => $count_fields]
                                    ) . '</div>';
                            } elseif ($v['type'] == 'fieldtype_dropdown_multilevel') {
                                $html_tab_content[$tabs['id']] .= \Models\Main\Fields_types::render(
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

                                $html_tab_content[$tabs['id']] .= '
	          <div class="form-group form-group-' . $v['id'] . ' form-group-' . $v['type'] . '">
	          	<label class="col-md-3 control-label" for="fields_' . $v['id'] . '">' .
                                    ($v['is_required'] == 1 ? '<span class="required-label">*</span>' : '') .
                                    ($v['tooltip_display_as'] == 'icon' ? \Helpers\App::tooltip_icon(
                                        $v['tooltip']
                                    ) : '') .
                                    \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']) .
                                    '</label>
	            <div class="col-md-9">	
	          	  <div id="fields_' . $v['id'] . '_rendered_value">' . \Models\Main\Fields_types::render(
                                        $v['type'],
                                        $v,
                                        \K::$fw->obj,
                                        [
                                            'parent_entity_item_id' => \K::$fw->parent_entity_item_id,
                                            'form' => 'item',
                                            'is_new_item' => $is_new_item
                                        ]
                                    ) . '</div>
	              ' . ($v['tooltip_display_as'] != 'icon' ? \Helpers\App::tooltip_text($v['tooltip']) : '') . '
	            </div>			
	          </div>        
	        ';
                            }

                            //including user password field for new user form
                            if ($v['type'] == 'fieldtype_user_username' and !isset(\K::$fw->GET['id'])) {
                                $html_tab_content[$tabs['id']] .= $html_user_password;
                            }

                            $count_fields++;
                        }

                        //handle rows
                        $forms_rows = new \Models\Main\Forms_rows(\K::$fw->current_entity_id, $tabs['id']);
                        $forms_rows->fields_access_schema = $fields_access_schema;
                        $forms_rows->obj = \K::$fw->obj;
                        $forms_rows->is_new_item = $is_new_item;
                        $forms_rows->parent_entity_item_id = \K::$fw->parent_entity_item_id;
                        $html_tab_content[$tabs['id']] .= $forms_rows_html = $forms_rows->render();

                        $html_tab_content[$tabs['id']] .= '</div>';

                        //if there is no fields for this tab then remove content from array
                        if ($count_fields == 0 and !strlen($forms_rows_html)) {
                            unset($html_tab_content[$tabs['id']]);
                        }

                        $count_tabs++;
                    }

                    //render nav-tabs
                    $html = '<ul class="nav nav-tabs" id="form_tabs"> ' . \Models\Main\Forms_tabs::render_tabs_nav(
                            \K::$fw->current_entity_id
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

                    /*$tabs_query = db_fetch_all(
                        'app_forms_tabs',
                        "entities_id='" . db_input(\K::$fw->current_entity_id) . "' order by  sort_order, name"
                    );
                    $tabs = db_fetch_array($tabs_query);*/

                    $tabs = \K::model()->db_fetch_one('app_forms_tabs', [
                        'entities_id = ?',
                        \K::$fw->current_entity_id
                    ], ['order' => 'sort_order,name'], 'id,description');

                    if (strlen($tabs['description'])) {
                        $html .= '<p>' . $tabs['description'] . '</p>';
                    }

                    /*$fields_query = db_query(
                        "select f.* from app_fields f where f.type not in (" . fields_types::get_type_list_excluded_in_form(
                        ) . ") and  f.entities_id='" . db_input(
                            \K::$fw->current_entity_id
                        ) . "' and length(f.forms_rows_position)=0 order by f.sort_order, f.name"
                    );*/

                    $fields_query = \K::model()->db_fetch('app_fields', [
                        'type not in (' . \Models\Main\Fields_types::get_type_list_excluded_in_form(
                        ) . ') and entities_id = ? and length(forms_rows_position) = 0',
                        \K::$fw->current_entity_id
                    ], ['order' => 'sort_order,name']);

                    //while ($v = db_fetch_array($fields_query)) {
                    foreach ($fields_query as $v) {
                        //check field access
                        if (isset($fields_access_schema[$v['id']])) {
                            continue;
                        }

                        //handle params from GET
                        if (isset(\K::$fw->GET['fields'][$v['id']])) {
                            \K::$fw->obj['field_' . $v['id']] = \K::model()->db_prepare_input(
                                \K::$fw->GET['fields'][$v['id']]
                            );
                        }

                        if ($v['type'] == 'fieldtype_section') {
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
                                \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']) .
                                '</label>
	            <div class="col-md-9">	
	          	  <div id="fields_' . $v['id'] . '_rendered_value">' . \Models\Main\Fields_types::render(
                                    $v['type'],
                                    $v,
                                    \K::$fw->obj,
                                    [
                                        'parent_entity_item_id' => \K::$fw->parent_entity_item_id,
                                        'form' => 'item',
                                        'is_new_item' => $is_new_item
                                    ]
                                ) . '</div>
	              ' . ($v['tooltip_display_as'] != 'icon' ? \Helpers\App::tooltip_text($v['tooltip']) : '') . '
	            </div>			
	          </div>        
	        ';
                        }

                        //including user password field for new user form
                        if ($v['type'] == 'fieldtype_user_username' and !isset(\K::$fw->GET['id'])) {
                            $html .= $html_user_password;
                        }

                        $count_fields++;
                    }

                    //handle rows
                    $forms_rows = new \Models\Main\Forms_rows(\K::$fw->current_entity_id, $tabs['id']);
                    $forms_rows->fields_access_schema = $fields_access_schema;
                    $forms_rows->obj = \K::$fw->obj;
                    $forms_rows->is_new_item = $is_new_item;
                    $forms_rows->parent_entity_item_id = \K::$fw->parent_entity_item_id;
                    $html .= $forms_rows->render();
                }

                echo $html;

                //render templates fields values
                if (\Helpers\App::is_ext_installed()) {
                    echo entities_templates::render_fields_values(\K::$fw->current_entity_id);
                }
                ?>
            </div>
        </div>

        <?php
        $forms_wizard = new \Models\Main\Items\Forms_wizard(
            $app_items_form_name,
            \K::$fw->current_entity_id,
            \K::$fw->entity_cfg
        );

        $extra_button = '';

        //prepare delete button for gantt report
        //require(component_path('items/items_form_gantt_delete_prepare'));
        \Helpers\Urls::components_path('main/items/items_form_gantt_delete_prepare');

        if (!isset(\K::$fw->GET['id']) and !isset(\K::$fw->GET['is_submodal']) and \K::$fw->entity_cfg->get(
                'redirect_after_adding'
            ) == 'form' and (\K::$fw->app_redirect_to == '' or substr(
                    \K::$fw->app_redirect_to,
                    0,
                    7
                ) == 'report_' or \K::$fw->app_redirect_to == 'parent_item_info_page')) {
            $extra_button = '
                <button type="submit" class="btn btn-primary btn-save-and-add btn-primary-modal-action">' . \K::$fw->TEXT_SAVE . ' +</button>
                <button type="button" class="btn btn-default btn-save-and-close btn-primary-modal-action">' . \K::$fw->TEXT_SAVE_AND_CLOSE . '</button>
                <script>
                    $(".btn-save-and-close").click(function(){
                        $("#' . $app_items_form_name . '").attr("save_and_close",1)
                        $("#' . $app_items_form_name . '").submit();
                    })
                    $(".btn-save-and-add").click(function(){
                        $("#' . $app_items_form_name . '").attr("save_and_close",0)
                    })
                </script>';

            echo \Helpers\App::ajax_modal_template_footer('hide-save-button', $extra_button);
        } elseif ($forms_wizard->is_active() and !isset(\K::$fw->GET['id']) and !isset(\K::$fw->GET['is_submodal'])) {
            echo $forms_wizard->ajax_modal_template_footer();
        } else {
            echo \Helpers\App::ajax_modal_template_footer(false, $extra_button);
        }

        //check rules for hidden fields by access
        if (isset(\K::$fw->GET['id'])) {
            echo \Models\Main\Forms_fields_rules::prepare_hidden_fields(
                \K::$fw->current_entity_id,
                \K::$fw->obj,
                $fields_access_schema
            );
        }
        ?>

        </form>
    </div>

<?php
if (\Helpers\App::is_ext_installed()) {
    $smart_input = new smart_input(\K::$fw->current_entity_id);
    echo $smart_input->render();
}
?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/items/items_form.js')); ?>

<?= \Models\Main\Forms_fields_rules::hidden_form_fields(\K::$fw->current_entity_id) ?>