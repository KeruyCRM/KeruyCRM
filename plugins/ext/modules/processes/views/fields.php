<?php
require(component_path('ext/processes/navigation')) ?>

<h3 class="page-title"><?php
    echo TEXT_EXT_PROCESS_ACTIONS_FIELDS ?></h3>

<?php
echo button_tag(
    TEXT_BUTTON_ADD_NEW_FIELD,
    url_for(
        'ext/processes/fields_form',
        'process_id=' . $app_process_info['id'] . '&actions_id=' . $app_actions_info['id']
    ),
    true
) ?>

<?php
if (strstr($app_actions_info['type'], 'edit_item_subentity_') or strstr(
        $app_actions_info['type'],
        'edit_item_related_entity_'
    )) {
    $count_filters = 0;

    $action_entity_id = processes::get_entity_id_from_action_type($app_actions_info['type']);

    $reports_info_query = db_query(
        "select * from app_reports where entities_id='" . db_input(
            $action_entity_id
        ) . "' and reports_type='process_action" . $app_actions_info['id'] . "'"
    );
    if ($reports_info = db_fetch_array($reports_info_query)) {
        $count_query = db_query(
            "select count(*) as total from app_reports_filters  where reports_id='" . $reports_info['id'] . "'"
        );
        $count = db_fetch_array($count_query);
        $count_filters = $count['total'];
    }

    echo '<a class="btn btn-default"  href="' . url_for(
            'ext/processes/actions_filters',
            'process_id=' . $app_process_info['id'] . '&actions_id=' . $app_actions_info['id']
        ) . '">' . TEXT_CONFIGURE_FILTERS . ' (' . $count_filters . ')</a>&nbsp;&nbsp;' . tooltip_icon(
            TEXT_EXT_CONFIGURE_ACTION_FILTERS_INFO
        );
}
?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th width="80"><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_VALUES ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        $actions_fields_query = db_query(
            "select af.id, af.fields_id, af.value, af.enter_manually, f.name, f.type as field_type from app_ext_processes_actions_fields af, app_fields f left join app_forms_tabs t on f.forms_tabs_id=t.id  where f.id=af.fields_id and af.actions_id='" . db_input(
                _get::int('actions_id')
            ) . "' order by t.sort_order, t.name, f.sort_order, f.name"
        );

        if (db_num_rows($actions_fields_query) == 0) {
            echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($actions_fields = db_fetch_array($actions_fields_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for(
                                'ext/processes/fields_delete',
                                'process_id=' . $app_process_info['id'] . '&actions_id=' . $app_actions_info['id'] . '&id=' . $actions_fields['id']
                            )
                        ) . ' ' . button_icon_edit(
                            url_for(
                                'ext/processes/fields_form',
                                'process_id=' . $app_process_info['id'] . '&actions_id=' . $app_actions_info['id'] . '&id=' . $actions_fields['id']
                            )
                        ) ?></td>
                <td><?php
                    echo fields_types::get_option(
                        $actions_fields['field_type'],
                        'name',
                        $actions_fields['name']
                    ) ?></td>
                <td><?php
                    echo processes::output_action_field_value($actions_fields) ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>

<?php
echo '<a href="' . url_for(
        'ext/processes/actions',
        'process_id=' . _get::int('process_id')
    ) . '" class="btn btn-default">' . TEXT_BUTTON_BACK . '</a>' ?>