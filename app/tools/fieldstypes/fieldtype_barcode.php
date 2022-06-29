<?php

namespace Tools\FieldsTypes;

require('includes/libs/tcpdf_min_barcode_generator/6.3.2/tcpdf_barcodes_2d.php');

require('includes/libs/php-barcode-generator-master/src/Exceptions/BarcodeException.php');
require('includes/libs/php-barcode-generator-master/src/Exceptions/InvalidCharacterException.php');
require('includes/libs/php-barcode-generator-master/src/Exceptions/InvalidCheckDigitException.php');
require('includes/libs/php-barcode-generator-master/src/Exceptions/InvalidFormatException.php');
require('includes/libs/php-barcode-generator-master/src/Exceptions/InvalidLengthException.php');
require('includes/libs/php-barcode-generator-master/src/Exceptions/UnknownTypeException.php');
require('includes/libs/php-barcode-generator-master/src/Helpers/BinarySequenceConverter.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeInterface.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeCodabar.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeCode11.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeCode128.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeCode128A.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeCode128B.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeCode128C.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeCode39.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeCode39Checksum.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeCode39Extended.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeCode39ExtendedChecksum.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeCode93.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeEanUpcBase.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeEan13.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeEan8.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeIntelligentMailBarcode.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeInterleaved25Checksum.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeInterleaved25.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeRms4cc.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeKix.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeMsiChecksum.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeMsi.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypePharmacode.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypePharmacodeTwoCode.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypePostnet.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypePlanet.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeStandard2of5.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeStandard2of5Checksum.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeUpcA.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeUpcE.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeUpcExtension2.php');
require('includes/libs/php-barcode-generator-master/src/Types/TypeUpcExtension5.php');

class Fieldtype_barcode
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_BARCODE_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_WIDTH,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => [
                'input-small' => \K::$fw->TEXT_INPUT_SMALL,
                'input-medium' => \K::$fw->TEXT_INPUT_MEDIUM,
                'input-large' => \K::$fw->TEXT_INPUT_LARGE,
                'input-xlarge' => \K::$fw->TEXT_INPUT_XLARGE
            ],
            'tooltip_icon' => \K::$fw->TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_IS_UNIQUE_FIELD_VALUE,
            'name' => 'is_unique',
            'type' => 'dropdown',
            'choices' => fields_types::get_is_unique_choices(_POST('entities_id')),
            'tooltip_icon' => \K::$fw->TEXT_IS_UNIQUE_FIELD_VALUE_TIP,
            'params' => ['class' => 'form-control input-large']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_ERROR_MESSAGE,
            'name' => 'unique_error_msg',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_UNIQUE_FIELD_VALUE_ERROR_MSG_TIP,
            'tooltip' => \K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->TEXT_UNIQUE_FIELD_VALUE_ERROR,
            'params' => ['class' => 'form-control input-xlarge']
        ];

        $choices = [
            'TYPE_CODE_39' => 'CODE_39',
            'TYPE_CODE_39_CHECKSUM' => 'CODE_39_CHECKSUM',
            'TYPE_CODE_39E' => 'CODE_39E',
            'TYPE_CODE_39E_CHECKSUM' => 'CODE_39E_CHECKSUM',
            'TYPE_CODE_93' => 'CODE_93',
            'TYPE_STANDARD_2_5' => 'STANDARD_2_5',
            'TYPE_STANDARD_2_5_CHECKSUM' => 'STANDARD_2_5_CHECKSUM',
            'TYPE_INTERLEAVED_2_5' => 'INTERLEAVED_2_5',
            'TYPE_INTERLEAVED_2_5_CHECKSUM' => 'INTERLEAVED_2_5_CHECKSUM',
            'TYPE_CODE_128' => 'CODE_128',
            'TYPE_CODE_128_A' => 'CODE_128_A',
            'TYPE_CODE_128_B' => 'CODE_128_B',
            'TYPE_CODE_128_C' => 'CODE_128_C',
            'TYPE_EAN_2' => 'EAN_2',
            'TYPE_EAN_5' => 'EAN_5',
            'TYPE_EAN_8' => 'EAN_8',
            'TYPE_EAN_13' => 'EAN_13',
            'TYPE_UPC_A' => 'UPC_A',
            'TYPE_UPC_E' => 'UPC_E',
            'TYPE_MSI' => 'MSI',
            'TYPE_MSI_CHECKSUM' => 'MSI_CHECKSUM',
            'TYPE_POSTNET' => 'POSTNET',
            'TYPE_PLANET' => 'PLANET',
            'TYPE_RMS4CC' => 'RMS4CC',
            'TYPE_KIX' => 'KIX',
            'TYPE_IMB' => 'IMB',
            'TYPE_CODABAR' => 'CODABAR',
            'TYPE_CODE_11' => 'CODE_11',
            'TYPE_PHARMA_CODE' => 'PHARMA_CODE',
            'TYPE_PHARMA_CODE_TWO_TRACKS' => 'PHARMA_CODE_TWO_TRACKS',
            'PDF417' => 'PDF417',
        ];

        $cfg[\K::$fw->TEXT_BARCODE][] = [
            'title' => \K::$fw->TEXT_FIELDTYPE_BARCODE_TYPE,
            'name' => 'barcode_type',
            'type' => 'dropdown',
            'choices' => $choices,
            'default' => 'TYPE_CODE_128',
            'tooltip_icon' => \K::$fw->TEXT_FIELDTYPE_BARCODE_ACCEPTED_TYPE_TIP,
            'params' => ['class' => 'form-control input-medium chosen-select']
        ];

        $cfg[\K::$fw->TEXT_BARCODE][] = [
            'title' => \K::$fw->TEXT_FIELDTYPE_BARCODE_METHOD_GENERATING . fields::get_available_fields_helper(
                    $_POST['entities_id'],
                    'fields_configuration_template'
                ),
            'name' => 'template',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_FIELDTYPE_BARCODE_METHOD_GENERATING_TIP_ICON,
            'tooltip' => \K::$fw->TEXT_FIELDTYPE_BARCODE_METHOD_GENERATING_TIP . '<br>' . \K::$fw->TEXT_ENTER_TEXT_PATTERN_DATE_INFO,
            'params' => ['class' => 'form-control input-large']
        ];

        $cfg[\K::$fw->TEXT_EXPORT][] = [
            'title' => \K::$fw->TEXT_HEIGHT,
            'name' => 'height',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_FIELDTYPE_BARCODE_HEIGHT_TIP,
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[\K::$fw->TEXT_EXPORT][] = [
            'title' => \K::$fw->TEXT_DISPLAY_FIELD_VALUE,
            'name' => 'display_field_value',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_FIELDTYPE_BARCODE_DISPLAY_TIP
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control ' . $cfg->get('width') .
                ' fieldtype_input field_' . $field['id'] .
                ($field['is_heading'] == 1 ? ' autofocus' : '') .
                ($field['is_required'] == 1 ? ' required' : '') .
                ($cfg->get('is_unique') > 0 ? ' is-unique' : '') .
                (strlen($cfg->get('template')) ? ' atuogenerate-value-by-template' : '')
        ];

        $attributes = fields_types::prepare_uniquer_error_msg_param($attributes, $cfg);

        //if entered temlate then barcode will be autogenerate
        if (strlen($cfg->get('template'))) {
            $attributes['readonly'] = true;
            $attributes['title'] = \K::$fw->TEXT_BARCODE_GENERATED_AUTOMATICALLY;

            $script = '
                <script>
                    $("#fields_' . $field['id'] . '").scannerDetection({                    
                        onComplete: function(barcode, qty){ $(this).val("") },                    
                    });
                </script>
                ';
        } else {
            $script = '
                <script>
                    $("#fields_' . $field['id'] . '").scannerDetection({                    
                        onComplete: function(barcode, qty){  },                    
                    });
                </script>
                ';
        }

        $submodal_url = url_for('dashboard/barcodescan', 'field_id=' . $field['id'] . '&is_submodal=1');

        $html = '
            <div class="input-group ' . $cfg->get('width') . '">
                ' . input_tag('fields[' . $field['id'] . ']', $obj['field_' . $field['id']], $attributes) . '
                ' . (is_mobile(
            ) ? '<span class="input-group-btn"><button class="btn btn-default btn-submodal-open" data-submodal-url="' . $submodal_url . '" type="button"><i class="fa fa-barcode" aria-hidden="true"></i></button></span>' : '') . '
            </div>
            ' . $script;

        return $html;
    }

    public function process($options)
    {
        return db_prepare_input($options['value']);
    }

    public function output($options)
    {
        if (isset($options['is_print']) and strlen($options['value'])) {
            $cfg = new fields_types_cfg($options['field']['configuration']);

            $height = (strlen($cfg->get('height')) ? $cfg->get('height') : 30);

            $type = (strlen($cfg->get('barcode_type')) ? $cfg->get('barcode_type') : 'TYPE_CODE_128');

            if ($type == 'PDF417') {
                $barcodeobj = new TCPDF2DBarcode(trim($options['value']), 'PDF417');

                //$barcodeobj->getBarcodePNG();

                $html = '<img src="data:image/png;base64,' . base64_encode($barcodeobj->getBarcodePngData(2, 1)) . '">';
            } else {
                $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
                $generator->useGd();

                $generated = $generator->getBarcode(
                    $options['value'],
                    self::get_barcode_generator_type($generator, $type),
                    1.5,
                    $height
                );

                $html = '<img src="data:image/png;base64,' . base64_encode($generated) . '">';
            }

            if ($cfg->get('display_field_value') == 1) {
                $html = '<table align="center"><tr><td>' . $html . '</td></tr><tr><td align="center">' . $options['value'] . '</td></tr></table>';
            }

            return $html;
        } else {
            return $options['value'];
        }
    }

    public static function get_barcode_generator_type($generator, $type)
    {
        switch ($type) {
            case 'TYPE_CODE_39':
                return $generator::TYPE_CODE_39;
                break;
            case 'TYPE_CODE_39_CHECKSUM':
                return $generator::TYPE_CODE_39_CHECKSUM;
                break;
            case 'TYPE_CODE_39E':
                return $generator::TYPE_CODE_39E;
                break;
            case 'TYPE_CODE_39E_CHECKSUM':
                return $generator::TYPE_CODE_39E_CHECKSUM;
                break;
            case 'TYPE_CODE_93':
                return $generator::TYPE_CODE_93;
                break;
            case 'TYPE_STANDARD_2_5':
                return $generator::TYPE_STANDARD_2_5;
                break;
            case 'TYPE_STANDARD_2_5_CHECKSUM':
                return $generator::TYPE_STANDARD_2_5_CHECKSUM;
                break;
            case 'TYPE_INTERLEAVED_2_5':
                return $generator::TYPE_INTERLEAVED_2_5;
                break;
            case 'TYPE_INTERLEAVED_2_5_CHECKSUM':
                return $generator::TYPE_INTERLEAVED_2_5_CHECKSUM;
                break;
            case 'TYPE_CODE_128':
                return $generator::TYPE_CODE_128;
                break;
            case 'TYPE_CODE_128_A':
                return $generator::TYPE_CODE_128_A;
                break;
            case 'TYPE_CODE_128_B':
                return $generator::TYPE_CODE_128_B;
                break;
            case 'TYPE_CODE_128_C':
                return $generator::TYPE_CODE_128_C;
                break;
            case 'TYPE_EAN_2':
                return $generator::TYPE_EAN_2;
                break;
            case 'TYPE_EAN_5':
                return $generator::TYPE_EAN_5;
                break;
            case 'TYPE_EAN_8':
                return $generator::TYPE_EAN_8;
                break;
            case 'TYPE_EAN_13':
                return $generator::TYPE_EAN_13;
                break;
            case 'TYPE_UPC_A':
                return $generator::TYPE_UPC_A;
                break;
            case 'TYPE_UPC_E':
                return $generator::TYPE_UPC_E;
                break;
            case 'TYPE_MSI':
                return $generator::TYPE_MSI;
                break;
            case 'TYPE_MSI_CHECKSUM':
                return $generator::TYPE_MSI_CHECKSUM;
                break;
            case 'TYPE_POSTNET':
                return $generator::TYPE_POSTNET;
                break;
            case 'TYPE_PLANET':
                return $generator::TYPE_PLANET;
                break;
            case 'TYPE_RMS4CC':
                return $generator::TYPE_RMS4CC;
                break;
            case 'TYPE_KIX':
                return $generator::TYPE_KIX;
                break;
            case 'TYPE_IMB':
                return $generator::TYPE_IMB;
                break;
            case 'TYPE_CODABAR':
                return $generator::TYPE_CODABAR;
                break;
            case 'TYPE_CODE_11':
                return $generator::TYPE_CODE_11;
                break;
            case 'TYPE_PHARMA_CODE':
                return $generator::TYPE_PHARMA_CODE;
                break;
            case 'TYPE_PHARMA_CODE_TWO_TRACKS':
                return $generator::TYPE_PHARMA_CODE_TWO_TRACKS;
                break;
            default:
                die('Barcode type "' . $type . '" is not supported. Please check field configuration.');
                break;
        }
    }

    public static function update_items_fields($entities_id, $items_id, $item_info)
    {
        global $app_fields_cache;

        if (isset($app_fields_cache[$entities_id])) {
            foreach ($app_fields_cache[$entities_id] as $fields) {
                if ($fields['type'] == 'fieldtype_barcode' and !strlen($item_info['field_' . $fields['id']])) {
                    $cfg = new fields_types_cfg($fields['configuration']);

                    //skip empyt template
                    if (!strlen($cfg->get('template'))) {
                        continue;
                    }

                    $fieldtype_text_pattern = new fieldtype_text_pattern();
                    $value = $fieldtype_text_pattern->output_singe_text(
                        $cfg->get('template'),
                        $entities_id,
                        $item_info
                    );

                    if (preg_match('/\[auto:(\d+)\]/', $value, $matches)) {
                        $rand_str = '';
                        for ($i = 0; $i < $matches[1]; $i++) {
                            $rand_str .= rand(0, 9);
                        }
                        $value = str_replace($matches[0], $rand_str, $value);
                    }

                    //echo $value;                    
                    //exit();

                    db_query(
                        "update app_entity_" . $entities_id . " set field_" . $fields['id'] . " = '" . db_input(
                            $value
                        ) . "' where id='" . $items_id . "'"
                    );
                }
            }
        }
    }
}