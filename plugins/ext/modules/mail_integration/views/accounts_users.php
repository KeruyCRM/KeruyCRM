<ul class="page-breadcrumb breadcrumb">
    <?php
    echo '
			<li>' . link_to(TEXT_EXT_MAIL_ACCOUNTS, url_for('ext/mail_integration/accounts')) . '<i class="fa fa-angle-right"></i></li>
			<li>' . $accounts_info['name'] . '<i class="fa fa-angle-right"></i></li>		
			<li>' . TEXT_USERS . '</li>';
    ?>
</ul>
<h3 class="page-title"><?php
    echo TEXT_USERS ?></h3>

<?php
echo button_tag(
    TEXT_BUTTON_ADD,
    url_for('ext/mail_integration/accounts_users_form', 'accounts_id=' . $accounts_info['id']),
    true
) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th width="100%"><?php
                echo TEXT_USERS ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $accounts_query = db_query(
            "select * from app_ext_mail_accounts_users where accounts_id='" . $accounts_info['id'] . "' order by id"
        );

        if (db_num_rows($accounts_query) == 0) {
            echo '<tr><td colspan="4">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($accounts = db_fetch_array($accounts_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for(
                                'ext/mail_integration/accounts_users_delete',
                                'id=' . $accounts['id'] . '&accounts_id=' . $accounts_info['id']
                            )
                        ) . ' ' . button_icon_edit(
                            url_for(
                                'ext/mail_integration/accounts_users_form',
                                'id=' . $accounts['id'] . '&accounts_id=' . $accounts_info['id']
                            )
                        ) ?></td>
                <td><?php
                    echo users::get_name_by_id($accounts['users_id']) ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>

<?php
echo '<a href="' . url_for(
        'ext/mail_integration/accounts'
    ) . '" class="btn btn-default">' . TEXT_BUTTON_BACK . '</a>' ?>
