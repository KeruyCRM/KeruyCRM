<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<h3 class="page-title"><?= \K::$fw->TEXT_AUTOMATIC_BACKUP ?></h3>

<div>
    <?= \Helpers\Html::button_tag(\K::$fw->TEXT_CONFIGURE, \Helpers\Urls::url_for('main/tools/db_backup_auto_cfg')); ?>
</div>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?= \K::$fw->TEXT_ACTION ?></th>
            <th><?= \K::$fw->TEXT_ID ?></th>
            <th width="100%"><?= \K::$fw->TEXT_FILENAME ?></th>
            <th><?= \K::$fw->TEXT_SIZE ?></th>
            <th><?= \K::$fw->TEXT_DATE_ADDED ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (\K::$fw->listing_split->number_of_rows() == 0) {
            echo '<tr><td colspan="5">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        //while ($backups = db_fetch_array($backups_query)):
        foreach (\K::$fw->backups_query as $backups)  :
            $backups = $backups->cast();
            ?>
            <tr>
                <td class="nowrap"><?= \Helpers\Html::button_icon_delete(
                        \Helpers\Urls::url_for('main/tools/db_backup_delete', 'id=' . $backups['id'])
                    ) . ' ' . \Helpers\Html::button_icon(
                        \K::$fw->TEXT_BUTTON_RESTORE,
                        'fa fa fa-repeat',
                        \Helpers\Urls::url_for('main/tools/db_restore', 'id=' . $backups['id'])
                    ) . ' ' . \Helpers\Html::button_icon(
                        \K::$fw->TEXT_BUTTON_DOWNLOAD,
                        'fa fa-download',
                        \Helpers\Urls::url_for('main/tools/db_backup/download', 'id=' . $backups['id']),
                        false
                    ); ?></td>
                <td><?= $backups['id'] ?></td>

                <td><?= $backups['filename']; ?></td>
                <td><?php
                    if (is_file($file_path = \K::$fw->DIR_FS_BACKUPS_AUTO . $backups['filename'])) {
                        echo \Tools\Attachments::file_size_convert(filesize($file_path));
                    }
                    ?></td>
                <td><?= \Helpers\App::format_date_time($backups['date_added']) ?></td>
            </tr>
        <?php
        endforeach; ?>

        <tbody>
    </table>
</div>

<?php
echo '
  <table width="100%">
    <tr>
      <td>' . \K::$fw->listing_split->display_count() . '</td>
      <td align="right">' . \K::$fw->listing_split->display_links() . '</td>
    </tr>
  </table>';
?>

<br>
<div><?= \K::$fw->TEXT_BACKUP_FOLDER . ': ' . \K::$fw->DIR_FS_BACKUPS_AUTO ?></div>
<div><?= \K::$fw->TEXT_CRON_BACKUP . ': ' . \K::$fw->DIR_FS_CATALOG . 'cron/backup' ?></div>

<script>
    function load_items_listing(listing_container, page) {
        location.href = "<?= \Helpers\Urls::url_for('main/tools/db_backup_auto') ?>&page=" + page;
    }
</script>