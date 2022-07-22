<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<h3 class="page-title"><?= \K::$fw->TEXT_HEADING_DB_BACKUP ?></h3>

<div>
    <?= \Helpers\Html::button_tag(
        \K::$fw->TEXT_BUTTON_CREATE_BACKUP,
        \Helpers\Urls::url_for('main/tools/db_backup_form')
    ) . ' ' .
    \Helpers\Html::button_tag(
        \K::$fw->TEXT_MENU_DATABASE_EXPORT,
        \Helpers\Urls::url_for('main/tools/db_export'),
        true,
        ['class' => 'btn btn-default']
    ) . ' ' .
    \Helpers\Html::button_tag(
        '<i class="fa fa-repeat" aria-hidden="true"></i> ' . \K::$fw->TEXT_BUTTON_DB_RESTORE_FROM_FILE,
        \Helpers\Urls::url_for('main/tools/db_restore_file'),
        true,
        ['class' => 'btn btn-default']
    );
    ?>
</div>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?= \K::$fw->TEXT_ACTION ?></th>
            <th><?= \K::$fw->TEXT_ID ?></th>
            <th><?= \K::$fw->TEXT_FILENAME ?></th>
            <th><?= \K::$fw->TEXT_SIZE ?></th>
            <th><?= \K::$fw->TEXT_DATE_ADDED ?></th>
            <th><?= \K::$fw->TEXT_CREATED_BY ?></th>
            <th width="100%"><?= \K::$fw->TEXT_COMMENT ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (count(\K::$fw->backups_query) == 0) {
            echo '<tr><td colspan="7">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td></tr>';
        } ?>
        <?php

        //while ($backups = db_fetch_array($backups_query)):
        foreach (\K::$fw->backups_query as $backups):
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
                        \Helpers\Urls::url_for('main/tools/db_backup/download', '&id=' . $backups['id']),
                        false
                    ); ?></td>
                <td><?= $backups['id'] ?></td>

                <td><?= $backups['filename']; ?></td>
                <td><?php
                    if (is_file($file_path = \K::$fw->DIR_FS_BACKUPS . $backups['filename'])) {
                        echo \Tools\Attachments::file_size_convert(filesize($file_path));
                    }
                    ?></td>
                <td><?= \Helpers\App::format_date_time($backups['date_added']) ?></td>
                <td><?php
                    echo($backups['users_id'] > 0 ? \Models\Main\Users\Users::get_name_by_id(
                        $backups['users_id']
                    ) : \K::$fw->TEXT_BACKUP_TYPE_AUTO) ?></td>
                <td class="white-space-normal"><?= nl2br($backups['description']) ?></td>
            </tr>
        <?php
        endforeach; ?>

        <tbody>
    </table>
</div>

<?= '
  <table width="100%">
    <tr>
      <td>' . \K::$fw->listing_split->display_count() . '</td>
      <td align="right">' . \K::$fw->listing_split->display_links() . '</td>
    </tr>
  </table>';
?>

<div><?= \K::$fw->TEXT_BACKUP_FOLDER . ': ' . \K::$fw->DIR_FS_BACKUPS ?></div>

<script>
    function load_items_listing(listing_container, page) {
        location.href = "<?= \Helpers\Urls::url_for('main/tools/db_backup') ?>&page=" + page;
    }
</script>