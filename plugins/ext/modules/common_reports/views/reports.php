<h3 class="page-title"><?php
    echo TEXT_EXT_COMMON_REPORTS ?></h3>

<p><?php
    echo TEXT_EXT_COMMON_REPORTS_DESCRIPTION ?></p>

<div class="row">
    <div class="col-md-9">
        <?php
        echo button_tag(TEXT_BUTTON_ADD_NEW_REPORT, url_for('ext/common_reports/form')) ?>
    </div>
    <div class="col-md-3">
        <?php
        echo form_tag('reports_filter_form', url_for('ext/common_reports/reports', 'action=set_reports_filter')) ?>
        <?php
        echo select_tag(
            'reports_filter',
            entities::get_choices_with_empty(),
            $common_reports_filter,
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
                echo TEXT_ID ?></th>
            <th><?php
                echo TEXT_USERS_GROUPS ?></th>
            <th><?php
                echo TEXT_REPORT_ENTITY ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_IN_MENU ?></th>
            <th><?php
                echo TEXT_IN_DASHBOARD ?></th>
            <th><?php
                echo TEXT_DISPLAY_IN_HEADER ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        $where_sql = '';

        if ($common_reports_filter > 0) {
            $where_sql .= " and r.entities_id='" . db_input($common_reports_filter) . "'";
        }

        $reports_query = db_query(
            "select r.*,e.name as entities_name,e.parent_id as entities_parent_id from app_reports r, app_entities e where e.id=r.entities_id and r.reports_type='common' {$where_sql} order by e.name, r.dashboard_sort_order, r.name"
        );
        if (db_num_rows($reports_query) == 0) {
            echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }
        while ($v = db_fetch_array($reports_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(url_for('ext/common_reports/delete', 'id=' . $v['id'])) . ' ' .
                        button_icon_edit(url_for('ext/common_reports/form', 'id=' . $v['id'])) . ' ' .
                        button_icon(
                            TEXT_COPY,
                            'fa fa-files-o',
                            url_for('ext/common_reports/reports', 'action=copy&reports_id=' . $v['id']),
                            false,
                            ['onClick' => 'return confirm("' . addslashes(TEXT_COPY_RECORD) . '?")']
                        ) . ' ' .
                        button_icon(
                            TEXT_BUTTON_CONFIGURE_FILTERS,
                            'fa fa-cogs',
                            url_for('ext/common_reports/filters', 'reports_id=' . $v['id']),
                            false
                        ) . ' ' .
                        button_icon(
                            TEXT_HEADING_REPORTS_SORTING,
                            'fa fa-sort-alpha-asc',
                            url_for('reports/sorting', 'reports_id=' . $v['id'] . '&redirect_to=common_reports')
                        );
                    ?></td>
                <td><?php
                    echo $v['id'] ?></td>
                <td>
                    <?php
                    $users_groups_list = [];
                    foreach (explode(',', $v['users_groups']) as $users_groups_id) {
                        $users_groups_list[] = access_groups::get_name_by_id($users_groups_id);
                    }

                    echo implode('<br>', $users_groups_list) . '<br>';

                    if (strlen($v['assigned_to']) > 0) {
                        $users_list = [];
                        foreach (explode(',', $v['assigned_to']) as $users_id) {
                            $users_list[] = users::get_name_by_id($users_id);
                        }

                        echo implode('<br>', $users_list);
                    }
                    ?>
                </td>
                <td><?php
                    echo $v['entities_name'] ?></td>
                <td><?php
                    echo $v['name'] ?></td>
                <td><?php
                    echo render_bool_value($v['in_menu']) ?></td>
                <td><?php
                    echo render_bool_value($v['in_dashboard']) ?></td>
                <td><?php
                    echo render_bool_value($v['in_header']) ?></td>
                <td><?php
                    echo $v['dashboard_sort_order'] ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>