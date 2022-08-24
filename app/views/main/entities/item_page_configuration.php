<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>

<h3 class="page-title"><?= \K::$fw->TEXT_NAV_ITEM_PAGE_CONFIG ?></h3>

<?= \Helpers\Html::form_tag(
    'cfg',
    \Helpers\Urls::url_for('main/entities/item_page_configuration/save', 'entities_id=' . \K::$fw->GET['entities_id']),
    ['class' => 'form-horizontal']
) ?>

<style>
    .tab-content {
        background: white;
    }

    .nav-tabs, .nav-pills {
        margin-bottom: 0px;
    }

    .tab-content {
        padding: 15px 15px 5px 15px;
    }
</style>

<ul class="nav nav-tabs">
    <li class="active"><a href="#general_info" data-toggle="tab"><?= \K::$fw->TEXT_GENERAL_INFO ?></a></li>
    <li><a href="#js_code" id="js_code_tab" data-toggle="tab"><?= \K::$fw->TEXT_JS_CODE ?></a></li>
    <li><a href="#php_code" id="php_code_tab" data-toggle="tab"><?= \K::$fw->TEXT_PHP_CODE ?></a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade active in" id="general_info">
        <div class="form-group">
            <label class="col-md-3 control-label" for="cfg_menu_title"><?= \K::$fw->TEXT_COLUMNS_SIZE; ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'cfg[item_page_columns_size]',
                    \K::$fw->choices,
                    \K::$fw->cfg->get('item_page_columns_size', '8-4'),
                    ['class' => 'form-control input-small']
                ); ?>
                <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_ITEM_PAGE_COLUMNS_SIZE) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"
                   for="cfg_menu_title"><?= \K::$fw->TEXT_ITEM_DETAILS_POSITION; ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'cfg[item_page_details_columns]',
                    \K::$fw->choices2,
                    \K::$fw->cfg->get('item_page_details_columns', '2'),
                    ['class' => 'form-control input-medium']
                ); ?>
                <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_ITEM_DETAILS_POSITION_INFO) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="cfg_menu_title"><?= \K::$fw->TEXT_HIDDEN_FIELDS; ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'cfg[item_page_hidden_fields][]',
                    \K::$fw->choices3,
                    \K::$fw->cfg->get('item_page_hidden_fields', ''),
                    ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                ); ?>
                <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_ITEM_HIDDEN_PAGE_INFO) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label" for="cfg_menu_title"><?= \K::$fw->TEXT_COMMENTS; ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'cfg[item_page_comments_position]',
                    \K::$fw->choices4,
                    \K::$fw->cfg->get('item_page_comments_position', 'left'),
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label"
                   for="cfg_menu_title"><?= \K::$fw->TEXT_ADD_RECORDS_TO_FAVORITES; ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'cfg[enable_favorites]',
                    \K::$fw->default_selector,
                    \K::$fw->cfg->get('enable_favorites', 0),
                    ['class' => 'form-control input-small']
                ); ?>
                <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_ADD_RECORDS_TO_FAVORITES_TIP) ?>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="js_code">
        <p><?= \K::$fw->TEXT_CODE_ON_ITEM_PAGE ?></p>

        <?= \Helpers\Html::textarea_tag(
            'cfg[javascript_in_item_page]',
            \K::$fw->cfg->get('javascript_in_item_page'),
            ['class' => 'form-control']
        ) ?>
    </div>
    <div class="tab-pane fade" id="php_code">
        <p><?= \K::$fw->TEXT_CODE_ON_ITEM_PAGE ?></p>

        <?= \Helpers\Html::textarea_tag(
            'cfg[php_in_item_page]',
            \K::$fw->cfg->get('php_in_item_page'),
            ['class' => 'form-control']
        ) ?>
        <p>
        <ul class="list-inline">
            <li><?= \K::$fw->TEXT_DEBUG_MODE ?></li>
            <li><?= \Helpers\Html::select_tag(
                    'cfg[php_in_item_page_debug_mode]',
                    \K::$fw->default_selector,
                    \K::$fw->cfg->get('php_in_item_page_debug_mode', 0),
                    ['class' => 'form-control input-small']
                ); ?></li>
        </ul>
        </p>
    </div>
    <div style="padding: 10px 0 10px 0;"><?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?></div>
</div>

<?= \Helpers\App::app_include_codemirror(['javascript', 'php', 'clike', 'css', 'xml']) ?>

<script>
    $('#js_code_tab').click(function () {
        if (!$(this).hasClass('active-codemirror')) {
            setTimeout(function () {
                var myCodeMirror2 = CodeMirror.fromTextArea(document.getElementById('cfg_javascript_in_item_page'), {
                    lineNumbers: true,
                    lineWrapping: true,
                    matchBrackets: true
                });
            }, 300);

            $(this).addClass('active-codemirror')
        }
    })

    $('#php_code_tab').click(function () {
        if (!$(this).hasClass('active-codemirror')) {
            setTimeout(function () {
                var myCodeMirror2 = CodeMirror.fromTextArea(document.getElementById('cfg_php_in_item_page'), {
                    mode: {
                        name: 'php',
                        startOpen: true
                    },
                    lineNumbers: true,
                    lineWrapping: true,
                    matchBrackets: true
                });
            }, 300);

            $(this).addClass('active-codemirror')
        }
    })
</script>
<hr>

<?php
//configure subentites
$html = '';

if (count(\K::$fw->entities_query)) {
    $html .= '
			<h1 class="page-title">' . \K::$fw->TEXT_SUB_ENTITIES . '</h1>
			<p>' . \K::$fw->TEXT_ITEM_DETAILS_SUM_ENTITIES . '</p>
		';

    $choices = [];
    $choices[''] = '';
    $choices['left_column'] = \K::$fw->TEXT_LEFT_COLUMN;
    $choices['right_column'] = \K::$fw->TEXT_RIGHT_COLUMN;

    $default_selector = ['0' => \K::$fw->TEXT_NO, '1' => \K::$fw->TEXT_YES];

    //while ($entities = db_fetch_array($entities_query)) {
    foreach (\K::$fw->entities_query as $entities) {
        $entities = $entities->cast();

        $html .= '
                <div class="form-group" style="margin-bottom: 0px;">
                    <label class="col-md-3 control-label" for="cfg_menu_title"><b>' . $entities['name'] . '</b></label>
                    <div class="col-md-9">
                            <ul class="list-inline">  			
                                <li>' . \Helpers\Html::select_tag(
                'cfg[item_page_subentity' . $entities['id'] . '_position]',
                $choices,
                \K::$fw->cfg->get('item_page_subentity' . $entities['id'] . '_position'),
                ['class' => 'form-control input-medium']
            ) . '</li>
                                <li>' . \K::$fw->TEXT_HIDE_EMPTY_BLOCK . ' </li>
                                <li>' . \Helpers\Html::select_tag(
                'cfg[hide_subentity' . $entities['id'] . '_if_empty]',
                $default_selector,
                \K::$fw->cfg->get('hide_subentity' . $entities['id'] . '_if_empty'),
                ['class' => 'form-control input-msmall']
            ) . '</li>
                                <li><a href="' . \Helpers\Urls::url_for(
                'main/entities/parent_infopage_filters',
                'entities_id=' . $entities['id']
            ) . '" title="' . \K::$fw->TEXT_CONFIGURE_FILTERS . '"><i class="fa fa-cogs" aria-hidden="true"></i> ' . \K::$fw->TEXT_CONFIGURE_FILTERS . ' (' . \Models\Main\Reports\Reports::count_filters_by_reports_type(
                $entities['id'],
                'parent_item_info_page'
            ) . ')</a></li>
                            </ul>
                    </div>			
                </div> 
                <div class="form-group" style="margin-bottom: 0px;">
                    <label class="col-md-3 control-label" for="cfg_menu_title">' . \Helpers\App::tooltip_icon(
                \K::$fw->TEXT_LISTING_HEADING_TOOLTIP
            ) . \K::$fw->TEXT_LISTING_HEADING . '</label>
                    <div class="col-md-9">
                        <ul class="list-inline">
                            <li>' . \Helpers\Html::input_tag(
                'cfg[item_page_subentity' . $entities['id'] . '_heading]',
                \K::$fw->cfg->get('item_page_subentity' . $entities['id'] . '_heading'),
                ['class' => 'form-control input-medium']
            ) . '</li>
                            <li>' . \K::$fw->TEXT_HIDE_IN_TOP_MENU . ' </li><li>' . \Helpers\Html::select_tag(
                'cfg[hide_subentity' . $entities['id'] . '_in_top_menu]',
                $default_selector,
                \K::$fw->cfg->get('hide_subentity' . $entities['id'] . '_in_top_menu'),
                ['class' => 'form-control input-msmall']
            ) . '</li>
                            <li><a href="' . \Helpers\Urls::url_for(
                'main/entities/hide_subentity_filters',
                'entities_id=' . $entities['id']
            ) . '" title="' . \K::$fw->TEXT_CONFIGURE_FILTERS . '"><i class="fa fa-cogs" aria-hidden="true"></i> ' . \K::$fw->TEXT_HIDE_BY_CONDITION . ' (' . \Models\Main\Reports\Reports::count_filters_by_reports_type(
                $entities['parent_id'],
                'hide_subentity_' . $entities['id']
            ) . ')</a></li>
                        </ul>
                    </div>			
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="cfg_menu_title">' . \K::$fw->TEXT_COLLAPSED . '</label>
                    <div class="col-md-9">
                        ' . \Helpers\Html::select_tag(
                'cfg[collapsed_subentity' . $entities['id'] . ']',
                $default_selector,
                \K::$fw->cfg->get('collapsed_subentity' . $entities['id']),
                ['class' => 'form-control input-msmall']
            ) . '
                    </div>			
                </div>
                <hr>
			';
    }

    $html .= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE);
}

echo $html;

//configure entites related by field Entity
$html = '';

$choices = [];
$choices[''] = '';
$choices['left_column'] = \K::$fw->TEXT_LEFT_COLUMN;
$choices['right_column'] = \K::$fw->TEXT_RIGHT_COLUMN;

//while ($fields = db_fetch_array($fields_query)) {
foreach (\K::$fw->fields_query2 as $fields) {
    $fields = $fields->cast();

    $field_cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

    if ($field_cfg->get('entity_id') == \K::$fw->GET['entities_id']) {
        $entities = \K::$fw->app_entities_cache[$fields['entities_id']];
        $html .= '
                <div class="form-group" style="margin-bottom: 0px;">
                    <label class="col-md-3 control-label" for="cfg_menu_title"><b>' . $fields['name'] . ' (' . $entities['name'] . ')</b></label>
                    <div class="col-md-9">
                        <ul class="list-inline">
                            <li>' . \Helpers\Html::select_tag(
                'cfg[item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_position]',
                $choices,
                \K::$fw->cfg->get('item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_position'),
                ['class' => 'form-control input-medium']
            ) . '</li>
                            <li>' . \K::$fw->TEXT_HIDE_EMPTY_BLOCK . ' </li>
                            <li>' . \Helpers\Html::select_tag(
                'cfg[hide_item_page_field' . $fields['id'] . '_if_empty]',
                \K::$fw->default_selector,
                \K::$fw->cfg->get('hide_item_page_field' . $fields['id'] . '_if_empty'),
                ['class' => 'form-control input-msmall']
            ) . '</li>
                            <li><a href="' . \Helpers\Urls::url_for(
                'main/entities/infopage_entityfield_filters',
                'entities_id=' . $entities['id'] . '&related_entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . $fields['id']
            ) . '" title="' . \K::$fw->TEXT_CONFIGURE_FILTERS . '"><i class="fa fa-cogs" aria-hidden="true"></i> ' . \K::$fw->TEXT_CONFIGURE_FILTERS . ' (' . \Models\Main\Reports\Reports::count_filters_by_reports_type(
                $entities['id'],
                'field' . $fields['id'] . '_entity_item_info_page'
            ) . ')</a></li>
                        </ul>
                    </div>
                </div>
                <div class="form-group"  style="margin-bottom: 10px;">
                    <label class="col-md-3 control-label" for="cfg_menu_title">' . \Helpers\App::tooltip_icon(
                \K::$fw->TEXT_LISTING_HEADING_TOOLTIP
            ) . \K::$fw->TEXT_LISTING_HEADING . '</label>
                    <div class="col-md-9">
                        ' . \Helpers\Html::input_tag(
                'cfg[item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_heading]',
                \K::$fw->cfg->get('item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_heading'),
                ['class' => 'form-control input-medium']
            ) . '  				
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">' . \K::$fw->TEXT_COLLAPSED . '</label>
                    <div class="col-md-9">
                        ' . \Helpers\Html::select_tag(
                'cfg[item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_collapsed]',
                \K::$fw->default_selector,
                \K::$fw->cfg->get('item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_collapsed'),
                ['class' => 'form-control input-msmall']
            ) . '
                    </div>			
                </div>
                
                <hr>
			';
    }
}

if (strlen($html)) {
    $html = '
				<h1 class="page-title">' . \K::$fw->TEXT_RELATED_ENTITIES_BY_FIELD_ENTITY . '</h1>
				<p>' . \K::$fw->TEXT_RELATED_ENTITIES_BY_FIELD_ENTITY_INFO . '</p>
			' . $html;

    $html .= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE);
}

echo $html;
?>

</form>

<script>
    $(function () {
        $('.tooltips').tooltip();
    });
</script>