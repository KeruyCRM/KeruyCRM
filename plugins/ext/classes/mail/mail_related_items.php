<?php

class mail_related_items
{

    public $mail_groups_id, $mail_accounts_id, $has_related_items;

    function __construct($mail_accounts_id, $mail_groups_id)
    {
        $this->mail_groups_id = $mail_groups_id;
        $this->mail_accounts_id = $mail_accounts_id;


        $this->has_related_items = false;

        $account_entities_query = db_query(
            "select * from app_ext_mail_accounts_entities where accounts_id='" . $this->mail_accounts_id . "'"
        );
        if ($account_entities = db_fetch_array($account_entities_query)) {
            $this->has_related_items = true;
        }
    }

    function has_related_items()
    {
        return $this->has_related_items;
    }

    function related_items_listing()
    {
        global $app_entities_cache;

        $html = '';
        $account_entities_query = db_query(
            "select * from app_ext_mail_accounts_entities where accounts_id='" . $this->mail_accounts_id . "' order by sort_order, id"
        );
        while ($account_entities = db_fetch_array($account_entities_query)) {
            if (!users::has_users_access_to_entity($account_entities['entities_id'])) {
                continue;
            }

            $html .= '
				<div class="portlet portlet-item-description">
				  <div class="portlet-title">
					 <div class="caption">        
				     ' . (strlen(
                    $account_entities['title']
                ) ? $account_entities['title'] : $app_entities_cache[$account_entities['entities_id']]['name']) . '                  
				   </div>
				   <div class="tools">
				        
						</div>
					</div>
					<div class="portlet-body">
				     ' . $this->render_single_list($account_entities) . '		
					</div>
				</div>
			';
        }

        return $html;
    }

    function count_related_items($entities_id)
    {
        $items_query = db_query(
            "select count(*) as total from app_ext_mail_to_items where mail_groups_id='" . $this->mail_groups_id . "' and entities_id='" . $entities_id . "'"
        );
        $items = db_fetch_array($items_query);

        return (int)$items['total'];
    }

    function render_single_list($account_entities)
    {
        global $current_path, $app_path, $app_user;

        $entities_id = $account_entities['entities_id'];

        $entities_access_schema = users::get_entities_access_schema($entities_id, $app_user['group_id']);

        $current_field_access = users::get_fields_access_schema($entities_id, $app_user['group_id']);

        $count_related_items = $this->count_related_items($entities_id);

        $reports_info = self::get_report_info($entities_id);

        $listing_container = 'entity_items_listing' . $reports_info['id'] . '_' . $reports_info['entities_id'];

        $entity_cfg = new entities_cfg($reports_info['entities_id']);

        $with_selected_menu = '';

        if (users::has_access('export_selected', $entities_access_schema) and users::has_access(
                'export',
                $entities_access_schema
            )) {
            $with_selected_menu .= '<li>' . link_to_modalbox(
                    '<i class="fa fa-file-excel-o"></i> ' . TEXT_EXPORT,
                    url_for(
                        'items/export',
                        'path=' . $reports_info["entities_id"] . '&reports_id=' . $reports_info['id']
                    )
                ) . '</li>';
        }

        $with_selected_menu .= plugins::include_dashboard_with_selected_menu_items(
            $reports_info['id'],
            '&path=' . $reports_info['entities_id'] . '&redirect_to=mail_info_page_' . $this->mail_groups_id
        );


        $html_btn = '';

        if (users::has_access('update', $entities_access_schema) and $current_field_access != 'view') {
            //add button
            if (users::has_access('create', $entities_access_schema) and $this->field_has_button(
                    'add',
                    $account_entities
                )) {
                $html_btn .= button_tag(
                        (strlen($entity_cfg->get('insert_button')) > 0 ? $entity_cfg->get('insert_button') : TEXT_ADD),
                        $this->get_add_url($entities_id),
                        true,
                        ['class' => 'btn btn-primary btn-sm']
                    ) . ' ';
            }

            if ($this->field_has_button('bind', $account_entities)) {
                //link button
                $html_btn .= button_tag(
                        '<i class="fa fa-link"></i>',
                        url_for(
                            'ext/mail/link_related_item',
                            'entities_id=' . $entities_id . '&mail_groups_id=' . $this->mail_groups_id
                        ),
                        true,
                        ['class' => 'btn btn-primary btn-sm', 'title' => TEXT_BUTTON_LINK]
                    ) . ' ';

                //unlink button
                if ($count_related_items > 0) {
                    $html_btn .= button_tag(
                        '<i class="fa fa-unlink"></i>',
                        url_for(
                            'ext/mail/unlink_related_item',
                            'entities_id=' . $entities_id . '&mail_groups_id=' . $this->mail_groups_id
                        ),
                        true,
                        ['class' => 'btn btn-primary btn-sm', 'title' => TEXT_UNLINK]
                    );
                }
            }

            //reset with selected menu if button is hidden
            if (!$this->field_has_button('with_selected', $account_entities)) {
                $with_selected_menu = '';
            }

            //with selected
            if (strlen($with_selected_menu) and $count_related_items > 0) {
                $html_btn .= '
	            <div class="btn-group">
	      				<button class="btn btn-default dropdown-toggle btn-sm" type="button" data-toggle="dropdown" data-hover="dropdown">
	      				' . TEXT_WITH_SELECTED . '<i class="fa fa-angle-down"></i>
	      				</button>
	      				<ul class="dropdown-menu" role="menu">
	      					' . $with_selected_menu . '
	      				</ul>
	      			</div>';
            }
        }


        $html = $html_btn . '
	
        <div id="' . $listing_container . '" class="entity_items_listing"></div>
        ' . input_hidden_tag($listing_container . '_order_fields', '') .
            input_hidden_tag($listing_container . '_has_with_selected', (strlen($with_selected_menu) ? 1 : 0)) .
            input_hidden_tag(
                $listing_container . '_force_display_id',
                implode(',', [0] + $this->get_related_items($entities_id, $account_entities['bind_to_sender']))
            ) .
            input_hidden_tag($listing_container . '_redirect_to', 'mail_info_page_' . $this->mail_groups_id) .
            input_hidden_tag($listing_container . '_force_popoup_fields', $account_entities['fields_in_popup']) . '
	    	
    <script>
		  $(function() {
		    load_items_listing("' . $listing_container . '",1);
		  });
	  </script>
    ';

        return $html;
    }

    function get_related_items($entities_id, $bind_to_sender)
    {
        if ($bind_to_sender) {
            $mail_query = db_query(
                "select id, from_email,to_email, is_sent from app_ext_mail where groups_id='" . $this->mail_groups_id . "' order by id limit 1"
            );
            $mail = db_fetch_array($mail_query);

            $from_email = '';

            if ($mail['is_sent'] == 1) {
                if (!strstr($mail['to_email'], ',')) {
                    $from_email = $mail['to_email'];
                }
            } else {
                $from_email = $mail['from_email'];
            }

            if (strlen($from_email) > 0) {
                $where_sql = "from_email='" . $from_email . "'";
            } else {
                return [];
            }
        } else {
            $where_sql = "mail_groups_id='" . $this->mail_groups_id . "'";
        }

        $items_list = [];
        $items_query = db_query(
            "select items_id from app_ext_mail_to_items where {$where_sql} and entities_id='" . $entities_id . "'"
        );
        while ($items = db_fetch_array($items_query)) {
            $items_list[$items['items_id']] = $items['items_id'];
        }

        //print_r($items_list);

        return $items_list;
    }

    function field_has_button($type, $account_entities)
    {
        if (!in_array($type, explode(',', $account_entities['hide_buttons']))) {
            return true;
        } else {
            return false;
        }
    }

    function get_add_url($entity_id)
    {
        global $current_path_array;

        $entity_info = db_find('app_entities', $entity_id);
        $current_entity_info = db_find('app_entities', $entity_id);

        //if parent items are different
        if ($entity_info['parent_id'] > 0) {
            $url_params = '';

            $account_entities_query = db_query(
                "select * from app_ext_mail_accounts_entities where entities_id='" . $entity_info['id'] . "' and parent_item_id>0"
            );
            if ($account_entities = db_fetch_array($account_entities_query)) {
                $url_params = '&parent_item_id=' . $entity_info['parent_id'] . '-' . $account_entities['parent_item_id'];
            }

            $account_entities_query = db_query(
                "select * from app_ext_mail_accounts_entities where entities_id='" . $entity_info['parent_id'] . "'"
            );
            if ($account_entities = db_fetch_array($account_entities_query)) {
                //print_r($account_entities);
                $related_items = $this->get_related_items(
                    $account_entities['entities_id'],
                    $account_entities['bind_to_sender']
                );

                if (count($related_items)) {
                    $url_params = '&parent_item_id=' . $account_entities['entities_id'] . '-' . current($related_items);
                }
            }

            $add_url = url_for(
                'reports/prepare_add_item',
                'mail_groups_id=' . $this->mail_groups_id . '&reports_id=' . reports::get_default_entity_report_id(
                    $entity_id,
                    'entity_menu'
                ) . $url_params
            );
        } else {
            $add_url = url_for('items/form', 'path=' . $entity_id . '&mail_groups_id=' . $this->mail_groups_id);
        }

        return $add_url;
    }

    static function get_report_info($entity_id)
    {
        global $app_heading_fields_id_cache;

        $reports_type = 'mail_related_items_' . $entity_id;

        $reports_info_query = db_query(
            "select * from app_reports where entities_id='" . db_input(
                $entity_id
            ) . "' and reports_type='" . $reports_type . "'"
        );
        if (!$reports_info = db_fetch_array($reports_info_query)) {
            $sql_data = [
                'name' => '',
                'entities_id' => $entity_id,
                'reports_type' => $reports_type,
                'in_menu' => 0,
                'in_dashboard' => 0,
                'fields_in_listing' => '',
                'created_by' => 0,
                'parent_entity_id' => 0,
                'parent_item_id' => 0,
            ];

            db_perform('app_reports', $sql_data);

            $reports_id = db_insert_id();

            reports::auto_create_parent_reports($reports_id);

            $reports_info = db_find('app_reports', $reports_id);
        }

        return $reports_info;
    }

}
