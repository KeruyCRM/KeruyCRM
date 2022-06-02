<ul class="page-breadcrumb breadcrumb">
    <?php
    $folders = mail_accounts::get_folders_choices();

    echo '
			<li>' . link_to($folders[$app_mail_filters['folder']], url_for('ext/mail/accounts')) . '<i class="fa fa-angle-right"></i></li>
			<li>' . TEXT_EXT_FILTERS_AND_BLOCKED_ADDRESSES . '</li>';
    ?>
</ul>

<p><?php
    echo TEXT_EXT_MAIL_FILTERS_INFO ?></p>

<?php
echo button_tag(TEXT_BUTTON_ADD, url_for('ext/mail/filters_form'), true) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th style="width: 90px;"><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_EXT_MAIL_ACCOUNT ?></th>
            <th><?php
                echo TEXT_EXT_EMAIL_FROM ?></th>
            <th><?php
                echo TEXT_EXT_HAS_WORDS ?></th>
            <th><?php
                echo TEXT_ACTION ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $filters_query = db_query(
            "select mf.*, ma.name as account_name from app_ext_mail_filters mf left join app_ext_mail_accounts ma on mf.accounts_id=ma.id where  mf.accounts_id in (select accounts_id from app_ext_mail_accounts_users where users_id='" . $app_user['id'] . "') order by mf.id"
        );

        if (db_num_rows($filters_query) == 0) {
            echo '<tr><td colspan="7">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($filters = db_fetch_array($filters_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for('ext/mail/filters_delete', 'id=' . $filters['id'])
                        ) . ' ' . button_icon_edit(url_for('ext/mail/filters_form', 'id=' . $filters['id'])) ?></td>
                <td><?php
                    echo $filters['account_name'] ?></td>
                <td><?php
                    echo $filters['from_email'] ?></td>
                <td style="white-space:normal"><?php
                    echo $filters['has_words'] ?></td>
                <td><?php
                    echo mail_filters::get_action_name($filters['action']) ?></td>

            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>