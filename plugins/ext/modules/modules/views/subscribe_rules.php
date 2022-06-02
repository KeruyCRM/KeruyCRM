<h3 class="page-title"><?php
    echo TEXT_EXT_SUBSCRIBE_RULES ?></h3>

<p><?php
    echo TEXT_EXT_SUBSCRIBE_RULES_INFO ?></p>

<?php
echo button_tag(TEXT_BUTTON_CREATE, url_for('ext/modules/subscribe_rules_form'), true) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_EXT_MODULE ?></th>
            <th><?php
                echo TEXT_REPORT_ENTITY ?></th>
            <th><?php
                echo TEXT_FIELD ?></th>

        </tr>
        </thead>
        <tbody>
        <?php

        $modules = new modules('mailing');

        $rules_query = db_query(
            "select r.*, f.type as field_type, f.name as field_name, e.name as entity_name, m.module from app_ext_subscribe_rules r left join app_ext_modules m on m.id=r.modules_id left join app_fields f on f.id=r.contact_email_field_id, app_entities e where e.id=r.entities_id order by e.name, r.id"
        );


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
                            url_for('ext/modules/subscribe_rules_delete', 'id=' . $rules['id'])
                        ) . ' ' . button_icon_edit(
                            url_for('ext/modules/subscribe_rules_form', 'id=' . $rules['id'])
                        ) ?></td>
                <td><?php
                    echo $module_title ?></td>
                <td><?php
                    echo $rules['entity_name'] ?></td>
                <td><?php
                    echo fields_types::get_option($rules['field_type'], 'name', $rules['field_name']) ?></td>

            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>