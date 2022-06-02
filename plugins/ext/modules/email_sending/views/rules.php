<?php
require(component_path('entities/navigation')) ?>

<h3 class="page-title"><?php
    echo TEXT_EXT_EMAIL_SENDING_RULES ?></h3>

<p><?php
    echo TEXT_EXT_EMAIL_SENDING_RULES_INFO ?></p>

<?php
echo button_tag(
    TEXT_BUTTON_CREATE,
    url_for('ext/email_sending/form', 'entities_id=' . _get::int('entities_id')),
    true
) ?>&nbsp;
<?php
echo button_tag(
    TEXT_EXT_HTML_BLOCKS,
    url_for('ext/email_sending/blocks', 'entities_id=' . _get::int('entities_id')),
    false,
    ['class' => 'btn btn-default']
) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_ID ?></th>
            <th><?php
                echo TEXT_REPORT_ENTITY ?></th>
            <th><?php
                echo TEXT_TYPE ?></th>
            <th><?php
                echo TEXT_EXT_RULE ?></th>
            <th><?php
                echo TEXT_EXT_SEND_TO_USERS ?></th>
            <th width="100%"><?php
                echo TEXT_EMAIL_SUBJECT ?></th>
            <th><?php
                echo TEXT_IS_ACTIVE ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        $fields_cahce = fields::get_name_cache();

        $rules_query = db_query(
            "select r.*, e.name as entity_name from app_ext_email_rules r, app_entities e where e.id=r.entities_id and e.id='" . _get::int(
                'entities_id'
            ) . "' order by e.name, r.action_type"
        );

        if (db_num_rows($rules_query) == 0) {
            echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($rules = db_fetch_array($rules_query)):

            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for(
                                'ext/email_sending/delete',
                                'id=' . $rules['id'] . '&entities_id=' . _get::int('entities_id')
                            )
                        ) . ' ' . button_icon_edit(
                            url_for(
                                'ext/email_sending/form',
                                'id=' . $rules['id'] . '&entities_id=' . _get::int('entities_id')
                            )
                        ) ?></td>
                <td><?php
                    echo $rules['id'] ?></td>
                <td><?php
                    echo $rules['entity_name'] ?></td>
                <td><?php
                    echo email_rules::get_action_type($rules['action_type']) ?></td>
                <td><?php
                    echo email_rules::get_action_type_name($rules['action_type']) . ' ' . tooltip_icon(
                            $rules['notes']
                        ) . ($rules['monitor_fields_id'] > 0 ? '<br><i>' . TEXT_EXT_PB_NOTIFY_FIELD_CHANGE . ':</i> <span class="label label-warning">' . $fields_cahce[$rules['monitor_fields_id']] . '</span>' : '') ?></td>
                <td><?php
                    $html = [];
                    if (strlen($rules['send_to_users'])) {
                        foreach (explode(',', $rules['send_to_users']) as $v) {
                            if (strlen($name = users::get_name_by_id($v))) {
                                $html[] = $name;
                            }
                        }
                    } elseif (strlen($rules['send_to_assigned_users'])) {
                        foreach (explode(',', $rules['send_to_assigned_users']) as $v) {
                            $fields_query = db_query(
                                "select id, type, name, entities_id from app_fields where id='" . $v . "'"
                            );
                            if ($fields = db_fetch_array($fields_query)) {
                                $html[] = $app_entities_cache[$fields['entities_id']]['name'] . ': ' . fields_types::get_option(
                                        $fields['type'],
                                        'name',
                                        $fields['name']
                                    );
                            }
                        }
                    } elseif (strlen($rules['send_to_email'])) {
                        $html[] = nl2br($rules['send_to_email']);
                    } elseif (strlen($rules['send_to_assigned_email'])) {
                        foreach (explode(',', $rules['send_to_assigned_email']) as $v) {
                            $fields_query = db_query(
                                "select id, type, name, entities_id from app_fields where id='" . $v . "'"
                            );
                            if ($fields = db_fetch_array($fields_query)) {
                                $html[] = $app_entities_cache[$fields['entities_id']]['name'] . ': ' . fields_types::get_option(
                                        $fields['type'],
                                        'name',
                                        $fields['name']
                                    );
                            }
                        }
                    }

                    if (count($html)) {
                        echo implode('<br>', $html);
                    }

                    ?></td>
                <td><?php
                    echo $rules['subject'] ?></td>
                <td><?php
                    echo render_bool_value($rules['is_active'], true) ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>