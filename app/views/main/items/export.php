<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_HEADING_EXPORT) ?>

<?php
if (count(\K::$fw->app_selected_items[\K::$fw->GET['reports_id']]) == 0) {
    echo '
    <div class="modal-body">    
      <div>' . \K::$fw->TEXT_PLEASE_SELECT_ITEMS . '</div>
    </div>    
  ' . \Helpers\App::ajax_modal_template_footer('hide-save-button');
} else {
    ?>
    <div class="modal-body">
        <ul class="nav nav-tabs" id="items_export_tabs">
            <li class="active"><a href="#select_fields_tab"
                                  data-toggle="tab"><?= \K::$fw->TEXT_SELECT_FIELD_TO_EXPORT ?></a></li>
            <li><a href="#my_templates_tab" data-toggle="tab"><?= \K::$fw->TEXT_MY_TEMPLATES ?></a></li>
            <?php
            if (count(\K::$fw->attachments_fields)) {
                echo '<li><a href="#attachments_tab"  data-toggle="tab">' . \K::$fw->TEXT_ATTACHMENTS . '</a></li>';
            }
            ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="select_fields_tab">
                <div id="items_export_templates_button"></div>
                <div id="items_export_templates_selected" style="display:none">
                    <br>
                    <div class="alert alert-info">
                        <span id="items_export_templates_selected_data"></span>
                        <div style="float: right"><a
                                    title="<?= addslashes(\K::$fw->TEXT_UPDATE_SELECTED_TEMPLATE_INFO) ?>"
                                    href="javascript: update_items_export_templates()"><i
                                        class="fa fa-refresh" aria-hidden="true"></i> <?= \K::$fw->TEXT_BUTTON_UPDATE ?>
                            </a></div>
                    </div>
                </div>
                <?= \Helpers\Html::form_tag(
                    'export_form',
                    \Helpers\Urls::url_for('main/items/export', 'path=' . \K::$fw->GET['path']),
                    ['class' => 'form-inline']
                ) . \Helpers\Html::input_hidden_tag('action', 'export') . \Helpers\Html::input_hidden_tag(
                    'reports_id',
                    \K::$fw->GET['reports_id']
                ) ?>
                <p>
                    <?php
                    //while ($tabs = db_fetch_array($tabs_query)) {
                    foreach (\K::$fw->tabs_query as $tabs) {
                        $tabs = $tabs->cast();

                        $fields_html = '';

                        $typeNotIn = ['fieldtype_action', 'fieldtype_section'];

                        if (\K::$fw->app_entities_cache[\K::$fw->current_entity_id]['parent_id'] == 0) {
                            $typeNotIn[] = 'fieldtype_parent_item_id';
                        }

                        $include_types = \K::model()->quoteToString(
                            ['fieldtype_id', 'fieldtype_date_added', 'fieldtype_created_by']
                        );
                        $exclude_types = \K::model()->quoteToString($typeNotIn);

                        /*$fields_query = \K::model()->db_query_exec(
                            "select f.*,if(f.type in (" . $include_types . "),-1,f.sort_order) as field_sort_order from app_fields f where  f.type not in (" . $exclude_types . ") and f.entities_id = ? and forms_tabs_id = ? order by field_sort_order, f.name",
                            [
                                \K::$fw->current_entity_id,
                                $tabs['id']
                            ]
                        );*/

                        $fields_query = \K::model()->db_fetch(
                            'app_fields', [
                            'type not in (' . $exclude_types . ') and entities_id = ? and forms_tabs_id = ?',
                            \K::$fw->current_entity_id,
                            $tabs['id']
                        ],
                            ['order' => 'field_sort_order,name'], null,
                            ['field_sort_order' => 'if(type in (' . $include_types . '),-1,sort_order)']
                        );

                        //while ($v = db_fetch_array($fields_query)) {
                        foreach ($fields_query as $v) {
                            $v = $v->cast();

                            //check field access
                            if (isset($fields_access_schema[$v['id']])) {
                                if ($fields_access_schema[$v['id']] == 'hide') {
                                    continue;
                                }
                            }

                            if (in_array(
                                $v['type'],
                                [
                                    'fieldtype_attachments',
                                    'fieldtype_textarea',
                                    'fieldtype_textarea_wysiwyg',
                                    'fieldtype_input_file',
                                    'fieldtype_attachments'
                                ]
                            )) {
                                $checked = '';
                            } else {
                                $checked = 'checked';
                            }

                            $fields_html .= '<div><label>' . \Helpers\Html::input_checkbox_tag(
                                    'fields[]',
                                    $v['id'],
                                    [
                                        'id' => 'fields_' . $v['id'],
                                        'class' => 'export_fields export_fields_' . $v['id'] . ' fields_tabs_' . $tabs['id'],
                                        'checked' => $checked
                                    ]
                                ) . ' ' . \Models\Main\Fields_types::get_option(
                                    $v['type'],
                                    'name',
                                    $v['name']
                                ) . '</label></div>';
                        }

                        if (strlen($fields_html) > 0) {
                            echo '<p><div><label><b>' . \Helpers\Html::input_checkbox_tag(
                                    'all_tab_fields_' . $tabs['id'],
                                    '',
                                    [
                                        'checked' => 'checked',
                                        'onChange' => 'select_all_by_classname(\'all_tab_fields_' . $tabs['id'] . '\',\'fields_tabs_' . $tabs['id'] . '\')'
                                    ]
                                ) . $tabs['name'] . '</b></label></div>' . $fields_html . '</p>';
                        }
                    }

                    echo '<div><label>' . \Helpers\Html::input_checkbox_tag(
                            'export_url',
                            'url',
                            ['class' => 'export_fields export_fields_url', 'checked' => 'checked']
                        ) . ' ' . \K::$fw->TEXT_URL . '</label></div>';

                    ?>
                </p>
                <br>
                <div class="form-group">
                    <?= \Helpers\Html::input_tag(
                        'filename',
                        \K::$fw->current_entity_info['name'],
                        ['class' => 'form-control input-large required']
                    )
                    ?>
                </div>
                <div class="form-group">
                    <label for="file_extension">&nbsp;</label>
                    <?= \Helpers\Html::select_tag(
                        'file_extension',
                        \K::$fw->choices,
                        'xlsx',
                        ['class' => 'form-control input-small']
                    ) ?>
                </div>
                <br><br>
                <div>
                    <?= '
				<button type="button" class="btn btn-primary" id="btn_export"><i class="fa fa-file-excel-o"></i> ' . \K::$fw->TEXT_BUTTON_EXPORT . '</button> 
				<button type="button" class="btn btn-primary" id="btn_export_print"><i class="fa fa-print"></i> ' . \K::$fw->TEXT_PRINT . '</button>';
                    ?>
                </div>
                </form>
            </div>
            <div class="tab-pane fade" id="my_templates_tab">
                <?= \Helpers\Html::form_tag(
                    'export_templates_form',
                    \Helpers\Urls::url_for('main/items/export/save_templates', 'path=' . \K::$fw->GET['path']),
                    ['class' => 'form-inline']
                ) ?>
                <?= \K::$fw->TEXT_ADD_NEW_TEMPLATE ?>
                <div class="row">
                    <div class="col-md-7">
                        <?= \Helpers\Html::input_tag(
                            'templates_name',
                            '',
                            ['class' => 'form-control required', 'placeholder' => \K::$fw->TEXT_ENTER_TEMPLATE_NAME]
                        ) ?>
                        <?= \Helpers\Html::input_hidden_tag('export_fields_list') ?>
                    </div>
                    <div class="col-md-5">
                        <?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_ADD) ?>
                    </div>
                </div>
                </form>
                <p><?= \Helpers\App::tooltip_text(\K::$fw->TEXT_SAVE_TEMPLATE_NOTE) ?></p>
                <div id="action_response_msg"></div>
                <br>
                <div id="items_export_templates"></div>
            </div>
            <?php
            if (count(\K::$fw->attachments_fields)) {
                $html = '
        <div class="tab-pane fade" id="attachments_tab">
        ' . \Helpers\Html::form_tag(
                        'export_form',
                        \Helpers\Urls::url_for('main/items/export_attachments', 'path=' . \K::$fw->GET['path']),
                        ['class' => 'form-inline']
                    ) . \Helpers\Html::input_hidden_tag('action', 'export') . \Helpers\Html::input_hidden_tag(
                        'reports_id',
                        (int)\K::$fw->GET['reports_id']
                    ) . '
            ' . implode('', \K::$fw->attachments_fields) . '

              <div class="input-group input-large margin-top-10 margin-bottom-10">
                ' . \Helpers\Html::input_tag(
                        'filename',
                        \K::$fw->current_entity_info['name'],
                        ['class' => 'form-control required', 'minlength' => 1, 'required' => 'required']
                    ) . '
                <span class="input-group-addon">.zip</span>
              </div>

            <button type="submit" class="btn btn-primary" id="btn_export"><i class="fa fa-file-archive-o"></i> ' . \K::$fw->TEXT_BUTTON_EXPORT . '</button>
          </form>
        </div>
        
        ';
                echo $html;
            }
            ?>
        </div>
    </div>
    <?= \Helpers\App::ajax_modal_template_footer('hide-save-button', '', \K::$fw->count_selected_text); ?>
    <?= \K::view()->render(\Helpers\Urls::components_path('main/items/export.js')); ?>
    <?php
} ?>
  
