<?php

class timer
{

    public $users_id;

    public $users_gouprs_id;

    public $entities_id;

    public $itmes_id;

    function __construct($entities_id, $itmes_id)
    {
        global $app_user;

        $this->users_id = $app_user['id'];

        $this->users_gouprs_id = $app_user['group_id'];

        $this->entities_id = $entities_id;

        $this->itmes_id = $itmes_id;
    }

    function get_seconds()
    {
        $timer_query = db_query(
            "select * from app_ext_timer where entities_id='" . db_input(
                $this->entities_id
            ) . "' and items_id='" . db_input($this->itmes_id) . "' and users_id='" . db_input($this->users_id) . "'"
        );
        if ($timer = db_fetch_array($timer_query)) {
            return $timer['seconds'];
        } else {
            return 0;
        }
    }

    function has_timer()
    {
        $timer_query = db_query(
            "select * from app_ext_timer where entities_id='" . db_input(
                $this->entities_id
            ) . "' and items_id='" . db_input($this->itmes_id) . "' and users_id='" . db_input($this->users_id) . "'"
        );
        if ($timer = db_fetch_array($timer_query)) {
            return true;
        } else {
            return false;
        }
    }

    function has_user_access()
    {
        $cfg_query = db_query(
            "select * from app_ext_timer_configuration where entities_id='" . db_input(
                $this->entities_id
            ) . "' and find_in_set(" . $this->users_gouprs_id . ", users_groups)"
        );
        if ($cfg = db_fetch_array($cfg_query)) {
            return true;
        } else {
            return false;
        }
    }

    function render_button()
    {
        $html = '';

        if ($this->has_user_access()) {
            $html = '
        <li>
          <button class="btn btn-sm btn-default button-timer-open"><i class="fa fa-clock-o"></i> ' . TEXT_EXT_TIMER . '</button>
        </li>
      ';
        }

        return $html;
    }

    function render()
    {
        if (!$this->has_user_access()) {
            return '';
        }

        $html = '
      <div class="prolet-body-actions panel-timer ' . (!$this->has_timer(
            ) ? 'hidden' : '') . '" data-active-timer-msg="' . addslashes(TEXT_EXT_TIMER_IS_ON) . '">        
        <ul class="list-inline">          
          <li >
            <button class="btn btn-sm btn-primary button-timer-start hidden"><i class="fa fa-play"></i> ' . TEXT_EXT_TIMER_START . '</button>
            <button class="btn btn-sm btn-warning button-timer-pause hidden"><i class="fa fa-pause"></i> ' . TEXT_EXT_TIMER_PAUSE . '</button>
            <button class="btn btn-sm btn-info button-timer-continue"><i class="fa fa-play"></i> ' . TEXT_EXT_TIMER_CONTINUE . '</button>
          </li>
          <li>
            <span id="timer-container" data-seconds="' . $this->get_seconds() . '" data-action-url="' . url_for(
                'ext/timer/timer'
            ) . '" data-entities-id="' . $this->entities_id . '" data-items-id="' . $this->itmes_id . '">
              00:00:00
            </span>
          </li>
          <li>' . TEXT_EXT_SPENT_HOURS . ': </li>
          <li><input class="form-control input-xsmall" id="timer-hours-container" val="0" readonly></li>
          <li><button class="btn btn-sm btn-default button-timer-reset" data-warn-msg="' . addslashes(
                TEXT_EXT_TIMER_RESET_MSG
            ) . '" title="' . addslashes(TEXT_EXT_TIMER_RESET) . '"><i class="fa fa-refresh"></i></button></li>
          <li><button class="btn btn-sm btn-default button-timer-close" data-warn-msg="' . addslashes(
                TEXT_EXT_TIMER_COLOSE_MSG
            ) . '">' . TEXT_EXT_TIMER_COLOSE . ' <i class="fa fa-times"></i></button></li>
        </ul>
      </div>          
    ';

        return $html;
    }

    static function get_configuration($entities_id)
    {
        $value = -1;

        $cfg_query = db_query(
            "select * from app_ext_timer_configuration where entities_id='" . db_input($entities_id) . "'"
        );
        if ($cfg = db_fetch_array($cfg_query)) {
            if (strlen($cfg['users_groups']) > 0) {
                $value = explode(',', $cfg['users_groups']);
            }
        }

        return $value;
    }

    static function render_header_menu()
    {
        return '
        <li class="dropdown timer-report" id="timer_report" >
          ' . self::render_header_dropdown_menu() . '
        </li>';
    }

    static function render_header_dropdown_menu()
    {
        global $app_user;

        $count_timers_query = db_query(
            "select count(*) as total from app_ext_timer where  users_id='" . db_input($app_user['id']) . "'"
        );
        $count_timers = db_fetch_array($count_timers_query);

        $html = '';

        if ($count_timers['total'] > 0) {
            $items_html = '';

            $timers_query = db_query("select * from app_ext_timer where  users_id='" . db_input($app_user['id']) . "'");
            while ($timers = db_fetch_array($timers_query)) {
                $heading_field_id = fields::get_heading_id($timers['entities_id']);

                $item_info_query = db_query(
                    "select e.* from app_entity_" . $timers['entities_id'] . " e where id='" . $timers['items_id'] . "'"
                );
                $item_info = db_fetch_array($item_info_query);

                $name = ($heading_field_id > 0 ? items::get_heading_field_value(
                    $heading_field_id,
                    $item_info
                ) : $item_info['id']);

                $path_info = items::get_path_info($timers['entities_id'], $timers['items_id']);

                $parent_name = '';

                if (strlen($path_info['parent_name']) > 0) {
                    $parent_name_array = explode('<br>', $path_info['parent_name']);
                    krsort($parent_name_array);
                    $parent_name = '<span class="parent-name"><i class="fa fa-angle-left"></i>' . implode(
                            '<i class="fa fa-angle-left"></i>',
                            $parent_name_array
                        ) . '</span>';
                }

                $items_html .= '
          <li>
  					<a href="' . url_for(
                        'items/info',
                        'path=' . $path_info['full_path']
                    ) . '">' . $name . $parent_name . '</a>
  				</li>
        ';
            }

            $dropdown_menu_height = ($count_timers['total'] < 11 ? ($count_timers['total'] * 42 + 42) : 420);

            $html = '
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
  				  <i class="fa fa-clock-o"></i>
  				  <span class="badge badge-info">' . $count_timers['total'] . '</span>
  				</a>
  				<ul class="dropdown-menu extended tasks">
  					<li style="cursor:pointer">
  						<p>' . TEXT_EXT_MY_OPEN_TIMERS . '</p>
  					</li>
  					<li>
  						<ul class="dropdown-menu-list scroller" style="height: ' . $dropdown_menu_height . 'px;">
  							' . $items_html . '                  
  						</ul>
  					</li>
            
  				</ul>            
        ';
        }

        return $html;
    }
}