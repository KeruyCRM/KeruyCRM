<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_image_map
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_IMAGE_MAP_TITLE, 'has_choices' => true];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DEFAULT_TEXT,
            'name' => 'default_text',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_DEFAULT_TEXT_INFO,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
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

        $cfg[] = ['title' => \K::$fw->TEXT_MAP_SETTINGS, 'type' => 'section'];

        $choices = [
            '6' => '1%',
            '5' => '3%',
            '4' => '6%',
            '3' => '12%',
            '2' => '25%',
            '1' => '50%',
            '0' => '100%',
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_SCALE,
            'name' => 'scale',
            'default' => 3,
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_WIDTH,
            'name' => 'map_width',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_HEIGHT,
            'name' => 'map_height',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        //fields in popup
        $exclude_types = \K::model()->quoteToString([
            'fieldtype_action',
            'fieldtype_parent_item_id',
            'fieldtype_related_records',
            'fieldtype_mapbbcode',
            'fieldtype_section',
            'fieldtype_image_map',
            'fieldtype_image_map_nested'
        ]);

        $choices = [];
        /*$fields_query = db_query(
            "select * from app_fields where type not in (" . implode(
                ",",
                $exclude_types
            ) . ") and entities_id='" . db_input($_POST['entities_id']) . "'"
        );*/

        $fields_query = \K::model()->db_fetch('app_fields', [
            'type not in (' . $exclude_types . ') and entities_id = ?',
            \K::$fw->POST['entities_id']
        ], [], 'id,name');

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $fields = $fields->cast();

            $choices[$fields['id']] = $fields['name'];
        }

        $cfg[] = [
            'title' => \K::$fw->TEXT_FIELDS_IN_POPUP,
            'name' => 'fields_in_popup',
            'default' => '',
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
        ];

        //background
        $choices = [];
        $choices[''] = '';
        $typeIn = \K::model()->quoteToString(['fieldtype_dropdown', 'fieldtype_radioboxes']);

        /*$fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_dropdown','fieldtype_radioboxes') and entities_id='" . db_input(
                $_POST['entities_id']
            ) . "'"
        );*/

        $fields_query = \K::model()->db_fetch('app_fields', [
            'type in (' . $typeIn . ') and entities_id = ?',
            \K::$fw->POST['entities_id']
        ], [], 'id,name');

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $fields = $fields->cast();

            $choices[$fields['id']] = $fields['name'];
        }

        if (count($choices) > 1) {
            $cfg[] = [
                'title' => \K::$fw->TEXT_BACKGROUND_COLOR,
                'name' => 'background',
                'tooltip_icon' => \K::$fw->TEXT_FIELDTYPE_IMAGE_MAP_BACKGROUND_COLOR_INFO,
                'type' => 'dropdown',
                'choices' => $choices,
                'params' => [
                    'class' => 'form-control input-medium',
                    'onChange' => 'fields_types_ajax_configuration(\'background_icons\',this.value)'
                ]
            ];

            $cfg[] = [
                'name' => 'background_icons',
                'type' => 'ajax',
                'html' => '<script>fields_types_ajax_configuration(\'background_icons\',$("#fields_configuration_background").val())</script>'
            ];
        }

        return $cfg;
    }

    public function get_ajax_configuration($name, $value)
    {
        $cfg = [];

        switch ($name) {
            case 'background_icons':
                if (strlen($value)) {
                    $choices = \Models\Main\Fields_choices::get_choices($value, false);
                    if (count($choices)) {
                        $cfg[] = [
                            'title' => \K::$fw->TEXT_ICONS,
                            'type' => 'section',
                            'html' => '<p class="form-section-description">' . \K::$fw->TEXT_FIELDTYPE_IMAGE_MAP_ICONS_TIP . '</p>'
                        ];

                        foreach ($choices as $k => $v) {
                            $cfg[] = [
                                'title' => $v,
                                'name' => 'icon_' . $k,
                                'type' => 'file',
                                'params' => ['class' => 'form-control input-large']
                            ];
                        }
                    }
                }
                break;
        }

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control ' . $cfg->get(
                    'width'
                ) . ' field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '') . ($cfg->get(
                    'use_search'
                ) == 1 ? ' chosen-select' : ''),
            'data-placeholder' => \K::$fw->TEXT_SELECT_SOME_VALUES
        ];

        $choices = [];

        if ($field['is_required'] == 0 or strlen($cfg->get('default_text'))) {
            $choices[''] = $cfg->get('default_text');
        }

        /*$choices_query = db_query(
            "select * from app_fields_choices where fields_id = '" . db_input(
                $field['id']
            ) . "' and parent_id=0 order by sort_order, name"
        );*/

        $choices_query = \K::model()->db_fetch('app_fields_choices', [
            'fields_id = ? and parent_id = 0',
            $field['id']
        ], ['order' => 'sort_order,name'], 'id,name');

        //while ($v = db_fetch_array($choices_query)) {
        foreach ($choices_query as $v) {
            $v = $v->cast();

            $choices[$v['id']] = $v['name'];
        }

        $default_id = \Models\Main\Fields_choices::get_default_id($field['id']);

        $value = ($obj['field_' . $field['id']] > 0 ? $obj['field_' . $field['id']] : ($params['form'] == 'comment' ? '' : $default_id));

        return \Helpers\Html::select_tag('fields[' . $field['id'] . ']', $choices, $value, $attributes);
    }

    public function process($options)
    {
        return $options['value'];
    }

    public function output($options)
    {
        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        //check if value exist
        if ($options['value'] == 0 or !isset(\K::$fw->app_choices_cache[$options['value']])) {
            return '';
        }

        if (isset($options['is_listing']) or isset($options['is_export'])) {
            return \Models\Main\Fields_choices::render_value($options['value']);
        } else {
            $width_css = (strlen($cfg->get('map_width')) ? 'style="max-width: ' . $cfg->get('map_width') . 'px"' : '');
            $height_css = (strlen($cfg->get('map_height')) ? 'style="height: ' . $cfg->get('map_height') . 'px"' : '');

            return '
      		<div class="image-map-iframe-box" ' . $width_css . '>
      			<iframe src="' . \Helpers\Urls::url_for(
                    'main/image_map/single',
                    'path=' . $options['path'] . '&map_id=' . $options['value'] . '&fields_id=' . $options['field']['id']
                ) . '" class="image-map-iframe" scrolling="no" frameborder="no" ' . $height_css . '></iframe>
      		</div>';
        }
    }

    public function reports_query($options)
    {
        return \Models\Main\Reports\Reports::getReportsQuery($options);
    }

    public static function upload_map_filename($choices_id)
    {
        //upload
        if (isset(\K::$fw->FILES['filename']['name'])) {
            if (strlen(\K::$fw->FILES['filename']['name']) > 0) {
                $map_dir = \K::$fw->DIR_WS_UPLOADS . 'maps/' . $choices_id;

                //check dir
                if (!is_dir($map_dir)) {
                    mkdir($map_dir);
                }

                //delete exist files in map dir
                foreach (glob($map_dir . '/*') as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }

                $filename = str_replace(' ', '_', \K::$fw->FILES['filename']['name']);
                $upload_filepath = $map_dir . '/' . $filename;

                if (move_uploaded_file(\K::$fw->FILES['filename']['tmp_name'], $upload_filepath)) {
                    //update db
                    /*db_query(
                        "update app_fields_choices set filename = '" . db_input(
                            $filename
                        ) . "' where id = '" . db_input($choices_id) . "'"
                    );*/

                    \K::model()->db_update('app_fields_choices', ['filename' => $filename], ['id = ?', $choices_id]);

                    require('app/libs/openzoom/GdThumb.php');
                    require('app/libs/openzoom/OzDeepzoomImageCreator.php');
                    require('app/libs/openzoom/OzDeepzoomDescriptor.php');

                    //prepare image
                    $mapCreator = @new \Flexphperia_OzDeepzoomImageCreator($upload_filepath, $map_dir);
                    @$mapCreator->create();
                }
            }
        }
    }

    public static function delete_map_files($choices_id)
    {
        $map_dir = \K::$fw->DIR_WS_UPLOADS . 'maps/' . $choices_id;

        if (is_dir($map_dir)) {
            //delete exist files in map dir
            foreach (glob($map_dir . '/*') as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            rmdir($map_dir);
        }

        //remove markers
        //db_query("delete from app_image_map_markers where map_id='" . $choices_id . "'");
        \K::model()->db_delete_row('app_image_map_markers', $choices_id, 'map_id');
    }
}