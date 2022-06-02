<ul class="page-breadcrumb breadcrumb">
    <?php
    echo '
			<li>' . link_to(TEXT_EXT_RELATD_ENTITIES, url_for('ext/mail_integration/entities')) . '<i class="fa fa-angle-right"></i></li>
			<li>' . $accounts_entities['server_name'] . '<i class="fa fa-angle-right"></i></li>		
			<li>' . $accounts_entities['entities_name'] . '<i class="fa fa-angle-right"></i></li>
			' . ($filters_id > 0 ? '<li>' . TEXT_FILTERS . ' (#' . $filters_id . ')<i class="fa fa-angle-right"></i></li>' : '') . '		
			<li>' . TEXT_FIELDS . '</li>';
    ?>
</ul>

<h3 class="page-title"><?php
    echo TEXT_EXT_PROCESS_ACTIONS_FIELDS ?></h3>

<?php
echo button_tag(
    TEXT_BUTTON_ADD,
    url_for(
        'ext/mail_integration/entities_fields_form',
        'account_entities_id=' . $accounts_entities['id'] . ($filters_id > 0 ? '&filters_id=' . $filters_id : '')
    ),
    true
) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th width="80"><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_VALUES ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        $actions_fields_query = db_query(
            "select af.id, af.fields_id, af.value, f.name, f.type as field_type from app_ext_mail_accounts_entities_fields af, app_fields f left join app_forms_tabs t on f.forms_tabs_id=t.id  where f.id=af.fields_id and af.account_entities_id='" . db_input(
                _get::int('account_entities_id')
            ) . "' and af.filters_id='" . $filters_id . "' order by t.sort_order, t.name, f.sort_order, f.name"
        );

        if (db_num_rows($actions_fields_query) == 0) {
            echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($actions_fields = db_fetch_array($actions_fields_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for(
                                'ext/mail_integration/entities_fields_delete',
                                'account_entities_id=' . $accounts_entities['id'] . '&id=' . $actions_fields['id'] . ($filters_id > 0 ? '&filters_id=' . $filters_id : '')
                            )
                        ) . ' ' . button_icon_edit(
                            url_for(
                                'ext/mail_integration/entities_fields_form',
                                'account_entities_id=' . $accounts_entities['id'] . '&id=' . $actions_fields['id'] . ($filters_id > 0 ? '&filters_id=' . $filters_id : '')
                            )
                        ) ?></td>
                <td><?php
                    echo $actions_fields['name'] ?></td>
                <td><?php
                    echo processes::output_action_field_value($actions_fields) ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>

<?php
if ($filters_id > 0) {
    echo '<a href="' . url_for(
            'ext/mail_integration/entities_filters',
            'account_entities_id=' . $accounts_entities['id']
        ) . '" class="btn btn-default">' . TEXT_BUTTON_BACK . '</a>';
} else {
    echo '<a href="' . url_for(
            'ext/mail_integration/entities'
        ) . '" class="btn btn-default">' . TEXT_BUTTON_BACK . '</a>';
}
?>