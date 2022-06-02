<?php

class calendar_notification
{
    public $personal_calendar_send_alerts;

    function __construct()
    {
        $this->personal_calendar_send_alerts = (!defined(
            'CFG_PERSONAL_CALENDAR_SEND_ALERTS'
        ) ? 0 : CFG_PERSONAL_CALENDAR_SEND_ALERTS);
        $this->public_calendar_send_alerts = (!defined(
            'CFG_PUBLIC_CALENDAR_SEND_ALERTS'
        ) ? 0 : CFG_PUBLIC_CALENDAR_SEND_ALERTS);
    }

    function send()
    {
        if ($this->personal_calendar_send_alerts == 1) {
            $this->send_personal();
        }

        if ($this->public_calendar_send_alerts == 1) {
            $this->send_public();
        }
    }

    function send_personal()
    {
        global $app_user;

        //check notification time
        if (CFG_PERSONAL_CALENDAR_ALERTS_TIME != date('H')) {
            return false;
        }

        $users_query = db_query(
            "select e.* from app_entity_1 e where e.field_5=1 and ((select count(*) as total from app_ext_calendar_access c where c.calendar_type='personal' and c.access_groups_id=e.field_6)>0 or e.field_6=0)"
        );
        while ($user = db_fetch_array($users_query)) {
            $app_user = [
                'id' => $user['id'],
                'group_id' => (int)$user['field_6'],
                'name' => users::output_heading_from_item($user),
                'email' => $user['field_9'],
                'language' => $user['field_13'],
            ];

            $events = calendar::get_events(date('Y-m-d'), date('Y-m-d'), 'personal');

            if (($events_count = count($events)) > 0) {
                $html = TEXT_EXT_ALERTS_EMAIL_TEXT . $this->render_events_html($events);

                $reports_url = CRON_HTTP_SERVER_HOST . 'index.php?module=ext/calendar/personal';

                $html .= '<p><a href="' . $reports_url . '">' . TEXT_EXT_OPEN_MY_CALENDAR . '</a></p>';

                $subject = (strlen(
                    CFG_PERSONAL_CALENDAR_ALERTS_SUBJECT
                ) ? CFG_PERSONAL_CALENDAR_ALERTS_SUBJECT : TEXT_EXT_PERSONAL_CALENDAR_ALERTS_SUBJECT);

                $options = [
                    'to' => $app_user['email'],
                    'to_name' => $app_user['name'],
                    'subject' => sprintf($subject, $events_count),
                    'body' => $html,
                    'from' => CFG_EMAIL_ADDRESS_FROM,
                    'from_name' => CFG_EMAIL_NAME_FROM
                ];

                //echo '<pre>';
                //print_r($options);
                //echo '<br><br>' . $html;


                users::send_email($options);
            }
        }
    }

    function render_events_html($events)
    {
        $html = '<ul>';

        $ongoing_html = '';

        foreach ($events as $event) {
            //print_r($event);

            //prepare name
            $name_html = '<span style="color: ' . $event['bg_color'] . '">' . $event['name'] . '</span>';

            //prepare description
            $description_html = '';
            if (strlen($event['description'])) {
                $description_html = '<br><i>' . $event['description'] . '</i>';
            }

            //prepare event time
            $when_html = '';
            if ($event['start_date'] != $event['end_date'] and date('Y-m-d', $event['start_date']) == date(
                    'Y-m-d',
                    $event['end_date']
                )) {
                $when_html = ' (' . sprintf(
                        TEXT_FROM_TO,
                        date('H:i', $event['start_date']),
                        date('H:i', $event['end_date'])
                    ) . ')';
            }

            //prepare ongoing events
            if ($event['start_date'] != $event['end_date'] and date('Y-m-d', $event['start_date']) != date(
                    'Y-m-d',
                    $event['end_date']
                )) {
                $when_html = ' (' . sprintf(
                        TEXT_FROM_TO,
                        format_date($event['start_date']),
                        format_date($event['end_date'])
                    ) . ')';

                $ongoing_html .= '<li style="padding-bottom: 7px;">' . TEXT_EXT_ONGOING_EVENT . ': ' . $name_html . $when_html . $description_html . '</li>';
            } else {
                //pepare default evenets
                $html .= '<li style="padding-bottom: 7px;">' . $name_html . $when_html . $description_html . '</li>';
            }
        }

        //joing ongoin events at the end of list
        $html .= $ongoing_html;

        $html .= '</ul>';

        return $html;
    }

    function send_public()
    {
        global $app_user;

        //check notification time
        if (CFG_PUBLIC_CALENDAR_ALERTS_TIME != date('H')) {
            return false;
        }

        $users_query = db_query(
            "select e.* from app_entity_1 e where e.field_5=1 and ((select count(*) as total from app_ext_calendar_access c where c.calendar_type='public' and c.access_groups_id=e.field_6)>0 or e.field_6=0)"
        );
        while ($user = db_fetch_array($users_query)) {
            $app_user = [
                'id' => $user['id'],
                'group_id' => (int)$user['field_6'],
                'name' => users::output_heading_from_item($user),
                'email' => $user['field_9'],
                'language' => $user['field_13'],
            ];

            $events = calendar::get_events(date('Y-m-d'), date('Y-m-d'), 'public');

            if (($events_count = count($events)) > 0) {
                $html = TEXT_EXT_ALERTS_EMAIL_TEXT . $this->render_events_html($events);

                $reports_url = CRON_HTTP_SERVER_HOST . 'index.php?module=ext/calendar/public';

                $html .= '<p><a href="' . $reports_url . '">' . TEXT_EXT_OPEN_CALENDAR . '</a></p>';

                $subject = (strlen(
                    CFG_PUBLIC_CALENDAR_ALERTS_SUBJECT
                ) ? CFG_PUBLIC_CALENDAR_ALERTS_SUBJECT : TEXT_EXT_PUBLIC_CALENDAR_ALERTS_SUBJECT);

                $options = [
                    'to' => $app_user['email'],
                    'to_name' => $app_user['name'],
                    'subject' => sprintf($subject, $events_count),
                    'body' => $html,
                    'from' => CFG_EMAIL_ADDRESS_FROM,
                    'from_name' => CFG_EMAIL_NAME_FROM
                ];

                //echo '<pre>';
                //print_r($options);

                users::send_email($options);
            }
        }
    }

}