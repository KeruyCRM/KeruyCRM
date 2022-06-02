<ul class="page-breadcrumb breadcrumb">
    <?php
    echo '
			<li>' . link_to(TEXT_EXT_RELATD_ENTITIES, url_for('ext/mail_integration/entities')) . '<i class="fa fa-angle-right"></i></li>
			<li>' . $accounts_entities['server_name'] . '<i class="fa fa-angle-right"></i></li>		
					<li>' . $accounts_entities['entities_name'] . '<i class="fa fa-angle-right"></i></li>
			<li>' . TEXT_RULES . '</li>';
    ?>
</ul>

<p><?php
    echo TEXT_EXT_MAIL_ENTITIES_RULES_INFO ?></p>

<?php
echo button_tag(
    TEXT_BUTTON_ADD,
    url_for('ext/mail_integration/entities_rules_form', 'account_entities_id=' . $accounts_entities['id']),
    true
) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th style="width: 90px;"><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_ID ?></th>
            <th><?php
                echo TEXT_EXT_EMAIL_FROM ?></th>
            <th><?php
                echo TEXT_EXT_HAS_WORDS ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $rules_query = db_query(
            "select * from app_ext_mail_accounts_entities_rules where account_entities_id='" . $accounts_entities['id'] . "'  order by id"
        );

        if (db_num_rows($rules_query) == 0) {
            echo '<tr><td colspan="7">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($rules = db_fetch_array($rules_query)):

            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for(
                                'ext/mail_integration/entities_rules_delete',
                                'account_entities_id=' . $accounts_entities['id'] . '&id=' . $rules['id']
                            )
                        ) . ' ' . button_icon_edit(
                            url_for(
                                'ext/mail_integration/entities_rules_form',
                                'account_entities_id=' . $accounts_entities['id'] . '&id=' . $rules['id']
                            )
                        ) ?></td>
                <td><?php
                    echo $rules['id'] ?></td>
                <td><?php
                    echo $rules['from_email'] ?></td>
                <td style="white-space:normal"><?php
                    echo $rules['has_words'] ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>

<?php
echo '<a href="' . url_for(
        'ext/mail_integration/entities'
    ) . '" class="btn btn-default">' . TEXT_BUTTON_BACK . '</a>' ?>