<?php

class icalendar
{
    function __construct($type, $reports_id)
    {
        $this->type = $type;

        $this->reports_id = $reports_id;
    }

    function export()
    {
        switch ($this->type) {
            case 'report':
                $this->export_report();
                break;
            case 'pivot_report':
                $this->export_pivot_report();
                break;
            case 'personal':
                $this->export_personal();
                break;
            case 'public':
                $this->export_public();
                break;
        }
    }

    function set_calname($name, $icalobj)
    {
        $datanode = new ZCiCalDataNode("X-WR-CALNAME:" . $name);
        $icalobj->curnode->data[$datanode->getName()] = $datanode;

        if (isset($_GET['GMT']) and $_GET['GMT'] != 0) {
            $datanode = new ZCiCalDataNode("X-WR-TIMEZONE:Etc/GMT" . urlencode(substr($_GET['GMT'], 0, 3)));
            $icalobj->curnode->data[$datanode->getName()] = $datanode;
        }
    }

    function export_public()
    {
        global $app_user;

        if (!calendar::user_has_public_access() or !CFG_PUBLIC_CALENDAR_ICAL) {
            die(TEXT_NO_ACCESS);
        }

        // create the ical object
        $icalobj = new ZCiCal();

        $this->set_calname(CFG_APP_NAME, $icalobj);

        $events_query = db_query(
            "select * from app_ext_calendar_events where (event_type='public' or (event_type='personal' and is_public=1)) order by start_date"
        );
        while ($events = db_fetch_array($events_query)) {
            $event_start = date('Y-m-d H:i:s', $events['start_date']);
            $event_end = date('Y-m-d H:i:s', $events['end_date']);

            if (strstr($event_end, ' 00:00:00')) {
                $event_end = date('Y-m-d H:i:s', strtotime('+1 day', $events['end_date']));
            }

            // create the event within the ical object
            $eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);

            // add title
            $eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $events['name']));
            $eventobj->addNode(new ZCiCalDataNode("DESCRIPTION:" . $events['description']));
            $eventobj->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($event_start)));
            $eventobj->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($event_end)));

            //recurring events
            $this->recurrence_rule($eventobj, $events);

            // UID is a required item in VEVENT, create unique string for this event            
            $uid = "public-event-" . $events['id'];
            $eventobj->addNode(new ZCiCalDataNode("UID:" . $uid));

            // DTSTAMP is a required item in VEVENT
            $eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime(date('Y-m-d H:i:s'))));
        }

        // write iCalendar feed to stdout
        echo $icalobj->export();
    }

    function export_personal()
    {
        global $app_user;

        if (!calendar::user_has_personal_access() or !CFG_PERSONAL_CALENDAR_ICAL) {
            die(TEXT_NO_ACCESS);
        }

        // create the ical object
        $icalobj = new ZCiCal();

        $this->set_calname(TEXT_EXT_MY_СALENDAR, $icalobj);

        $events_query = db_query(
            "select * from app_ext_calendar_events where event_type='personal' and users_id='" . db_input(
                $app_user['id']
            ) . "' order by start_date"
        );
        while ($events = db_fetch_array($events_query)) {
            $event_start = date('Y-m-d H:i:s', $events['start_date']);
            $event_end = date('Y-m-d H:i:s', $events['end_date']);

            if (strstr($event_end, ' 00:00:00')) {
                $event_end = date('Y-m-d H:i:s', strtotime('+1 day', $events['end_date']));
            }

            // create the event within the ical object
            $eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);

            // add title
            $eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $events['name']));
            $eventobj->addNode(new ZCiCalDataNode("DESCRIPTION:" . $events['description']));
            $eventobj->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($event_start)));
            $eventobj->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($event_end)));

            //recurring events
            $this->recurrence_rule($eventobj, $events);

            // UID is a required item in VEVENT, create unique string for this event            
            $uid = "personal-event-" . $events['id'];
            $eventobj->addNode(new ZCiCalDataNode("UID:" . $uid));

            // DTSTAMP is a required item in VEVENT
            $eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime(date('Y-m-d H:i:s'))));
        }

        // write iCalendar feed to stdout
        echo $icalobj->export();
    }

    function recurrence_rule($eventobj, $events)
    {
        $rule = '';

        if (strlen($events['repeat_type'])) {
            $rule .= 'FREQ=' . strtoupper($events['repeat_type']) . ';';
            $rule .= 'INTERVAL=' . $events['repeat_interval'] . ';';

            if ($events['repeat_limit'] > 0) {
                $rule .= 'COUNT=' . ($events['repeat_limit'] + 1);
            } elseif ($events['repeat_end'] > 0) {
                $rule .= 'UNTIL=' . date('Ymd', $events['repeat_end']) . 'T000000Z';
            }

            $eventobj->addNode(new ZCiCalDataNode("RRULE:" . $rule));
        }

        return $rule;
    }

    function export_pivot_report()
    {
        global $app_fields_cache, $app_entities_cache, $sql_query_having;

        //check if report exist
        $reports_query = db_query(
            "select * from app_ext_pivot_calendars where id='" . db_input($this->reports_id) . "' and enable_ical=1"
        );
        if (!$reports = db_fetch_array($reports_query)) {
            die(TEXT_NO_RECORDS_FOUND);
        }

        if (!pivot_calendars::has_access($reports['users_groups'])) {
            die(TEXT_NO_ACCESS);
        }

        // create the ical object
        $icalobj = new ZCiCal();

        $this->set_calname($reports['name'], $icalobj);

        $reports_entities_query = db_query(
            "select ce.*, e.name from app_ext_pivot_calendars_entities ce, app_entities e where e.id=ce.entities_id and ce.calendars_id='" . $reports['id'] . "' order by e.name"
        );
        while ($reports_entities = db_fetch_array($reports_entities_query)) {
            if (!users::has_users_access_name_to_entity('view', $reports_entities['entities_id'])) {
                continue;
            }

            $listing_sql_query = '';
            $listing_sql_query_having = '';
            $sql_query_having = [];

            $is_start_date_dynamic = ($app_fields_cache[$reports_entities['entities_id']][$reports_entities['start_date']]['type'] == 'fieldtype_dynamic_date' ? true : false);
            $is_end_date_dynamic = ($app_fields_cache[$reports_entities['entities_id']][$reports_entities['end_date']]['type'] == 'fieldtype_dynamic_date' ? true : false);

            $listing_sql_query = reports::add_filters_query(
                pivot_calendars::get_reports_id_by_calendar_entity(
                    $reports_entities['id'],
                    $reports_entities['entities_id']
                ),
                $listing_sql_query
            );

            $listing_sql_query = items::add_access_query($reports_entities['entities_id'], $listing_sql_query);

            //prepare forumulas query
            $listing_sql_query_select = fieldtype_formula::prepare_query_select($reports_entities['entities_id'], '');

            //prepare dynamic dates
            if ($is_start_date_dynamic) {
                $sql_query_having[$reports_entities['entities_id']][] = "field_" . $reports_entities['start_date'] . ">0";
            } else {
                $listing_sql_query .= " and e.field_" . $reports_entities['start_date'] . ">0";
            }

            if ($is_end_date_dynamic) {
                $sql_query_having[$reports_entities['entities_id']][] = "field_" . $reports_entities['end_date'] . ">0";
            } else {
                $listing_sql_query .= " and e.field_" . $reports_entities['end_date'] . ">0";
            }


            //prepare having query for formula fields
            if (isset($sql_query_having[$reports_entities['entities_id']])) {
                $listing_sql_query_having = reports::prepare_filters_having_query(
                    $sql_query_having[$reports_entities['entities_id']]
                );
            }

            //add having query
            $listing_sql_query .= $listing_sql_query_having;

            $events_query = db_query(
                "select e.* " . $listing_sql_query_select . " from app_entity_" . $reports_entities['entities_id'] . " e where e.id>0 " . $listing_sql_query
            );
            while ($events = db_fetch_array($events_query)) {
                if (strlen($reports_entities['heading_template']) > 0) {
                    $options = [
                        'custom_pattern' => $reports_entities['heading_template'],
                        'item' => $events
                    ];

                    $options['field']['configuration'] = '';

                    $options['field']['entities_id'] = $reports_entities['entities_id'];

                    $fieldtype_text_pattern = new fieldtype_text_pattern();
                    $title = $fieldtype_text_pattern->output($options);
                } else {
                    $title = items::get_heading_field($reports_entities['entities_id'], $events['id']);
                }

                if ($app_entities_cache[$reports_entities['entities_id']]['parent_id'] > 0) {
                    $path_info = items::get_path_info($reports_entities['entities_id'], $events['id']);

                    $title = str_replace('<br>', ' / ', $path_info['parent_name']) . ' / ' . $title;
                }

                $event_start = date('Y-m-d H:i:s', $events['field_' . $reports_entities['start_date']]);
                $event_end = date('Y-m-d H:i:s', $events['field_' . $reports_entities['end_date']]);

                if (strstr($event_end, ' 00:00:00')) {
                    $event_end = date(
                        'Y-m-d H:i:s',
                        strtotime('+1 day', $events['field_' . $reports_entities['end_date']])
                    );
                }

                // create the event within the ical object
                $eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);

                // add title
                $eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $title));
                $eventobj->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($event_start)));
                $eventobj->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($event_end)));

                // UID is a required item in VEVENT, create unique string for this event            
                $uid = "event-" . $reports_entities['entities_id'] . '-' . $events['id'] . "-pivot-report-" . $reports['id'];
                $eventobj->addNode(new ZCiCalDataNode("UID:" . $uid));

                // DTSTAMP is a required item in VEVENT
                $eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime(date('Y-m-d H:i:s'))));
            }
        }

        // write iCalendar feed to stdout
        echo $icalobj->export();
    }

    function export_report()
    {
        global $app_fields_cache, $app_entities_cache, $sql_query_having;

        $reports_query = db_query(
            "select * from app_ext_calendar where id='" . db_input($this->reports_id) . "' and enable_ical=1"
        );
        if (!$reports = db_fetch_array($reports_query)) {
            die(TEXT_NO_RECORDS_FOUND);
        }

        if (!calendar::user_has_reports_access($reports)) {
            die(TEXT_NO_ACCESS);
        }

        $is_start_date_dynamic = ($app_fields_cache[$reports['entities_id']][$reports['start_date']]['type'] == 'fieldtype_dynamic_date' ? true : false);
        $is_end_date_dynamic = ($app_fields_cache[$reports['entities_id']][$reports['end_date']]['type'] == 'fieldtype_dynamic_date' ? true : false);

        $fiters_reports_id = default_filters::get_reports_id(
            $reports['entities_id'],
            'calendarreport' . $reports['id']
        );

        $listing_sql_query = '';
        $listing_sql_query_having = '';
        $sql_query_having = [];

        $listing_sql_query = reports::add_filters_query($fiters_reports_id, $listing_sql_query);

        if (isset($_GET['path'])) {
            $path_info = items::parse_path($_GET['path']);
            if ($path_info['parent_entity_item_id'] > 0) {
                $listing_sql_query .= " and e.parent_item_id='" . $path_info['parent_entity_item_id'] . "'";
            }
        }

        $listing_sql_query = items::add_access_query($reports['entities_id'], $listing_sql_query);

        //prepare forumulas query
        $listing_sql_query_select = fieldtype_formula::prepare_query_select($reports['entities_id'], '');

        //prepare dynamic dates
        if ($is_start_date_dynamic) {
            $sql_query_having[$reports['entities_id']][] = "field_" . $reports['start_date'] . ">0";
        } else {
            $listing_sql_query .= " and e.field_" . $reports['start_date'] . ">0";
        }

        if ($is_end_date_dynamic) {
            $sql_query_having[$reports['entities_id']][] = "field_" . $reports['end_date'] . ">0";
        } else {
            $listing_sql_query .= " and e.field_" . $reports['end_date'] . ">0";
        }

        //prepare having query for formula fields
        if (isset($sql_query_having[$reports['entities_id']])) {
            $listing_sql_query_having = reports::prepare_filters_having_query(
                $sql_query_having[$reports['entities_id']]
            );
        }

        //add having query
        $listing_sql_query .= $listing_sql_query_having;

        // create the ical object
        $icalobj = new ZCiCal();

        $this->set_calname($reports['name'], $icalobj);

        $events_query = db_query(
            "select e.* " . $listing_sql_query_select . " from app_entity_" . $reports['entities_id'] . " e where e.id>0 " . $listing_sql_query,
            false
        );
        while ($events = db_fetch_array($events_query)) {
            if (strlen($reports['heading_template']) > 0) {
                $options = [
                    'custom_pattern' => $reports['heading_template'],
                    'item' => $events
                ];

                $options['field']['configuration'] = '';

                $options['field']['entities_id'] = $reports['entities_id'];

                $fieldtype_text_pattern = new fieldtype_text_pattern();
                $title = $fieldtype_text_pattern->output($options);
            } else {
                $title = items::get_heading_field($reports['entities_id'], $events['id']);
            }

            if ($app_entities_cache[$reports['entities_id']]['parent_id'] > 0 and !isset($_GET['path'])) {
                $path_info = items::get_path_info($reports['entities_id'], $events['id']);

                $title = str_replace('<br>', ' / ', $path_info['parent_name']) . ' / ' . $title;
            }

            $event_start = date('Y-m-d H:i:s', $events['field_' . $reports['start_date']]);
            $event_end = date('Y-m-d H:i:s', $events['field_' . $reports['end_date']]);

            if (strstr($event_end, ' 00:00:00')) {
                $event_end = date('Y-m-d H:i:s', strtotime('+1 day', $events['field_' . $reports['end_date']]));
            }

            // create the event within the ical object
            $eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);

            // add title
            $eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $title));
            $eventobj->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($event_start)));
            $eventobj->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($event_end)));

            // UID is a required item in VEVENT, create unique string for this event            
            $uid = "event-" . $reports['entities_id'] . '-' . $events['id'] . "-report-" . $reports['id'];
            $eventobj->addNode(new ZCiCalDataNode("UID:" . $uid));

            // DTSTAMP is a required item in VEVENT
            $eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime(date('Y-m-d H:i:s'))));
        }

        // write iCalendar feed to stdout
        echo $icalobj->export();
    }

    static function get_url($is_enabled, $type, $reports_id = 0)
    {
        if ($is_enabled) {
            return '&nbsp;<a class="ical-export-url" href="javascript: open_dialog(\'' . url_for(
                    'ext/calendar/ical',
                    'type=' . $type . ($reports_id > 0 ? '&id=' . $reports_id : '') . (isset($_GET['path']) ? '&path=' . $_GET['path'] : '')
                ) . '\')" title="' . TEXT_EXT_SHARE_THIS_CALENDAR . '"><i class="fa fa-rss" aria-hidden="true"></i></a>';
        }
    }

    static function generate_url($type, $reports_id = 0)
    {
        global $app_user;

        $url = url_for(
            'feeders/ical',
            'GMT=0&type=' . $type . '&client=' . $app_user['client_id'] . ($reports_id > 0 ? '&id=' . $reports_id : '') . (isset($_GET['path']) ? '&path=' . $_GET['path'] : '')
        );

        return $url;
    }
}

