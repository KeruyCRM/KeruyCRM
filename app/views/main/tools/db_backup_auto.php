<h3 class="page-title"><?php
    echo TEXT_AUTOMATIC_BACKUP ?></h3>

<div>
    <?php
    echo button_tag(TEXT_CONFIGURE, url_for('tools/db_backup_auto_cfg'));
    ?>
</div>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_ID ?></th>
            <th width="100%"><?php
                echo TEXT_FILENAME ?></th>
            <th><?php
                echo TEXT_SIZE ?></th>
            <th><?php
                echo TEXT_DATE_ADDED ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (db_count('app_backups') == 0) {
            echo '<tr><td colspan="7">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        } ?>
        <?php

        backup::reset(true);

        $backups_query = "select * from app_backups where is_auto=1 order by date_added desc";
        $listing_split = new split_page($backups_query, 'records_listing');
        $backups_query = db_query($listing_split->sql_query);

        if ($listing_split->number_of_rows == 0) {
            echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($backups = db_fetch_array($backups_query)):
            ?>
            <tr>
                <td class="nowrap"><?php
                    echo button_icon_delete(
                            url_for('tools/db_backup_delete', 'id=' . $backups['id'])
                        ) . ' ' . button_icon(
                            TEXT_BUTTON_RESTORE,
                            'fa fa fa-repeat',
                            url_for('tools/db_restore', 'id=' . $backups['id'])
                        ) . ' ' . button_icon(
                            TEXT_BUTTON_DOWNLOAD,
                            'fa fa-download',
                            url_for('tools/db_backup', 'action=download&id=' . $backups['id']),
                            false
                        ); ?></td>
                <td><?php
                    echo $backups['id'] ?></td>

                <td><?php
                    echo $backups['filename']; ?></td>
                <td><?php
                    if (is_file($file_path = DIR_FS_BACKUPS_AUTO . $backups['filename'])) {
                        echo attachments::file_size_convert(filesize($file_path));
                    }
                    ?></td>
                <td><?php
                    echo format_date_time($backups['date_added']) ?></td>
            </tr>
        <?php
        endwhile ?>

        <tbody>
    </table>
</div>

<?php
echo '
  <table width="100%">
    <tr>
      <td>' . $listing_split->display_count() . '</td>
      <td align="right">' . $listing_split->display_links() . '</td>
    </tr>
  </table>';
?>

<br>
<div><?php
    echo TEXT_BACKUP_FOLDER . ': ' . DIR_FS_BACKUPS_AUTO ?></div>
<div><?php
    echo TEXT_CRON_BACKUP . ': ' . DIR_FS_CATALOG . 'cron/backup.php' ?></div>

<script>
    function load_items_listing(listing_container, page) {
        location.href = "<?php echo url_for('tools/db_backup_auto') ?>&page=" + page;
    }
</script>
