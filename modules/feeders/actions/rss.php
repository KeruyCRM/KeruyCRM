<?php

$client_id = _GET('client');
$rss_id = _GET('rss');

//check if user exist by client ID and user is active
$user_query = db_query("select * from app_entity_1 where client_id='{$client_id}' and field_5=1");
if (!$user = db_fetch_array($user_query)) {
    die(TEXT_NO_ACCESS);
} else {
    $app_user = [
        'id' => $user['id'],
        'group_id' => (int)$user['field_6']
    ];
}

$feed_query = db_query(
    "select * from app_ext_rss_feeds where (find_in_set(" . $app_user['group_id'] . ",users_groups) or find_in_set(" . $app_user['id'] . ",assigned_to)) and rss_id={$rss_id}"
);
if (!$feed = db_fetch_array($feed_query)) {
    die(TEXT_NO_RECORDS_FOUND);
}

header("Content-Type: text/xml");

echo '
    <rss version="2.0">
        <channel>
            <title>' . $feed['name'] . '</title>
    ';

$rss_feed = new rss_feed($feed);
echo $rss_feed->render();

echo '
        </channel>
    </rss>
    ';


app_exit();