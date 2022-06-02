<ul class="page-breadcrumb breadcrumb">
    <?php
    echo '
			<li>' . link_to(TEXT_EXT_RELATD_ENTITIES, url_for('ext/mail_integration/entities')) . '<i class="fa fa-angle-right"></i></li>
			<li>' . $accounts_entities['server_name'] . '<i class="fa fa-angle-right"></i></li>		
					<li>' . $accounts_entities['entities_name'] . '<i class="fa fa-angle-right"></i></li>
			<li>' . TEXT_FILTERS . '</li>';
    ?>
</ul>

<p><?php
    echo TEXT_EXT_MAIL_ENTITIES_FILTERS_INFO ?></p>

<?php
echo button_tag(
    TEXT_BUTTON_ADD,
    url_for('ext/mail_integration/entities_filters_form', 'account_entities_id=' . $accounts_entities['id']),
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
            <?php
            if ($accounts_entities['parent_id'] > 0) echo '<th>' . TEXT_PARENT . '</th>' ?>
            <th><?php
                echo TEXT_EXT_FIELDS_VALUES ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $filters_query = db_query(
            "select * from app_ext_mail_accounts_entities_filters where account_entities_id='" . $accounts_entities['id'] . "'  order by id"
        );

        if (db_num_rows($filters_query) == 0) {
            echo '<tr><td colspan="7">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($filters = db_fetch_array($filters_query)):

            $count_fields_query = db_query(
                "select count(*) as total from app_ext_mail_accounts_entities_fields where account_entities_id='" . $accounts_entities['id'] . "' and filters_id='" . $filters['id'] . "'"
            );
            $count_fields = db_fetch_array($count_fields_query);

            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for(
                                'ext/mail_integration/entities_filters_delete',
                                'account_entities_id=' . $accounts_entities['id'] . '&id=' . $filters['id']
                            )
                        ) . ' ' . button_icon_edit(
                            url_for(
                                'ext/mail_integration/entities_filters_form',
                                'account_entities_id=' . $accounts_entities['id'] . '&id=' . $filters['id']
                            )
                        ) ?></td>
                <td><?php
                    echo $filters['id'] ?></td>
                <td><?php
                    echo $filters['from_email'] ?></td>
                <td style="white-space:normal"><?php
                    echo $filters['has_words'] ?></td>
                <?php
                if ($accounts_entities['parent_id'] > 0) echo '<td>' . items::get_heading_field(
                        $accounts_entities['parent_id'],
                        $filters['parent_item_id']
                    ) . '</td>' ?>
                <td><?php
                    echo link_to(
                        TEXT_EXT_FIELDS_VALUES . ' (' . $count_fields['total'] . ')',
                        url_for(
                            'ext/mail_integration/entities_fields',
                            'filters_id=' . $filters['id'] . '&account_entities_id=' . $accounts_entities['id']
                        )
                    ) ?></td>
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