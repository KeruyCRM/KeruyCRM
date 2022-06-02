<h3 class="page-title"><?php
    echo TEXT_EXT_KANBAN ?></h3>

<p><?php
    echo TEXT_EXT_KANBAN_DESCRIPTION ?></p>

<div class="row">
    <div class="col-md-9">
        <?php
        echo button_tag(TEXT_BUTTON_CREATE, url_for('ext/kanban/form')) ?>
    </div>
    <div class="col-md-3">
        <?php
        echo form_tag('reports_filter_form', url_for('ext/kanban/reports', 'action=set_reports_filter')) ?>
        <?php
        echo select_tag(
            'reports_filter',
            entities::get_choices_with_empty(),
            $kanban_entity_filter,
            ['class' => 'form-control input-large float-right', 'onChange' => 'this.form.submit()']
        ) ?>
        </form>
    </div>
</div>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_REPORT_ENTITY ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_EXT_GROUP_BY_FIELD ?></th>
            <th><?php
                echo TEXT_EXT_SUM_BY_FIELD ?></th>
            <th><?php
                echo TEXT_FIELDS_IN_LISTING ?></th>
            <th><?php
                echo TEXT_USERS_GROUPS ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        $fields_cahce = fields::get_name_cache();

        $where_sql = ($kanban_entity_filter > 0 ? " where entities_id={$kanban_entity_filter}" : '');

        $reports_query = db_query("select * from app_ext_kanban {$where_sql} order by name");

        if (db_num_rows($reports_query) == 0) {
            echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($reports = db_fetch_array($reports_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for('ext/kanban/delete', 'id=' . $reports['id'])
                        ) . ' ' . button_icon_edit(url_for('ext/kanban/form', 'id=' . $reports['id'])) ?></td>
                <td><?php
                    echo $app_entities_cache[$reports['entities_id']]['name'] ?></td>
                <td><?php
                    echo $reports['name'] ?></td>

                <td><?php
                    echo $fields_cahce[$reports['group_by_field']] ?></td>
                <td><?php
                    if (strlen($reports['sum_by_field'])) {
                        foreach (explode(',', $reports['sum_by_field']) as $field_id) {
                            echo $fields_cahce[$field_id] . '<br>';
                        }
                    }
                    ?></td>
                <td><?php
                    if (strlen($reports['fields_in_listing'])) {
                        foreach (explode(',', $reports['fields_in_listing']) as $field_id) {
                            echo $fields_cahce[$field_id] . '<br>';
                        }
                    }
                    ?></td>
                <td>
                    <?php
                    if (strlen($reports['users_groups'])) {
                        $users_groups_list = [];
                        foreach (explode(',', $reports['users_groups']) as $users_groups_id) {
                            $users_groups_list[] = access_groups::get_name_by_id($users_groups_id);
                        }

                        echo implode('<br>', $users_groups_list);
                    }
                    ?>
                </td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>
