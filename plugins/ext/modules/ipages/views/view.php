<?php
$ipage = db_find('app_ext_ipages', $_GET['id']); ?>

    <h3 class="page-title"><?php
        echo $ipage['name'] ?></h3>

    <div class="ipage-description">
        <?php

        echo ipages::prepare_attachments_in_text($ipage['description'], $ipage['attachments']);

        $output_options = [
            'class' => 'fieldtype_attachments',
            'value' => $ipage['attachments'],
            'path' => 1,
            'is_ipages' => _GET('id'),
            'field' => ['entities_id' => 1, 'configuration' => ''],
            'item' => ['id' => $ipage['id']]
        ];

        echo fields_types::output($output_options);

        ?>
    </div>

<?php
echo(strlen($ipage['html_code']) ? $ipage['html_code'] : '') ?>