<?php

exit();

/*
require('plugins/ext/classes/icalexporter.php');


$calendar_type = 'public';


$where_sql = '';

switch($calendar_type)
{
  case 'personal':
      $where_sql .= "event_type='personal' and users_id='" . db_input($app_user['id']) . "' "; 
    break;
  case 'public':
      $where_sql .= "(event_type='public' or (event_type='personal' and is_public=1)) ";
      $PRODID = 'Public Calendar;
    break;
}
   
$events_array = array();             
$events_query = db_query("select * from app_ext_calendar_events where " . $where_sql . " order by start_date");
while($events = db_fetch_array($events_query))
{
  $events_array[] = array('id' => $events['id'],
                          'start_date' => date('Y-m-d H:i:s',$events['start_date']),
                          'end_date' => date('Y-m-d H:i:s',$events['end_date']),
                          'text' => $events['name'],
                          'rec_type' => '',
                          'event_pid' => null,
                          'event_length' => null
                      ); 
}

//echo '<pre>';
//print_r($events_array);

$export = new icalexporter();
$export->setTitle(CFG_APP_NAME);
$ical = $export->toICal($events_array,array('PRODID'=>$PRODID));

//echo '<pre>';
//echo $ical;
//exit();

//set correct content-type-header
//header('Content-type: text/calendar; charset=utf-8');
//header('Content-Disposition: inline; filename=calendar.ics');

echo $ical;


*/


exit();