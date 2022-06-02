<h3 class="page-title"><?php
    echo TEXT_EXT_REPORT_DESIGNER ?></h3>

<p><?php
    echo TEXT_EXT_REPORT_DESIGNER_INFO ?></p>

<?php
$where_sql = '';

if ($report_page_filter > 0) {
    $where_sql .= " and rp.entities_id='" . db_input($report_page_filter) . "'";
}

$report_query = db_query(
    "select rp.*, e.name as entities_name from app_ext_report_page rp left join app_entities e on e.id=rp.entities_id {$where_sql} order by e.name, rp.sort_order, rp.name"
);
?>


<div class="row">
    <div class="col-md-9">
        <?php
        echo button_tag(TEXT_BUTTON_CREATE, url_for('ext/report_page/form'), true) ?>
        <?php
        if (db_num_rows($report_query) > 1 and $report_page_filter > 0) echo button_tag(
            TEXT_SORT_ORDER,
            url_for('ext/report_page/sort'),
            true,
            ['class' => 'btn btn-default']
        ) ?>
    </div>
    <div class="col-md-3">
        <?php
        echo form_tag('report_filter', url_for('ext/report_page/reports', 'action=set_filter')) ?>
        <?php
        echo select_tag(
            'report_page_filter',
            entities::get_choices_with_empty(),
            $report_page_filter,
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
                echo TEXT_ACCESS ?></th>
            <th><?php
                echo TEXT_IS_ACTIVE ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (db_num_rows($report_query) == 0) {
            echo '<tr><td colspan="7">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        $access_groups_cache = access_groups::get_cache();

        while ($report = db_fetch_array($report_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(url_for('ext/report_page/delete', 'id=' . $report['id'])) . ' ' .
                        button_icon_edit(url_for('ext/report_page/form', 'id=' . $report['id'])) . ' ' .
                        button_icon(
                            TEXT_COPY,
                            'fa fa-files-o',
                            url_for('ext/report_page/reports', 'action=copy&id=' . $report['id']),
                            false,
                            ['onClick' => 'return confirm("' . addslashes(TEXT_COPY_RECORD) . '?")']
                        )
                    ?></td>
                <td><?php
                    echo $report['entities_name'] ?></td>
                <td>
                    <?php
                    echo link_to($report['name'], url_for('ext/report_page/configure', 'id=' . $report['id'])) . '<br>';

                    $count = db_count('app_ext_report_page_blocks', $report['id'], 'report_id');
                    echo '<small>' . link_to(
                            TEXT_EXT_HTML_BLOCKS . ' (' . $count . ')',
                            url_for('ext/report_page/blocks', 'report_id=' . $report['id'])
                        ) . '</small>';

                    if ($report['entities_id'] > 0) {
                        echo '<small> | ' . link_to(
                                TEXT_FILTERS . ' (' . reports::count_filters_by_reports_type(
                                    $report['entities_id'],
                                    'report_page' . $report['id']
                                ) . ')',
                                url_for(
                                    'default_filters/filters',
                                    'reports_id=' . default_filters::get_reports_id(
                                        $report['entities_id'],
                                        'report_page' . $report['id']
                                    ) . '&redirect_to=report_page' . $report['id']
                                )
                            ) . '</small>';
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if (strlen($report['users_groups']) > 0) {
                        $users_groups = [];
                        foreach (explode(',', $report['users_groups']) as $id) {
                            $users_groups[] = $access_groups_cache[$id];
                        }

                        if (count($users_groups) > 0) {
                            echo '<span style="display:block" data-html="true" data-toggle="tooltip" data-placement="left" title="' . addslashes(
                                    implode(', ', $users_groups)
                                ) . '">' . TEXT_USERS_GROUPS . ' (' . count($users_groups) . ')</span>';
                        }
                    }

                    if ($report['assigned_to'] > 0) {
                        $assigned_to = [];
                        foreach (explode(',', $report['assigned_to']) as $id) {
                            $assigned_to[] = $app_users_cache[$id]['name'];
                        }

                        if (count($assigned_to) > 0) {
                            echo '<span data-html="true" data-toggle="tooltip" data-placement="left" title="' . addslashes(
                                    implode(', ', $assigned_to)
                                ) . '">' . TEXT_USERS_LIST . ' (' . count($assigned_to) . ')</span>';
                        }
                    }
                    ?>
                </td>
                <td><?php
                    echo render_bool_value($report['is_active']) ?></td>
                <td><?php
                    echo $report['sort_order'] ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>