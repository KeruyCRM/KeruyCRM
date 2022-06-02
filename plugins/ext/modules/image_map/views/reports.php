<h3 class="page-title"><?php
    echo TEXT_EXT_IMAGE_MAP ?></h3>

<p><?php
    echo TEXT_EXT_IMAGE_MAP_DESCRIPTION ?></p>

<?php
echo button_tag(TEXT_BUTTON_CREATE, url_for('ext/image_map/form')) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_REPORT_ENTITY ?></th>
            <th><?php
                echo TEXT_FIELD ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_IN_MENU ?></th>
            <th><?php
                echo TEXT_USERS_GROUPS ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        $fields_cahce = fields::get_name_cache();

        $reports_query = db_query("select * from app_ext_image_map order by name");

        if (db_num_rows($reports_query) == 0) {
            echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($reports = db_fetch_array($reports_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for('ext/image_map/delete', 'id=' . $reports['id'])
                        ) . ' ' . button_icon_edit(url_for('ext/image_map/form', 'id=' . $reports['id'])) ?></td>
                <td><?php
                    echo $app_entities_cache[$reports['entities_id']]['name'] ?></td>
                <td><?php
                    echo $app_fields_cache[$reports['entities_id']][$reports['fields_id']]['name'] ?></td>
                <td><?php
                    echo link_to($reports['name'], url_for('ext/image_map/view', 'id=' . $reports['id'])) ?></td>
                <td><?php
                    echo render_bool_value($reports['in_menu']) ?></td>
                <td>
                    <?php
                    if (strlen($reports['users_groups'])) {
                        $users_groups_list = [];
                        foreach (json_decode($reports['users_groups'], true) as $id => $access) {
                            if (strlen($access)) {
                                $users_groups_list[] = access_groups::get_name_by_id($id);
                            }
                        }

                        echo implode('<br>', $users_groups_list);
                    }
                    ?>
                </td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>
