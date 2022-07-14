<h3 class="page-title"><?= \K::$fw->TEXT_HOLIDAYS ?></h3>

<p><?= \K::$fw->TEXT_HOLIDAYS_INFO ?></p>

<div class="row">
    <div class="col-md-9">
        <?= \Helpers\Html::button_tag(\K::$fw->TEXT_ADD, \Helpers\Urls::url_for('main/holidays/form')) ?>
        <?= \Helpers\Html::button_tag(
            \K::$fw->TEXT_COPY,
            \Helpers\Urls::url_for('main/holidays/copy'),
            true,
            ['class' => 'btn btn-default']
        ) ?>
    </div>
    <div class="col-md-3">
        <?php
        if (count(\K::$fw->choices)) {
            echo \Helpers\Html::form_tag(
                    'holidays_filter_form',
                    \Helpers\Urls::url_for('main/holidays/holidays/set_holidays_filter')
                ) .
                \Helpers\Html::select_tag(
                    'holidays_filter',
                    \K::$fw->choices,
                    \K::$fw->holidays_filter,
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
            <th><?= \Helpers\Html::input_checkbox_tag('select_all_holidays', '', ['class' => 'select_all_holidays']
                ) ?></th>
            <th><?= \K::$fw->TEXT_ACTION ?></th>
            <th width="100%"><?= \K::$fw->TEXT_NAME ?></th>
            <th><?= \K::$fw->TEXT_START_DATE ?></th>
            <th><?= \K::$fw->TEXT_END_DATE ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        if (!count(\K::$fw->groups_query)) {
            echo '<tr><td colspan="5">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        foreach (\K::$fw->groups_query as $v) {
            ?>
            <tr>
                <td><?= \Helpers\Html::input_checkbox_tag('holidays[]', $v['id'], ['class' => 'holidays_checkbox']
                    ) ?></td>
                <td style="white-space: nowrap;"><?= \Helpers\Html::button_icon_delete(
                        \Helpers\Urls::url_for('main/holidays/delete', 'id=' . $v['id'])
                    ) . ' ' . \Helpers\Html::button_icon_edit(
                        \Helpers\Urls::url_for('main/holidays/form', 'id=' . $v['id'])
                    ) ?></td>
                <td><?= $v['name'] ?></td>
                <td><?= \Helpers\App::format_date(\Helpers\App::get_date_timestamp($v['start_date'])) ?></td>
                <td><?= \Helpers\App::format_date(\Helpers\App::get_date_timestamp($v['end_date'])) ?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>

<script>
    $('#select_all_holidays').click(function () {
        select_all_by_classname('select_all_holidays', 'holidays_checkbox')
    })
</script>