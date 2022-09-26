<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

require(\K::$fw->CFG_PATH_TO_PHPSPREADSHEET);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Import_preview extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();
    }

    public function index()
    {
        if (!\Models\Main\Users\Users::has_access('import') or !strlen(\K::$fw->app_path)) {
            \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
        }

        \K::$fw->multilevel_import = (\K::$fw->POST['multilevel_import'] ?? 0);

        //check heading fields
        if (\K::$fw->multilevel_import > 0) {
            $choices = [];
            $choices[] = \K::$fw->multilevel_import;
            foreach (\Models\Main\Entities::get_parents(\K::$fw->multilevel_import) as $entity_id) {
                $choices[] = $entity_id;

                if ($entity_id == \K::$fw->current_entity_id) {
                    break;
                }
            }

            $choices = array_reverse($choices);

            foreach ($choices as $entity_id) {
                if (!\Models\Main\Fields::get_heading_id($entity_id)) {
                    \K::flash()->addMessage(
                        sprintf(
                            \K::$fw->TEXT_MULTI_LEVEL_IMPORT_HEADING_ERROR,
                            \Models\Main\Entities::get_name_by_id($entity_id)
                        ),
                        'error'
                    );
                    \Helpers\Urls::redirect_to('main/items/items', 'path=' . \K::$fw->app_path);
                }
            }
        }

        \K::$fw->worksheet = [];

        if (strlen($filename = \K::$fw->FILES['filename']['name']) > 0) {
            //rename file (issue with HTML.php:495 if file have UTF symbols)
            $filename = 'import_data.' . (strstr($filename, '.xls') ? 'xls' : 'xlsx');

            if (move_uploaded_file(\K::$fw->FILES['filename']['tmp_name'], \K::$fw->DIR_WS_UPLOADS . $filename)) {
                $objPHPExcel = IOFactory::load(\K::$fw->DIR_WS_UPLOADS . $filename);

                unlink(\K::$fw->DIR_WS_UPLOADS . $filename);

                $objWorksheet = $objPHPExcel->getActiveSheet();

                $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
                $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'

                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString(
                    $highestColumn
                ); // e.g. 5

                for ($row = 0; $row <= $highestRow; ++$row) {
                    $is_empty_row = true;
                    $worksheet_cols = [];

                    for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                        $value = trim($objWorksheet->getCellByColumnAndRow($col, $row)->getValue());
                        $worksheet_cols[$col] = $value;

                        if (strlen($value) > 0) {
                            $is_empty_row = false;
                        }
                    }

                    if (!$is_empty_row) {
                        \K::$fw->worksheet[] = $worksheet_cols;
                    }
                }
            } else {
                \K::flash()->addMessage(\K::$fw->TEXT_FILE_NOT_LOADED, 'warning');
                \Helpers\Urls::redirect_to('main/items/items', 'path=' . \K::$fw->app_path);
            }
        }

        if (isset(\K::$fw->POST['import_template'])) {
            if (\K::$fw->POST['import_template'] > 0) {
                /*$templates_query = db_query(
                    "select * from app_ext_import_templates where id='" . (int)\K::$fw->POST['import_template'] . "'"
                );*/

                $templates = \K::model()->db_fetch_one('app_ext_import_templates', [
                    'id = ?',
                    \K::$fw->POST['import_template']
                ]);

                if ($templates) {
                    $import_fields_list = (strlen($templates['import_fields']) ? json_decode(
                        $templates['import_fields'],
                        true
                    ) : []);

                    \K::$fw->import_fields = [];

                    foreach ($import_fields_list as $k => $v) {
                        if ($v > 0) {
                            \K::$fw->import_fields[$k + 1] = $v;
                        }
                    }
                }
            }
        }

        \K::$fw->app_breadcrumb[] = ['title' => \K::$fw->TEXT_IMPORT];

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'import_preview.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }
}