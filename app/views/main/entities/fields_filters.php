<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php
require(component_path('entities/navigation')) ?>

    <h3 class="page-title"><?php
        echo $fields_info['name'] . ' <i class="fa fa-angle-right"></i> ' . TEXT_FILTERS ?></h3>

<?php
$description = '';
switch ($fields_info['type']) {
    case 'fieldtype_users_approve':
        $description = TEXT_FIELDTYPE_USERS_APPROVE_FILTERS_INFO;
        break;
    case 'fieldtype_signature':
    case 'fieldtype_digital_signature':
        $description = TEXT_SET_FILTERS_FOR_ACTION_BUTTON;
        break;
}
?>

    <p><?php
        echo $description ?></p>

<?php
echo button_tag(
    TEXT_BUTTON_ADD_NEW_REPORT_FILTER,
    url_for(
        'entities/fields_filters_form',
        'reports_id=' . $reports_info['id'] . '&entities_id=' . $_GET['entities_id'] . '&fields_id=' . $_GET['fields_id']
    )
) ?>

    <div class="table-scrollable">
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th><?php
                    echo TEXT_ACTION ?></th>
                <th width="100%"><?php
                    echo TEXT_FIELD ?></th>
                <th><?php
                    echo TEXT_FILTERS_CONDITION ?></th>
                <th><?php
                    echo TEXT_VALUES ?></th>

            </tr>
            </thead>
            <tbody>
            <?php
            if (db_count('app_reports_filters', $reports_info['id'], 'reports_id') == 0) {
                echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
            } ?>
            <?php
            $filters_query = db_query(
                "select rf.*, f.name, f.type from app_reports_filters rf left join app_fields f on rf.fields_id=f.id where rf.reports_id='" . db_input(
                    $reports_info['id']
                ) . "' order by rf.id"
            );
            while ($v = db_fetch_array($filters_query)):
                ?>
                <tr>
                    <td style="white-space: nowrap;"><?php
                        echo button_icon_delete(
                                url_for(
                                    'entities/fields_filters_delete',
                                    'id=' . $v['id'] . '&reports_id=' . $reports_info['id'] . '&entities_id=' . $_GET['entities_id'] . '&fields_id=' . $_GET['fields_id']
                                )
                            ) . ' ' . button_icon_edit(
                                url_for(
                                    'entities/fields_filters_form',
                                    'id=' . $v['id'] . '&reports_id=' . $reports_info['id'] . '&entities_id=' . $_GET['entities_id'] . '&fields_id=' . $_GET['fields_id']
                                )
                            ) ?></td>
                    <td><?php
                        echo fields_types::get_option($v['type'], 'name', $v['name']) ?></td>
                    <td><?php
                        echo reports::get_condition_name_by_key($v['filters_condition']) ?></td>
                    <td class="nowrap"><?php
                        echo reports::render_filters_values(
                            $v['fields_id'],
                            $v['filters_values'],
                            '<br>',
                            $v['filters_condition']
                        ) ?></td>
                </tr>
            <?php
            endwhile ?>
            </tbody>
        </table>
    </div>

<?php
echo '<a class="btn btn-default" href="' . url_for(
        'entities/fields',
        'entities_id=' . $_GET['entities_id']
    ) . '">' . TEXT_BUTTON_BACK . '</a>'; ?>