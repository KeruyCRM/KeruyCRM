<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<div class="row">
    <div class="col-md-6">
        <fieldset>
            <legend><?= \K::$fw->TEXT_FIELDS_IN_LISTING ?></legend>
            <p><?= \K::$fw->TEXT_FIELDS_IN_LISTING_RELATED_ITEMS ?></p>

            <?php
            $reports_info = \Tools\Related_records::get_report_info(\K::$fw->fields_info) ?>

            <p>
                <?= \Helpers\Html::button_tag(
                    \K::$fw->TEXT_CONFIGURE_FILTERS,
                    \Helpers\Urls::url_for(
                        'msin/entities/fields_filters',
                        'fields_id=' . \K::$fw->fields_info['id'] . '&entities_id=' . $reports_info['entities_id']
                    ),
                    false
                ) ?>
                <?= \Helpers\Html::button_tag(
                    \K::$fw->TEXT_NAV_LISTING_CONFIG,
                    \Helpers\Urls::url_for(
                        'main/reports/configure',
                        'reports_id=' . $reports_info['id'] . '&redirect_to=related_records_field_settings&fields_id=' . \K::$fw->fields_info['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                    )
                ) . ' ' . \Helpers\Html::button_tag(
                    \K::$fw->TEXT_BUTTON_CONFIGURE_SORTING,
                    \Helpers\Urls::url_for(
                        'main/reports/sorting',
                        'reports_id=' . $reports_info['id'] . '&redirect_to=related_records_field_settings&fields_id=' . \K::$fw->fields_info['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                    )
                ) ?>
            </p>
        </fieldset>
        <?php
        $entity_info = \K::model()->db_find('app_entities', \K::$fw->GET['entities_id']);
        $choices = [
            'no' => \K::$fw->TEXT_NO,
            'comment' => \K::$fw->TEXT_ADD_COMMENT_WITHOUT_NOTIFICATION,
            'comment_notification' => \K::$fw->TEXT_ADD_COMMENT_WITH_NOTIFICATION,
        ];
        ?>
        <br>
        <fieldset>
            <legend><?= \K::$fw->TEXT_ENTITY . ' "' . $entity_info['name'] . '"' ?></legend>
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="create_related_comment"><?= \K::$fw->TEXT_ADD_COMMENT_CREATE_RELATED_ITEM ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'create_related_comment',
                            $choices,
                            \K::$fw->cfg['create_related_comment'],
                            ['class' => 'form-control input-large']
                        ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="create_related_comment_text"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_ENTER_TEXT_PATTERN_INFO
                        ) . \K::$fw->TEXT_PATTERN ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::textarea_tag(
                            'create_related_comment_text',
                            \K::$fw->cfg['create_related_comment_text'],
                            ['style' => 'min-height:50px', 'class' => 'form-control']
                        ) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="delete_related_comment"><?= \K::$fw->TEXT_ADD_COMMENT_DELETE_RELATED_ITEM ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'delete_related_comment',
                            $choices,
                            \K::$fw->cfg['delete_related_comment'],
                            ['class' => 'form-control input-large']
                        ) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="delete_related_comment_text"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_ENTER_TEXT_PATTERN_INFO
                        ) . \K::$fw->TEXT_PATTERN ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::textarea_tag(
                            'delete_related_comment_text',
                            \K::$fw->cfg['delete_related_comment_text'],
                            ['style' => 'min-height:50px', 'class' => 'form-control']
                        ) ?>
                    </div>
                </div>
            </div>
        </fieldset>
        <?php
        $entity_info = \K::model()->db_find('app_entities', \K::$fw->cfg['entity_id']) ?>
        <br>
        <fieldset>
            <legend><?= \K::$fw->TEXT_ENTITY . ' "' . $entity_info['name'] . '"' ?></legend>
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="create_related_comment_to"><?= \K::$fw->TEXT_ADD_COMMENT_CREATE_RELATED_ITEM ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'create_related_comment_to',
                            $choices,
                            \K::$fw->cfg['create_related_comment_to'],
                            ['class' => 'form-control input-large']
                        ) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="create_related_comment_text"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_ENTER_TEXT_PATTERN_INFO
                        ) . \K::$fw->TEXT_PATTERN ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::textarea_tag(
                            'create_related_comment_to_text',
                            \K::$fw->cfg['create_related_comment_to_text'],
                            ['style' => 'min-height:50px', 'class' => 'form-control']
                        ) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="delete_related_comment_to"><?= \K::$fw->TEXT_ADD_COMMENT_DELETE_RELATED_ITEM ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::select_tag(
                            'delete_related_comment_to',
                            $choices,
                            \K::$fw->cfg['delete_related_comment_to'],
                            ['class' => 'form-control input-large']
                        ) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"
                           for="delete_related_comment_to_text"><?= \Helpers\App::tooltip_icon(
                            \K::$fw->TEXT_ENTER_TEXT_PATTERN_INFO
                        ) . \K::$fw->TEXT_PATTERN ?></label>
                    <div class="col-md-9">
                        <?= \Helpers\Html::textarea_tag(
                            'delete_related_comment_to_text',
                            \K::$fw->cfg['delete_related_comment_to_text'],
                            ['style' => 'min-height:50px', 'class' => 'form-control']
                        ) ?>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
    <div class="col-md-6">
        <fieldset>
            <legend><?= \K::$fw->TEXT_FIELDS_IN_POPUP ?></legend>
            <p><?= \K::$fw->TEXT_FIELDS_IN_POPUP_RELATED_ITEMS ?></p>
            <div class="checkbox-list">
                <?php
                $fields_query = \K::model()->db_query_exec(
                    "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where is_heading = 0 and f.type not in (" . \K::model(
                    )->quoteToString(['fieldtype_action', 'fieldtype_parent_item_id']
                    ) . ") and f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
                    \K::$fw->cfg['entity_id'],
                    'app_fields,app_forms_tabs'
                );

                //while ($v = db_fetch_array($fields_query)) {
                foreach ($fields_query as $v) {
                    echo '<label>' . \Helpers\Html::input_checkbox_tag(
                            'fields_in_popup[]',
                            $v['id'],
                            ['checked' => in_array($v['id'], explode(',', \K::$fw->cfg['fields_in_popup']))]
                        ) . ' ' . \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']) . '</label>';
                }
                ?>
            </div>
        </fieldset>
    </div>
</div>