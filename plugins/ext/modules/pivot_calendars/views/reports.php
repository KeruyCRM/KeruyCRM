<h3 class="page-title"><?php
    echo TEXT_EXT_PIVOT_СALENDAR ?></h3>

<p><?php
    echo TEXT_EXT_PIVOT_СALENDAR_INFO ?></p>

<?php
echo button_tag(TEXT_BUTTON_CREATE, url_for('ext/pivot_calendars/form'), true) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_IN_MENU ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $reports_query = db_query("select * from app_ext_pivot_calendars order by sort_order,name");

        if (db_num_rows($reports_query) == 0) {
            echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($reports = db_fetch_array($reports_query)):

            $count_query = db_query(
                "select count(*) as total from app_ext_pivot_calendars_entities where calendars_id='" . $reports['id'] . "'"
            );
            $count = db_fetch_array($count_query);
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for('ext/pivot_calendars/delete', 'id=' . $reports['id'])
                        ) . ' ' . button_icon_edit(url_for('ext/pivot_calendars/form', 'id=' . $reports['id'])) ?></td>
                <td><?php
                    echo link_to(
                            $reports['name'],
                            url_for('ext/pivot_calendars/entities', 'calendars_id=' . $reports['id'])
                        ) . '<br>' . tooltip_text(TEXT_EXT_ENTITIES . ': ' . $count['total']) ?></td>
                <td><?php
                    echo render_bool_value($reports['in_menu']) ?></td>
                <td><?php
                    echo $reports['sort_order'] ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>