<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->header_menu_button . \K::$fw->TEXT_COMMENT_INFO) ?>

<?= \Helpers\Html::form_tag(
    'comments_form',
    \Helpers\Urls::url_for('main/items/comments/save', (isset(\K::$fw->GET['id']) ? 'id=' . \K::$fw->GET['id'] : '')),
    ['class' => 'form-horizontal']
) ?>
    <div class="modal-body">
        <div class="form-body">
            <?= \Helpers\Html::input_hidden_tag('path', \K::$fw->GET['path']) ?>

            <?php

            $html_tab = [];
            $html_tab_content = [];

            if (!isset(\K::$fw->GET['id'])) {
                $fields_access_schema = \Models\Main\Users\Users::get_fields_access_schema(
                    \K::$fw->current_entity_id,
                    \K::$fw->app_user['group_id']
                );

                //check fields access rules for item
                $item_info = \K::model()->db_find(
                    'app_entity_' . (int)\K::$fw->current_entity_id,
                    \K::$fw->current_item_id
                );
                $access_rules = new \Models\Main\Access_rules(\K::$fw->current_entity_id, $item_info);
                $fields_access_schema += $access_rules->get_fields_view_only_access();

                //build default tab
                $html_default_tab = '';
                /*$fields_query = db_query(
                    "select f.* from app_fields f where f.type not in (" . fields_types::get_reserverd_types_list(
                    ) . ',' . fields_types::get_users_types_list() . ") and  f.entities_id='" . db_input(
                        \K::$fw->current_entity_id
                    ) . "' and f.comments_status=1 and f.comments_forms_tabs_id=0 order by f.comments_sort_order, f.name"
                );*/

                $fields_query = \K::model()->db_fetch('app_fields', [
                    'type not in (' . \Models\Main\Fields_types::get_reserverd_types_list(
                    ) . ',' . \Models\Main\Fields_types::get_users_types_list(
                    ) . ') and entities_id = ? and comments_status = 1 and comments_forms_tabs_id = 0',
                    \K::$fw->current_entity_id
                ], ['order' => 'comments_sort_order,name']);

                //while ($v = db_fetch_array($fields_query)) {
                foreach ($fields_query as $v) {
                    $v = $v->cast();

                    //check field access
                    if (isset($fields_access_schema[$v['id']])) {
                        continue;
                    }

                    //set off required option for comment form
                    $v['is_required'] = 0;

                    $html_default_tab .= '
          <div class="form-group form-group-' . $v['id'] . '">
          	<label class="col-md-3 control-label" for="fields_' . $v['id'] . '">' . ($v['tooltip_display_as'] == 'icon' ? \Helpers\App::tooltip_icon(
                            $v['tooltip']
                        ) : '') . \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']) . '</label>
            <div class="col-md-9">	
          	  ' . \Models\Main\Fields_types::render(
                            $v['type'],
                            $v,
                            ['field_' . $v['id'] => ''],
                            ['parent_entity_item_id' => \K::$fw->parent_entity_item_id, 'form' => 'comment']
                        ) . '
              ' . ($v['tooltip_display_as'] != 'icon' ? \Helpers\App::tooltip_text($v['tooltip']) : '') . '
            </div>			
          </div>        
        ';
                }

                //build tabs heading
                $html_tab[0] = '<li class="form_tab_0 active"><a data-toggle="tab" href="#form_tab_0">' . \K::$fw->TEXT_GENERAL_INFO . '</a></li>';
                /*$tabs_query = db_fetch_all(
                    'app_comments_forms_tabs',
                    "entities_id='" . db_input(\K::$fw->current_entity_id) . "' order by  sort_order, name"
                );*/

                $tabs_query = \K::model()->db_fetch('app_comments_forms_tabs', [
                    'entities_id = ?',
                    \K::$fw->current_entity_id
                ], ['order' => 'sort_order,name'], 'id,name');

                //while ($tabs = db_fetch_array($tabs_query)) {
                /*foreach ($tabs_query as $tabs) {
                    $tabs = $tabs->cast();

                    $html_tab[$tabs['id']] = '<li class="form_tab_' . $tabs['id'] . '"><a data-toggle="tab" href="#form_tab_' . $tabs['id'] . '">' . $tabs['name'] . '</a></li>';
                }*/

                //build tabs content
                /*$tabs_query = db_fetch_all(
                    'app_comments_forms_tabs',
                    "entities_id='" . db_input(\K::$fw->current_entity_id) . "' order by  sort_order, name"
                );*/
                //while ($tabs = db_fetch_array($tabs_query)) {
                foreach ($tabs_query as $tabs) {
                    $tabs = $tabs->cast();

                    $html_tab[$tabs['id']] = '<li class="form_tab_' . $tabs['id'] . '"><a data-toggle="tab" href="#form_tab_' . $tabs['id'] . '">' . $tabs['name'] . '</a></li>';

                    $html = '';
                    /*$fields_query = db_query(
                        "select f.* from app_fields f where f.type not in (" . fields_types::get_reserverd_types_list(
                        ) . ',' . fields_types::get_users_types_list() . ") and  f.entities_id='" . db_input(
                            \K::$fw->current_entity_id
                        ) . "' and f.comments_status=1 and f.comments_forms_tabs_id='" . $tabs['id'] . "' order by f.comments_sort_order, f.name"
                    );*/

                    $fields_query = \K::model()->db_fetch('app_fields', [
                        'type not in (' . \Models\Main\Fields_types::get_reserverd_types_list(
                        ) . ',' . \Models\Main\Fields_types::get_users_types_list(
                        ) . ') and f.entities_id = ? and comments_status = 1 and comments_forms_tabs_id = ?',
                        \K::$fw->current_entity_id,
                        $tabs['id']
                    ], ['order' => 'comments_sort_order,name']);

                    //while ($v = db_fetch_array($fields_query)) {
                    foreach ($fields_query as $v) {
                        $v = $v->cast();

                        //check field access
                        if (isset($fields_access_schema[$v['id']])) {
                            continue;
                        }

                        //set off required option for comment form
                        $v['is_required'] = 0;

                        $html .= '
          <div class="form-group form-group-' . $v['id'] . '">
          	<label class="col-md-3 control-label" for="fields_' . $v['id'] . '">' . ($v['tooltip_display_as'] == 'icon' ? \Helpers\App::tooltip_icon(
                                $v['tooltip']
                            ) : '') . \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']) . '</label>
            <div class="col-md-9">
          	  ' . \Models\Main\Fields_types::render(
                                $v['type'],
                                $v,
                                ['field_' . $v['id'] => ''],
                                ['parent_entity_item_id' => \K::$fw->parent_entity_item_id, 'form' => 'comment']
                            ) . '
              ' . ($v['tooltip_display_as'] != 'icon' ? \Helpers\App::tooltip_text($v['tooltip']) : '') . '
            </div>
          </div>
        ';
                    }

                    if (strlen($html)) {
                        $html_tab_content[$tabs['id']] = '<div class="tab-pane fade" id="form_tab_' . $tabs['id'] . '">' . $html . '</div>';
                    }
                }

                //render tabs heading if tabs exists
                if (count($html_tab_content)) {
                    $html = '<ul class="nav nav-tabs" id="form_tabs">';

                    $html .= $html_tab[0];

                    //build tabs heading and skip tabs with no fields
                    foreach ($html_tab_content as $tab_id => $content) {
                        $html .= $html_tab[$tab_id];
                    }

                    $html .= '</ul>';

                    $html .= '
  		<div class="tab-content">
  				<div class="tab-pane fade active in" id="form_tab_0">';
                    echo $html;
                }

                //output fields for default tab
                echo $html_default_tab;
            }
            ?>
            <div class="form-group">
                <label class="col-md-3 control-label" for="name"><?= \K::$fw->TEXT_COMMENT ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::textarea_tag(
                        'description',
                        \K::$fw->obj['description'],
                        [
                            'class' => 'form-control autofocus ' . (\K::$fw->entity_cfg->get(
                                    'use_editor_in_comments'
                                ) == 1 ? 'editor-auto-focus' : '')
                        ]
                    ) ?>
                </div>
            </div>

            <?php
            if (\K::$fw->entity_cfg->get('disable_attachments_in_comments') != 1): ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?= \K::$fw->TEXT_ATTACHMENTS ?></label>
                    <div class="col-md-9">
                        <?= \Models\Main\Fields_types::render(
                            'fieldtype_attachments',
                            ['id' => 'attachments'],
                            ['field_attachments' => \K::$fw->obj['attachments']]
                        ) ?>
                        <?= \Helpers\Html::input_hidden_tag(
                            'comments_attachments',
                            '',
                            ['class' => 'form-control required_group']
                        ) ?>
                    </div>
                </div>
            <?php
            endif ?>

            <?php
            //render tabs content
            if (count($html_tab_content)) {
                //build tabs content
                echo '</div>' . implode('', $html_tab_content) . '</div>';
            }

            //render templates fields values
            if (class_exists('comments_templates')) {
                echo comments_templates::render_fields_values(\K::$fw->current_entity_id);
            }
            ?>

        </div>
    </div>

<?= \Helpers\App::ajax_modal_template_footer(
    'hide-save-button',
    '<button type="button" onClick="submit_comments_form()" class="btn btn-primary btn-primary-modal-action">' . addslashes(
        \K::$fw->TEXT_BUTTON_SAVE
    ) . '</button>'
) ?>

    </form>

<?= \K::view()->render(\Helpers\Urls::components_path('main/items/comments_form_validation.js')); ?>

<?= \K::view()->render(\Helpers\Urls::components_path('main/items/forms_fields_rules.js')); ?>