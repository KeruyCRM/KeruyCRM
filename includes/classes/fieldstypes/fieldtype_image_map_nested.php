<?php

class fieldtype_image_map_nested
{

    public $options;

    function __construct()
    {
        $this->options = ['title' => TEXT_FIELDTYPE_IMAGE_MAP_NESTED_TITLE];
    }

    function get_configuration()
    {
        $cfg = [];

        $choices = ['' => ''];
        $entities_query = db_query("select id,name from app_entities where parent_id='" . _POST('entities_id') . "'");
        while ($entities = db_fetch_array($entities_query)) {
            $choices[$entities['id']] = $entities['name'];
        }

        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_ENTITY,
            'name' => 'entity_id',
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => [
                'class' => 'form-control input-xlarge required',
                'onChange' => 'fields_types_ajax_configuration(\'fields_in_popup_box\',this.value)'
            ]
        ];

        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_FILES_UPLOAD_SIZE_LIMIT,
            'name' => 'upload_size_limit',
            'type' => 'input',
            'tooltip_icon' => TEXT_FILES_UPLOAD_SIZE_LIMIT_TIP,
            'tooltip' => TEXT_MAX_UPLOAD_FILE_SIZE . ' ' . CFG_SERVER_UPLOAD_MAX_FILESIZE . 'MB ' . TEXT_MAX_UPLOAD_FILE_SIZE_TIP,
            'params' => ['class' => 'form-control input-xsmall']
        ];
        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_PREVIEW_IMAGE_SIZE_IN_LISTING,
            'name' => 'width_in_listing',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        $choices = [
            '6' => '1%',
            '5' => '3%',
            '4' => '6%',
            '3' => '12%',
            '2' => '25%',
            '1' => '50%',
            '0' => '100%',
        ];

        $cfg[TEXT_MAP_SETTINGS][] = [
            'title' => TEXT_SCALE,
            'name' => 'scale',
            'default' => 3,
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => ['class' => 'form-control input-small']
        ];
        $cfg[TEXT_MAP_SETTINGS][] = [
            'title' => TEXT_WIDHT,
            'name' => 'map_width',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];
        $cfg[TEXT_MAP_SETTINGS][] = [
            'title' => TEXT_HEIGHT,
            'name' => 'map_height',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[TEXT_FIELDS][] = [
            'name' => 'fields_in_popup_box',
            'type' => 'ajax',
            'html' => '<script>fields_types_ajax_configuration(\'fields_in_popup_box\',$("#fields_configuration_entity_id").val())</script>'
        ];


        return $cfg;
    }

    function get_ajax_configuration($name, $value)
    {
        $cfg = [];

        switch ($name) {
            case 'fields_in_popup_box':
                $entities_id = (int)$value;

                if (!$entities_id) {
                    return $cfg;
                }

                //fields in popup   
                $exclude_types = [
                    "'fieldtype_action'",
                    "'fieldtype_parent_item_id'",
                    "'fieldtype_related_records'",
                    "'fieldtype_mapbbcode'",
                    "'fieldtype_section'",
                    "'fieldtype_image_map'",
                    "'fieldtype_image_map_nested'"
                ];
                $choices = [];
                $fields_query = db_query(
                    "select * from app_fields where type not in (" . implode(
                        ",",
                        $exclude_types
                    ) . ") and entities_id='" . db_input($entities_id) . "'"
                );
                while ($fields = db_fetch_array($fields_query)) {
                    $choices[$fields['id']] = $fields['name'];
                }

                $cfg[] = [
                    'title' => TEXT_FIELDS_IN_POPUP,
                    'name' => 'fields_in_popup',
                    'default' => '',
                    'type' => 'dropdown',
                    'choices' => $choices,
                    'params' => ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                ];


                //background
                $choices = [];
                $choices[''] = '';
                $fields_query = db_query(
                    "select * from app_fields where type in ('fieldtype_dropdown','fieldtype_radioboxes','fieldtype_autostatus') and entities_id='" . db_input(
                        $entities_id
                    ) . "'"
                );
                while ($fields = db_fetch_array($fields_query)) {
                    $choices[$fields['id']] = $fields['name'];
                }

                $cfg[] = [
                    'title' => TEXT_BACKGROUND_COLOR,
                    'name' => 'background',
                    'tooltip_icon' => TEXT_FIELDTYPE_IMAGE_MAP_BACKGROUND_COLOR_INFO,
                    'type' => 'dropdown',
                    'choices' => $choices,
                    'params' => [
                        'class' => 'form-control input-medium',
                        'onChange' => 'fields_types_ajax_configuration(\'background_icons_box\',this.value)'
                    ]
                ];

                $cfg[] = [
                    'name' => 'background_icons_box',
                    'type' => 'ajax',
                    'html' => '<script>fields_types_ajax_configuration(\'background_icons_box\',$("#fields_configuration_background").val())</script>'
                ];


                break;
            case 'background_icons_box':
                $choices = fields_choices::get_choices($value, false);
                if (count($choices)) {
                    $cfg[] = [
                        'title' => TEXT_ICONS,
                        'type' => 'section',
                        'html' => '<p class="form-section-description">' . TEXT_FIELDTYPE_IMAGE_MAP_ICONS_TIP . '</p>'
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
                break;
        }

        return $cfg;
    }

    function render($field, $obj, $params = [])
    {
        global $uploadify_attachments, $uploadify_attachments_queue, $current_path, $app_user, $app_items_form_name, $public_form, $app_session_token;

        $filename = $obj['field_' . $field['id']];
        $html = '';


        $field_id = $field['id'];

        $uploadify_attachments[$field_id] = [];
        $uploadify_attachments_queue[$field_id] = [];

        if (strlen($obj['field_' . $field['id']]) > 0) {
            $uploadify_attachments[$field_id] = explode(',', $obj['field_' . $field['id']]);
        }

        $timestamp = time();

        $delete_file_url = '';

        if ($app_items_form_name == 'registration_form') {
            $form_token = md5($app_session_token . $timestamp);
            $uploadScript = url_for('users/registration', 'action=attachments_upload&field_id=' . $field_id, true);
            $previewScript = url_for(
                'users/registration',
                'action=attachments_preview&field_id=' . $field_id . '&token=' . $form_token
            );
        } elseif ($app_items_form_name == 'public_form' or (isset($_GET['form_name'])) and $_GET['form_name'] == 'public_form') {
            $public_form['id'] = isset($_GET['public_form_id']) ? _GET('public_form_id') : $public_form['id'];
            $form_token = md5($app_session_token . $timestamp);
            $uploadScript = url_for(
                'ext/public/form',
                'action=attachments_upload&id=' . $public_form['id'] . '&field_id=' . $field_id,
                true
            );
            $previewScript = url_for(
                'ext/public/form',
                'action=attachments_preview&field_id=' . $field_id . '&id=' . $public_form['id'] . '&token=' . $form_token,
                true
            );
        } elseif ($app_items_form_name == 'account_form') {
            $form_token = md5($app_user['id'] . $timestamp);
            $uploadScript = url_for(
                'users/account',
                'action=attachments_upload&path=' . $current_path . '&field_id=' . $field_id,
                true
            );
            $previewScript = url_for(
                'users/account',
                'action=attachments_preview&field_id=' . $field_id . '&path=' . $current_path . '&token=' . $form_token
            );
            $delete_file_url = url_for('users/account', 'action=attachments_delete_in_queue');
        } else {
            $form_token = md5($app_user['id'] . $timestamp);
            $uploadScript = url_for(
                'items/items',
                'action=attachments_upload&path=' . $current_path . '&field_id=' . $field_id,
                true
            );
            $previewScript = url_for(
                'items/items',
                'action=attachments_preview&field_id=' . $field_id . '&path=' . $current_path . '&token=' . $form_token
            );
            $delete_file_url = url_for('items/items', 'action=attachments_delete_in_queue&path=' . $_GET['path']);
        }

        $cfg = new fields_types_cfg($field['configuration']);


        $allowed_extensions = is_array($cfg->get('allowed_extensions')) ? $cfg->get('allowed_extensions') : [
            'gif',
            'jpg',
            'jpeg',
            'png'
        ];

        $mime_types = fieldtype_attachments::get_mime_types();
        $allowed_mime_types = [];
        foreach ($allowed_extensions as $v) {
            foreach ($mime_types[$v] as $vv) {
                $allowed_mime_types[] = $vv;
            }
        }

        $attachments_preview_html = attachments::render_preview(
            $field_id,
            $uploadify_attachments[$field_id],
            $delete_file_url
        );

        $html .= '
        <div class="form-control-static"> 
          <input style="cursor: pointer" type="file" name="uploadifive_attachments_upload_' . $field_id . '" id="uploadifive_attachments_upload_' . $field_id . '" /> 
        </div>

        <div id="uploadifive_queue_list_' . $field_id . '"></div>
        <div id="uploadifive_attachments_list_' . $field_id . '">
          ' . $attachments_preview_html . '        
        </div>
<script>       

function uploadifive_oncomplate_filed_' . $field_id . '()
{
    $(".uploadifive-queue-item.complete").fadeOut();
    $("#uploadifive_attachments_list_' . $field_id . '").append("<div class=\"loading_data\"></div>");
    $("#uploadifive_attachments_list_' . $field_id . '").load("' . $previewScript . '"); 
    $("#uploadifive_queue_list_' . $field_id . '").html("");
}

$(function(){
    $("#uploadifive_attachments_upload_' . $field['id'] . '").uploadifive({
        auto             : true,  
        dnd              : false, 
        fileType         : [\'' . implode("','", $allowed_mime_types) . '\'],
        fileTypeExtra    : "' . implode(',', $allowed_extensions) . '",
        buttonClass      : "btn btn-default btn-upload",
        buttonText       : "<i class=\"fa fa-upload\"></i> ' . TEXT_SELECT_IMAGE . '",				            
        formData       :  {
                                "timestamp" : ' . $timestamp . ',
                                "token"     : "' . $form_token . '",
                                "form_session_token" : "' . $app_session_token . '"		
                            },    
        queueID          : "uploadifive_queue_list_' . $field_id . '",
        fileSizeLimit : "' . (strlen($cfg->get('upload_size_limit')) ? (int)$cfg->get(
                'upload_size_limit'
            ) : CFG_SERVER_UPLOAD_MAX_FILESIZE) . 'MB",
        multi: false,
        uploadScript: "' . $uploadScript . '",
        onUpload: function (filesToUpload)
        {
            
        },
        onUploadComplete: function (file, data)
        {
            uploadifive_oncomplate_filed_' . $field_id . '()              
        },
        onError: function (errorType)
        {
            
        },
        onCancel: function ()
        {
            
        }
    });
})        
</script>    
        ';

        return $html;
    }

    function process($options)
    {
        $attachment = '';


        $attachment = $options['value'];


        return $attachment;
    }

    function output($options)
    {
        $options_cfg = new fields_types_options_cfg($options);

        if (strlen($options['value']) > 0) {
            $file = attachments::parse_filename($options['value']);

            if (isset($options['is_print'])) {
                return '<img width=120 height=120 src=' . url_for(
                        'items/info&path=' . $options['field']['entities_id'],
                        '&action=download_attachment&preview=small&file=' . urlencode(base64_encode($options['value']))
                    ) . '>';
            } elseif (isset($options['is_export']) or isset($options['is_email'])) {
                return $file['name'];
            } else {
                if (isset($options['is_listing'])) {
                    $cfg = new fields_types_cfg($options['field']['configuration']);

                    $fancybox_css_class = 'fancybox' . $options['field']['id'] . time();

                    $img = '<img class="fieldtype_image field_' . $options['field']['id'] . '"   src="' . url_for(
                            'items/info&path=' . $options['path'],
                            '&action=download_attachment&preview=small&file=' . urlencode(
                                base64_encode($options['value'])
                            )
                        ) . '">';

                    $width = (isset($options['is_listing']) ? (strlen($cfg->get('width_in_listing')) ? $cfg->get(
                        'width_in_listing'
                    ) : 250) : (strlen($cfg->get('width')) ? $cfg->get('width') : 250));

                    $html = '
          <div class="fieldtype-image-container" style="width: ' . $width . 'px; max-height: ' . $width . 'px;">' .
                        link_to(
                            $img,
                            url_for(
                                'items/info&path=' . $options['path'],
                                '&action=preview_attachment_image&file=' . urlencode(base64_encode($options['value']))
                            ),
                            ['class' => $fancybox_css_class]
                        ) . '
           </div> 
          ';

                    if (!isset($options['is_listing'])) {
                        $html .= '
          	<div class="fieldtype-image-filename" style="width: ' . $width . 'px">
              ' . link_to(
                                '<i class="fa fa-download"></i> ' . $file['name'],
                                url_for(
                                    'items/info',
                                    'path=' . $options['path'] . '&action=download_attachment&file=' . urlencode(
                                        base64_encode($options['value'])
                                    )
                                )
                            ) . '
            </div>';
                    }

                    $html .= '
          <script>
            $(document).ready(function() {
            	$(".' . $fancybox_css_class . '").fancybox({
                    type: "ajax",
                    beforeLoad : function() { 
                        this.href = this.href+\'&windowWidth=\' + $(window).width()+\'&windowHeight=\' + $(window).height();
                    }
                });
            });
          </script>
          ';

                    return $html;
                } else {
                    return $this->output_map($options);
                }
            }
        } else {
            return '';
        }
    }

    function output_map($options)
    {
        $cfg = new fields_types_cfg($options['field']['configuration']);

        $file = attachments::parse_filename($options['value']);

        $map_filepath = $this->prepare_map_files($options['field']['id'], $options['item']['id'], $file);

        if (is_file($map_filepath)) {
            $width_css = (strlen($cfg->get('map_width')) ? 'style="max-width: ' . $cfg->get('map_width') . 'px"' : '');
            $height_css = (strlen($cfg->get('map_height')) ? 'style="height: ' . $cfg->get('map_height') . 'px"' : '');

            return '
                <div class="image-map-iframe-box image-map-iframe-box-' . $options['field']['id'] . '" ' . $width_css . '>
                    <div class="image-map-nested-fullscreen-action" data_field_id="' . $options['field']['id'] . '"><i class="fa fa-arrows-alt"></i></div>    
                    <iframe src="' . url_for(
                    'image_map/nested',
                    'path=' . $options['path'] . '&map_filename=' . urlencode(
                        $file['name']
                    ) . '&fields_id=' . $options['field']['id']
                ) . '" class="image-map-iframe image-map-iframe-' . $options['field']['id'] . '" scrolling="no" frameborder="no" ' . $height_css . '></iframe>
                </div>';
        } else {
            return '';
        }
    }

    function prepare_map_files($field_id, $item_id, $file)
    {
        if (!is_dir(DIR_WS_UPLOADS . 'maps_nested')) {
            mkdir(DIR_WS_UPLOADS . 'maps_nested');
        }

        if (!is_dir(DIR_WS_UPLOADS . 'maps_nested/' . $field_id)) {
            mkdir(DIR_WS_UPLOADS . 'maps_nested/' . $field_id);
        }

        if (!is_dir(DIR_WS_UPLOADS . 'maps_nested/' . $field_id . '/' . $item_id)) {
            mkdir(DIR_WS_UPLOADS . 'maps_nested/' . $field_id . '/' . $item_id);
        }

        $map_dir = DIR_WS_UPLOADS . 'maps_nested/' . $field_id . '/' . $item_id . '/';


        $map_filepath = $map_dir . $file['name'];

        if (!is_file($map_filepath)) {
            //delete exist files in map dir
            foreach (glob($map_dir . '/*') as $v) {
                if (is_file($v)) {
                    unlink($v);
                }
            }

            if (copy($file['file_path'], $map_filepath)) {
                require_once('includes/libs/openzoom/GdThumb.php');
                require_once('includes/libs/openzoom/OzDeepzoomImageCreator.php');
                require_once('includes/libs/openzoom/OzDeepzoomDescriptor.php');

                //prepare image               
                $mapCreator = @new Flexphperia_OzDeepzoomImageCreator($map_filepath, $map_dir);
                @$mapCreator->create();
            }
        }

        return $map_filepath;
    }

}
