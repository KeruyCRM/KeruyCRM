<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php
require(component_path('items/navigation')) ?>

<?php
$current_item_info = [];
$item_page_columns = explode('-', $entity_cfg->get('item_page_columns_size', '8-4'));
?>

<!-- include form fields display rules in info page  -->
<?php
require(component_path('items/forms_fields_rules.js')); ?>

<div class="row">

    <!-- First Column  -->
    <div class="col-md-<?php
    echo $item_page_columns[0] ?> project-info">

        <?php
        $portlets = new portlets('item_info_' . $current_item_id) ?>
        <div class="portlet portlet-item-description">
            <div class="portlet-title">
                <div class="caption">
                    <?php
                    echo $app_breadcrumb[count($app_breadcrumb) - 1]['title'] ?>
                </div>
                <div class="tools">
                    <?php

                    $favorites = new favorites($current_entity_id, $current_item_id);
                    echo $favorites->render_icon();

                    $help_pages = new help_pages($current_entity_id);
                    echo $help_pages->render_icon('info');
                    ?>

                    <a href="javascript:;" class="<?php
                    echo $portlets->button_css() ?>"></a>
                </div>
            </div>
            <div class="portlet-body" <?php
            echo $portlets->render_body() ?>>


                <!-- Inlucde timer from Extension -->
                <?php
                $access_rules = new access_rules($current_entity_id, $item_info);

                $item_actions_menu = '';

                if (is_ext_installed()) {
                    $timer = new timer($current_entity_id, $current_item_id);
                    $item_actions_menu .= $timer->render_button();
                }

                if (users::has_access('update', $access_rules->get_access_schema())) {
                    $item_actions_menu .= '<li>' . button_tag(
                            TEXT_BUTTON_EDIT,
                            url_for(
                                'items/form',
                                'id=' . $current_item_id . '&entity_id=' . $current_entity_id . '&path=' . $_GET['path'] . '&redirect_to=items_info'
                            ),
                            true,
                            ['class' => 'btn btn-primary btn-sm'],
                            'fa-edit'
                        ) . '</li>';
                }

                $export_templates_menu = '';

                if (is_ext_installed()) {
                    $processes = new processes($current_entity_id);
                    $processes->items_id = $current_item_id;
                    $item_actions_menu .= $processes->render_buttons('default');

                    //print templates
                    $export_templates_menu .= export_templates::get_users_templates_by_position(
                            $current_entity_id,
                            'default'
                        )
                        . report_page\report::get_buttons_by_position($current_entity_id, $current_item_id, 'default');

                    $export_templates_menu_print = export_templates::get_users_templates_by_position(
                            $current_entity_id,
                            'menu_print'
                        )
                        . report_page\report::get_buttons_by_position(
                            $current_entity_id,
                            $current_item_id,
                            'menu_print'
                        );

                    if (strlen($export_templates_menu_print)) {
                        $export_templates_menu .= '
                            <li>
                                <div class="btn-group">
                                    <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown">
                                        <i class="fa fa-print"></i> ' . TEXT_PRINT . ' <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">                                       
                                        ' . $export_templates_menu_print . '												
                                    </ul>
                                </div>
                            </li>
                            ';
                    }

                    $item_actions_menu .= $export_templates_menu;

                    $item_actions_menu .= xml_export::get_users_templates_by_position($current_entity_id, 'default');
                    $item_actions_menu .= xml_export::get_users_templates_by_position(
                        $current_entity_id,
                        'menu_export'
                    );

                    $item_actions_menu .= xml_import::get_users_templates_by_position($current_entity_id, 'default');
                }

                $more_actions_menu = '';

                if (is_ext_installed()) {
                    $more_actions_menu .= $processes->render_buttons('menu_more_actions');
                }

                $more_actions_menu .= plugins::render_simple_menu_items('more_actions');

                if ((users::has_access('update', $access_rules->get_access_schema()) or users::has_access(
                            'create',
                            $access_rules->get_access_schema()
                        )) and listing_types::has_tree_table($current_entity_id)) {
                    $more_actions_menu .= '<li>' . link_to_modalbox(
                            '<i class="fa fa-sitemap"></i> ' . TEXT_CHANGE_PARENT_ITEM,
                            url_for('items/change_parent', 'path=' . $_GET['path'])
                        ) . '</li>';
                }

                if (users::has_access('export', $access_rules->get_access_schema())) {
                    $more_actions_menu .= '<li>' . link_to_modalbox(
                            '<i class="fa fa-file-pdf-o"></i> ' . TEXT_BUTTON_EXPORT,
                            url_for('items/single_export', 'path=' . $_GET['path'])
                        ) . '</li>';
                }

                if (users::has_access('update', $access_rules->get_access_schema()) and $current_entity_id == 1) {
                    $more_actions_menu .= '<li>' . link_to(
                            '<i class="fa fa-unlock-alt"></i> ' . TEXT_CHANGE_PASSWORD,
                            url_for('items/change_user_password', 'path=' . $_GET['path'])
                        ) . '</li>';
                }

                if (users::has_access('delete', $access_rules->get_access_schema())) {
                    $check = true;

                    if (users::has_access(
                            'delete_creator',
                            $access_rules->get_access_schema()
                        ) and $item_info['created_by'] != $app_user['id']) {
                        $check = false;
                    }

                    if ($check) {
                        $more_actions_menu .= '<li><a href="#" onClick="open_dialog(\'' . url_for(
                                'items/delete',
                                'id=' . $current_item_id . '&entity_id=' . $current_entity_id . '&path=' . $_GET['path']
                            ) . '\'); return false;"><i class="fa fa-trash-o"></i> ' . TEXT_BUTTON_DELETE . '</a></li>';
                    }
                }

                //check access to action with assigned only
                if (users::has_access('action_with_assigned')) {
                    if (!users::has_access_to_assigned_item($current_entity_id, $current_item_id)) {
                        $item_actions_menu = $more_actions_menu = '';
                        $item_actions_menu = $export_templates_menu;
                    }
                }

                if (strlen($more_actions_menu)) {
                    $item_actions_menu .= '
			<li>
	  	 <div class="btn-group">
					<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown">
					' . TEXT_MORE_ACTIONS . ' <i class="fa fa-angle-down"></i>
					</button>
					<ul class="dropdown-menu" role="menu">                                       
					' . $more_actions_menu . '												
					</ul>
				</div>
			</li>
	';
                }

                if (strlen($item_actions_menu)) {
                    echo '
		<div class="prolet-body-actions">        
          <ul class="list-inline">
					' . $item_actions_menu . '
          </ul>
        </div>
		';
                }


                //Stages panels
                echo stages_panel::render($current_entity_id, $item_info);
                ?>

                <!-- Inlucde timer from Extension -->
                <?php
                if (class_exists('timer')) {
                    echo $timer->render();
                }
                ?>

                <div class="item-content-box ckeditor-images-content-prepare">
                    <?php
                    if (in_array(
                        $entity_cfg->get('item_page_details_columns'),
                        ['one_column_tabs', 'one_column_accordion']
                    )) {
                        $items_page = new items_page($current_entity_id, $current_item_id);
                        echo $items_page->render($entity_cfg->get('item_page_details_columns'));
                    } elseif ($entity_cfg->get('item_page_details_columns', '2') == 1) {
                        echo items::render_info_box($current_entity_id, $current_item_id, false, false);
                    } else {
                        echo items::render_content_box($current_entity_id, $current_item_id);
                    }
                    ?>
                </div>

            </div>
        </div>

        <?php
        //include related emails
        if (is_ext_installed()) {
            $mail_related = new mail_related($current_entity_id, 'left_column');
            echo $mail_related->render_list($current_item_id);
        }

        //include reladed records that displays as single list
        $reladed_records = new related_records($current_entity_id, $current_item_id);
        echo $reladed_records->render_as_single_list();

        echo tree_table::render_nested_items($current_entity_id, $current_item_id, 'left_column');

        //includes subentity imtes listins if configure for item info page
        $subentities_items_position = 'left_column';
        require(component_path('items/load_subentities_items'));

        //includes field entity imtes listins if configure for item info page
        $field_entity_items_position = 'left_column';
        require(component_path('items/load_field_entity_items'));

        if (is_ext_installed()) {
            $item_pivot_tables = new item_pivot_tables($current_entity_id, 'left_column');
            echo $item_pivot_tables->render();
        }
        ?>

        <?php
        //include items comments if user have access and comments enabled
        if (users::has_comments_access('view', $access_rules->get_comments_access_schema()) and $entity_cfg->get(
                'use_comments'
            ) == 1 and $entity_cfg->get('item_page_comments_position', 'left_column') == 'left_column') {
            require(component_path('items/comments'));
        }
        ?>

    </div>

    <!-- Second Column  -->
    <div class="col-md-<?php
    echo $item_page_columns[1] ?>" style="position:static">

        <?php
        //include related emails
        if (is_ext_installed()) {
            $mail_related = new mail_related($current_entity_id, 'right_column');
            echo $mail_related->render_list($current_item_id);
        }

        //include related records in box    
        echo $reladed_records->render_as_single_list(false);

        echo tree_table::render_nested_items($current_entity_id, $current_item_id, 'right_column');
        ?>

        <?php
        if ($entity_cfg->get('item_page_details_columns', '2') == 2 and strlen(
                $info_box = items::render_info_box($current_entity_id, $current_item_id)
            )): ?>
            <div class="panel panel-info item-details">
                <div class="panel-body item-details">
                    <?php
                    echo $info_box ?>
                </div>
            </div>
        <?php
        endif ?>

        <?php
        //includes subentity imtes listins if configure for item info page
        $subentities_items_position = 'right_column';
        require(component_path('items/load_subentities_items'));

        //includes field entity imtes listins if configure for item info page
        $field_entity_items_position = 'right_column';
        require(component_path('items/load_field_entity_items'));

        if (is_ext_installed()) {
            $item_pivot_tables = new item_pivot_tables($current_entity_id, 'right_column');
            echo $item_pivot_tables->render();
        }
        ?>

        <?php
        //include items comments if user have access and comments enabled     
        if (users::has_comments_access('view') and $entity_cfg->get('use_comments') == 1 and $entity_cfg->get(
                'item_page_comments_position',
                ''
            ) == 'right_column') {
            require(component_path('items/comments'));
        }
        ?>

    </div>
</div>

<script>
    $(function () {
        ckeditor_images_content_prepare();
    })
</script>

<!-- inluce js to load item listing -->
<?php
require(component_path('items/load_items_listing.js')); ?>

<?php
require(component_path('items/item_page_custom_code')); ?>

