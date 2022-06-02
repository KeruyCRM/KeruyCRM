<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_ID ?></th>
            <th><?php
                echo TEXT_ENTITY ?></th>
            <th><?php
                echo TEXT_EXT_EVENT_REPEAT_TYPE ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_IS_ACTIVE ?></th>
            <th><?php
                echo TEXT_CREATED_BY ?></th>
            <th><?php
                echo TEXT_DATE_ADDED ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        if (db_num_rows($tasks_query) == 0) {
            echo '<tr><td colspan="10">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($tasks = db_fetch_array($tasks_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for(
                                'ext/recurring_tasks/delete',
                                'id=' . $tasks['id'] . $redirect_to . (strlen($app_path) ? '&path=' . $app_path : '')
                            )
                        ) . ' ' . button_icon_edit(
                            url_for(
                                'ext/recurring_tasks/form',
                                'id=' . $tasks['id'] . $redirect_to . (strlen($app_path) ? '&path=' . $app_path : '')
                            )
                        ) ?></td>
                <td><?php
                    echo $tasks['id'] ?></td>
                <td><?php
                    echo $app_entities_cache[$tasks['entities_id']]['name'] ?></td>
                <td><?php
                    $choices = recurring_tasks::get_repeat_types();
                    echo $choices[$tasks['repeat_type']];

                    if (strlen($tasks['repeat_days'])) {
                        $choices = calendar::get_events_repeat_days();
                        foreach (explode(',', $tasks['repeat_days']) as $v) {
                            echo '<br> - ' . $choices[$v];
                        }
                    }

                    ?></td>
                <td><?php
                    $html = link_to(
                        items::get_heading_field($tasks['entities_id'], $tasks['items_id']),
                        url_for('items/info', 'path=' . $tasks['entities_id'] . '-' . $tasks['items_id'])
                    );
                    $html .= '
    		<ul>
    			<li>' . TEXT_EXT_INTERVAL . ': ' . $tasks['repeat_interval'] . '</li>
    			<li>' . TEXT_EXT_REPEAT_TIME . ': ' . ($tasks['repeat_time'] < 10 ? '0' . $tasks['repeat_time'] : $tasks['repeat_time']) . ':00' . '</li>
    			<li>' . TEXT_EXT_REPEAT_START . ': ' . format_date($tasks['repeat_start']) . '</li>
    			<li>' . TEXT_EXT_REPEAT_END . ': ' . ($tasks['repeat_end'] == 0 ? '&infin;' : format_date(
                            $tasks['repeat_end']
                        )) . '</li>
    			<li>' . TEXT_EXT_EVENT_REPEAT_LIMIT_SHORT . ': ' . ($tasks['repeat_limit'] == 0 ? '&infin;' : $tasks['repeat_limit']) . '</li>
    		</ul>
  		
  		<a href="' . url_for(
                            'ext/recurring_tasks/fields',
                            'tasks_id=' . $tasks['id'] . '&path=' . (strlen(
                                $app_path
                            ) ? $app_path : $tasks['entities_id'] . '-' . $tasks['items_id'])
                        ) . '"><i class="fa fa-angle-right"></i> ' . TEXT_EXT_PROCESS_ACTIONS_FIELDS . '</a>
    		';

                    $tasks_fields_query = db_query(
                        "select tf.id, tf.fields_id, tf.value, f.name, f.type as field_type from app_ext_recurring_tasks_fields tf, app_fields f left join app_forms_tabs t on f.forms_tabs_id=t.id  where f.id=tf.fields_id and tf.tasks_id='" . $tasks['id'] . "' order by t.sort_order, t.name, f.sort_order, f.name"
                    );
                    $html .= '<ul>';
                    while ($tasks_fields = db_fetch_array($tasks_fields_query)) {
                        $html .= '<li>' . $tasks_fields['name'] . ': ' . strip_tags(
                                recurring_tasks::output_action_field_value($tasks_fields)
                            ) . '</li>';
                    }
                    $html .= '</ul>';

                    echo $html;

                    ?></td>
                <td><?php
                    echo render_bool_value($tasks['is_active']) ?></td>

                <td><?php
                    echo $app_users_cache[$tasks['created_by']]['name'] ?></td>
                <td><?php
                    echo format_date_time($tasks['date_added']) ?></td>

            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>