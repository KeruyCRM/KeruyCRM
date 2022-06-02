<ul class="page-breadcrumb breadcrumb">
    <?php
    echo '
			<li>' . link_to(TEXT_EXT_PROCESSES, url_for('ext/processes/processes')) . '<i class="fa fa-angle-right"></i></li>				
			<li>' . TEXT_EXT_BUTTONS_GROUPS . '</li>';
    ?>
</ul>

<p><?php
    echo TEXT_EXT_PROCESSES_BUTTONS_GROUPS_INFO ?></p>

<div class="row">
    <div class="col-md-9">
        <?php
        echo button_tag(TEXT_BUTTON_ADD, url_for('ext/processes/buttons_groups_form')) ?>
    </div>
    <div class="col-md-3">
        <?php
        echo form_tag(
            'processes_filter_form',
            url_for('ext/processes/buttons_groups', 'action=set_processes_filter')
        ) ?>
        <?php
        echo select_tag(
            'processes_filter',
            entities::get_choices_with_empty(),
            $processes_filter,
            ['class' => 'form-control chosen-select', 'onChange' => 'this.form.submit()']
        ) ?>
        </form>
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
                echo TEXT_REPORT_ENTITY ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_COLOR ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>

        <?php
        $where_sql = '';

        if ($processes_filter > 0) {
            $where_sql .= " and p.entities_id='" . db_input($processes_filter) . "'";
        }

        $buttons_query = db_query(
            "select p.*, e.name as entities_name from app_ext_processes_buttons_groups p, app_entities e where e.id=p.entities_id {$where_sql} order by p.sort_order, e.name, p.name"
        );

        if (db_num_rows($buttons_query) == 0) {
            echo '<tr><td colspan="8">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($v = db_fetch_array($buttons_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for('ext/processes/buttons_groups_delete', 'id=' . $v['id'])
                        ) . ' ' . button_icon_edit(
                            url_for('ext/processes/buttons_groups_form', 'id=' . $v['id'])
                        ); ?></td>
                <td><?php
                    echo $v['id'] ?></td>
                <td><?php
                    echo $v['entities_name'] ?></td>
                <td><?php
                    echo $v['name']; ?></td>
                <td><?php
                    echo render_bg_color_block($v['button_color']) ?></td>
                <td><?php
                    echo $v['sort_order'] ?></td>

            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>

<?php
echo '<a class="btn btn-default" href="' . url_for('ext/processes/processes') . '">' . TEXT_BUTTON_BACK . '</a>'; ?>