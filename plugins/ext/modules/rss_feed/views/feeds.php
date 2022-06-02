<h3 class="page-title"><?php
    echo TEXT_EXT_RSS_FEED ?></h3>

<p><?php
    echo TEXT_EXT_RSS_FEED_INFO ?></p>

<?php
echo button_tag(TEXT_BUTTON_CREATE, url_for('ext/rss_feed/form'), true) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_ID ?></th>
            <th><?php
                echo TEXT_TYPE ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_ENTITY ?></th>
            <th><?php
                echo TEXT_ACCESS ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $feeds_query = db_query(
            "select f.*, e.name as entity_name from app_ext_rss_feeds f left join app_entities e on e.id=f.entities_id order by f.type, f.sort_order, f.name"
        );


        if (db_num_rows($feeds_query) == 0) {
            echo '<tr><td colspan="7">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($feeds = db_fetch_array($feeds_query)) {
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for('ext/rss_feed/delete', 'id=' . $feeds['id'])
                        ) . ' ' . button_icon_edit(url_for('ext/rss_feed/form', 'id=' . $feeds['id'])) ?></td>
                <td><?php
                    echo $feeds['rss_id'] ?></td>
                <td><?php
                    echo rss_feed::get_type_title_by_key($feeds['type']) ?></td>
                <td><?php
                    if (in_array($feeds['type'], ['entity', 'entity_calendar'])) {
                        echo link_to(
                                $feeds['name'],
                                url_for(
                                    'default_filters/filters',
                                    'reports_id=' . default_filters::get_reports_id(
                                        $feeds['entities_id'],
                                        'rss_feed' . $feeds['id']
                                    ) . '&redirect_to=rss_feed' . $feeds['id']
                                )
                            ) . '<br>' . tooltip_text(
                                TEXT_FILTERS . ': ' . reports::count_filters_by_reports_type(
                                    $feeds['entities_id'],
                                    'rss_feed' . $feeds['id']
                                )
                            );
                    } else {
                        echo $feeds['name'];
                    }
                    ?></td>
                <td><?php
                    echo $feeds['entity_name'] ?></td>
                <td>
                    <?php
                    if (strlen($feeds['users_groups']) > 0) {
                        $users_groups_list = [];
                        foreach (explode(',', $feeds['users_groups']) as $users_groups_id) {
                            $users_groups_list[] = access_groups::get_name_by_id($users_groups_id);
                        }

                        echo implode('<br>', $users_groups_list) . '<br>';
                    }

                    if (strlen($feeds['assigned_to']) > 0) {
                        $users_list = [];
                        foreach (explode(',', $feeds['assigned_to']) as $users_id) {
                            $users_list[] = users::get_name_by_id($users_id);
                        }

                        echo implode('<br>', $users_list);
                    }
                    ?>
                </td>
                <td><?php
                    echo $feeds['sort_order'] ?></td>
            </tr>
        <?php
        } ?>
        </tbody>
    </table>
</div>
