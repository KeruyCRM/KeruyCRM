<h3 class="page-title"><?php
    echo TEXT_EXT_MAIL_ACCOUNTS ?></h3>

<p><?php
    echo TEXT_EXT_MAIL_ACCOUNTS_INFO ?></p>

<?php
echo button_tag(TEXT_BUTTON_ADD, url_for('ext/mail_integration/accounts_form'), true) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_IS_ACTIVE ?></th>
            <th><?php
                echo TEXT_IS_DEFAULT ?></th>
            <th><?php
                echo TEXT_EXT_IMAP_SERVER ?></th>
            <th><?php
                echo TEXT_EXT_MAILBOX ?></th>
            <th><?php
                echo TEXT_USERNAME ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $accounts_query = db_query(
            "select ma.*, (select count(*) from app_ext_mail_accounts_users mau where ma.id=mau.accounts_id) as count_users from app_ext_mail_accounts ma order by ma.id"
        );

        if (db_num_rows($accounts_query) == 0) {
            echo '<tr><td colspan="7">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($accounts = db_fetch_array($accounts_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for('ext/mail_integration/accounts_delete', 'id=' . $accounts['id'])
                        ) . ' ' . button_icon_edit(
                            url_for('ext/mail_integration/accounts_form', 'id=' . $accounts['id'])
                        ) ?></td>
                <td><?php
                    echo link_to(
                            $accounts['name'],
                            url_for('ext/mail_integration/accounts_users', 'accounts_id=' . $accounts['id'])
                        ) . '<br>' .
                        '<small>&nbsp;&nbsp;' . link_to(
                            TEXT_EXT_ASSIGNED_USERS . ' (' . $accounts['count_users'] . ')',
                            url_for('ext/mail_integration/accounts_users', 'accounts_id=' . $accounts['id'])
                        ) . '<br>&nbsp;&nbsp;' . link_to_modalbox(
                            TEXT_EXT_CHECK_CONNECTION,
                            url_for('ext/mail_integration/accounts_check', 'id=' . $accounts['id'])
                        ) . ' | ' . link_to_modalbox(
                            TEXT_BUTTON_SEND_TEST_EMAIL,
                            url_for('ext/mail_integration/accounts_check_smtp', 'id=' . $accounts['id'])
                        ) . '</small>';
                    echo '<small> | ' . link_to_modalbox(
                            TEXT_CLEAR,
                            url_for('ext/mail_integration/accounts_clear', 'id=' . $accounts['id'])
                        ) . '</small>';
                    ?></td>
                <td><?php
                    echo render_bool_value($accounts['is_active']) ?></td>
                <td><?php
                    echo render_bool_value($accounts['is_default']) ?></td>
                <td><?php
                    echo $accounts['imap_server'] ?></td>
                <td><?php
                    echo $accounts['mailbox'] ?></td>
                <td><?php
                    echo render_bg_color_block($accounts['bg_color'], $accounts['login']) ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>