<?php
require(component_path('entities/navigation')) ?>

<h3 class="page-title"><?php
    echo TEXT_NAV_ITEM_PAGE_CONFIG ?></h3>

<?php
$default_selector = ['1' => TEXT_YES, '0' => TEXT_NO]; ?>

<?php
echo form_tag(
    'cfg',
    url_for('entities/item_page_configuration', 'action=save&entities_id=' . $_GET['entities_id']),
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
    <li class="active"><a href="#general_info" data-toggle="tab"><?php
            echo TEXT_GENERAL_INFO ?></a></li>
    <li><a href="#js_code" id="js_code_tab" data-toggle="tab"><?php
            echo TEXT_JS_CODE ?></a></li>
    <li><a href="#php_code" id="php_code_tab" data-toggle="tab"><?php
            echo TEXT_PHP_CODE ?></a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade active in" id="general_info">

        <?php
        $choices = [];
        $choices['3-9'] = '20% - 80%';
        $choices['4-8'] = '30% - 70%';
        $choices['5-7'] = '40% - 60%';
        $choices['6-6'] = '50% - 50%';
        $choices['7-5'] = '60% - 40%';
        $choices['8-4'] = '70% - 30%';
        $choices['9-3'] = '80% - 20%';
        $choices['12-12'] = '100% - 0%';
        ?>
        <div class="form-group">
            <label class="col-md-3 control-label" for="cfg_menu_title"><?php
                echo TEXT_COLUMNS_SIZE; ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'cfg[item_page_columns_size]',
                    $choices,
                    $cfg->get('item_page_columns_size', '8-4'),
                    ['class' => 'form-control input-small']
                ); ?>
                <?php
                echo tooltip_text(TEXT_ITEM_PAGE_COLUMNS_SIZE) ?>
            </div>
        </div>

        <?php
        $choices = [];
        $choices['1'] = TEXT_ONE_COLUMN;
        $choices['one_column_tabs'] = TEXT_ONE_COLUMN_TABS;
        $choices['one_column_accordion'] = TEXT_ONE_COLUMN_ACCORDION;
        $choices['2'] = TEXT_TWO_COLUMNS;
        ?>
        <div class="form-group">
            <label class="col-md-3 control-label" for="cfg_menu_title"><?php
                echo TEXT_ITEM_DETAILS_POSITION; ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'cfg[item_page_details_columns]',
                    $choices,
                    $cfg->get('item_page_details_columns', '2'),
                    ['class' => 'form-control input-medium']
                ); ?>
                <?php
                echo tooltip_text(TEXT_ITEM_DETAILS_POSITION_INFO) ?>
            </div>
        </div>

        <?php
        $choices = [];
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.is_heading!=1 and f.entities_id='" . db_input(
                _get::int('entities_id')
            ) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['tab_name']][$fields['id']] = $fields['name'];
        }
        ?>
        <div class="form-group">
            <label class="col-md-3 control-label" for="cfg_menu_title"><?php
                echo TEXT_HIDEN_FIELDS; ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'cfg[item_page_hidden_fields][]',
                    $choices,
                    $cfg->get('item_page_hidden_fields', ''),
                    ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                ); ?>
                <?php
                echo tooltip_text(TEXT_ITEM_HIDDEN_PAGE_INFO) ?>
            </div>
        </div>

        <?php
        $choices = [];
        $choices['left_column'] = TEXT_LEFT_COLUMN;
        $choices['right_column'] = TEXT_RIGHT_COLUMN;
        ?>
        <div class="form-group">
            <label class="col-md-3 control-label" for="cfg_menu_title"><?php
                echo TEXT_COMMENTS; ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'cfg[item_page_comments_position]',
                    $choices,
                    $cfg->get('item_page_comments_position', 'left'),
                    ['class' => 'form-control input-medium']
                ); ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="cfg_menu_title"><?php
                echo TEXT_ADD_RECORDS_TO_FAVORITES; ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'cfg[enable_favorites]',
                    $default_selector,
                    $cfg->get('enable_favorites', 0),
                    ['class' => 'form-control input-small']
                ); ?>
                <?php
                echo tooltip_text(TEXT_ADD_RECORDS_TO_FAVORITES_TIP) ?>
            </div>
        </div>


    </div>
    <div class="tab-pane fade" id="js_code">
        <p><?php
            echo TEXT_CODE_ON_ITEM_PAGE ?></p>

        <?php
        echo textarea_tag(
            'cfg[javascript_in_item_page]',
            $cfg->get('javascript_in_item_page'),
            ['class' => 'form-control']
        ) ?>
    </div>
    <div class="tab-pane fade" id="php_code">
        <p><?php
            echo TEXT_CODE_ON_ITEM_PAGE ?></p>

        <?php
        echo textarea_tag('cfg[php_in_item_page]', $cfg->get('php_in_item_page'), ['class' => 'form-control']) ?>
        <p>
        <ul class="list-inline">
            <li><?php
                echo TEXT_DEBUG_MODE ?></li>
            <li><?php
                echo select_tag(
                    'cfg[php_in_item_page_debug_mode]',
                    $default_selector,
                    $cfg->get('php_in_item_page_debug_mode', 0),
                    ['class' => 'form-control input-small']
                ); ?></li>
        </ul>
        </p>
    </div>

    <div style="padding: 10px 0 10px 0;"><?php
        echo submit_tag(TEXT_BUTTON_SAVE) ?></div>

</div>

<?php
echo app_include_codemirror(['javascript', 'php', 'clike', 'css', 'xml']) ?>

<script>

    $('#js_code_tab').click(function () {
        if (!$(this).hasClass('acitve-codemirror')) {
            setTimeout(function () {
                var myCodeMirror2 = CodeMirror.fromTextArea(document.getElementById('cfg_javascript_in_item_page'), {
                    lineNumbers: true,
                    lineWrapping: true,
                    matchBrackets: true
                });
            }, 300);

            $(this).addClass('acitve-codemirror')
        }
    })

    $('#php_code_tab').click(function () {
        if (!$(this).hasClass('acitve-codemirror')) {
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

            $(this).addClass('acitve-codemirror')
        }
    })

</script>


<hr>

<?php
//configure subentites
$html = '';
$entities_query = db_query("select * from app_entities where parent_id = '" . db_input(_get::int('entities_id')) . "'");
if (db_num_rows($entities_query)) {
    $html .= '
			<h1 class="page-title">' . TEXT_SUB_ENTITIES . '</h1>
			<p>' . TEXT_ITEM_DETAILS_SUM_ENTITIES . '</p>
		';

    $choices = [];
    $choices[''] = '';
    $choices['left_column'] = TEXT_LEFT_COLUMN;
    $choices['right_column'] = TEXT_RIGHT_COLUMN;

    $default_selector = ['0' => TEXT_NO, '1' => TEXT_YES];

    while ($entities = db_fetch_array($entities_query)) {
        $html .= '
                <div class="form-group" style="margin-bottom: 0px;">
                    <label class="col-md-3 control-label" for="cfg_menu_title"><b>' . $entities['name'] . '</b></label>
                    <div class="col-md-9">
                            <ul class="list-inline">  			
                                <li>' . select_tag(
                'cfg[item_page_subentity' . $entities['id'] . '_position]',
                $choices,
                $cfg->get('item_page_subentity' . $entities['id'] . '_position'),
                ['class' => 'form-control input-medium']
            ) . '</li>
                                <li>' . TEXT_HIDE_EMPTY_BLOCK . ' </li>
                                <li>' . select_tag(
                'cfg[hide_subentity' . $entities['id'] . '_if_empty]',
                $default_selector,
                $cfg->get('hide_subentity' . $entities['id'] . '_if_empty'),
                ['class' => 'form-control input-msmall']
            ) . '</li>
                                <li><a href="' . url_for(
                'entities/parent_infopage_filters',
                'entities_id=' . $entities['id']
            ) . '" title="' . TEXT_CONFIGURE_FILTERS . '"><i class="fa fa-cogs" aria-hidden="true"></i> ' . TEXT_CONFIGURE_FILTERS . ' (' . reports::count_filters_by_reports_type(
                $entities['id'],
                'parent_item_info_page'
            ) . ')</a></li>
                            </ul>
                    </div>			
                </div> 
                <div class="form-group" style="margin-bottom: 0px;">
                    <label class="col-md-3 control-label" for="cfg_menu_title">' . tooltip_icon(
                TEXT_LISTING_HEADING_TOOLTIP
            ) . TEXT_LISTING_HEADING . '</label>
                    <div class="col-md-9">
                        <ul class="list-inline">
                            <li>' . input_tag(
                'cfg[item_page_subentity' . $entities['id'] . '_heading]',
                $cfg->get('item_page_subentity' . $entities['id'] . '_heading'),
                ['class' => 'form-control input-medium']
            ) . '</li>
                            <li>' . TEXT_HIDE_IN_TOP_MENU . ' </li><li>' . select_tag(
                'cfg[hide_subentity' . $entities['id'] . '_in_top_menu]',
                $default_selector,
                $cfg->get('hide_subentity' . $entities['id'] . '_in_top_menu'),
                ['class' => 'form-control input-msmall']
            ) . '</li>
                            <li><a href="' . url_for(
                'entities/hide_subentity_filters',
                'entities_id=' . $entities['id']
            ) . '" title="' . TEXT_CONFIGURE_FILTERS . '"><i class="fa fa-cogs" aria-hidden="true"></i> ' . TEXT_HIDE_BY_CONDITION . ' (' . reports::count_filters_by_reports_type(
                $entities['parent_id'],
                'hide_subentity_' . $entities['id']
            ) . ')</a></li>
                        </ul>
                    </div>			
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="cfg_menu_title">' . TEXT_COLLAPSED . '</label>
                    <div class="col-md-9">
                        ' . select_tag(
                'cfg[collapsed_subentity' . $entities['id'] . ']',
                $default_selector,
                $cfg->get('collapsed_subentity' . $entities['id']),
                ['class' => 'form-control input-msmall']
            ) . '
                    </div>			
                </div>
                <hr>
			';
    }

    $html .= submit_tag(TEXT_BUTTON_SAVE);
}

echo $html;

//configure entites related by field Entity
$html = '';

$choices = [];
$choices[''] = '';
$choices['left_column'] = TEXT_LEFT_COLUMN;
$choices['right_column'] = TEXT_RIGHT_COLUMN;

$fields_query = db_query(
    "select id, name, configuration, entities_id from app_fields where entities_id!='" . db_input(
        _get::int('entities_id')
    ) . "' and type in ('fieldtype_entity','fieldtype_entity_ajax','fieldtype_entity_multilevel')"
);
while ($fields = db_fetch_array($fields_query)) {
    $field_cfg = new fields_types_cfg($fields['configuration']);

    if ($field_cfg->get('entity_id') == _get::int('entities_id')) {
        $entities = $app_entities_cache[$fields['entities_id']];
        $html .= '
                <div class="form-group" style="margin-bottom: 0px;">
                    <label class="col-md-3 control-label" for="cfg_menu_title"><b>' . $fields['name'] . ' (' . $entities['name'] . ')</b></label>
                    <div class="col-md-9">
                        <ul class="list-inline">
                            <li>' . select_tag(
                'cfg[item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_position]',
                $choices,
                $cfg->get('item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_position'),
                ['class' => 'form-control input-medium']
            ) . '</li>
                            <li>' . TEXT_HIDE_EMPTY_BLOCK . ' </li>
                            <li>' . select_tag(
                'cfg[hide_item_page_field' . $fields['id'] . '_if_empty]',
                $default_selector,
                $cfg->get('hide_item_page_field' . $fields['id'] . '_if_empty'),
                ['class' => 'form-control input-msmall']
            ) . '</li>
                            <li><a href="' . url_for(
                'entities/infopage_entityfield_filters',
                'entities_id=' . $entities['id'] . '&related_entities_id=' . _get::int(
                    'entities_id'
                ) . '&fields_id=' . $fields['id']
            ) . '" title="' . TEXT_CONFIGURE_FILTERS . '"><i class="fa fa-cogs" aria-hidden="true"></i> ' . TEXT_CONFIGURE_FILTERS . ' (' . reports::count_filters_by_reports_type(
                $entities['id'],
                'field' . $fields['id'] . '_entity_item_info_page'
            ) . ')</a></li>
                        </ul>
                    </div>
                </div>
                <div class="form-group"  style="margin-bottom: 10px;">
                    <label class="col-md-3 control-label" for="cfg_menu_title">' . tooltip_icon(
                TEXT_LISTING_HEADING_TOOLTIP
            ) . TEXT_LISTING_HEADING . '</label>
                    <div class="col-md-9">
                        ' . input_tag(
                'cfg[item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_heading]',
                $cfg->get('item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_heading'),
                ['class' => 'form-control input-medium']
            ) . '  				
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">' . TEXT_COLLAPSED . '</label>
                    <div class="col-md-9">
                        ' . select_tag(
                'cfg[item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_collapsed]',
                $default_selector,
                $cfg->get('item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_collapsed'),
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
				<h1 class="page-title">' . TEXT_RELATED_ENTITIES_BY_FIELD_ENTITY . '</h1>
				<p>' . TEXT_RELATED_ENTITIES_BY_FIELD_ENTITY_INFO . '</p>
			' . $html;

    $html .= submit_tag(TEXT_BUTTON_SAVE);
}

echo $html;
?>

</form>


<script>
    $(function () {
        $('.tooltips').tooltip();
    });
</script>    



