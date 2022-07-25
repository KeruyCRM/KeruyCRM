<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<h3 class="page-title"><?= \K::$fw->TEXT_USERS_ALERTS ?></h3>

<p><?= \K::$fw->TEXT_USERS_ALERTS_INFO; ?></p>

<?= \Helpers\Html::button_tag(\K::$fw->TEXT_BUTTON_ADD, \Helpers\Urls::url_for('main/users_alerts/form')) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?= \K::$fw->TEXT_ACTION ?></th>
            <th><?= \K::$fw->TEXT_TYPE ?></th>
            <th width="100%"><?= \K::$fw->TEXT_TITLE ?></th>
            <th><?= \K::$fw->TEXT_LOCATION ?></th>
            <th><?= \K::$fw->TEXT_DATE_FROM ?></th>
            <th><?= \K::$fw->TEXT_DATE_TO ?></th>
            <th><?= \K::$fw->TEXT_ASSIGNED_TO ?></th>
            <th><?= \K::$fw->TEXT_IS_ACTIVE ?></th>
            <th><?= \K::$fw->TEXT_CREATED_BY ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        //while ($alerts = db_fetch_array($alets_query)):
        foreach (\K::$fw->alerts_query as $alerts):
            $alerts = $alerts->cast();
            ?>
            <tr>
                <td style="white-space: nowrap;"><?= \Helpers\Html::button_icon_delete(
                        \Helpers\Urls::url_for('main/users_alerts/delete', 'id=' . $alerts['id'])
                    ) . ' ' . \Helpers\Html::button_icon_edit(
                        \Helpers\Urls::url_for('main/users_alerts/form', 'id=' . $alerts['id'])
                    ); ?></td>
                <td><?= '<span class="label label-' . $alerts['type'] . '">' . \Models\Main\Users\Users_alerts::get_type_by_name(
                        $alerts['type']
                    ) . '</span>' ?></td>
                <td><?= $alerts['title'] ?></td>
                <td><?= ($alerts['location'] == 'all' ? \K::$fw->TEXT_LOCATION_ON_ALL_PAGES : \K::$fw->TEXT_LOCATION_ON_DASHBOARD) ?></td>
                <td><?= ($alerts['start_date'] ? \Helpers\App::format_date($alerts['start_date']) : '') ?></td>
                <td><?= ($alerts['end_date'] ? \Helpers\App::format_date($alerts['end_date']) : '') ?></td>
                <td>
                    <?php
                    if (strlen($alerts['users_groups']) > 0) {
                        $users_groups = [];
                        foreach (explode(',', $alerts['users_groups']) as $id) {
                            $users_groups[] = \K::$fw->app_access_groups_cache[$id];
                        }

                        if (count($users_groups) > 0) {
                            echo '<span style="display:block" data-html="true" data-toggle="tooltip" data-placement="left" title="' . addslashes(
                                    implode(', ', $users_groups)
                                ) . '">' . \K::$fw->TEXT_USERS_GROUPS . ' (' . count($users_groups) . ')</span>';
                        }
                    }

                    if ($alerts['assigned_to'] > 0) {
                        $assigned_to = [];
                        foreach (explode(',', $alerts['assigned_to']) as $id) {
                            $assigned_to[] = \K::$fw->app_users_cache[$id]['name'];
                        }

                        if (count($assigned_to) > 0) {
                            echo '<span data-html="true" data-toggle="tooltip" data-placement="left" title="' . addslashes(
                                    implode(', ', $assigned_to)
                                ) . '">' . \K::$fw->TEXT_USERS_LIST . ' (' . count($assigned_to) . ')</span>';
                        }
                    }
                    ?>
                </td>
                <td><?= \Helpers\App::render_bool_value($alerts['is_active']) ?></td>
                <td><?= \Models\Main\Users\Users::get_name_by_id($alerts['created_by']) ?></td>
            </tr>
        <?php
        endforeach; ?>
        <?php
        if (count(\K::$fw->alerts_query) == 0) {
            echo '<tr><td colspan="9">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td></tr>';
        } ?>
        </tbody>
    </table>
</div>