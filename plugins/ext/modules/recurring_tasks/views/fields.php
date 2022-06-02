<?php

$app_breadcrumb = items::get_breadcrumb($current_path_array);
$app_breadcrumb[] = ['title' => TEXT_EXT_REPEAT, 'url' => url_for('ext/recurring_tasks/repeat', 'path=' . $app_path)];
$app_breadcrumb[] = ['title' => TEXT_EXT_PROCESS_ACTIONS_FIELDS];

require(component_path('items/navigation'));

?>

    <h3 class="page-title"><?php
        echo TEXT_EXT_REPEAT . ' <i class="fa fa-angle-right"></i> ' . TEXT_EXT_PROCESS_ACTIONS_FIELDS ?></h3>

<?php
echo button_tag(
    TEXT_BUTTON_ADD_NEW_FIELD,
    url_for('ext/recurring_tasks/fields_form', 'tasks_id=' . _get::int('tasks_id') . '&path=' . $app_path),
    true
) ?>


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

            $tasks_fields_query = db_query(
                "select tf.id, tf.fields_id, tf.value, f.name, f.type as field_type from app_ext_recurring_tasks_fields tf, app_fields f left join app_forms_tabs t on f.forms_tabs_id=t.id  where f.id=tf.fields_id and tf.tasks_id='" . db_input(
                    _get::int('tasks_id')
                ) . "' order by t.sort_order, t.name, f.sort_order, f.name"
            );

            if (db_num_rows($tasks_fields_query) == 0) {
                echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
            }

            while ($tasks_fields = db_fetch_array($tasks_fields_query)):
                ?>
                <tr>
                    <td style="white-space: nowrap;"><?php
                        echo button_icon_delete(
                                url_for(
                                    'ext/recurring_tasks/fields_delete',
                                    'tasks_id=' . _get::int(
                                        'tasks_id'
                                    ) . '&path=' . $app_path . '&id=' . $tasks_fields['id']
                                )
                            ) . ' ' . button_icon_edit(
                                url_for(
                                    'ext/recurring_tasks/fields_form',
                                    'tasks_id=' . _get::int(
                                        'tasks_id'
                                    ) . '&path=' . $app_path . '&id=' . $tasks_fields['id']
                                )
                            ) ?></td>
                    <td><?php
                        echo $tasks_fields['name'] ?></td>
                    <td><?php
                        echo recurring_tasks::output_action_field_value($tasks_fields) ?></td>
                </tr>
            <?php
            endwhile ?>
            </tbody>
        </table>
    </div>

<?php
echo '<a href="' . url_for(
        'ext/recurring_tasks/repeat',
        'path=' . $app_path
    ) . '" class="btn btn-default">' . TEXT_BUTTON_BACK . '</a>'; ?>