<?php

class track_changes
{
    public $entities_id;

    public $items_id;

    public $reports_id;

    public $track_fields;

    function __construct($entities_id, $items_id)
    {
        $this->entities_id = $entities_id;

        $this->items_id = $items_id;

        $this->reports_id = false;

        $this->track_fields = false;

        $check_query = db_query(
            "select * from app_ext_track_changes_entities tce, app_ext_track_changes tc where tc.id=tce.reports_id and tc.is_active=1 and tce.entities_id='" . $this->entities_id . "'"
        );
        if ($check = db_fetch_array($check_query)) {
            $this->reports_id = $check['reports_id'];

            if (strlen($check['track_fields'])) {
                $this->track_fields = $check['track_fields'];
            }
        }
    }

    function log_prepare($is_exist_item, $previous_item_info)
    {
        if ($is_exist_item) {
            $this->log_update($previous_item_info);
        } else {
            $this->log_insert();
        }
    }

    function log_insert()
    {
        if ($this->reports_id) {
            $this->log(['type' => 'insert']);
        }
    }

    function log_update($previous_item_info)
    {
        if (!$this->reports_id) {
            return false;
        }

        $fields_list = [];

        //get current imem info
        $item_query = db_query(
            "select e.* from app_entity_" . $this->entities_id . " e where id='" . $this->items_id . "'"
        );
        $item = db_fetch_array($item_query);

        //compare fields with previous item value
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_type_list_excluded_in_form(
            ) . ",'fieldtype_section') and f.entities_id='" . $this->entities_id . "' and f.forms_tabs_id=t.id " . ($this->track_fields ? "and f.id in (" . $this->track_fields . ")" : "") . " order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            if ($item['field_' . $fields['id']] != $previous_item_info['field_' . $fields['id']]) {
                $fields_list[$fields['id']] = $item['field_' . $fields['id']];
            }
        }

        //insert log if fields changed
        if (count($fields_list)) {
            $this->log(['type' => 'update', 'fields' => $fields_list]);
        }
    }

    function log_move($parent_item_id)
    {
        if (!$this->reports_id) {
            return false;
        }

        $field_query = db_query(
            "select id from app_fields where type='fieldtype_parent_item_id' and entities_id='" . $this->entities_id . "'"
        );
        $field = db_fetch_array($field_query);

        $fields_list = [];
        $fields_list[$field['id']] = $parent_item_id;

        $this->log(['type' => 'update', 'fields' => $fields_list]);
    }

    function log_comment($comments_id, $fields)
    {
        $fields_list = [];
        foreach ($fields as $id => $value) {
            //check if field changed
            if (is_array($value)) {
                $fields_list[$id] = implode(',', $value);
            } elseif (strlen($value)) {
                $fields_list[$id] = $value;
            }
        }

        $this->log(['type' => 'comment', 'comments_id' => $comments_id, 'fields' => $fields_list]);
    }

    function log_delete()
    {
        $items_name = items::get_heading_field($this->entities_id, $this->items_id);
        $this->log(['type' => 'delete', 'items_name' => $items_name]);
    }

    function log($params = [])
    {
        global $app_user;

        if (!$this->reports_id) {
            return false;
        }

        $sql_data = [
            'reports_id' => $this->reports_id,
            'type' => $params['type'],
            'entities_id' => $this->entities_id,
            'items_id' => $this->items_id,
            'comments_id' => (isset($params['comments_id']) ? $params['comments_id'] : 0),
            'items_name' => (isset($params['items_name']) ? $params['items_name'] : ''),
            'date_added' => time(),
            'created_by' => $app_user['id'],
            'is_cron' => defined('IS_CRON'),
        ];

        db_perform('app_ext_track_changes_log', $sql_data);
        $log_id = db_insert_id();

        if (isset($params['fields'])) {
            $sql_data = [];
            foreach ($params['fields'] as $fields_id => $value) {
                $sql_data[] = [
                    'log_id' => $log_id,
                    'fields_id' => $fields_id,
                    'value' => $value,
                ];
            }

            if (count($sql_data)) {
                db_batch_insert('app_ext_track_changes_log_fields', $sql_data);
            }
        }
    }

    static function exclude_hidden_entities_query()
    {
        global $app_user;

        if ($app_user['group_id'] > 0) {
            $entities_list = [0];
            $acess_info_query = db_query(
                "select * from app_entities_access where access_groups_id='" . db_input(
                    $app_user['group_id']
                ) . "' and length(access_schema)>0 and not find_in_set('view_assigned',access_schema)"
            );
            while ($acess_info = db_fetch_array($acess_info_query)) {
                $entities_list[] = $acess_info['entities_id'];
            }

            return " and tcl.entities_id in (" . implode(',', $entities_list) . ")";
        }

        return '';
    }

    static function render_header_menu()
    {
        global $app_user, $app_users_cache;

        $html = '';

        $poup_items_limit = 10;

        $reports_query = db_query(
            "select * from app_ext_track_changes where is_active=1 and find_in_set('in_header_menu',position) and (find_in_set('" . $app_user['group_id'] . "',users_groups)  or find_in_set('" . $app_user['id'] . "',assigned_to))"
        );
        while ($reports = db_fetch_array($reports_query)) {
            $count_items = 0;
            $count_display_items = 0;

            $items_holder = [];

            $items_list = '';

            $listing_sql = "select tcl.*,tc.color_delete, tc.color_insert, tc.color_update, tc.color_comment, e.name as entity_name, c.description as comment from app_ext_track_changes_log tcl left join app_entities e on e.id=tcl.entities_id left join app_comments c on c.id=tcl.comments_id, app_ext_track_changes tc where tcl.reports_id='" . $reports['id'] . "' and tc.id=tcl.reports_id and FROM_UNIXTIME(tcl.date_added,'%Y-%m-%d')=date_format(now(),'%Y-%m-%d') " . self::exclude_hidden_entities_query(
                ) . " order by tcl.id desc";
            $items_query = db_query($listing_sql);
            while ($item = db_fetch_array($items_query)) {
                if ($count_items < $poup_items_limit) {
                    if (!isset($items_holder[$item['entities_id']][$item['items_id']])) {
                        if ($item['type'] == 'delete') {
                            $items_holder[$item['entities_id']][$item['items_id']] = [
                                'path' => '',
                                'name' => $item['items_name'],
                            ];
                        } else {
                            $items_holder[$item['entities_id']][$item['items_id']] = [
                                'path' => items::get_path_info($item['entities_id'], $item['items_id']),
                                'name' => items::get_heading_field($item['entities_id'], $item['items_id']),
                            ];
                        }
                    }

                    $item_info = $items_holder[$item['entities_id']][$item['items_id']];

                    $items_list .= '
	                <li>
	  					 <a href="' . (isset($item_info['path']['full_path']) ? url_for(
                            'items/info',
                            'path=' . $item_info['path']['full_path']
                        ) : '#') . '">' . $item_info['name'] . ' ' . track_changes::get_item_label_by_type($item) . '
	  					 <span class="parent-name"><i class="fa fa-angle-left"></i>' . self::get_created_by_label(
                            $item
                        ) . '</span>		
	  					 </a>
	  				</li>
	        ';
                    $count_display_items++;
                }

                $count_items++;
            }

            if (!strlen($items_list)) {
                $items_list .= '
          <li>
  					<a onClick="return false;">' . TEXT_NO_RECORDS_FOUND . '</a>
  				</li>
        ';
            }

            if ($count_items > 0) {
                $items_list .= '
          <li class="external">
						<a href="' . url_for('ext/track_changes/view', 'reports_id=' . $reports['id']) . '">' . sprintf(
                        TEXT_DISPLAY_NUMBER_OF_ITEMS_OPEN_REPORT,
                        $count_display_items
                    ) . '</a>
					</li>
        ';
            }

            $dropdown_menu_height = ($count_display_items < 11 ? ($count_display_items * 42 + 42) : 420);

            $html .= '
				<li class="dropdown hot-reports" id="track_changes_' . $reports['id'] . '" >	
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
  				  <i class="fa ' . $reports['menu_icon'] . '"></i>
  				  <span class="badge badge-info">' . $count_items . '</span>
  				</a>
  				<ul class="dropdown-menu extended tasks">
  					<li style="cursor:pointer" onClick="location.href=\'' . url_for(
                    'ext/track_changes/view',
                    'reports_id=' . $reports['id']
                ) . '\'">
  						<p>' . $reports['name'] . ' (' . format_date(time()) . ')</p>
  					</li>
  					<li>
  						<ul class="dropdown-menu-list scroller" style="height: ' . $dropdown_menu_height . 'px;">
  						' . $items_list . '	
  						</ul>
  					</li>
			
  				</ul>
  			</li>					
        ';
        }

        return $html;
    }

    static function delete_log($entities_id, $items_id)
    {
        db_query(
            "delete from app_ext_track_changes_log_fields where  log_id in (select id  from app_ext_track_changes_log where entities_id='" . $entities_id . "' and items_id='" . $items_id . "')"
        );
        db_query(
            "delete from app_ext_track_changes_log where type!='delete' and entities_id='" . $entities_id . "' and items_id='" . $items_id . "'"
        );

        //if delete user then delete all logs created by this user
        if ($entities_id == 1) {
            db_query(
                "delete from app_ext_track_changes_log_fields where log_id in (select id  from app_ext_track_changes_log where created_by='" . $items_id . "')"
            );
            db_query("delete from app_ext_track_changes_log where created_by='" . $items_id . "'");
        }
    }

    static function reset($reports)
    {
        if ($reports['keep_history'] > 0) {
            $where_sql = "FROM_UNIXTIME(date_added,'%Y-%m-%d')<date_format(DATE_SUB(now(),INTERVAL " . (int)$reports['keep_history'] . " DAY),'%Y-%m-%d')";

            db_query(
                "delete from app_ext_track_changes_log_fields where log_id in (select id from app_ext_track_changes_log where {$where_sql})"
            );
            db_query("delete from app_ext_track_changes_log where " . $where_sql);
        }
    }

    static function get_item_label_by_type($item)
    {
        $type_label = '';

        switch ($item['type']) {
            case 'insert':
                $type_label = '<span class="label" style="background-color: ' . $item['color_insert'] . ';">' . TEXT_EXT_NEW_RECORD . '</span>';
                break;
            case 'update':
                $type_label = '<span class="label" style="background-color: ' . $item['color_update'] . ';">' . TEXT_EXT_CHANGED . '</span>';
                break;
            case 'comment':
                $type_label = '<span class="label" style="background-color: ' . $item['color_comment'] . ';">' . TEXT_EXT_NEW_COMMENT . '</span>';
                break;
            case 'delete':
                $type_label = '<span class="label" style="background-color: ' . $item['color_delete'] . ';">' . TEXT_EXT_DELETED . '</span>';
                break;
        }

        return $type_label;
    }

    static function get_type_chocies()
    {
        $choices = [
            '' => '',
            'insert' => TEXT_EXT_NEW_RECORD,
            'update' => TEXT_EXT_CHANGED,
            'comment' => TEXT_EXT_NEW_COMMENT,
            'delete' => TEXT_EXT_DELETED,
        ];

        return $choices;
    }

    static function get_created_by_label($item)
    {
        global $app_users_cache;

        if ($item['is_cron'] == 1) {
            return '<span class="label label-default">' . TEXT_EXT_AUTOMATICALLY . '</span>';
        } elseif ($item['created_by'] == 0) {
            return '<span class="label label-default">' . TEXT_EXT_PUBLIC_FORM . '</span>';
        } elseif (isset($app_users_cache[$item['created_by']])) {
            return $app_users_cache[$item['created_by']]['name'];
        } else {
            return '';
        }
    }
}