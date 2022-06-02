<h3 class="page-title"><?php
    echo TEXT_EXT_CHANGE_HISTORY ?></h3>

<p><?php
    echo TEXT_EXT_CHANGE_HISTORY_REPORT_INFO ?></p>

<?php
echo button_tag(TEXT_EXT_BUTTON_ADD_REPORT, url_for('ext/track_changes/form')) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_ID ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_EXT_KEEP_HISTORY ?></th>
            <th><?php
                echo TEXT_IS_ACTIVE ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (db_count('app_ext_track_changes') == 0) {
            echo '<tr><td colspan="8">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        } ?>
        <?php
        $reports_query = db_query("select * from app_ext_track_changes order by name");
        while ($v = db_fetch_array($reports_query)):

            $entities_list = [];
            $entities_query = db_query(
                "select te.*, t.name as report_name, e.name as entity_name from app_ext_track_changes_entities te left join app_entities e on te.entities_id=e.id, app_ext_track_changes t where t.id=te.reports_id and te.reports_id='" . $v['id'] . "' order by e.name"
            );
            while ($entities = db_fetch_array($entities_query)) {
                $entities_list[] = $entities['entity_name'];
            }
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for('ext/track_changes/delete', 'id=' . $v['id'])
                        ) . ' ' . button_icon_edit(url_for('ext/track_changes/form', 'id=' . $v['id'])); ?></td>
                <td><?php
                    echo $v['id'] ?></td>
                <td><?php
                    echo link_to($v['name'], url_for('ext/track_changes/entities', 'reports_id=' . $v['id']));
                    if (count($entities_list)) {
                        echo tooltip_text(TEXT_ENTITY . ': ' . implode(',', $entities_list));
                    }
                    ?></td>
                <td><?php
                    echo $v['keep_history'] ?></td>
                <td><?php
                    echo render_bool_value($v['is_active']) ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>