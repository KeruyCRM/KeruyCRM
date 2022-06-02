<h3 class="page-title"><?php
    echo TEXT_EXT_PUBLIC_FORMS ?></h3>

<p><?php
    echo TEXT_EXT_PUBLIC_FORMS_DESCRIPTION ?></p>

<div class="row">
    <div class="col-md-9">
        <?php
        echo button_tag(TEXT_BUTTON_ADD, url_for('ext/public_forms/form')) ?>
    </div>
</div>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_ID ?></th>
            <th><?php
                echo TEXT_IS_ACTIVE ?></th>
            <th><?php
                echo TEXT_REPORT_ENTITY ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_EXT_PB_CHECK_ENQUIRY ?></th>
            <th><?php
                echo TEXT_NOTE ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (db_count('app_ext_public_forms') == 0) {
            echo '<tr><td colspan="7">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        } ?>
        <?php
        $where_sql = '';

        $forms_query = db_query(
            "select f.*, e.name as entities_name from app_ext_public_forms f, app_entities e where e.id=f.entities_id {$where_sql} order by e.name, f.name"
        );
        while ($v = db_fetch_array($forms_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(url_for('ext/public_forms/delete', 'id=' . $v['id'])) . ' ' .
                        button_icon_edit(url_for('ext/public_forms/form', 'id=' . $v['id'])) . ' ' .
                        button_icon(
                            TEXT_COPY,
                            'fa fa-files-o',
                            url_for('ext/public_forms/public_forms', 'action=copy&id=' . $v['id']),
                            false,
                            ['onClick' => 'return confirm("' . addslashes(TEXT_COPY_RECORD) . '?")']
                        ) . ' ' .
                        button_icon(
                            TEXT_INFO,
                            'fa fa-share-alt',
                            url_for('ext/public_forms/share', 'id=' . $v['id'])
                        ); ?></td>
                <td><?php
                    echo $v['id'] ?></td>
                <td><?php
                    echo render_bool_value($v['is_active']) ?></td>
                <td><?php
                    echo $v['entities_name'] ?></td>
                <td><?php
                    echo link_to($v['name'], url_for('ext/public/form', 'id=' . $v['id']), ['target' => '_blank']
                    ) ?></td>
                <td><?php
                    echo($v['check_enquiry'] ? '<a href="' . url_for(
                            'ext/public/check',
                            'id=' . $v['id']
                        ) . '" target="_blank">' . TEXT_VIEW . '</a>' : TEXT_NO) ?></td>
                <td><?php
                    echo tooltip_icon($v['notes'], 'left') ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>

