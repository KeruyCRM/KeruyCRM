<h3 class="page-title"><?php
    echo TEXT_EXT_COMMON_FILTERS ?></h3>

<p><?php
    echo TEXT_EXT_COMMON_FILTERS_INFO ?></p>

<div class="row">
    <div class="col-md-9">
        <?php
        echo button_tag(TEXT_ADD, url_for('ext/common_filters/form')) ?>
    </div>
    <div class="col-md-3">
        <?php
        echo form_tag('reports_filter_form', url_for('ext/common_filters/reports', 'action=set_reports_filter')) ?>
        <?php
        echo select_tag(
            'reports_filter',
            entities::get_choices_with_empty(),
            $common_filters_filter,
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
                echo TEXT_REPORT_ENTITY ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_USERS_GROUPS ?></th>
            <th><?php
                echo TEXT_DISPLAY_AS_COUNTER ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        $where_sql = '';

        if ($common_filters_filter > 0) {
            $where_sql .= " and r.entities_id='" . db_input($common_filters_filter) . "'";
        }

        $reports_query = db_query(
            "select r.*,e.name as entities_name,e.parent_id as entities_parent_id from app_reports r, app_entities e where e.id=r.entities_id and r.reports_type='common_filters' {$where_sql} order by  e.name, r.in_dashboard_counter desc, r.dashboard_sort_order, r.name"
        );
        if (db_num_rows($reports_query) == 0) {
            echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }
        while ($v = db_fetch_array($reports_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(url_for('ext/common_filters/delete', 'id=' . $v['id'])) . ' ' .
                        button_icon_edit(url_for('ext/common_filters/form', 'id=' . $v['id'])) . ' ' .
                        button_icon(
                            TEXT_COPY,
                            'fa fa-files-o',
                            url_for('ext/common_filters/reports', 'action=copy&reports_id=' . $v['id']),
                            false,
                            ['onClick' => 'return confirm("' . addslashes(TEXT_COPY_RECORD) . '?")']
                        ) . ' ' .
                        button_icon(
                            TEXT_BUTTON_CONFIGURE_FILTERS,
                            'fa fa-cogs',
                            url_for('ext/common_filters/filters', 'reports_id=' . $v['id']),
                            false
                        ) . ' ' .
                        button_icon(
                            TEXT_HEADING_REPORTS_SORTING,
                            'fa fa-sort-alpha-asc',
                            url_for('reports/sorting', 'reports_id=' . $v['id'] . '&redirect_to=common_filters')
                        )

                    ?></td>
                <td><?php
                    echo $v['id'] ?></td>

                <td><?php
                    echo $v['entities_name'] ?></td>
                <td><?php
                    echo '<a href="' . url_for(
                            'ext/common_filters/filters',
                            'reports_id=' . $v['id']
                        ) . '">' . $v['name'] . '</a>';

                    $count_query = db_query(
                        "select count(*) as total from app_reports_filters where reports_id='" . $v['id'] . "'"
                    );
                    $count = db_fetch_array($count_query);
                    echo tooltip_text(TEXT_FILTERS . ': ' . $count['total']);

                    ?></td>
                <td>
                    <?php
                    if (strlen($v['users_groups'])) {
                        $users_groups_list = [];
                        foreach (explode(',', $v['users_groups']) as $users_groups_id) {
                            $users_groups_list[] = access_groups::get_name_by_id($users_groups_id);
                        }

                        echo implode('<br>', $users_groups_list);
                    }
                    ?>
                </td>
                <td><?php
                    echo render_bool_value($v['in_dashboard_counter']) ?></td>
                <td><?php
                    echo $v['dashboard_sort_order'] ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>