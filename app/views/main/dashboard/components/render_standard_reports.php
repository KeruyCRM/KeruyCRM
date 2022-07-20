<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?><?php

//get report entity info
$entity_info = \K::model()->db_find('app_entities', \K::$fw->reports['entities_id']);
$entity_cfg = new \Models\Main\Entities_cfg(\K::$fw->reports['entities_id']);

//check if parent reports was not set
if ($entity_info['parent_id'] > 0 and \K::$fw->reports['parent_id'] == 0) {
    \Models\Main\Reports\Reports::auto_create_parent_reports(\K::$fw->reports['id']);
}

//get report entity access schema
$access_schema = \Models\Main\Users\Users::get_entities_access_schema(
    \K::$fw->reports['entities_id'],
    \K::$fw->app_user['group_id']
);
$user_access_schema = \Models\Main\Users\Users::get_entities_access_schema(1, \K::$fw->app_user['group_id']);

$add_button = '';
if (\Models\Main\Users\Users::has_access('create', $access_schema) and $entity_cfg->get(
        'reports_hide_insert_button'
    ) != 1) {
    if ($entity_info['parent_id'] == 0) {
        $url = \Helpers\Urls::url_for(
            'main/items/form',
            'path=' . \K::$fw->reports['entities_id'] . '&redirect_to=report_' . \K::$fw->reports['id']
        );
    } elseif ($entity_info['parent_id'] == 1 and !\Models\Main\Users\Users::has_access('view', $user_access_schema)) {
        $url = \Helpers\Urls::url_for(
            'main/items/form',
            'path=1-' . \K::$fw->app_user['id'] . '/' . \K::$fw->reports['entities_id'] . '&redirect_to=report_' . \K::$fw->reports['id']
        );
    } else {
        $url = \Helpers\Urls::url_for('main/reports/prepare_add_item', 'reports_id=' . \K::$fw->reports['id']);
    }
    $add_button = \Helpers\Html::button_tag(
            (strlen($entity_cfg->get('insert_button')) > 0 ? $entity_cfg->get('insert_button') : \K::$fw->TEXT_ADD),
            $url
        ) . ' ';
}

$listing_container = 'entity_items_listing' . \K::$fw->reports['id'] . '_' . \K::$fw->reports['entities_id'];

$gotopage = (isset($_GET['gotopage'][\K::$fw->reports['id']]) ? (int)$_GET['gotopage'][\K::$fw->reports['id']] : 1);

$with_selected_menu = '';

if (\Models\Main\Users\Users::has_access('export_selected', $access_schema) and \Models\Main\Users\Users::has_access(
        'export',
        $access_schema
    )) {
    $with_selected_menu .= '<li>' . \Helpers\Urls::link_to_modalbox(
            '<i class="fa fa-file-excel-o"></i> ' . \K::$fw->TEXT_EXPORT,
            \Helpers\Urls::url_for(
                'main/items/export',
                'path=' . \K::$fw->reports["entities_id"] . '&reports_id=' . \K::$fw->reports['id']
            )
        ) . '</li>';
}

$with_selected_menu .= \Tools\Plugins::include_dashboard_with_selected_menu_items(\K::$fw->reports['id']);

$report_title_html = '
  		<div class="row">
        <div class="col-md-12"><h3 class="page-title"><a href="' . \Helpers\Urls::url_for(
        'main/reports/view',
        'reports_id=' . \K::$fw->reports['id']
    ) . '">' . \K::$fw->reports['name'] . '</a></h3></div>
      </div>
  		';

if (!strlen($add_button) and !strlen($with_selected_menu)) {
    $add_button = $report_title_html;
    $report_title_html = (!\K::$fw->has_reports_on_dashboard ? '' : '');//<br><br>
}

$listing = new \Tools\Items\Items_listing(\K::$fw->reports['id']);
$curren_listing_type = $listing->get_listing_type();
$select_all_html = '';
if (in_array($curren_listing_type, ['grid', 'mobile'])) {
    if (\Models\Main\Listing_types::has_action_field($curren_listing_type, \K::$fw->reports['entities_id'])) {
        $select_all_html = '
			  			<label>' . \Helpers\Html::input_checkbox_tag(
                'select_all_items',
                \K::$fw->reports['id'],
                ['class' => $listing_container . '_select_all_items_force', 'data-container-id' => $listing_container]
            ) . ' ' . \K::$fw->TEXT_SELECT_ALL . '</label>
		  			';
    }
}

$listing_search_form = \Helpers\App::render_listing_search_form(
    \K::$fw->reports["entities_id"],
    $listing_container,
    \K::$fw->reports['id']
);

echo '

    <div class="row dashboard-reports-container" ' . (!isset($reports_groups_info) ? 'id="dashboard-reports-container"' : '') . '>
      <div class="col-md-12">

      ' . $report_title_html . '

      <div class="row">
        <div class="' . (strlen($listing_search_form) ? 'col-sm-6' : 'col-sm-12') . '">
      		<div class="entitly-listing-buttons-left">
             ' . $add_button . '

            ' . (strlen($with_selected_menu) ? '
            <div class="btn-group">
      				<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown">
      				' . \K::$fw->TEXT_WITH_SELECTED . '<i class="fa fa-angle-down"></i>
      				</button>
      				<ul class="dropdown-menu" role="menu">
      					' . $with_selected_menu . '
      				</ul>
      			</div>' : '') .
    $select_all_html .
    '
      		</div>
      	</div> 
      	' . (strlen($listing_search_form) ? '				
		        <div class="col-sm-6">
		         ' . $listing_search_form . '
		        </div>' : '') . '
      </div>

      <div id="' . $listing_container . '" class="entity_items_listing"></div>
      ' . \Helpers\Html::input_hidden_tag(
        $listing_container . '_order_fields',
        \K::$fw->reports['listing_order_fields']
    ) . '
      ' . \Helpers\Html::input_hidden_tag(
        $listing_container . '_has_with_selected',
        (strlen($with_selected_menu) ? 1 : 0)
    ) . '
      ' . (isset($use_redirect_to) ? \Helpers\Html::input_hidden_tag(
        $listing_container . '_redirect_to',
        $use_redirect_to
    ) : '') . '

      </div>
    </div>


    <script>
      $(function() {
        load_items_listing("' . $listing_container . '",' . $gotopage . ');
      });
    </script>
  ';