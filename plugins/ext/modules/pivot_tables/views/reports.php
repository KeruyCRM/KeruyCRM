<h3 class="page-title"><?php
    echo TEXT_EXT_PIVOT_TABLES ?></h3>
<p><?php
    echo TEXT_EXT_PIVOT_TABLES_INFO ?></p>


<div class="row">
    <div class="col-md-9">
        <?php
        echo button_tag(TEXT_BUTTON_ADD_NEW_REPORT, url_for('ext/pivot_tables/reports_form')) ?>
    </div>
    <div class="col-md-3">
        <?php
        echo form_tag('reports_filter_form', url_for('ext/pivot_tables/reports', 'action=set_reports_filter')) ?>
        <?php
        echo select_tag(
            'reports_filter',
            entities::get_choices_with_empty(),
            $pivot_reports_filter,
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
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        $where_sql = '';

        if ($pivot_reports_filter > 0) {
            $where_sql .= " where entities_id='" . db_input($pivot_reports_filter) . "'";
        }

        $reports_query = db_query("select * from app_ext_pivot_tables {$where_sql} order by sort_order, name");

        $entities_cache = entities::get_name_cache();
        $fields_cahce = fields::get_name_cache();


        if (db_num_rows($reports_query) == 0) {
            echo '<tr><td colspan="4">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($reports = db_fetch_array($reports_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for('ext/pivot_tables/reports_delete', 'id=' . $reports['id'])
                        ) . ' ' . button_icon_edit(
                            url_for('ext/pivot_tables/reports_form', 'id=' . $reports['id'])
                        ) ?></td>
                <td><?php
                    echo $entities_cache[$reports['entities_id']] ?></td>
                <td>
                    <?php
                    echo link_to($reports['name'], url_for('ext/pivot_tables/view', 'id=' . $reports['id'])) . '<br>';

                    echo '<small>&nbsp;&nbsp;' . link_to(
                            TEXT_EXT_PIVOTREPORTS_FIELDS,
                            url_for('ext/pivot_tables/fields', 'id=' . $reports['id'])
                        ) . '</small>';

                    if (!strlen($reports['filters_panel'])) {
                        echo '<small>&nbsp;|&nbsp;' . link_to(
                                TEXT_DEFAULT_FILTERS,
                                url_for(
                                    'default_filters/filters',
                                    'reports_id=' . default_filters::get_reports_id(
                                        $reports['entities_id'],
                                        'default_pivot_tables' . $reports['id']
                                    ) . '&redirect_to=pivot_tables' . $reports['id']
                                )
                            ) . '</small>';
                    }

                    if ($reports['filters_panel'] == 'quick_filters') {
                        echo '<small>&nbsp;|&nbsp;' . TEXT_QUICK_FILTERS_PANELS . ': <a href="' . url_for(
                                'ext/filters_panels/fields',
                                'panels_id=' . filters_panels::get_id_by_type(
                                    $reports['entities_id'],
                                    'pivot_tables' . $reports['id']
                                ) . '&entities_id=' . $reports['entities_id'] . '&redirect_to=pivot_tables' . $reports['id']
                            ) . '">' . TEXT_CONFIGURE . '</a></small></small>';
                    }
                    ?>
                </td>
                <td><?php
                    echo $reports['sort_order'] ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>

