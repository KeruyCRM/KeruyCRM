<h3 class="page-title"><?php
    echo TEXT_EXT_SMS_SENDING_RULES ?></h3>

<p><?php
    echo TEXT_EXT_SMS_SENDING_RULES_INFO ?></p>

<?php
echo button_tag(TEXT_BUTTON_CREATE, url_for('ext/modules/sms_rules_form'), true) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_EXT_SMS_MODULE ?></th>
            <th><?php
                echo TEXT_REPORT_ENTITY ?></th>
            <th><?php
                echo TEXT_TYPE ?></th>
            <th><?php
                echo TEXT_EXT_RULE ?></th>
            <th><?php
                echo TEXT_EXT_SEND_TO_NUMBER ?></th>
            <th width="100%"><?php
                echo TEXT_EXT_MESSAGE_TEXT ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        $modules = new modules('sms');

        $rules_query = db_query(
            "select r.*, e.name as entity_name, m.module from app_ext_sms_rules r left join app_ext_modules m on m.id=r.modules_id, app_entities e where e.id=r.entities_id order by e.name, r.action_type"
        );

        $fields_cahce = fields::get_name_cache();

        if (db_num_rows($rules_query) == 0) {
            echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($rules = db_fetch_array($rules_query)):

            $module_title = '';
            if (strlen($rules['module'])) {
                $module = new $rules['module'];
                $module_title = $module->title;
            }

            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for('ext/modules/sms_rules_delete', 'id=' . $rules['id'])
                        ) . ' ' . button_icon_edit(url_for('ext/modules/sms_rules_form', 'id=' . $rules['id'])) ?></td>
                <td><?php
                    echo $module_title ?></td>
                <td><?php
                    echo $rules['entity_name'] ?></td>
                <td><?php
                    echo sms::get_action_type($rules['action_type']) ?></td>
                <td><?php
                    echo sms::get_action_type_name(
                            $rules['action_type']
                        ) . ($rules['monitor_fields_id'] > 0 ? '<br><i>' . TEXT_EXT_PB_NOTIFY_FIELD_CHANGE . ':</i> <span class="label label-warning">' . $fields_cahce[$rules['monitor_fields_id']] . '</span>' : '') ?></td>
                <td><?php
                    if (in_array(
                        $rules['action_type'],
                        ['insert_send_to_number_in_entity', 'edit_send_to_number_in_entity']
                    )) {
                        $value = explode(':', $rules['phone']);

                        $fields_query = db_query("select configuration from app_fields where id='" . $value[0] . "'");
                        if ($fields = db_fetch_array($fields_query)) {
                            $cfg = new settings($fields['configuration']);
                            echo entities::get_name_by_id($cfg->get('entity_id')) . ': ' . fields::get_name_by_id(
                                    $value[1]
                                );
                        }
                    } else {
                        echo($rules['fields_id'] > 0 ? TEXT_FIELD . ': ' . $fields_cahce[$rules['fields_id']] : $rules['phone']);
                    }
                    ?></td>
                <td><?php
                    echo nl2br($rules['description']) ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>