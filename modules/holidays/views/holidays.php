<h3 class="page-title"><?php
    echo TEXT_HOLIDAYS ?></h3>

<p><?php
    echo TEXT_HOLIDAYS_INFO ?></p>


<div class="row">
    <div class="col-md-9">
        <?php
        echo button_tag(TEXT_ADD, url_for('holidays/form')) ?>
        <?php
        echo button_tag(TEXT_COPY, url_for('holidays/copy'), true, ['class' => 'btn btn-default']) ?>
    </div>
    <div class="col-md-3">
        <?php
        $cholices = holidays::get_year_choices();

        if (count($cholices)) {
            echo form_tag('holidays_filter_form', url_for('holidays/holidays', 'action=set_holidays_filter')) .
                select_tag(
                    'holidays_filter',
                    holidays::get_year_choices(),
                    $holidays_filter,
                    ['class' => 'form-control input-small float-right', 'onChange' => 'this.form.submit()']
                ) .
                '</form>';
        }
        ?>
    </div>
</div>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo input_checkbox_tag('select_all_holidays', '', ['class' => 'select_all_holidays']) ?></th>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_START_DATE ?></th>
            <th><?php
                echo TEXT_END_DATE ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $groups_query = db_fetch_all('app_holidays', "year(start_date)='" . $holidays_filter . "'", 'start_date desc');

        if (db_num_rows($groups_query) == 0) {
            echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($v = db_fetch_array($groups_query)):
            ?>
            <tr>
                <td><?php
                    echo input_checkbox_tag('holidays[]', $v['id'], ['class' => 'holidays_checkbox']) ?></td>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(url_for('holidays/delete', 'id=' . $v['id'])) . ' ' . button_icon_edit(
                            url_for('holidays/form', 'id=' . $v['id'])
                        ) ?></td>
                <td><?php
                    echo $v['name'] ?></td>
                <td><?php
                    echo format_date(get_date_timestamp($v['start_date'])) ?></td>
                <td><?php
                    echo format_date(get_date_timestamp($v['end_date'])) ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>

<script>
    $('#select_all_holidays').click(function () {
        select_all_by_classname('select_all_holidays', 'holidays_checkbox')
    })
</script>