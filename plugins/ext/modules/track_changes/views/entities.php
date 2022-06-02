<?php
require(component_path('ext/track_changes/navigation')) ?>

    <h3 class="page-title"><?php
        echo TEXT_EXT_ENTITIES ?></h3>

    <p><?php
        echo TEXT_EXT_CHANGE_HISTORY_REPORT_ENTITIES_INFO ?></p>

<?php
echo button_tag(TEXT_BUTTON_ADD, url_for('ext/track_changes/entities_form', 'reports_id=' . _get::int('reports_id'))) ?>

    <div class="table-scrollable">
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th><?php
                    echo TEXT_ACTION ?></th>
                <th><?php
                    echo TEXT_REPORT ?></th>
                <th width="100%"><?php
                    echo TEXT_ENTITY ?></th>
                <th><?php
                    echo TEXT_FIELDS ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $reports_query = db_query(
                "select te.*, t.name as report_name, e.name as entity_name from app_ext_track_changes_entities te left join app_entities e on te.entities_id=e.id, app_ext_track_changes t where t.id=te.reports_id and te.reports_id='" . $app_reports['id'] . "' order by e.name"
            );

            if (!db_num_rows($reports_query)) {
                echo '<tr><td colspan="8">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
            }

            while ($v = db_fetch_array($reports_query)):
                ?>
                <tr>
                    <td style="white-space: nowrap;"><?php
                        echo button_icon_delete(
                                url_for(
                                    'ext/track_changes/entities_delete',
                                    'reports_id=' . _get::int('reports_id') . '&id=' . $v['id']
                                )
                            ) . ' ' . button_icon_edit(
                                url_for(
                                    'ext/track_changes/entities_form',
                                    'reports_id=' . _get::int('reports_id') . '&id=' . $v['id']
                                )
                            ); ?></td>
                    <td><?php
                        echo $v['report_name'] ?></td>
                    <td><?php
                        echo $v['entity_name'] ?></td>
                    <td><?php

                        if (strlen($v['track_fields'])) {
                            $fields_list = [];
                            $fields_query = db_query(
                                "select f.name from app_fields f, app_forms_tabs t where f.entities_id='" . $v['entities_id'] . "' and f.id in (" . $v['track_fields'] . ") and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
                            );
                            while ($fields = db_fetch_array($fields_query)) {
                                $fields_list[] = $fields['name'];
                            }

                            echo(count($fields_list) ? implode(', ', $fields_list) : TEXT_EXT_ALL_FIELDS);
                        } else {
                            echo TEXT_EXT_ALL_FIELDS;
                        }


                        ?></td>
                </tr>
            <?php
            endwhile ?>
            </tbody>
        </table>
    </div>

<?php
echo '<a href="' . url_for('ext/track_changes/reports') . '" class="btn btn-default">' . TEXT_BUTTON_BACK . '</a>' ?>