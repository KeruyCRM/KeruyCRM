<h3 class="page-title"><?php
    echo TEXT_EXT_FILE_STORAGE_RULES ?></h3>

<p><?php
    echo TEXT_EXT_FILE_STORAGE_RULES_INFO . '<br>' . DIR_FS_CATALOG . 'cron/file_storage.php' ?></p>

<?php
echo button_tag(TEXT_BUTTON_CREATE, url_for('ext/modules/file_storage_rules_form'), true) ?>

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
            <th width="100%"><?php
                echo TEXT_FIELDS ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        $modules = new modules('file_storage');

        $rules_query = db_query(
            "select r.*, e.name as entity_name, m.module from app_ext_file_storage_rules r left join app_ext_modules m on m.id=r.modules_id, app_entities e where e.id=r.entities_id order by e.name"
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
                            url_for('ext/modules/file_storage_rules_delete', 'id=' . $rules['id'])
                        ) . ' ' . button_icon_edit(
                            url_for('ext/modules/file_storage_rules_form', 'id=' . $rules['id'])
                        ) ?></td>
                <td><?php
                    echo $module_title ?></td>
                <td><?php
                    echo $rules['entity_name'] ?></td>
                <td><?php
                    $fields = [];
                    foreach (explode(',', $rules['fields']) as $id) {
                        if (isset($fields_cahce[$id])) {
                            $fields[] = $fields_cahce[$id];
                        }
                    }

                    echo implode(', ', $fields);
                    ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>