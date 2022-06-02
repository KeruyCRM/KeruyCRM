<h3 class="page-title"><?php
    echo TEXT_EXT_SAMRT_INPUT_RULES ?></h3>

<p><?php
    echo TEXT_EXT_SAMRT_INPUT_RULES_INFO ?></p>

<?php
echo button_tag(TEXT_BUTTON_CREATE, url_for('ext/modules/smart_input_rules_form'), true) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_EXT_MODULE ?></th>
            <th><?php
                echo TEXT_ENTITY ?></th>
            <th><?php
                echo TEXT_FIELD ?></th>
            <th><?php
                echo TEXT_TYPE ?></th>
            <th width="100%"><?php
                echo TEXT_RULE_FOR_FIELD ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        $modules = new modules('smart_input');

        $rules_query = db_query(
            "select r.*, e.name as entity_name, m.module from app_ext_smart_input_rules r left join app_ext_modules m on m.id=r.modules_id, app_entities e where e.id=r.entities_id order by e.name"
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
                            url_for('ext/modules/smart_input_rules_delete', 'id=' . $rules['id'])
                        ) . ' ' . button_icon_edit(
                            url_for('ext/modules/smart_input_rules_form', 'id=' . $rules['id'])
                        ) ?></td>
                <td><?php
                    echo $module_title ?></td>
                <td><?php
                    echo $rules['entity_name'] ?></td>
                <td><?php
                    echo($rules['fields_id'] > 0 ? $fields_cahce[$rules['fields_id']] : '') ?></td>
                <td><?php
                    echo smart_input::render_module_itnegration_type_name($rules['modules_id'], $rules['type']) ?></td>
                <td><?php
                    echo nl2br($rules['rules']) ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>