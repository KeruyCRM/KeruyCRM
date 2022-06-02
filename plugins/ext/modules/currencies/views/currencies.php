<h3 class="page-title"><?php
    echo TEXT_EXT_CURRENCIES ?></h3>

<p><?php
    echo TEXT_EXT_CURRENCIES_INFO . '<br>' . TEXT_EXT_CURRENCIES_INFO_FORMULA ?></p>

<?php
echo button_tag(TEXT_BUTTON_ADD, url_for('ext/currencies/form')) . ' ' . button_tag(
        TEXT_EXT_UPDATE_CURRENCIES . ' (' . strtoupper(CFG_CURRENCIES_UPDATE_MODULE) . ')',
        url_for('ext/currencies/update'),
        true,
        ['class' => 'btn btn-default']
    ) . ' ' . button_tag(TEXT_EXT_EXCHANGE_RATES, url_for('ext/currencies/widget'), true, ['class' => 'btn btn-default']
    ) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th width="100%"><?php
                echo TEXT_TITLE ?></th>
            <th><?php
                echo TEXT_EXT_CODE ?></th>
            <th><?php
                echo TEXT_EXT_SYMBOL ?></th>
            <th><?php
                echo TEXT_EXT_VALUE ?></th>
            <th><?php
                echo TEXT_IS_DEFAULT ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $currencies_query = db_query("select * from app_ext_currencies order by sort_order,title");

        if (db_num_rows($currencies_query) == 0) {
            echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($currencies = db_fetch_array($currencies_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for('ext/currencies/delete', 'id=' . $currencies['id'])
                        ) . ' ' . button_icon_edit(url_for('ext/currencies/form', 'id=' . $currencies['id'])) ?></td>

                <td><?php
                    echo $currencies['title'] ?></td>
                <td><?php
                    echo $currencies['code'] ?></td>
                <td><?php
                    echo $currencies['symbol'] ?></td>
                <td><?php
                    echo $currencies['value'] ?></td>
                <td><?php
                    echo render_bool_value($currencies['is_default']) ?></td>
                <td><?php
                    echo $currencies['sort_order'] ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>