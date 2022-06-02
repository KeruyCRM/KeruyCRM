<h3 class="page-title"><?php
    echo TEXT_EXT_RELATD_ENTITIES ?></h3>

<p><?php
    echo TEXT_EXT_MAIL_RELATD_ENTITIES_INFO ?></p>

<?php
echo button_tag(TEXT_BUTTON_ADD, url_for('ext/mail_integration/entities_form'), true) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_EXT_MAIL_ACCOUNTS ?></th>
            <th width="100%"><?php
                echo TEXT_ENTITY ?></th>
            <th><?php
                echo TEXT_EXT_AUTO_CREATE_RECORD ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $accounts_query = db_query(
            "select me.*, ma.name as server_name from app_ext_mail_accounts_entities me left join app_ext_mail_accounts ma on me.accounts_id=ma.id order by me.sort_order, me.id"
        );

        if (db_num_rows($accounts_query) == 0) {
            echo '<tr><td colspan="7">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($accounts = db_fetch_array($accounts_query)):

            $count_fields_query = db_query(
                "select count(*) as total from app_ext_mail_accounts_entities_fields where account_entities_id='" . $accounts['id'] . "' and filters_id=0"
            );
            $count_fields = db_fetch_array($count_fields_query);

            $count_filters_query = db_query(
                "select count(*) as total from app_ext_mail_accounts_entities_filters where account_entities_id='" . $accounts['id'] . "'"
            );
            $count_filters = db_fetch_array($count_filters_query);

            $count_rules_query = db_query(
                "select count(*) as total from app_ext_mail_accounts_entities_rules where account_entities_id='" . $accounts['id'] . "'"
            );
            $count_rules = db_fetch_array($count_rules_query);

            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for('ext/mail_integration/entities_delete', 'id=' . $accounts['id'])
                        ) . ' ' . button_icon_edit(
                            url_for('ext/mail_integration/entities_form', 'id=' . $accounts['id'])
                        ) ?></td>
                <td><?php
                    echo $accounts['server_name'] ?></td>
                <td><?php
                    echo link_to(
                        $app_entities_cache[$accounts['entities_id']]['name'],
                        url_for('ext/mail_integration/entities_fields', 'account_entities_id=' . $accounts['id'])
                    );
                    echo '<br><small>' . link_to(
                            TEXT_EXT_FIELDS_VALUES . ' (' . $count_fields['total'] . ')',
                            url_for('ext/mail_integration/entities_fields', 'account_entities_id=' . $accounts['id'])
                        ) . '</samll>';
                    if ($accounts['auto_create'] != 0) {
                        echo '<small> | ' . link_to(
                                TEXT_RULES . ' (' . $count_rules['total'] . ')',
                                url_for('ext/mail_integration/entities_rules', 'account_entities_id=' . $accounts['id'])
                            ) . ' | ' . link_to(
                                TEXT_FILTERS . ' (' . $count_filters['total'] . ')',
                                url_for(
                                    'ext/mail_integration/entities_filters',
                                    'account_entities_id=' . $accounts['id']
                                )
                            ) . '</small>';
                    }
                    ?></td>
                <td><?php
                    echo mail_accounts::get_auto_create_choices_name($accounts['auto_create']) ?></td>
                <td><?php
                    echo $accounts['sort_order'] ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>