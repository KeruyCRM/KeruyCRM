<?php

switch ($_GET['type']) {
    case 'payment':
        $page_title = TEXT_EXT_PAYMENT_MODULES;
        $page_description = TEXT_EXT_PAYMENT_MODULES_DESCRIPTION;
        break;
    case 'sms':
        $page_title = TEXT_EXT_SMS_MODULES;
        $page_description = TEXT_EXT_SMS_MODULES_DESCRIPTION;
        break;
    case 'file_storage':
        $page_title = TEXT_EXT_FILE_STORAGE_MODULES;
        $page_description = TEXT_EXT_FILE_STORAGE_MODULES_DESCRIPTION;
        break;
    case 'smart_input':
        $page_title = TEXT_EXT_SAMRT_INPUT;
        $page_description = TEXT_EXT_SMART_INTPUT_MODULES_DESCRIPTION;
        break;
    case 'mailing':
        $page_title = TEXT_EXT_MAILING_SERVICES;
        $page_description = TEXT_EXT_MAILING_SERVICES_DESCRIPTION;
        break;
    case 'telephony':
        $page_title = TEXT_EXT_TELEPHONY_MODULES;
        $page_description = TEXT_EXT_TELEPHONY_MODULES_DESCRIPTION;
        break;
    case 'digital_signature':
        $page_title = TEXT_EXT_ELECTRONIC_DIGITAL_SIGNATURE;
        $page_description = TEXT_EXT_ELECTRONIC_DIGITAL_SIGNATURE_INFO;
        break;
}
?>

<h3 class="page-title"><?php
    echo $page_title ?></h3>

<p><?php
    echo $page_description ?></p>

<?php
echo button_tag(TEXT_EXT_INSTALL_MODULE, url_for('ext/modules/install', 'type=' . $_GET['type'])) ?>

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
            <th></th>
            <th></th>
            <th><?php
                echo TEXT_EXT_VERSION ?></th>
            <th><?php
                echo TEXT_IS_ACTIVE ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (db_count('app_ext_modules', $_GET['type'], 'type') == 0) {
            echo '<tr><td colspan="8">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        } ?>
        <?php
        $modules_query = db_query(
            "select * from app_ext_modules where type='" . $_GET['type'] . "' order by sort_order"
        );
        while ($v = db_fetch_array($modules_query)):

            $module = new $v['module'];
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for('ext/modules/delete', 'id=' . $v['id'] . '&type=' . $_GET['type'])
                        ) . ' ' . button_icon_edit(
                            url_for('ext/modules/form', 'id=' . $v['id'] . '&type=' . $_GET['type'])
                        ); ?></td>
                <td><?php
                    echo $v['id'] ?></td>
                <td><?php
                    echo $module->title ?></td>
                <td><?php
                    echo '<a href="' . $module->site . '" target="_blank">' . str_replace(['http://', 'https://'],
                            '',
                            $module->site) . '</a>' ?></td>
                <td><?php
                    echo(strlen($module->api) ? '<a href="' . $module->api . '" target="_blank">API</a>' : '') ?></td>
                <td><?php
                    echo $module->version ?></td>
                <td><?php
                    echo render_bool_value($v['is_active'], true) ?></td>
                <td><?php
                    echo $v['sort_order'] ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>