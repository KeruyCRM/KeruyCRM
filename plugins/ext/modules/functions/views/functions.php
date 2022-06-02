<h3 class="page-title"><?php
    echo TEXT_EXT_FUNCTIONS ?></h3>

<p><?php
    echo TEXT_EXT_FUNCTIONS_DESCRIPTION ?></p>
<p><?php
    echo TEXT_EXT_FUNCTIONS_DESCRIPTION_RELATED_FIELD ?></p>

<div class="row">
    <div class="col-md-9">
        <?php
        echo button_tag(TEXT_BUTTON_ADD_NEW_FUNCTION, url_for('ext/functions/form')) ?>
    </div>
    <div class="col-md-3">
        <?php
        echo form_tag('functions_filter_form', url_for('ext/functions/functions', 'action=set_functions_filter')) ?>
        <?php
        echo select_tag(
            'functions_filter',
            entities::get_choices_with_empty(),
            $functions_filter,
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
                echo TEXT_NOTE ?></th>
            <th><?php
                echo TEXT_EXT_FUNCTION ?></th>
            <th><?php
                echo TEXT_FORMULA ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (db_count('app_ext_functions') == 0) {
            echo '<tr><td colspan="7">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        } ?>
        <?php
        $where_sql = '';

        if ($functions_filter > 0) {
            $where_sql .= " and f.entities_id='" . db_input($functions_filter) . "'";
        }

        $functions_query = db_query(
            "select f.*, e.name as entities_name from app_ext_functions f, app_entities e where e.id=f.entities_id {$where_sql} order by e.name, f.functions_name"
        );
        while ($v = db_fetch_array($functions_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(url_for('ext/functions/delete', 'id=' . $v['id'])) . ' ' . button_icon_edit(
                            url_for('ext/functions/form', 'id=' . $v['id'])
                        ) . ' ' . button_icon(
                            TEXT_BUTTON_CONFIGURE_FILTERS,
                            'fa fa-cogs',
                            url_for('ext/functions/filters', 'functions_id=' . $v['id']),
                            false
                        ); ?></td>
                <td><?php
                    echo $v['id'] ?></td>
                <td><?php
                    echo $v['entities_name'] ?></td>
                <td><?php
                    echo link_to($v['name'], url_for('ext/functions/filters', 'functions_id=' . $v['id']));

                    $count_query = db_query(
                        "select count(*) as total from app_reports_filters where reports_id='" . $v['reports_id'] . "'"
                    );
                    $count = db_fetch_array($count_query);
                    echo tooltip_text(TEXT_APPLIED_FILTERS . ': ' . $count['total']);

                    ?></td>
                <td><?php
                    echo tooltip_icon($v['notes'], 'left') ?></td>
                <td><?php
                    echo $v['functions_name'] ?></td>
                <td><?php
                    echo $v['functions_formula'] ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>