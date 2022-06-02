<?php

class rss_feed
{
    function __construct($feed)
    {
        $this->feed = $feed;

        $this->entities_id = $feed['entities_id'];
        $this->id = $feed['id'];
    }

    function render()
    {
        switch ($this->feed['type']) {
            case 'entity_calendar':
            case 'entity':
                return $this->render_entity();
                break;
            case 'public_calendar':
            case 'personal_calendar':
                return $this->render_calendar();
                break;
            case 'chat_messages':
                return $this->render_chat();
                break;
        }
    }

    function render_chat()
    {
        global $app_user;

        $rss = '';

        $messages_query = db_query(
            "select * from app_ext_chat_messages where id in (select messages_id from app_ext_chat_unread_messages where conversations_id=0 and assigned_to ={$app_user['id']}) order by id desc limit 100"
        );
        while ($messages = db_fetch_array($messages_query)) {
            $rss .= '
                <item>
                    <title>' . xml_str(users::get_name_by_id($messages['users_id']) . ': ' . $messages['message']) . '</title>
                    <link>' . xml_str(url_for('ext/app_chat/chat_window')) . '</link>                    
                    <pubDate>' . date('r', $messages['date_added']) . '</pubDate>
                    <guid>chat_msg_' . $messages['id'] . '</guid>
                </item>
                ';
        }

        $messages_query = db_query(
            "select * from app_ext_chat_conversations_messages where id in (select messages_id  from app_ext_chat_unread_messages where conversations_id>0 and assigned_to = {$app_user['id']}) order by id desc limit 100"
        );
        while ($messages = db_fetch_array($messages_query)) {
            $rss .= '
                <item>
                    <title>' . xml_str(users::get_name_by_id($messages['users_id']) . ': ' . $messages['message']) . '</title>
                    <link>' . xml_str(url_for('ext/app_chat/chat_window')) . '</link>                    
                    <pubDate>' . date('r', $messages['date_added']) . '</pubDate>
                    <guid>conv_msg_' . $messages['id'] . '</guid>
                </item>
                ';
        }

        return $rss;
    }

    function render_calendar()
    {
        $rss = '';

        $calendar_type = ($this->feed['type'] == 'personal_calendar' ? 'personal' : 'public');

        foreach (calendar::get_events(date('Y-m-d'), date('Y-m-d'), $calendar_type) as $events) {
            $title = format_date($events['start_date']) . ': ' . $events['name'];
            $url = url_for('ext/calendar/' . $calendar_type);

            $rss .= '
                <item>
                    <title>' . xml_str($title) . '</title>
                    <link>' . xml_str($url) . '</link>                    
                    <pubDate>' . date('r', $events['start_date']) . '</pubDate>
                    <guid>' . $calendar_type . '_calendar_event_' . $events['id'] . '</guid>
                </item>
                ';
        }

        return $rss;
    }

    function render_entity()
    {
        global $sql_query_having, $app_user;

        $rss = '';

        $fields_access_schema = users::get_fields_access_schema($this->entities_id, $app_user['group_id']);

        $listing_sql_query = '';
        $listing_sql_query_having = '';
        $sql_query_having = [];

        $resources = [];

        $listing_sql_query = reports::add_filters_query(
            default_filters::get_reports_id($this->entities_id, 'rss_feed' . $this->id),
            $listing_sql_query
        );

        $listing_sql_query = items::add_access_query($this->entities_id, $listing_sql_query);

        //prepare having query for formula fields
        if (isset($sql_query_having[$this->entities_id])) {
            $listing_sql_query_having = reports::prepare_filters_having_query($sql_query_having[$this->entities_id]);
        }

        if ($this->feed['type'] == 'entity_calendar') {
            $listing_sql_query .= " and (FROM_UNIXTIME(field_{$this->feed['start_date']},'%Y-%m-%d')<='" . date(
                    'Y-m-d'
                ) . "'  and FROM_UNIXTIME(field_{$this->feed['end_date']},'%Y-%m-%d')>='" . date('Y-m-d') . "')";
        }

        //add having query
        $listing_sql_query .= $listing_sql_query_having;

        $listing_sql_query .= " order by e.id desc";

        $items_query = db_query(
            "select e.* from app_entity_" . $this->entities_id . " e where e.id>0 " . $listing_sql_query,
            false
        );
        while ($items = db_fetch_array($items_query)) {
            if (strlen($this->feed['heading_template']) > 0) {
                $options = [
                    'custom_pattern' => $this->feed['heading_template'],
                    'item' => $items
                ];

                $options['field']['configuration'] = '';
                $options['field']['entities_id'] = $this->feed['entities_id'];

                $fieldtype_text_pattern = new fieldtype_text_pattern();
                $title = $fieldtype_text_pattern->output($options);
            } else {
                $title = items::get_heading_field($this->feed['entities_id'], $items['id']);
            }

            $url = xml_str(url_for('items/info', 'path=' . $this->entities_id . '-' . $items['id']));
            $rss .= '
                <item>
                    <title>' . xml_str($title) . '</title>
                    <link>' . $url . '</link>                    
                    <pubDate>' . date('r', $items['date_added']) . '</pubDate>
                    <guid>' . $url . '</guid>
                </item>
                ';
        }

        return $rss;
    }


    static function generate_rss_id($id)
    {
        $sql_data = [
            'rss_id' => mt_rand(10000, 99999) . $id
        ];
        db_perform('app_ext_rss_feeds', $sql_data, 'update', "id='" . db_input($id) . "'");
    }

    static function get_type_choices()
    {
        $choices = [
            'entity' => TEXT_ENTITY,
            'entity_calendar' => TEXT_EXT_CALENDAR_REPORT,
            'public_calendar' => TEXT_EXT_СALENDAR_PUBLIC,
            'personal_calendar' => TEXT_EXT_СALENDAR_PERSONAL,
            'chat_messages' => TEXT_EXT_MENU_CHAT
        ];

        return $choices;
    }

    static function get_type_title_by_key($key)
    {
        $choices = self::get_type_choices();

        return $choices[$key] ?? '';
    }

    static function has_user_feeds()
    {
        global $app_user;

        $check_query = db_query(
            "select id from app_ext_rss_feeds where find_in_set(" . $app_user['group_id'] . ",users_groups) or find_in_set(" . $app_user['id'] . ",assigned_to) limit 1"
        );
        if ($check = db_fetch_array($check_query)) {
            return true;
        } else {
            return false;
        }
    }

}

