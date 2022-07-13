<h3 class="page-title"><?= \K::$fw->TEXT_PDF_EXPORT_FONTS ?></h3>
<p><?= \K::$fw->TEXT_PDF_EXPORT_FONTS_INFO ?></p>

<?php
$rootDir = $fontDir = '';
$fonts_list = require \K::$fw->CFG_PATH_TO_DOMPDF_FONTS . '/dompdf_font_family_cache.php';

echo \Helpers\Html::button_tag(\K::$fw->TEXT_ADD, \Helpers\Urls::url_for('main/configuration/pdf_form'), true);
?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?= \K::$fw->TEXT_NAME ?></th>
            <th><?= \K::$fw->TEXT_FILENAME ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($fonts_list as $font_name => $font_types) {
            echo '
                <tr>
                    <td>' . $font_name . '</td>
                    <td>' . $font_types['normal'] . '</td>
                </tr>
                ';
        }
        ?>
        </tbody>
    </table>
</div>
<?= \K::$fw->TEXT_FONTS_FOLDER . ': ' . \K::$fw->CFG_PATH_TO_DOMPDF_FONTS ?>
