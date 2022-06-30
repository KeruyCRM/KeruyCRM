<?php
require(component_path('entities/navigation')) ?>

    <h3 class="page-title"><?php
        echo '<a href="' . url_for(
                'ext/email_sending/rules',
                'entities_id=' . _get::int('entities_id')
            ) . '">' . TEXT_EXT_EMAIL_SENDING_RULES . '</a> <i class="fa fa-angle-right"></i> ' . TEXT_EXT_HTML_BLOCKS ?></h3>

    <p><?php
        echo TEXT_EXT_EMAIL_SENDING_HTML_BLOCK_INFO ?></p>

<?php
echo button_tag(
    TEXT_BUTTON_CREATE,
    url_for('ext/email_sending/blocks_form', 'entities_id=' . _get::int('entities_id')),
    true
) ?>

    <div class="table-scrollable">
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th><?php
                    echo TEXT_ACTION ?></th>
                <th><?php
                    echo TEXT_ID ?></th>
                <th width="100%"><?php
                    echo TEXT_NAME ?></th>
            </tr>
            </thead>
            <tbody>
            <?php

            $blocks_query = db_query("select * from app_ext_email_rules_blocks  order by name");

            if (db_num_rows($blocks_query) == 0) {
                echo '<tr><td colspan="3">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
            }

            while ($blocks = db_fetch_array($blocks_query)):

                ?>
                <tr>
                    <td style="white-space: nowrap;"><?php
                        echo button_icon_delete(
                                url_for(
                                    'ext/email_sending/blocks_delete',
                                    'id=' . $blocks['id'] . '&entities_id=' . _get::int('entities_id')
                                )
                            ) . ' ' . button_icon_edit(
                                url_for(
                                    'ext/email_sending/blocks_form',
                                    'id=' . $blocks['id'] . '&entities_id=' . _get::int('entities_id')
                                )
                            ) ?></td>
                    <td><?php
                        echo '<input value="[block_' . $blocks['id'] . ']" readonly="readonly" class="form-control input-small select-all">' ?></td>
                    <td><?php
                        echo $blocks['name'] ?></td>
                </tr>
            <?php
            endwhile ?>
            </tbody>
        </table>
    </div>

<?php
echo button_tag(
    TEXT_BUTTON_BACK,
    url_for('ext/email_sending/rules', 'entities_id=' . _get::int('entities_id')),
    false,
    ['class' => 'btn btn-default']
) ?>