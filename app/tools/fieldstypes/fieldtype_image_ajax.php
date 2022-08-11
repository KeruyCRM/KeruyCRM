<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_image_ajax
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_IMAGE_AJAX_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_ALLOW_CHANGE_FILE_NAME,
            'name' => 'allow_change_file_name',
            'type' => 'checkbox'
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_FILES_UPLOAD_SIZE_LIMIT,
            'name' => 'upload_size_limit',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_FILES_UPLOAD_SIZE_LIMIT_TIP,
            'tooltip' => \K::$fw->TEXT_MAX_UPLOAD_FILE_SIZE . ' ' . \K::$fw->CFG_SERVER_UPLOAD_MAX_FILESIZE . 'MB ' . \K::$fw->TEXT_MAX_UPLOAD_FILE_SIZE_TIP,
            'params' => ['class' => 'form-control input-xsmall']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_PREVIEW_IMAGE_SIZE,
            'name' => 'width',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_PREVIEW_IMAGE_SIZE_TIP,
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_PREVIEW_IMAGE_SIZE_IN_LISTING,
            'name' => 'width_in_listing',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        $choices = [
            'gif' => 'gif',
            'jpg' => 'jpg',
            'jpeg' => 'jpeg',
            'png' => 'png',
        ];
        $cfg[] = [
            'title' => \K::$fw->TEXT_ALLOWED_EXTENSIONS,
            'name' => 'allowed_extensions',
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => ['class' => 'form-control chosen-select input-large', 'multiple' => 'multiple']
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $html = '';

        $field_id = $field['id'];

        \K::$fw->uploadify_attachments[$field_id] = [];
        \K::$fw->uploadify_attachments_queue[$field_id] = [];

        if (strlen($obj['field_' . $field['id']]) > 0) {
            \K::$fw->uploadify_attachments[$field_id] = explode(',', $obj['field_' . $field['id']]);
        }

        $delete_file_url = '';
        $form_token = \K::security()->getAppToken(64);

        if (\K::$fw->app_items_form_name == 'registration_form') {
            //$form_token = md5($app_session_token . $timestamp);
            $uploadScript = \Helpers\Urls::url_for(
                'main/users/registration/attachments_upload',
                'field_id=' . $field_id
            );
            $previewScript = \Helpers\Urls::url_for(
                'main/users/registration/attachments_preview',
                'field_id=' . $field_id . '&token=' . $form_token
            );
        } elseif (\K::$fw->app_items_form_name == 'public_form' or (isset(\K::$fw->GET['form_name'])) and $_GET['form_name'] == 'public_form') {
            \K::$fw->public_form['id'] = \K::$fw->GET['public_form_id'] ?? \K::$fw->public_form['id'];
            //$form_token = md5($app_session_token . $timestamp);
            $uploadScript = \Helpers\Urls::url_for(
                'ext/public/form/attachments_upload',
                'id=' . \K::$fw->public_form['id'] . '&field_id=' . $field_id
            );
            $previewScript = \Helpers\Urls::url_for(
                'ext/public/form/attachments_preview',
                'field_id=' . $field_id . '&id=' . \K::$fw->public_form['id'] . '&token=' . $form_token
            );
        } elseif (\K::$fw->app_items_form_name == 'account_form') {
            //$form_token = md5(\K::$fw->app_user['id'] . $timestamp);
            $uploadScript = \Helpers\Urls::url_for(
                'main/users/account/attachments_upload',
                'path=' . \K::$fw->current_path . '&field_id=' . $field_id
            );
            $previewScript = \Helpers\Urls::url_for(
                'main/users/account/attachments_preview',
                'field_id=' . $field_id . '&path=' . \K::$fw->current_path . '&token=' . $form_token
            );
            $delete_file_url = \Helpers\Urls::url_for('main/users/account/attachments_delete_in_queue');
        } else {
            //$form_token = md5(\K::$fw->app_user['id'] . $timestamp);
            $uploadScript = \Helpers\Urls::url_for(
                'main/items/items/attachments_upload',
                'path=' . \K::$fw->current_path . '&field_id=' . $field_id
            );
            $previewScript = \Helpers\Urls::url_for(
                'main/items/items/attachments_preview',
                'field_id=' . $field_id . '&path=' . \K::$fw->current_path . '&token=' . $form_token
            );
            $delete_file_url = \Helpers\Urls::url_for(
                'main/items/items/attachments_delete_in_queue',
                'path=' . \K::$fw->GET['path']
            );
        }

        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $allowed_extensions = is_array($cfg->get('allowed_extensions')) ? $cfg->get('allowed_extensions') : [
            'gif',
            'jpg',
            'jpeg',
            'png'
        ];

        $mime_types = \Tools\FieldsTypes\Fieldtype_attachments::get_mime_types();
        $allowed_mime_types = [];
        foreach ($allowed_extensions as $v) {
            foreach ($mime_types[$v] as $vv) {
                $allowed_mime_types[] = $vv;
            }
        }

        $attachments_preview_html = \Tools\Attachments::render_preview(
            $field_id,
            \K::$fw->uploadify_attachments[$field_id],
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
        buttonText       : "<i class=\"fa fa-upload\"></i> ' . \K::$fw->TEXT_SELECT_IMAGE . '",
        formData       :  {
                                "token"     : "' . $form_token . '",
                                "form_session_token" : "' . \K::$fw->app_session_token . '"
                            },    
        queueID          : "uploadifive_queue_list_' . $field_id . '",
        fileSizeLimit : "' . (strlen($cfg->get('upload_size_limit')) ? (int)$cfg->get(
                'upload_size_limit'
            ) : \K::$fw->CFG_SERVER_UPLOAD_MAX_FILESIZE) . 'MB",
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

    public function process($options)
    {
        $attachment = '';

        if (is_array($options['value'])) {
            foreach ($options['value'] as $v) {
                $file = \Tools\Attachments::parse_filename($v['file']);

                if ($file['filename'] == $v['name']) {
                    $attachment = $v['file'];
                } else {
                    $new_name = $file['date_added'] . '_' . \K::model()->db_input_protect($v['name']) . (strlen(
                            $file['extension']
                        ) ? '.' . $file['extension'] : '');
                    $filepath = \K::$fw->DIR_WS_ATTACHMENTS . $file['folder'] . '/' . (\K::$fw->CFG_ENCRYPT_FILE_NAME == 1 ? sha1(
                            $new_name
                        ) : $new_name);

                    if (is_file($file['file_path'])) {
                        rename($file['file_path'], $filepath);
                    }

                    $attachment = $new_name;
                }
            }
        } else {
            $attachment = $options['value'];
        }

        return $attachment;
    }

    public function output($options)
    {
        $options_cfg = new \Models\Main\Fields_types_options_cfg($options);

        if (strlen($options['value']) > 0) {
            $file = \Tools\Attachments::parse_filename($options['value']);

            if (isset($options['is_print'])) {
                return '<img width=120 height=120 src=' . \Helpers\Urls::url_for(
                        'main/items/info/download_attachment',
                        'path=' . $options['field']['entities_id'] . '&preview=small&file=' . urlencode(
                            base64_encode($options['value'])
                        )
                    ) . '>';
            } elseif (isset($options['is_email'])) {
                if ($options_cfg->get('hide_attachments_url') == 1) {
                    return $file['name'];
                } else {
                    return \Helpers\Urls::link_to(
                            $file['name'],
                            \Helpers\Urls::url_for(
                                'main/items/info/download_attachment',
                                'path=' . $options['path'] . '&file=' . urlencode(
                                    base64_encode($options['value'])
                                ) . '&field=' . $options['field']['id']
                            ),
                            ['target' => '_blank']
                        ) . ' <small>(' . $file['size'] . ')</small>';
                }
            } elseif (isset($options['is_export'])) {
                return $file['name'];
            } elseif ($file['is_image']) {
                $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

                $fancybox_css_class = 'fancybox' . $options['field']['id'] . time();

                $img = '<img class="fieldtype_image field_' . $options['field']['id'] . '"   src="' . \Helpers\Urls::url_for(
                        'main/items/info/download_attachment',
                        'path=' . $options['path'] . '&preview=small&file=' . urlencode(
                            base64_encode($options['value'])
                        )
                    ) . '">';

                $width = (isset($options['is_listing']) ? (strlen($cfg->get('width_in_listing')) ? $cfg->get(
                    'width_in_listing'
                ) : 250) : (strlen($cfg->get('width')) ? $cfg->get('width') : 250));

                $html = '
          <div class="fieldtype-image-container" style="width: ' . $width . 'px; max-height: ' . $width . 'px;">' .
                    \Helpers\Urls::link_to(
                        $img,
                        \Helpers\Urls::url_for(
                            'main/items/info/preview_attachment_image',
                            'path=' . $options['path'] . '&file=' . urlencode(base64_encode($options['value']))
                        ),
                        ['class' => $fancybox_css_class]
                    ) . '
           </div> 
          ';

                if (!isset($options['is_listing'])) {
                    $html .= '
          	<div class="fieldtype-image-filename" style="width: ' . $width . 'px">
              ' . \Helpers\Urls::link_to(
                            '<i class="fa fa-download"></i> ' . $file['name'],
                            \Helpers\Urls::url_for(
                                'main/items/info/download_attachment',
                                'path=' . $options['path'] . '&file=' . urlencode(
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
                return '<img src="' . $file['icon'] . '"> ' . \Helpers\Urls::link_to(
                        $file['name'],
                        \Helpers\Urls::url_for(
                            'main/items/info/download_attachment',
                            'path=' . $options['path'] . '&file=' . urlencode(
                                base64_encode($options['value'])
                            )
                        ),
                        ['target' => '_blank']
                    ) . '  <small>(' . $file['size'] . ')</small>';
            }
        } else {
            return '';
        }
    }
}