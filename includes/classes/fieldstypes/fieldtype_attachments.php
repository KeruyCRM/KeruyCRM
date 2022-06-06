<?php

class fieldtype_attachments
{
    public $options;

    function __construct()
    {
        $this->options = ['title' => TEXT_FIELDTYPE_ATTACHMENTS_TITLE];
    }

    function get_configuration()
    {
        $cfg = [];

        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_NOTIFY_WHEN_CHANGED,
            'name' => 'notify_when_changed',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_NOTIFY_WHEN_CHANGED_TIP
        ];
        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_ALLOW_SEARCH_TIP
        ];
        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_USE_IMAGE_PREVIEW,
            'name' => 'use_image_preview',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_USE_IMAGE_PREVIEW_TIP
        ];
        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_DISPLAY_FILE_DATE_ADDED,
            'name' => 'display_date_added',
            'type' => 'checkbox'
        ];
        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_ALLOW_CHANGE_FILE_NAME,
            'name' => 'allow_change_file_name',
            'type' => 'checkbox'
        ];
        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_FILES_UPLOAD_LIMIT,
            'name' => 'upload_limit',
            'type' => 'input',
            'tooltip_icon' => TEXT_FILES_UPLOAD_LIMIT_TIP,
            'params' => ['class' => 'form-control input-xsmall']
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
            'title' => TEXT_ALLOWED_EXTENSIONS,
            'name' => 'allowed_extensions',
            'type' => 'input',
            'tooltip_icon' => TEXT_ALLOWED_EXTENSIONS_TIP,
            'params' => ['class' => 'form-control input-large']
        ];
        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_PLAY_AUDIO_FILE,
            'name' => 'play_audio_file',
            'type' => 'checkbox',
            'tooltip_icon' => 'mp3, wav, ogg'
        ];


        $cfg[TEXT_DELETION][] = ['title' => TEXT_CONFIRM_DELETION, 'name' => 'confirm_delation', 'type' => 'checkbox'];
        $cfg[TEXT_DELETION][] = [
            'title' => TEXT_ALLOWS_DELETE_IF_HAS_DELETE_ACCESS,
            'name' => 'check_delete_access',
            'type' => 'checkbox'
        ];

        return $cfg;
    }

    function render($field, $obj, $params = [])
    {
        global $uploadify_attachments, $uploadify_attachments_queue, $current_path, $app_user, $app_items_form_name, $public_form, $app_session_token;

        if (!isset($field['configuration'])) {
            $field['configuration'] = '';
        }

        $cfg = new fields_types_cfg($field['configuration']);

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
        } elseif ($app_items_form_name == 'ipage_form') {
            $form_token = md5($app_user['id'] . $timestamp);
            $uploadScript = url_for(
                'ext/ipages/configuration',
                'action=attachments_upload&field_id=' . $field_id,
                true
            );
            $previewScript = url_for(
                'ext/ipages/configuration',
                'action=attachments_preview&field_id=' . $field_id . '&token=' . $form_token
            );
            $delete_file_url = url_for('ext/ipages/configuration', 'action=attachments_delete_in_queue');
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

        $uploadLimit = (strlen($cfg->get('upload_limit')) ? (int)$cfg->get('upload_limit') : 0);
        $onComplateAction = ($uploadLimit > 0 ? 'onUploadComplete' : 'onQueueComplete');

        $fileType = "''";
        if (strlen($cfg->get('allowed_extensions'))) {
            $extensions = [];
            foreach (explode(',', $cfg->get('allowed_extensions')) as $v) {
                $extensions[] = trim(strip_tags(str_replace(['"', "'"], '', $v)));
            }

            $fileTypeList = [];
            $mime_types = self::get_mime_types();

            foreach ($extensions as $v) {
                if (isset($mime_types[$v])) {
                    foreach ($mime_types[$v] as $vv) {
                        $fileTypeList[] = $vv;
                    }
                }
            }

            $fileType = "['" . implode("','", $fileTypeList) . "']";
            //print_r($mime_types);
        }


        $attachments_preview_html = attachments::render_preview(
            $field_id,
            $uploadify_attachments[$field_id],
            $delete_file_url
        );

        $html = '
      <div class="form-control-static"> 
        <input style="cursor: pointer" type="file" name="uploadifive_attachments_upload_' . $field_id . '" id="uploadifive_attachments_upload_' . $field_id . '" /> 
      </div>
      
      <div id="uploadifive_queue_list_' . $field_id . '"></div>
      <div id="uploadifive_attachments_list_' . $field_id . '">
        ' . $attachments_preview_html . '        
      </div>
      
      <script type="text/javascript">
		
      var is_file_uploading = null;  
        		
        function uploadifive_oncomplate_filed_' . $field_id . '()
        {
            is_file_uploading = null  
            
            $(".uploadifive-queue-item.complete").fadeOut()
            
            $("#uploadifive_attachments_list_' . $field_id . '").append("<div class=\"loading_data\"></div>")
            $("#uploadifive_attachments_list_' . $field_id . '").load("' . $previewScript . '")
        }		
      
        $(function() {
        
            $("#uploadifive_attachments_upload_' . $field_id . '").uploadifive({
                "auto"             : true,  
                "dnd"              : false, 
                "fileType"		   :   ' . $fileType . ',
                "fileTypeExtra"	   :   "' . $cfg->get('allowed_extensions') . '",
                "buttonClass"      : "btn btn-default btn-upload",
                "buttonText"       : "<i class=\"fa fa-upload\"></i> ' . TEXT_ADD_ATTACHMENTS . '",				
                "formData"         : {
                                        "timestamp" : ' . $timestamp . ',
                                        "token"     : "' . $form_token . '",
                                        "form_session_token" : "' . $app_session_token . '"		
                                     },
                "queueID"          : "uploadifive_queue_list_' . $field_id . '",
                "fileSizeLimit" : "' . (strlen($cfg->get('upload_size_limit')) ? (int)$cfg->get(
                'upload_size_limit'
            ) : CFG_SERVER_UPLOAD_MAX_FILESIZE) . 'MB",
                "queueSizeLimit" : ' . $uploadLimit . ',
                "uploadScript"     : "' . $uploadScript . '",
                "onUpload"         :  function(filesToUpload){
                  is_file_uploading = true;  					
                },
                "' . $onComplateAction . '" : function(file, data) {
                    uploadifive_oncomplate_filed_' . $field_id . '()
                },
                "onError":function(errorType) {
                     is_file_uploading = null;             
                },
                "onCancel"     : function() { 	
                     is_file_uploading = null;  				
                } 		
            });
                        
        $("button[type=submit]").bind("click",function(){                                                 
            if(is_file_uploading)
            {
              alert("' . TEXT_PLEASE_WAIT_FILES_LOADING . '"); return false;
            }                           
          });
        
  		});
	</script>
    ';

        return $html;
    }

    function process($options)
    {
        global $app_changed_fields;


        if (is_array($options['value'])) {
            $attachments = [];

            //print_rr($options['value']);

            foreach ($options['value'] as $v) {
                $file = attachments::parse_filename($v['file']);

                if ($file['filename'] == $v['name']) {
                    $attachments[] = $v['file'];
                } else {
                    $new_name = $file['date_added'] . '_' . db_input_protect($v['name']) . (strlen(
                            $file['extension']
                        ) ? '.' . $file['extension'] : '');
                    $filepath = DIR_WS_ATTACHMENTS . $file['folder'] . '/' . (CFG_ENCRYPT_FILE_NAME == 1 ? sha1(
                            $new_name
                        ) : $new_name);

                    if (is_file($file['file_path'])) {
                        rename($file['file_path'], $filepath);
                    }

                    $attachments[] = $new_name;
                }
            }

            //print_rr($attachments);
            //exit();
        } else {
            $attachments = explode(',', $options['value']);
        }

        if (isset($_POST['delete_attachments'])) {
            foreach ($_POST['delete_attachments'] as $filename => $v) {
                if (($key = array_search($filename, $attachments)) !== false) {
                    unset($attachments[$key]);
                }

                $file = attachments::parse_filename($filename);
                if (is_file(DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file_sha1'])) {
                    unlink(DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file_sha1']);
                }
            }
        }

        //remove out of data tmp attachments
        db_query("delete from app_attachments where date_added!='" . date('Y-m-d') . "'");

        $options['value'] = implode(',', $attachments);


        //notify when changed
        if (isset($options['is_new_item'])) {
            if (!$options['is_new_item']) {
                $cfg = new fields_types_cfg($options['field']['configuration']);

                if ($options['value'] != $options['current_field_value'] and $cfg->get('notify_when_changed') == 1) {
                    $app_changed_fields[] = [
                        'name' => $options['field']['name'],
                        'value' => (strlen($options['value']) ? count(explode(',', $options['value'])) : 0),
                        'fields_id' => $options['field']['id'],
                        'fields_value' => $options['value'],
                    ];
                }
            }
        }

        return $options['value'];
    }

    function output($options)
    {
        $options_cfg = new fields_types_options_cfg($options);

        if (strlen($options['value']) > 0) {
            $use_file_storage = false;

            //check if field using file storage
            if (isset($options['field']['id'])) {
                if (is_ext_installed() and $options['field']['id'] > 0) {
                    $use_file_storage = file_storage::check($options['field']['id']);
                }
            }

            if (isset($options['is_public_form'])) {
                $html = [];
                foreach (explode(',', $options['value']) as $filename) {
                    $file = attachments::parse_filename($filename);
                    $html[] = link_to(
                            $file['name'],
                            url_for(
                                'ext/public/check',
                                'action=download_attachment&id=' . $options['is_public_form'] . '&item=' . $options['item']['id'] . '&field=' . $options['field']['id'] . '&file=' . urlencode(
                                    base64_encode($filename)
                                )
                            ),
                            ['target' => '_blank']
                        ) . ($use_file_storage ? '' : '  <small>(' . $file['size'] . ')</small>');
                }

                return implode('<br>', $html);
            } elseif (isset($options['is_ipages'])) {
                $cfg = new fields_types_cfg(
                    (isset($options['field']['configuration']) ? $options['field']['configuration'] : '')
                );

                $fancybox_css_class = '';

                $html = '
		                  <ul style="padding: 0px; margin: 0px;">';
                foreach (explode(',', $options['value']) as $filename) {
                    $file = attachments::parse_filename($filename);

                    $file['name'] = app_crop_str($file['name']);

                    if ($file['is_image']) {
                        if (strlen($fancybox_css_class) == 0) {
                            $fancybox_css_class = 'fancybox' . time();
                        }

                        $link = link_to(
                            $file['name'],
                            url_for(
                                'ext/ipages/view',
                                'id=' . $options['is_ipages'] . '&action=preview_attachment_image&file=' . urlencode(
                                    base64_encode($filename)
                                )
                            ),
                            [
                                'class' => $fancybox_css_class,
                                'title' => $file['name'],
                                'data-fancybox-group' => 'gallery'
                            ]
                        );
                    } elseif ($file['is_pdf']) {
                        $link = link_to(
                            $file['name'],
                            url_for(
                                'ext/ipages/view',
                                'id=' . $options['is_ipages'] . '&action=download_attachment&preview=1&file=' . urlencode(
                                    base64_encode($filename)
                                )
                            ),
                            ['target' => '_blank']
                        );
                    } else {
                        $link = link_to(
                            $file['name'],
                            url_for(
                                'ext/ipages/view',
                                'id=' . $options['is_ipages'] . '&action=download_attachment&file=' . urlencode(
                                    base64_encode($filename)
                                )
                            )
                        );
                    }

                    $link .= ' ' . link_to(
                            '<i class="fa fa-download"></i>',
                            url_for(
                                'ext/ipages/view',
                                'id=' . $options['is_ipages'] . '&action=download_attachment&file=' . urlencode(
                                    base64_encode($filename)
                                )
                            )
                        );

                    $html .= '
		              <li style="list-style-image: url(' . url_for_file(
                            $file['icon']
                        ) . '); margin-left: 20px;">' . $link . ' <small>(' . $file['size'] . ')' . self::add_file_date_added(
                            $file,
                            $cfg
                        ) . '</small></li>
		            ';
                }
                $html .= '
		                  </ul>';

                if (strlen($fancybox_css_class) > 0) {
                    $html .= '
            <script type="text/javascript">
            	$(document).ready(function() {
            		$(".' . $fancybox_css_class . '").fancybox({type: "ajax"});
            	});
            </script>
          ';
                }

                return $html;
            } elseif (isset($options['is_export'])) {
                $html = [];
                foreach (explode(',', $options['value']) as $filename) {
                    $file = attachments::parse_filename($filename);
                    $html[] = $file['name'];
                }

                return implode(', ', $html);
            } elseif (isset($options['is_email'])) {
                $html = [];
                foreach (explode(',', $options['value']) as $filename) {
                    $file = attachments::parse_filename($filename);

                    if ($options_cfg->get('hide_attachments_url') == 1) {
                        $html[] = $file['name'];
                    } else {
                        $html[] = link_to(
                                $file['name'],
                                url_for(
                                    'items/info',
                                    'path=' . $options['path'] . '&action=download_attachment&file=' . urlencode(
                                        base64_encode($filename)
                                    ) . '&field=' . $options['field']['id']
                                ),
                                ['target' => '_blank']
                            ) . ($use_file_storage ? '' : '  <small>(' . $file['size'] . ')</small>');
                    }
                }

                return implode('<br>', $html);
            } else {
                $is_listing = (isset($options['is_listing']) ? true : false);

                $cfg = new fields_types_cfg(
                    (isset($options['field']['configuration']) ? $options['field']['configuration'] : '')
                );

                $image_gallery = [];

                $fancybox_css_class = '';

                $html = '';

                /*
                if(!$is_listing)
                {
                    $html .= '
                    <div class="table-scrollable">
                      <table class="table">
                        <tbody>
                          <tr>
                            <td>';
                }
                */


                if ($use_file_storage) {
                    $html .= '
		                  <ul style="padding: 0px; margin: 0px;">';
                    foreach (explode(',', $options['value']) as $filename) {
                        $file = attachments::parse_filename($filename);

                        $file['name'] = app_crop_str($file['name']);

                        $link = link_to(
                            $file['name'],
                            url_for(
                                'items/info',
                                'path=' . $options['path'] . '&action=download_attachment&file=' . urlencode(
                                    base64_encode($filename)
                                )
                            ) . '&field=' . $options['field']['id']
                        );

                        $link .= ' ' . link_to(
                                '<i class="fa fa-download"></i>',
                                url_for(
                                    'items/info',
                                    'path=' . $options['path'] . '&action=download_attachment&file=' . urlencode(
                                        base64_encode($filename)
                                    )
                                ) . '&field=' . $options['field']['id']
                            );

                        $html .= '
		              <li style="list-style-image: url(' . url_for_file(
                                $file['icon']
                            ) . '); margin-left: 20px;">' . $link . '</li>
		            ';
                    }

                    $html .= '
		                  </ul>';
                } else {
                    $html .= ' 		
		                  <ul style="padding: 0px; margin: 0px;">';
                    foreach (explode(',', $options['value']) as $filename) {
                        $file = attachments::parse_filename($filename);

                        $file['name'] = app_crop_str($file['name']);

                        if ($file['is_image']) {
                            if (strlen($fancybox_css_class) == 0) {
                                $fancybox_css_class = 'fancybox' . time();
                            }

                            $link = link_to(
                                $file['name'],
                                url_for(
                                    'items/info',
                                    'path=' . $options['path'] . '&action=preview_attachment_image&file=' . urlencode(
                                        base64_encode($filename)
                                    )
                                ),
                                [
                                    'class' => $fancybox_css_class,
                                    'title' => $file['name'],
                                    'data-fancybox-group' => 'gallery'
                                ]
                            );

                            //generate image preview
                            if ($cfg->get('use_image_preview') == 1 and !$is_listing) {
                                $img = '<img src="' . url_for(
                                        'items/info',
                                        'path=' . $options['path'] . '&action=download_attachment&preview=small&file=' . urlencode(
                                            base64_encode($filename)
                                        )
                                    ) . '">';
                                $image_gallery[] = [
                                    'image' => link_to(
                                        $img,
                                        url_for(
                                            'items/info',
                                            'path=' . $options['path'] . '&action=preview_attachment_image&file=' . urlencode(
                                                base64_encode($filename)
                                            )
                                        ),
                                        [
                                            'class' => $fancybox_css_class,
                                            'title' => $file['name'],
                                            'data-fancybox-group' => 'gallery'
                                        ]
                                    ),
                                    'download_link' => link_to(
                                        '<i class="fa fa-download"></i> ' . TEXT_DOWNLOAD,
                                        url_for(
                                            'items/info',
                                            'path=' . $options['path'] . '&action=download_attachment&file=' . urlencode(
                                                base64_encode($filename)
                                            )
                                        )
                                    )
                                ];

                                continue;
                            }
                        } elseif ($file['is_pdf']) {
                            $link = link_to(
                                $file['name'],
                                url_for(
                                    'items/info',
                                    'path=' . $options['path'] . '&action=download_attachment&preview=1&file=' . urlencode(
                                        base64_encode($filename)
                                    )
                                ),
                                ['target' => '_blank']
                            );
                        } else {
                            $link = link_to(
                                $file['name'],
                                url_for(
                                    'items/info',
                                    'path=' . $options['path'] . '&action=download_attachment&file=' . urlencode(
                                        base64_encode($filename)
                                    )
                                )
                            );
                        }

                        $link .= ' ' . link_to(
                                '<i class="fa fa-download"></i>',
                                url_for(
                                    'items/info',
                                    'path=' . $options['path'] . '&action=download_attachment&file=' . urlencode(
                                        base64_encode($filename)
                                    )
                                )
                            );

                        //add public link
                        if (isset($options['field']['id']) and in_array(
                                $options['field']['id'],
                                explode(',', CFG_PUBLIC_ATTACHMENTS)
                            )) {
                            $link .= ' ' . link_to(
                                    '<i class="fa fa-link"></i>',
                                    url_for(
                                        'export/file',
                                        'id=' . $options['field']['id'] . '&path=' . $options['field']['entities_id'] . '-' . $options['item']['id'] . '&file=' . urlencode(
                                            $filename
                                        )
                                    ),
                                    ['target' => "_blank"]
                                );
                        }

                        $link .= '  <small>(' . $file['size'] . ')' . self::add_file_date_added(
                                $file,
                                $cfg
                            ) . '</small>';

                        if ($file['is_audio'] and $cfg->get('play_audio_file') == 1) {
                            $link .= '
	                        <audio controls class="audio-controls" controlsList="nodownload">
                              <source src="' . url_for(
                                    'items/info',
                                    'path=' . $options['path'] . '&action=download_attachment&preview=1&file=' . urlencode(
                                        base64_encode($filename)
                                    )
                                ) . '" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
	                      ';
                        }

                        $html .= '
		              <li style="list-style-image: url(' . url_for_file(
                                $file['icon']
                            ) . '); margin-left: 20px;">' . $link . '</li>
		            ';
                    }
                    $html .= '  
		                  </ul>';
                }

                /*
                if(!$is_listing)
                {
                    $html .='
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>';
                }
                */

                //display preview if available
                if (count($image_gallery) > 0 and !$is_listing) {
                    if (count($image_gallery) == count(explode(',', $options['value']))) {
                        $html = '';
                    }

                    $html .= '
            <div class="attachments-gallery">
              <ul>';
                    foreach ($image_gallery as $v) {
                        $html .= '
              <li>
                <div class="gallery-image">' . $v['image'] . '</div>
                <div class="gallery-download-link">' . $v['download_link'] . '</div>
              </li>';
                    }

                    $html .= '</ul>
            </div>
            <div style="clear:both"></div>
            ';
                }

                if (strlen($fancybox_css_class) > 0) {
                    $html .= '
            <script type="text/javascript">
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
                }

                return $html;
            }
        } else {
            return '';
        }
    }

    static function add_file_date_added($file, $cfg)
    {
        if ($cfg->get('display_date_added') == 1) {
            return ' - ' . format_date_time($file['date_added']);
        } else {
            return '';
        }
    }

    static function get_accept_types($cfg)
    {
        $params = [];

        if (strlen($cfg->get('allowed_extensions'))) {
            $extensions = [];
            foreach (explode(',', $cfg->get('allowed_extensions')) as $v) {
                $extensions[] = trim(strip_tags(str_replace(['"', "'"], '', $v)));
            }

            $fileTypeList = [];
            $mime_types = self::get_mime_types();

            foreach ($extensions as $v) {
                if (isset($mime_types[$v])) {
                    foreach ($mime_types[$v] as $vv) {
                        $fileTypeList[] = $vv;
                    }
                }
            }

            $params = ['accept' => implode(',', $fileTypeList), 'data-msg-accept' => TEXT_ERROR_FILE_EXTENSION];
        }

        return $params;
    }

    static function get_accept_types_by_file($allowed_extensions = '')
    {
        $params = [];

        if (strlen($allowed_extensions)) {
            $extensions = [];
            foreach (explode(',', $allowed_extensions) as $v) {
                $extensions[] = trim(strip_tags(str_replace(['"', "'"], '', $v)));
            }

            $fileTypeList = [];
            $mime_types = self::get_mime_types();

            foreach ($extensions as $v) {
                if (isset($mime_types[$v])) {
                    foreach ($mime_types[$v] as $vv) {
                        $fileTypeList[] = $vv;
                    }
                }
            }

            $params = ['accept' => implode(',', $fileTypeList), 'data-msg-accept' => TEXT_ERROR_FILE_EXTENSION];
        }

        return $params;
    }

    static function get_accept_types_by_extensions($allowed_extensions)
    {
        if (strlen($allowed_extensions)) {
            $extensions = [];
            foreach (explode(',', $allowed_extensions) as $v) {
                $extensions[] = trim(strip_tags(str_replace(['"', "'"], '', $v)));
            }

            $fileTypeList = [];
            $mime_types = self::get_mime_types();

            foreach ($extensions as $v) {
                if (isset($mime_types[$v])) {
                    foreach ($mime_types[$v] as $vv) {
                        $fileTypeList[] = $vv;
                    }
                }
            }

            return implode(',', $fileTypeList);
        }

        return '';
    }

    static function get_mime_types()
    {
        return [
            '3dm' => ['x-world/x-3dmf'],
            '3dmf' => ['x-world/x-3dmf'],
            '3dml' => ['text/vnd.in3d.3dml'],
            '3ds' => ['image/x-3ds'],
            '3g2' => ['video/3gpp2'],
            '3gp' => ['video/3gpp'],
            '7z' => ['application/x-7z-compressed'],
            'a' => ['application/octet-stream'],
            'aab' => ['application/x-authorware-bin'],
            'aac' => ['audio/x-aac'],
            'aam' => ['application/x-authorware-map'],
            'aas' => ['application/x-authorware-seg'],
            'abc' => ['text/vnd.abc'],
            'abw' => ['application/x-abiword'],
            'ac' => ['application/pkix-attr-cert'],
            'acc' => ['application/vnd.americandynamics.acc'],
            'ace' => ['application/x-ace-compressed'],
            'acgi' => ['text/html'],
            'acu' => ['application/vnd.acucobol'],
            'acutc' => ['application/vnd.acucorp'],
            'adp' => ['audio/adpcm'],
            'aep' => ['application/vnd.audiograph'],
            'afl' => ['video/animaflex'],
            'afm' => ['application/x-font-type1'],
            'afp' => ['application/vnd.ibm.modcap'],
            'ahead' => ['application/vnd.ahead.space'],
            'ai' => ['application/postscript'],
            'aif' => ['audio/aiff', 'audio/x-aiff'],
            'aifc' => ['audio/aiff', 'audio/x-aiff'],
            'aiff' => ['audio/aiff', 'audio/x-aiff'],
            'aim' => ['application/x-aim'],
            'aip' => ['text/x-audiosoft-intra'],
            'air' => ['application/vnd.adobe.air-application-installer-package+zip'],
            'ait' => ['application/vnd.dvb.ait'],
            'ami' => ['application/vnd.amiga.ami'],
            'ani' => ['application/x-navi-animation'],
            'aos' => ['application/x-nokia-9000-communicator-add-on-software'],
            'apk' => ['application/vnd.android.package-archive'],
            'appcache' => ['text/cache-manifest'],
            'application' => ['application/x-ms-application'],
            'apr' => ['application/vnd.lotus-approach'],
            'aps' => ['application/mime'],
            'arc' => ['application/x-freearc'],
            'arj' => ['application/arj', 'application/octet-stream'],
            'art' => ['image/x-jg'],
            'asc' => ['application/pgp-signature'],
            'asf' => ['video/x-ms-asf'],
            'asm' => ['text/x-asm'],
            'aso' => ['application/vnd.accpac.simply.aso'],
            'asp' => ['text/asp'],
            'asx' => ['application/x-mplayer2', 'video/x-ms-asf', 'video/x-ms-asf-plugin'],
            'atc' => ['application/vnd.acucorp'],
            'atom' => ['application/atom+xml'],
            'atomcat' => ['application/atomcat+xml'],
            'atomsvc' => ['application/atomsvc+xml'],
            'atx' => ['application/vnd.antix.game-component'],
            'au' => ['audio/basic'],
            'avi' => ['application/x-troff-msvideo', 'video/avi', 'video/msvideo', 'video/x-msvideo'],
            'avs' => ['video/avs-video'],
            'aw' => ['application/applixware'],
            'azf' => ['application/vnd.airzip.filesecure.azf'],
            'azs' => ['application/vnd.airzip.filesecure.azs'],
            'azw' => ['application/vnd.amazon.ebook'],
            'bat' => ['application/x-msdownload'],
            'bcpio' => ['application/x-bcpio'],
            'bdf' => ['application/x-font-bdf'],
            'bdm' => ['application/vnd.syncml.dm+wbxml'],
            'bed' => ['application/vnd.realvnc.bed'],
            'bh2' => ['application/vnd.fujitsu.oasysprs'],
            'bin' => [
                'application/mac-binary',
                'application/macbinary',
                'application/octet-stream',
                'application/x-binary',
                'application/x-macbinary'
            ],
            'blb' => ['application/x-blorb'],
            'blorb' => ['application/x-blorb'],
            'bm' => ['image/bmp'],
            'bmi' => ['application/vnd.bmi'],
            'bmp' => ['image/bmp', 'image/x-windows-bmp'],
            'boo' => ['application/book'],
            'book' => ['application/vnd.framemaker'],
            'box' => ['application/vnd.previewsystems.box'],
            'boz' => ['application/x-bzip2'],
            'bpk' => ['application/octet-stream'],
            'bsh' => ['application/x-bsh'],
            'btif' => ['image/prs.btif'],
            'buffer' => ['application/octet-stream'],
            'bz' => ['application/x-bzip'],
            'bz2' => ['application/x-bzip2'],
            'c' => ['text/x-c'],
            'c++' => ['text/plain'],
            'c11amc' => ['application/vnd.cluetrust.cartomobile-config'],
            'c11amz' => ['application/vnd.cluetrust.cartomobile-config-pkg'],
            'c4d' => ['application/vnd.clonk.c4group'],
            'c4f' => ['application/vnd.clonk.c4group'],
            'c4g' => ['application/vnd.clonk.c4group'],
            'c4p' => ['application/vnd.clonk.c4group'],
            'c4u' => ['application/vnd.clonk.c4group'],
            'cab' => ['application/vnd.ms-cab-compressed'],
            'caf' => ['audio/x-caf'],
            'cap' => ['application/vnd.tcpdump.pcap'],
            'car' => ['application/vnd.curl.car'],
            'cat' => ['application/vnd.ms-pki.seccat'],
            'cb7' => ['application/x-cbr'],
            'cba' => ['application/x-cbr'],
            'cbr' => ['application/x-cbr'],
            'cbt' => ['application/x-cbr'],
            'cbz' => ['application/x-cbr'],
            'cc' => ['text/plain', 'text/x-c'],
            'ccad' => ['application/clariscad'],
            'cco' => ['application/x-cocoa'],
            'cct' => ['application/x-director'],
            'ccxml' => ['application/ccxml+xml'],
            'cdbcmsg' => ['application/vnd.contact.cmsg'],
            'cdf' => ['application/cdf', 'application/x-cdf', 'application/x-netcdf'],
            'cdkey' => ['application/vnd.mediastation.cdkey'],
            'cdmia' => ['application/cdmi-capability'],
            'cdmic' => ['application/cdmi-container'],
            'cdmid' => ['application/cdmi-domain'],
            'cdmio' => ['application/cdmi-object'],
            'cdmiq' => ['application/cdmi-queue'],
            'cdx' => ['chemical/x-cdx'],
            'cdxml' => ['application/vnd.chemdraw+xml'],
            'cdy' => ['application/vnd.cinderella'],
            'cer' => ['application/pkix-cert', 'application/x-x509-ca-cert'],
            'cfs' => ['application/x-cfs-compressed'],
            'cgm' => ['image/cgm'],
            'cha' => ['application/x-chat'],
            'chat' => ['application/x-chat'],
            'chm' => ['application/vnd.ms-htmlhelp'],
            'chrt' => ['application/vnd.kde.kchart'],
            'cif' => ['chemical/x-cif'],
            'cii' => ['application/vnd.anser-web-certificate-issue-initiation'],
            'cil' => ['application/vnd.ms-artgalry'],
            'cla' => ['application/vnd.claymore'],
            'class' => ['application/java', 'application/java-byte-code', 'application/x-java-class'],
            'clkk' => ['application/vnd.crick.clicker.keyboard'],
            'clkp' => ['application/vnd.crick.clicker.palette'],
            'clkt' => ['application/vnd.crick.clicker.template'],
            'clkw' => ['application/vnd.crick.clicker.wordbank'],
            'clkx' => ['application/vnd.crick.clicker'],
            'clp' => ['application/x-msclip'],
            'cmc' => ['application/vnd.cosmocaller'],
            'cmdf' => ['chemical/x-cmdf'],
            'cml' => ['chemical/x-cml'],
            'cmp' => ['application/vnd.yellowriver-custom-menu'],
            'cmx' => ['image/x-cmx'],
            'cod' => ['application/vnd.rim.cod'],
            'com' => ['application/octet-stream', 'text/plain'],
            'conf' => ['text/plain'],
            'cpio' => ['application/x-cpio'],
            'cpp' => ['text/x-c'],
            'cpt' => ['application/x-compactpro', 'application/x-cpt'],
            'crd' => ['application/x-mscardfile'],
            'crl' => ['application/pkcs-crl', 'application/pkix-crl'],
            'crt' => ['application/pkix-cert', 'application/x-x509-ca-cert', 'application/x-x509-user-cert'],
            'crx' => ['application/x-chrome-extension'],
            'cryptonote' => ['application/vnd.rig.cryptonote'],
            'csh' => ['application/x-csh', 'text/x-script.csh'],
            'csml' => ['chemical/x-csml'],
            'csp' => ['application/vnd.commonspace'],
            'css' => ['application/x-pointplus', 'text/css'],
            'cst' => ['application/x-director'],
            'csv' => ['text/csv'],
            'cu' => ['application/cu-seeme'],
            'curl' => ['text/vnd.curl'],
            'cww' => ['application/prs.cww'],
            'cxt' => ['application/x-director'],
            'cxx' => ['text/x-c'],
            'dae' => ['model/vnd.collada+xml'],
            'daf' => ['application/vnd.mobius.daf'],
            'dart' => ['application/vnd.dart'],
            'dataless' => ['application/vnd.fdsn.seed'],
            'davmount' => ['application/davmount+xml'],
            'dbk' => ['application/docbook+xml'],
            'dcr' => ['application/x-director'],
            'dcurl' => ['text/vnd.curl.dcurl'],
            'dd2' => ['application/vnd.oma.dd2+xml'],
            'ddd' => ['application/vnd.fujixerox.ddd'],
            'deb' => ['application/x-debian-package'],
            'deepv' => ['application/x-deepv'],
            'def' => ['text/plain'],
            'deploy' => ['application/octet-stream'],
            'der' => ['application/x-x509-ca-cert'],
            'dfac' => ['application/vnd.dreamfactory'],
            'dgc' => ['application/x-dgc-compressed'],
            'dic' => ['text/x-c'],
            'dif' => ['video/x-dv'],
            'diff' => ['text/plain'],
            'dir' => ['application/x-director'],
            'dis' => ['application/vnd.mobius.dis'],
            'dist' => ['application/octet-stream'],
            'distz' => ['application/octet-stream'],
            'djv' => ['image/vnd.djvu'],
            'djvu' => ['image/vnd.djvu'],
            'dl' => ['video/dl', 'video/x-dl'],
            'dll' => ['application/x-msdownload'],
            'dmg' => ['application/x-apple-diskimage'],
            'dmp' => ['application/vnd.tcpdump.pcap'],
            'dms' => ['application/octet-stream'],
            'dna' => ['application/vnd.dna'],
            'doc' => ['application/msword'],
            'docm' => ['application/vnd.ms-word.document.macroenabled.12'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'dot' => ['application/msword'],
            'dotm' => ['application/vnd.ms-word.template.macroenabled.12'],
            'dotx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.template'],
            'dp' => ['application/vnd.osgi.dp'],
            'dpg' => ['application/vnd.dpgraph'],
            'dra' => ['audio/vnd.dra'],
            'drw' => ['application/drafting'],
            'dsc' => ['text/prs.lines.tag'],
            'dssc' => ['application/dssc+der'],
            'dtb' => ['application/x-dtbook+xml'],
            'dtd' => ['application/xml-dtd'],
            'dts' => ['audio/vnd.dts'],
            'dtshd' => ['audio/vnd.dts.hd'],
            'dump' => ['application/octet-stream'],
            'dv' => ['video/x-dv'],
            'dvb' => ['video/vnd.dvb.file'],
            'dvi' => ['application/x-dvi'],
            'dwf' => ['drawing/x-dwf (old)', 'model/vnd.dwf'],
            'dwg' => ['application/acad', 'image/vnd.dwg', 'image/x-dwg'],
            'dxf' => ['image/vnd.dxf'],
            'dxp' => ['application/vnd.spotfire.dxp'],
            'dxr' => ['application/x-director'],
            'ecelp4800' => ['audio/vnd.nuera.ecelp4800'],
            'ecelp7470' => ['audio/vnd.nuera.ecelp7470'],
            'ecelp9600' => ['audio/vnd.nuera.ecelp9600'],
            'ecma' => ['application/ecmascript'],
            'edm' => ['application/vnd.novadigm.edm'],
            'edx' => ['application/vnd.novadigm.edx'],
            'efif' => ['application/vnd.picsel'],
            'ei6' => ['application/vnd.pg.osasli'],
            'el' => ['text/x-script.elisp'],
            'elc' => ['application/x-bytecode.elisp (compiled elisp)', 'application/x-elc'],
            'emf' => ['application/x-msmetafile'],
            'eml' => ['message/rfc822'],
            'emma' => ['application/emma+xml'],
            'emz' => ['application/x-msmetafile'],
            'env' => ['application/x-envoy'],
            'eol' => ['audio/vnd.digital-winds'],
            'eot' => ['application/vnd.ms-fontobject'],
            'eps' => ['application/postscript'],
            'epub' => ['application/epub+zip'],
            'es' => ['application/x-esrehber'],
            'es3' => ['application/vnd.eszigno3+xml'],
            'esa' => ['application/vnd.osgi.subsystem'],
            'esf' => ['application/vnd.epson.esf'],
            'et3' => ['application/vnd.eszigno3+xml'],
            'etx' => ['text/x-setext'],
            'eva' => ['application/x-eva'],
            'event-stream' => ['text/event-stream'],
            'evy' => ['application/envoy', 'application/x-envoy'],
            'exe' => ['application/x-msdownload'],
            'exi' => ['application/exi'],
            'ext' => ['application/vnd.novadigm.ext'],
            'ez' => ['application/andrew-inset'],
            'ez2' => ['application/vnd.ezpix-album'],
            'ez3' => ['application/vnd.ezpix-package'],
            'f' => ['text/plain', 'text/x-fortran'],
            'f4v' => ['video/x-f4v'],
            'f77' => ['text/x-fortran'],
            'f90' => ['text/plain', 'text/x-fortran'],
            'fbs' => ['image/vnd.fastbidsheet'],
            'fcdt' => ['application/vnd.adobe.formscentral.fcdt'],
            'fcs' => ['application/vnd.isac.fcs'],
            'fdf' => ['application/vnd.fdf'],
            'fe_launch' => ['application/vnd.denovo.fcselayout-link'],
            'fg5' => ['application/vnd.fujitsu.oasysgp'],
            'fgd' => ['application/x-director'],
            'fh' => ['image/x-freehand'],
            'fh4' => ['image/x-freehand'],
            'fh5' => ['image/x-freehand'],
            'fh7' => ['image/x-freehand'],
            'fhc' => ['image/x-freehand'],
            'fif' => ['application/fractals', 'image/fif'],
            'fig' => ['application/x-xfig'],
            'flac' => ['audio/flac'],
            'fli' => ['video/fli', 'video/x-fli'],
            'flo' => ['application/vnd.micrografx.flo'],
            'flv' => ['video/x-flv'],
            'flw' => ['application/vnd.kde.kivio'],
            'flx' => ['text/vnd.fmi.flexstor'],
            'fly' => ['text/vnd.fly'],
            'fm' => ['application/vnd.framemaker'],
            'fmf' => ['video/x-atomic3d-feature'],
            'fnc' => ['application/vnd.frogans.fnc'],
            'for' => ['text/plain', 'text/x-fortran'],
            'fpx' => ['image/vnd.fpx', 'image/vnd.net-fpx'],
            'frame' => ['application/vnd.framemaker'],
            'frl' => ['application/freeloader'],
            'fsc' => ['application/vnd.fsc.weblaunch'],
            'fst' => ['image/vnd.fst'],
            'ftc' => ['application/vnd.fluxtime.clip'],
            'fti' => ['application/vnd.anser-web-funds-transfer-initiation'],
            'funk' => ['audio/make'],
            'fvt' => ['video/vnd.fvt'],
            'fxp' => ['application/vnd.adobe.fxp'],
            'fxpl' => ['application/vnd.adobe.fxp'],
            'fzs' => ['application/vnd.fuzzysheet'],
            'g' => ['text/plain'],
            'g2w' => ['application/vnd.geoplan'],
            'g3' => ['image/g3fax'],
            'g3w' => ['application/vnd.geospace'],
            'gac' => ['application/vnd.groove-account'],
            'gam' => ['application/x-tads'],
            'gbr' => ['application/rpki-ghostbusters'],
            'gca' => ['application/x-gca-compressed'],
            'gdl' => ['model/vnd.gdl'],
            'geo' => ['application/vnd.dynageo'],
            'gex' => ['application/vnd.geometry-explorer'],
            'ggb' => ['application/vnd.geogebra.file'],
            'ggt' => ['application/vnd.geogebra.tool'],
            'ghf' => ['application/vnd.groove-help'],
            'gif' => ['image/gif'],
            'gim' => ['application/vnd.groove-identity-message'],
            'gl' => ['video/gl', 'video/x-gl'],
            'gml' => ['application/gml+xml'],
            'gmx' => ['application/vnd.gmx'],
            'gnumeric' => ['application/x-gnumeric'],
            'gph' => ['application/vnd.flographit'],
            'gpx' => ['application/gpx+xml'],
            'gqf' => ['application/vnd.grafeq'],
            'gqs' => ['application/vnd.grafeq'],
            'gram' => ['application/srgs'],
            'gramps' => ['application/x-gramps-xml'],
            'gre' => ['application/vnd.geometry-explorer'],
            'grv' => ['application/vnd.groove-injector'],
            'grxml' => ['application/srgs+xml'],
            'gsd' => ['audio/x-gsm'],
            'gsf' => ['application/x-font-ghostscript'],
            'gsm' => ['audio/x-gsm'],
            'gsp' => ['application/x-gsp'],
            'gss' => ['application/x-gss'],
            'gtar' => ['application/x-gtar'],
            'gtm' => ['application/vnd.groove-tool-message'],
            'gtw' => ['model/vnd.gtw'],
            'gv' => ['text/vnd.graphviz'],
            'gxf' => ['application/gxf'],
            'gxt' => ['application/vnd.geonext'],
            'gz' => ['application/x-compressed', 'application/x-gzip'],
            'gzip' => ['application/x-gzip', 'multipart/x-gzip'],
            'h' => ['text/plain', 'text/x-h'],
            'h261' => ['video/h261'],
            'h263' => ['video/h263'],
            'h264' => ['video/h264'],
            'hal' => ['application/vnd.hal+xml'],
            'hbci' => ['application/vnd.hbci'],
            'hdf' => ['application/x-hdf'],
            'help' => ['application/x-helpfile'],
            'hgl' => ['application/vnd.hp-hpgl'],
            'hh' => ['text/plain', 'text/x-h'],
            'hlb' => ['text/x-script'],
            'hlp' => ['application/hlp', 'application/x-helpfile', 'application/x-winhelp'],
            'hpg' => ['application/vnd.hp-hpgl'],
            'hpgl' => ['application/vnd.hp-hpgl'],
            'hpid' => ['application/vnd.hp-hpid'],
            'hps' => ['application/vnd.hp-hps'],
            'hqx' => [
                'application/binhex',
                'application/binhex4',
                'application/mac-binhex',
                'application/mac-binhex40',
                'application/x-binhex40',
                'application/x-mac-binhex40'
            ],
            'hta' => ['application/hta'],
            'htc' => ['text/x-component'],
            'htke' => ['application/vnd.kenameaapp'],
            'htm' => ['text/html'],
            'html' => ['text/html'],
            'htmls' => ['text/html'],
            'htt' => ['text/webviewhtml'],
            'htx' => ['text/html'],
            'hvd' => ['application/vnd.yamaha.hv-dic'],
            'hvp' => ['application/vnd.yamaha.hv-voice'],
            'hvs' => ['application/vnd.yamaha.hv-script'],
            'i2g' => ['application/vnd.intergeo'],
            'icc' => ['application/vnd.iccprofile'],
            'ice' => ['x-conference/x-cooltalk'],
            'icm' => ['application/vnd.iccprofile'],
            'ico' => ['image/x-icon', 'image/vnd.microsoft.icon'],
            'ics' => ['text/calendar'],
            'idc' => ['text/plain'],
            'ief' => ['image/ief'],
            'iefs' => ['image/ief'],
            'ifb' => ['text/calendar'],
            'ifm' => ['application/vnd.shana.informed.formdata'],
            'iges' => ['application/iges', 'model/iges'],
            'igl' => ['application/vnd.igloader'],
            'igm' => ['application/vnd.insors.igm'],
            'igs' => ['application/iges', 'model/iges'],
            'igx' => ['application/vnd.micrografx.igx'],
            'iif' => ['application/vnd.shana.informed.interchange'],
            'ima' => ['application/x-ima'],
            'imap' => ['application/x-httpd-imap'],
            'imp' => ['application/vnd.accpac.simply.imp'],
            'ims' => ['application/vnd.ms-ims'],
            'in' => ['text/plain'],
            'inf' => ['application/inf'],
            'ink' => ['application/inkml+xml'],
            'inkml' => ['application/inkml+xml'],
            'ins' => ['application/x-internett-signup'],
            'install' => ['application/x-install-instructions'],
            'iota' => ['application/vnd.astraea-software.iota'],
            'ip' => ['application/x-ip2'],
            'ipfix' => ['application/ipfix'],
            'ipk' => ['application/vnd.shana.informed.package'],
            'irm' => ['application/vnd.ibm.rights-management'],
            'irp' => ['application/vnd.irepository.package+xml'],
            'iso' => ['application/x-iso9660-image'],
            'isu' => ['video/x-isvideo'],
            'it' => ['audio/it'],
            'itp' => ['application/vnd.shana.informed.formtemplate'],
            'iv' => ['application/x-inventor'],
            'ivp' => ['application/vnd.immervision-ivp'],
            'ivr' => ['i-world/i-vrml'],
            'ivu' => ['application/vnd.immervision-ivu'],
            'ivy' => ['application/x-livescreen'],
            'jad' => ['text/vnd.sun.j2me.app-descriptor'],
            'jam' => ['application/vnd.jam'],
            'jar' => ['application/java-archive'],
            'jav' => ['text/plain', 'text/x-java-source'],
            'java' => ['text/plain', 'text/x-java-source'],
            'jcm' => ['application/x-java-commerce'],
            'jfif' => ['image/jpeg', 'image/pjpeg'],
            'jfif-tbnl' => ['image/jpeg'],
            'jisp' => ['application/vnd.jisp'],
            'jlt' => ['application/vnd.hp-jlyt'],
            'jnlp' => ['application/x-java-jnlp-file'],
            'joda' => ['application/vnd.joost.joda-archive'],
            'jpe' => ['image/jpeg', 'image/pjpeg'],
            'jpeg' => ['image/jpeg', 'image/pjpeg'],
            'jpg' => ['image/jpeg', 'image/pjpeg'],
            'jpgm' => ['video/jpm'],
            'jpgv' => ['video/jpeg'],
            'jpm' => ['video/jpm'],
            'jps' => ['image/x-jps'],
            'js' => ['application/javascript'],
            'json' => ['application/json', 'text/plain'],
            'jsonml' => ['application/jsonml+json'],
            'jut' => ['image/jutvision'],
            'kar' => ['audio/midi', 'music/x-karaoke'],
            'karbon' => ['application/vnd.kde.karbon'],
            'kfo' => ['application/vnd.kde.kformula'],
            'kia' => ['application/vnd.kidspiration'],
            'kil' => ['application/x-killustrator'],
            'kml' => ['application/vnd.google-earth.kml+xml'],
            'kmz' => ['application/vnd.google-earth.kmz'],
            'kne' => ['application/vnd.kinar'],
            'knp' => ['application/vnd.kinar'],
            'kon' => ['application/vnd.kde.kontour'],
            'kpr' => ['application/vnd.kde.kpresenter'],
            'kpt' => ['application/vnd.kde.kpresenter'],
            'kpxx' => ['application/vnd.ds-keypoint'],
            'ksh' => ['application/x-ksh', 'text/x-script.ksh'],
            'ksp' => ['application/vnd.kde.kspread'],
            'ktr' => ['application/vnd.kahootz'],
            'ktx' => ['image/ktx'],
            'ktz' => ['application/vnd.kahootz'],
            'kwd' => ['application/vnd.kde.kword'],
            'kwt' => ['application/vnd.kde.kword'],
            'la' => ['audio/nspaudio', 'audio/x-nspaudio'],
            'lam' => ['audio/x-liveaudio'],
            'lasxml' => ['application/vnd.las.las+xml'],
            'latex' => ['application/x-latex'],
            'lbd' => ['application/vnd.llamagraphics.life-balance.desktop'],
            'lbe' => ['application/vnd.llamagraphics.life-balance.exchange+xml'],
            'les' => ['application/vnd.hhe.lesson-player'],
            'lha' => ['application/lha', 'application/octet-stream', 'application/x-lha'],
            'lhx' => ['application/octet-stream'],
            'link66' => ['application/vnd.route66.link66+xml'],
            'list' => ['text/plain'],
            'list3820' => ['application/vnd.ibm.modcap'],
            'listafp' => ['application/vnd.ibm.modcap'],
            'lma' => ['audio/nspaudio', 'audio/x-nspaudio'],
            'lnk' => ['application/x-ms-shortcut'],
            'log' => ['text/plain'],
            'lostxml' => ['application/lost+xml'],
            'lrf' => ['application/octet-stream'],
            'lrm' => ['application/vnd.ms-lrm'],
            'lsp' => ['application/x-lisp', 'text/x-script.lisp'],
            'lst' => ['text/plain'],
            'lsx' => ['text/x-la-asf'],
            'ltf' => ['application/vnd.frogans.ltf'],
            'ltx' => ['application/x-latex'],
            'lua' => ['text/x-lua'],
            'luac' => ['application/x-lua-bytecode'],
            'lvp' => ['audio/vnd.lucent.voice'],
            'lwp' => ['application/vnd.lotus-wordpro'],
            'lzh' => ['application/octet-stream', 'application/x-lzh'],
            'lzx' => ['application/lzx', 'application/octet-stream', 'application/x-lzx'],
            'm' => ['text/plain', 'text/x-m'],
            'm13' => ['application/x-msmediaview'],
            'm14' => ['application/x-msmediaview'],
            'm1v' => ['video/mpeg'],
            'm21' => ['application/mp21'],
            'm2a' => ['audio/mpeg'],
            'm2v' => ['video/mpeg'],
            'm3a' => ['audio/mpeg'],
            'm3u' => ['audio/x-mpegurl'],
            'm3u8' => ['application/x-mpegURL'],
            'm4a' => ['audio/mp4'],
            'm4p' => ['application/mp4'],
            'm4u' => ['video/vnd.mpegurl'],
            'm4v' => ['video/x-m4v'],
            'ma' => ['application/mathematica'],
            'mads' => ['application/mads+xml'],
            'mag' => ['application/vnd.ecowin.chart'],
            'maker' => ['application/vnd.framemaker'],
            'man' => ['text/troff'],
            'manifest' => ['text/cache-manifest'],
            'map' => ['application/x-navimap'],
            'mar' => ['application/octet-stream'],
            'markdown' => ['text/x-markdown'],
            'mathml' => ['application/mathml+xml'],
            'mb' => ['application/mathematica'],
            'mbd' => ['application/mbedlet'],
            'mbk' => ['application/vnd.mobius.mbk'],
            'mbox' => ['application/mbox'],
            'mc' => ['application/x-magic-cap-package-1.0'],
            'mc1' => ['application/vnd.medcalcdata'],
            'mcd' => ['application/mcad', 'application/x-mathcad'],
            'mcf' => ['image/vasa', 'text/mcf'],
            'mcp' => ['application/netmc'],
            'mcurl' => ['text/vnd.curl.mcurl'],
            'md' => ['text/x-markdown'],
            'mdb' => ['application/x-msaccess'],
            'mdi' => ['image/vnd.ms-modi'],
            'me' => ['text/troff'],
            'mesh' => ['model/mesh'],
            'meta4' => ['application/metalink4+xml'],
            'metalink' => ['application/metalink+xml'],
            'mets' => ['application/mets+xml'],
            'mfm' => ['application/vnd.mfmp'],
            'mft' => ['application/rpki-manifest'],
            'mgp' => ['application/vnd.osgeo.mapguide.package'],
            'mgz' => ['application/vnd.proteus.magazine'],
            'mht' => ['message/rfc822'],
            'mhtml' => ['message/rfc822'],
            'mid' => [
                'application/x-midi',
                'audio/midi',
                'audio/x-mid',
                'audio/x-midi',
                'music/crescendo',
                'x-music/x-midi'
            ],
            'midi' => [
                'application/x-midi',
                'audio/midi',
                'audio/x-mid',
                'audio/x-midi',
                'music/crescendo',
                'x-music/x-midi'
            ],
            'mie' => ['application/x-mie'],
            'mif' => ['application/x-frame', 'application/x-mif'],
            'mime' => ['message/rfc822', 'www/mime'],
            'mj2' => ['video/mj2'],
            'mjf' => ['audio/x-vnd.audioexplosion.mjuicemediafile'],
            'mjp2' => ['video/mj2'],
            'mjpg' => ['video/x-motion-jpeg'],
            'mk3d' => ['video/x-matroska'],
            'mka' => ['audio/x-matroska'],
            'mkd' => ['text/x-markdown'],
            'mks' => ['video/x-matroska'],
            'mkv' => ['video/x-matroska'],
            'mlp' => ['application/vnd.dolby.mlp'],
            'mm' => ['application/base64', 'application/x-meme'],
            'mmd' => ['application/vnd.chipnuts.karaoke-mmd'],
            'mme' => ['application/base64'],
            'mmf' => ['application/vnd.smaf'],
            'mmr' => ['image/vnd.fujixerox.edmics-mmr'],
            'mng' => ['video/x-mng'],
            'mny' => ['application/x-msmoney'],
            'mobi' => ['application/x-mobipocket-ebook'],
            'mod' => ['audio/mod', 'audio/x-mod'],
            'mods' => ['application/mods+xml'],
            'moov' => ['video/quicktime'],
            'mov' => ['video/quicktime'],
            'movie' => ['video/x-sgi-movie'],
            'mp2' => ['audio/mpeg', 'audio/x-mpeg', 'video/mpeg', 'video/x-mpeg', 'video/x-mpeq2a'],
            'mp21' => ['application/mp21'],
            'mp2a' => ['audio/mpeg'],
            'mp3' => ['audio/mpeg', 'audio/mpeg3', 'audio/x-mpeg-3', 'video/mpeg', 'video/x-mpeg'],
            'mp4' => ['video/mp4'],
            'mp4a' => ['audio/mp4'],
            'mp4s' => ['application/mp4'],
            'mp4v' => ['video/mp4'],
            'mpa' => ['audio/mpeg', 'video/mpeg'],
            'mpc' => ['application/vnd.mophun.certificate'],
            'mpe' => ['video/mpeg'],
            'mpeg' => ['video/mpeg'],
            'mpg' => ['audio/mpeg', 'video/mpeg'],
            'mpg4' => ['video/mp4'],
            'mpga' => ['audio/mpeg'],
            'mpkg' => ['application/vnd.apple.installer+xml'],
            'mpm' => ['application/vnd.blueice.multipass'],
            'mpn' => ['application/vnd.mophun.application'],
            'mpp' => ['application/vnd.ms-project'],
            'mpt' => ['application/vnd.ms-project'],
            'mpv' => ['application/x-project'],
            'mpx' => ['application/x-project'],
            'mpy' => ['application/vnd.ibm.minipay'],
            'mqy' => ['application/vnd.mobius.mqy'],
            'mrc' => ['application/marc'],
            'mrcx' => ['application/marcxml+xml'],
            'ms' => ['text/troff'],
            'mscml' => ['application/mediaservercontrol+xml'],
            'mseed' => ['application/vnd.fdsn.mseed'],
            'mseq' => ['application/vnd.mseq'],
            'msf' => ['application/vnd.epson.msf'],
            'msh' => ['model/mesh'],
            'msi' => ['application/x-msdownload'],
            'msl' => ['application/vnd.mobius.msl'],
            'msty' => ['application/vnd.muvee.style'],
            'mts' => ['model/vnd.mts'],
            'mus' => ['application/vnd.musician'],
            'musicxml' => ['application/vnd.recordare.musicxml+xml'],
            'mv' => ['video/x-sgi-movie'],
            'mvb' => ['application/x-msmediaview'],
            'mwf' => ['application/vnd.mfer'],
            'mxf' => ['application/mxf'],
            'mxl' => ['application/vnd.recordare.musicxml'],
            'mxml' => ['application/xv+xml'],
            'mxs' => ['application/vnd.triscape.mxs'],
            'mxu' => ['video/vnd.mpegurl'],
            'my' => ['audio/make'],
            'mzz' => ['application/x-vnd.audioexplosion.mzz'],
            'n-gage' => ['application/vnd.nokia.n-gage.symbian.install'],
            'n3' => ['text/n3'],
            'nap' => ['image/naplps'],
            'naplps' => ['image/naplps'],
            'nb' => ['application/mathematica'],
            'nbp' => ['application/vnd.wolfram.player'],
            'nc' => ['application/x-netcdf'],
            'ncm' => ['application/vnd.nokia.configuration-message'],
            'ncx' => ['application/x-dtbncx+xml'],
            'nfo' => ['text/x-nfo'],
            'ngdat' => ['application/vnd.nokia.n-gage.data'],
            'nif' => ['image/x-niff'],
            'niff' => ['image/x-niff'],
            'nitf' => ['application/vnd.nitf'],
            'nix' => ['application/x-mix-transfer'],
            'nlu' => ['application/vnd.neurolanguage.nlu'],
            'nml' => ['application/vnd.enliven'],
            'nnd' => ['application/vnd.noblenet-directory'],
            'nns' => ['application/vnd.noblenet-sealer'],
            'nnw' => ['application/vnd.noblenet-web'],
            'npx' => ['image/vnd.net-fpx'],
            'nsc' => ['application/x-conference'],
            'nsf' => ['application/vnd.lotus-notes'],
            'ntf' => ['application/vnd.nitf'],
            'nvd' => ['application/x-navidoc'],
            'nws' => ['message/rfc822'],
            'nzb' => ['application/x-nzb'],
            'o' => ['application/octet-stream'],
            'oa2' => ['application/vnd.fujitsu.oasys2'],
            'oa3' => ['application/vnd.fujitsu.oasys3'],
            'oas' => ['application/vnd.fujitsu.oasys'],
            'obd' => ['application/x-msbinder'],
            'obj' => ['application/x-tgif'],
            'oda' => ['application/oda'],
            'odb' => ['application/vnd.oasis.opendocument.database'],
            'odc' => ['application/vnd.oasis.opendocument.chart'],
            'odf' => ['application/vnd.oasis.opendocument.formula'],
            'odft' => ['application/vnd.oasis.opendocument.formula-template'],
            'odg' => ['application/vnd.oasis.opendocument.graphics'],
            'odi' => ['application/vnd.oasis.opendocument.image'],
            'odm' => ['application/vnd.oasis.opendocument.text-master'],
            'odp' => ['application/vnd.oasis.opendocument.presentation'],
            'ods' => ['application/vnd.oasis.opendocument.spreadsheet'],
            'odt' => ['application/vnd.oasis.opendocument.text'],
            'oga' => ['audio/ogg'],
            'ogg' => ['audio/ogg'],
            'ogv' => ['video/ogg'],
            'ogx' => ['application/ogg'],
            'omc' => ['application/x-omc'],
            'omcd' => ['application/x-omcdatamaker'],
            'omcr' => ['application/x-omcregerator'],
            'omdoc' => ['application/omdoc+xml'],
            'onepkg' => ['application/onenote'],
            'onetmp' => ['application/onenote'],
            'onetoc' => ['application/onenote'],
            'onetoc2' => ['application/onenote'],
            'opf' => ['application/oebps-package+xml'],
            'opml' => ['text/x-opml'],
            'oprc' => ['application/vnd.palm'],
            'org' => ['application/vnd.lotus-organizer'],
            'osf' => ['application/vnd.yamaha.openscoreformat'],
            'osfpvg' => ['application/vnd.yamaha.openscoreformat.osfpvg+xml'],
            'otc' => ['application/vnd.oasis.opendocument.chart-template'],
            'otf' => ['font/opentype'],
            'otg' => ['application/vnd.oasis.opendocument.graphics-template'],
            'oth' => ['application/vnd.oasis.opendocument.text-web'],
            'oti' => ['application/vnd.oasis.opendocument.image-template'],
            'otm' => ['application/vnd.oasis.opendocument.text-master'],
            'otp' => ['application/vnd.oasis.opendocument.presentation-template'],
            'ots' => ['application/vnd.oasis.opendocument.spreadsheet-template'],
            'ott' => ['application/vnd.oasis.opendocument.text-template'],
            'oxps' => ['application/oxps'],
            'oxt' => ['application/vnd.openofficeorg.extension'],
            'p' => ['text/x-pascal'],
            'p10' => ['application/pkcs10', 'application/x-pkcs10'],
            'p12' => ['application/pkcs-12', 'application/x-pkcs12'],
            'p7a' => ['application/x-pkcs7-signature'],
            'p7b' => ['application/x-pkcs7-certificates'],
            'p7c' => ['application/pkcs7-mime', 'application/x-pkcs7-mime'],
            'p7m' => ['application/pkcs7-mime', 'application/x-pkcs7-mime'],
            'p7r' => ['application/x-pkcs7-certreqresp'],
            'p7s' => ['application/pkcs7-signature'],
            'p8' => ['application/pkcs8'],
            'part' => ['application/pro_eng'],
            'pas' => ['text/x-pascal'],
            'paw' => ['application/vnd.pawaafile'],
            'pbd' => ['application/vnd.powerbuilder6'],
            'pbm' => ['image/x-portable-bitmap'],
            'pcap' => ['application/vnd.tcpdump.pcap'],
            'pcf' => ['application/x-font-pcf'],
            'pcl' => ['application/vnd.hp-pcl', 'application/x-pcl'],
            'pclxl' => ['application/vnd.hp-pclxl'],
            'pct' => ['image/x-pict'],
            'pcurl' => ['application/vnd.curl.pcurl'],
            'pcx' => ['image/x-pcx'],
            'pdb' => ['application/vnd.palm'],
            'pdf' => ['application/pdf'],
            'pfa' => ['application/x-font-type1'],
            'pfb' => ['application/x-font-type1'],
            'pfm' => ['application/x-font-type1'],
            'pfr' => ['application/font-tdpfr'],
            'pfunk' => ['audio/make'],
            'pfx' => ['application/x-pkcs12'],
            'pgm' => ['image/x-portable-graymap'],
            'pgn' => ['application/x-chess-pgn'],
            'pgp' => ['application/pgp-encrypted'],
            'php' => ['text/x-php'],
            'pic' => ['image/x-pict'],
            'pict' => ['image/pict'],
            'pkg' => ['application/octet-stream'],
            'pki' => ['application/pkixcmp'],
            'pkipath' => ['application/pkix-pkipath'],
            'pko' => ['application/vnd.ms-pki.pko'],
            'pl' => ['text/plain', 'text/x-script.perl'],
            'plb' => ['application/vnd.3gpp.pic-bw-large'],
            'plc' => ['application/vnd.mobius.plc'],
            'plf' => ['application/vnd.pocketlearn'],
            'pls' => ['application/pls+xml'],
            'plx' => ['application/x-pixclscript'],
            'pm' => ['image/x-xpixmap', 'text/x-script.perl-module'],
            'pm4' => ['application/x-pagemaker'],
            'pm5' => ['application/x-pagemaker'],
            'pml' => ['application/vnd.ctc-posml'],
            'png' => ['image/png'],
            'pnm' => ['application/x-portable-anymap', 'image/x-portable-anymap'],
            'portpkg' => ['application/vnd.macports.portpkg'],
            'pot' => ['application/mspowerpoint', 'application/vnd.ms-powerpoint'],
            'potm' => ['application/vnd.ms-powerpoint.template.macroenabled.12'],
            'potx' => ['application/vnd.openxmlformats-officedocument.presentationml.template'],
            'pov' => ['model/x-pov'],
            'ppa' => ['application/vnd.ms-powerpoint'],
            'ppam' => ['application/vnd.ms-powerpoint.addin.macroenabled.12'],
            'ppd' => ['application/vnd.cups-ppd'],
            'ppm' => ['image/x-portable-pixmap'],
            'pps' => ['application/mspowerpoint', 'application/vnd.ms-powerpoint'],
            'ppsm' => ['application/vnd.ms-powerpoint.slideshow.macroenabled.12'],
            'ppsx' => ['application/vnd.openxmlformats-officedocument.presentationml.slideshow'],
            'ppt' => [
                'application/mspowerpoint',
                'application/powerpoint',
                'application/vnd.ms-powerpoint',
                'application/x-mspowerpoint'
            ],
            'pptm' => ['application/vnd.ms-powerpoint.presentation.macroenabled.12'],
            'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
            'ppz' => ['application/mspowerpoint'],
            'pqa' => ['application/vnd.palm'],
            'prc' => ['application/x-mobipocket-ebook'],
            'pre' => ['application/vnd.lotus-freelance'],
            'prf' => ['application/pics-rules'],
            'prt' => ['application/pro_eng'],
            'ps' => ['application/postscript'],
            'psb' => ['application/vnd.3gpp.pic-bw-small'],
            'psd' => ['image/vnd.adobe.photoshop'],
            'psf' => ['application/x-font-linux-psf'],
            'pskcxml' => ['application/pskc+xml'],
            'ptid' => ['application/vnd.pvi.ptid1'],
            'pub' => ['application/x-mspublisher'],
            'pvb' => ['application/vnd.3gpp.pic-bw-var'],
            'pvu' => ['paleovu/x-pv'],
            'pwn' => ['application/vnd.3m.post-it-notes'],
            'pwz' => ['application/vnd.ms-powerpoint'],
            'py' => ['text/x-script.phyton'],
            'pya' => ['audio/vnd.ms-playready.media.pya'],
            'pyc' => ['applicaiton/x-bytecode.python'],
            'pyo' => ['application/x-python-code'],
            'pyv' => ['video/vnd.ms-playready.media.pyv'],
            'qam' => ['application/vnd.epson.quickanime'],
            'qbo' => ['application/vnd.intu.qbo'],
            'qcp' => ['audio/vnd.qcelp'],
            'qd3' => ['x-world/x-3dmf'],
            'qd3d' => ['x-world/x-3dmf'],
            'qfx' => ['application/vnd.intu.qfx'],
            'qif' => ['image/x-quicktime'],
            'qps' => ['application/vnd.publishare-delta-tree'],
            'qt' => ['video/quicktime'],
            'qtc' => ['video/x-qtc'],
            'qti' => ['image/x-quicktime'],
            'qtif' => ['image/x-quicktime'],
            'qwd' => ['application/vnd.quark.quarkxpress'],
            'qwt' => ['application/vnd.quark.quarkxpress'],
            'qxb' => ['application/vnd.quark.quarkxpress'],
            'qxd' => ['application/vnd.quark.quarkxpress'],
            'qxl' => ['application/vnd.quark.quarkxpress'],
            'qxt' => ['application/vnd.quark.quarkxpress'],
            'ra' => ['audio/x-pn-realaudio', 'audio/x-pn-realaudio-plugin', 'audio/x-realaudio'],
            'ram' => ['audio/x-pn-realaudio'],
            'rar' => ['application/x-rar-compressed', 'application/vnd.rar', 'application/x-rar'],
            'ras' => ['application/x-cmu-raster', 'image/cmu-raster', 'image/x-cmu-raster'],
            'rast' => ['image/cmu-raster'],
            'rcprofile' => ['application/vnd.ipunplugged.rcprofile'],
            'rdf' => ['application/rdf+xml'],
            'rdz' => ['application/vnd.data-vision.rdz'],
            'rep' => ['application/vnd.businessobjects'],
            'res' => ['application/x-dtbresource+xml'],
            'rexx' => ['text/x-script.rexx'],
            'rf' => ['image/vnd.rn-realflash'],
            'rgb' => ['image/x-rgb'],
            'rif' => ['application/reginfo+xml'],
            'rip' => ['audio/vnd.rip'],
            'ris' => ['application/x-research-info-systems'],
            'rl' => ['application/resource-lists+xml'],
            'rlc' => ['image/vnd.fujixerox.edmics-rlc'],
            'rld' => ['application/resource-lists-diff+xml'],
            'rm' => ['application/vnd.rn-realmedia', 'audio/x-pn-realaudio'],
            'rmi' => ['audio/midi'],
            'rmm' => ['audio/x-pn-realaudio'],
            'rmp' => ['audio/x-pn-realaudio', 'audio/x-pn-realaudio-plugin'],
            'rms' => ['application/vnd.jcp.javame.midlet-rms'],
            'rmvb' => ['application/vnd.rn-realmedia-vbr'],
            'rnc' => ['application/relax-ng-compact-syntax'],
            'rng' => ['application/ringing-tones', 'application/vnd.nokia.ringing-tone'],
            'rnx' => ['application/vnd.rn-realplayer'],
            'roa' => ['application/rpki-roa'],
            'roff' => ['text/troff'],
            'rp' => ['image/vnd.rn-realpix'],
            'rp9' => ['application/vnd.cloanto.rp9'],
            'rpm' => ['audio/x-pn-realaudio-plugin'],
            'rpss' => ['application/vnd.nokia.radio-presets'],
            'rpst' => ['application/vnd.nokia.radio-preset'],
            'rq' => ['application/sparql-query'],
            'rs' => ['application/rls-services+xml'],
            'rsd' => ['application/rsd+xml'],
            'rss' => ['application/rss+xml'],
            'rt' => ['text/richtext', 'text/vnd.rn-realtext'],
            'rtf' => ['application/rtf', 'application/x-rtf', 'text/richtext'],
            'rtx' => ['application/rtf', 'text/richtext'],
            'rv' => ['video/vnd.rn-realvideo'],
            's' => ['text/x-asm'],
            's3m' => ['audio/s3m'],
            'saf' => ['application/vnd.yamaha.smaf-audio'],
            'saveme' => ['aapplication/octet-stream'],
            'sbk' => ['application/x-tbook'],
            'sbml' => ['application/sbml+xml'],
            'sc' => ['application/vnd.ibm.secure-container'],
            'scd' => ['application/x-msschedule'],
            'scm' => ['application/x-lotusscreencam', 'text/x-script.guile', 'text/x-script.scheme', 'video/x-scm'],
            'scq' => ['application/scvp-cv-request'],
            'scs' => ['application/scvp-cv-response'],
            'scurl' => ['text/vnd.curl.scurl'],
            'sda' => ['application/vnd.stardivision.draw'],
            'sdc' => ['application/vnd.stardivision.calc'],
            'sdd' => ['application/vnd.stardivision.impress'],
            'sdkd' => ['application/vnd.solent.sdkm+xml'],
            'sdkm' => ['application/vnd.solent.sdkm+xml'],
            'sdml' => ['text/plain'],
            'sdp' => ['application/sdp', 'application/x-sdp'],
            'sdr' => ['application/sounder'],
            'sdw' => ['application/vnd.stardivision.writer'],
            'sea' => ['application/sea', 'application/x-sea'],
            'see' => ['application/vnd.seemail'],
            'seed' => ['application/vnd.fdsn.seed'],
            'sema' => ['application/vnd.sema'],
            'semd' => ['application/vnd.semd'],
            'semf' => ['application/vnd.semf'],
            'ser' => ['application/java-serialized-object'],
            'set' => ['application/set'],
            'setpay' => ['application/set-payment-initiation'],
            'setreg' => ['application/set-registration-initiation'],
            'sfd-hdstx' => ['application/vnd.hydrostatix.sof-data'],
            'sfs' => ['application/vnd.spotfire.sfs'],
            'sfv' => ['text/x-sfv'],
            'sgi' => ['image/sgi'],
            'sgl' => ['application/vnd.stardivision.writer-global'],
            'sgm' => ['text/sgml', 'text/x-sgml'],
            'sgml' => ['text/sgml', 'text/x-sgml'],
            'sh' => ['application/x-bsh', 'application/x-sh', 'application/x-shar', 'text/x-script.sh'],
            'shar' => ['application/x-bsh', 'application/x-shar'],
            'shf' => ['application/shf+xml'],
            'shtml' => ['text/html', 'text/x-server-parsed-html'],
            'si' => ['text/vnd.wap.si'],
            'sic' => ['application/vnd.wap.sic'],
            'sid' => ['image/x-mrsid-image'],
            'sig' => ['application/pgp-signature'],
            'sil' => ['audio/silk'],
            'silo' => ['model/mesh'],
            'sis' => ['application/vnd.symbian.install'],
            'sisx' => ['application/vnd.symbian.install'],
            'sit' => ['application/x-sit', 'application/x-stuffit'],
            'sitx' => ['application/x-stuffitx'],
            'skd' => ['application/vnd.koan'],
            'skm' => ['application/vnd.koan'],
            'skp' => ['application/vnd.koan'],
            'skt' => ['application/vnd.koan'],
            'sl' => ['application/x-seelogo'],
            'slc' => ['application/vnd.wap.slc'],
            'sldm' => ['application/vnd.ms-powerpoint.slide.macroenabled.12'],
            'sldx' => ['application/vnd.openxmlformats-officedocument.presentationml.slide'],
            'slt' => ['application/vnd.epson.salt'],
            'sm' => ['application/vnd.stepmania.stepchart'],
            'smf' => ['application/vnd.stardivision.math'],
            'smi' => ['application/smil+xml'],
            'smil' => ['application/smil+xml'],
            'smv' => ['video/x-smv'],
            'smzip' => ['application/vnd.stepmania.package'],
            'snd' => ['audio/basic', 'audio/x-adpcm'],
            'snf' => ['application/x-font-snf'],
            'so' => ['application/octet-stream'],
            'sol' => ['application/solids'],
            'spc' => ['application/x-pkcs7-certificates', 'text/x-speech'],
            'spf' => ['application/vnd.yamaha.smaf-phrase'],
            'spl' => ['application/x-futuresplash'],
            'spot' => ['text/vnd.in3d.spot'],
            'spp' => ['application/scvp-vp-response'],
            'spq' => ['application/scvp-vp-request'],
            'spr' => ['application/x-sprite'],
            'sprite' => ['application/x-sprite'],
            'spx' => ['audio/ogg'],
            'sql' => ['application/x-sql'],
            'src' => ['application/x-wais-source'],
            'srt' => ['application/x-subrip'],
            'sru' => ['application/sru+xml'],
            'srx' => ['application/sparql-results+xml'],
            'ssdl' => ['application/ssdl+xml'],
            'sse' => ['application/vnd.kodak-descriptor'],
            'ssf' => ['application/vnd.epson.ssf'],
            'ssi' => ['text/x-server-parsed-html'],
            'ssm' => ['application/streamingmedia'],
            'ssml' => ['application/ssml+xml'],
            'sst' => ['application/vnd.ms-pki.certstore'],
            'st' => ['application/vnd.sailingtracker.track'],
            'stc' => ['application/vnd.sun.xml.calc.template'],
            'std' => ['application/vnd.sun.xml.draw.template'],
            'step' => ['application/step'],
            'stf' => ['application/vnd.wt.stf'],
            'sti' => ['application/vnd.sun.xml.impress.template'],
            'stk' => ['application/hyperstudio'],
            'stl' => ['application/sla', 'application/vnd.ms-pki.stl', 'application/x-navistyle'],
            'stp' => ['application/step'],
            'str' => ['application/vnd.pg.format'],
            'stw' => ['application/vnd.sun.xml.writer.template'],
            'sub' => ['text/vnd.dvb.subtitle'],
            'sus' => ['application/vnd.sus-calendar'],
            'susp' => ['application/vnd.sus-calendar'],
            'sv4cpio' => ['application/x-sv4cpio'],
            'sv4crc' => ['application/x-sv4crc'],
            'svc' => ['application/vnd.dvb.service'],
            'svd' => ['application/vnd.svd'],
            'svf' => ['image/vnd.dwg', 'image/x-dwg'],
            'svg' => ['image/svg+xml'],
            'svgz' => ['image/svg+xml'],
            'svr' => ['application/x-world', 'x-world/x-svr'],
            'swa' => ['application/x-director'],
            'swf' => ['application/x-shockwave-flash'],
            'swi' => ['application/vnd.aristanetworks.swi'],
            'sxc' => ['application/vnd.sun.xml.calc'],
            'sxd' => ['application/vnd.sun.xml.draw'],
            'sxg' => ['application/vnd.sun.xml.writer.global'],
            'sxi' => ['application/vnd.sun.xml.impress'],
            'sxm' => ['application/vnd.sun.xml.math'],
            'sxw' => ['application/vnd.sun.xml.writer'],
            't' => ['text/troff'],
            't3' => ['application/x-t3vm-image'],
            'taglet' => ['application/vnd.mynfc'],
            'talk' => ['text/x-speech'],
            'tao' => ['application/vnd.tao.intent-module-archive'],
            'tar' => ['application/x-tar'],
            'tbk' => ['application/toolbook', 'application/x-tbook'],
            'tcap' => ['application/vnd.3gpp2.tcap'],
            'tcl' => ['application/x-tcl', 'text/x-script.tcl'],
            'tcsh' => ['text/x-script.tcsh'],
            'teacher' => ['application/vnd.smart.teacher'],
            'tei' => ['application/tei+xml'],
            'teicorpus' => ['application/tei+xml'],
            'tex' => ['application/x-tex'],
            'texi' => ['application/x-texinfo'],
            'texinfo' => ['application/x-texinfo'],
            'text' => ['application/plain', 'text/plain'],
            'tfi' => ['application/thraud+xml'],
            'tfm' => ['application/x-tex-tfm'],
            'tga' => ['image/x-tga'],
            'tgz' => ['application/gnutar', 'application/x-compressed'],
            'thmx' => ['application/vnd.ms-officetheme'],
            'tif' => ['image/tiff', 'image/x-tiff'],
            'tiff' => ['image/tiff', 'image/x-tiff'],
            'tmo' => ['application/vnd.tmobile-livetv'],
            'torrent' => ['application/x-bittorrent'],
            'tpl' => ['application/vnd.groove-tool-template'],
            'tpt' => ['application/vnd.trid.tpt'],
            'tr' => ['text/troff'],
            'tra' => ['application/vnd.trueapp'],
            'trm' => ['application/x-msterminal'],
            'ts' => ['video/MP2T'],
            'tsd' => ['application/timestamped-data'],
            'tsi' => ['audio/tsp-audio'],
            'tsp' => ['application/dsptype', 'audio/tsplayer'],
            'tsv' => ['text/tab-separated-values'],
            'ttc' => ['application/x-font-ttf'],
            'ttf' => ['application/x-font-ttf'],
            'ttl' => ['text/turtle'],
            'turbot' => ['image/florian'],
            'twd' => ['application/vnd.simtech-mindmapper'],
            'twds' => ['application/vnd.simtech-mindmapper'],
            'txd' => ['application/vnd.genomatix.tuxedo'],
            'txf' => ['application/vnd.mobius.txf'],
            'txt' => ['text/plain'],
            'u32' => ['application/x-authorware-bin'],
            'udeb' => ['application/x-debian-package'],
            'ufd' => ['application/vnd.ufdl'],
            'ufdl' => ['application/vnd.ufdl'],
            'uil' => ['text/x-uil'],
            'ulx' => ['application/x-glulx'],
            'umj' => ['application/vnd.umajin'],
            'uni' => ['text/uri-list'],
            'unis' => ['text/uri-list'],
            'unityweb' => ['application/vnd.unity'],
            'unv' => ['application/i-deas'],
            'uoml' => ['application/vnd.uoml+xml'],
            'uri' => ['text/uri-list'],
            'uris' => ['text/uri-list'],
            'urls' => ['text/uri-list'],
            'ustar' => ['application/x-ustar', 'multipart/x-ustar'],
            'utz' => ['application/vnd.uiq.theme'],
            'uu' => ['application/octet-stream', 'text/x-uuencode'],
            'uue' => ['text/x-uuencode'],
            'uva' => ['audio/vnd.dece.audio'],
            'uvd' => ['application/vnd.dece.data'],
            'uvf' => ['application/vnd.dece.data'],
            'uvg' => ['image/vnd.dece.graphic'],
            'uvh' => ['video/vnd.dece.hd'],
            'uvi' => ['image/vnd.dece.graphic'],
            'uvm' => ['video/vnd.dece.mobile'],
            'uvp' => ['video/vnd.dece.pd'],
            'uvs' => ['video/vnd.dece.sd'],
            'uvt' => ['application/vnd.dece.ttml+xml'],
            'uvu' => ['video/vnd.uvvu.mp4'],
            'uvv' => ['video/vnd.dece.video'],
            'uvva' => ['audio/vnd.dece.audio'],
            'uvvd' => ['application/vnd.dece.data'],
            'uvvf' => ['application/vnd.dece.data'],
            'uvvg' => ['image/vnd.dece.graphic'],
            'uvvh' => ['video/vnd.dece.hd'],
            'uvvi' => ['image/vnd.dece.graphic'],
            'uvvm' => ['video/vnd.dece.mobile'],
            'uvvp' => ['video/vnd.dece.pd'],
            'uvvs' => ['video/vnd.dece.sd'],
            'uvvt' => ['application/vnd.dece.ttml+xml'],
            'uvvu' => ['video/vnd.uvvu.mp4'],
            'uvvv' => ['video/vnd.dece.video'],
            'uvvx' => ['application/vnd.dece.unspecified'],
            'uvvz' => ['application/vnd.dece.zip'],
            'uvx' => ['application/vnd.dece.unspecified'],
            'uvz' => ['application/vnd.dece.zip'],
            'vcard' => ['text/vcard'],
            'vcd' => ['application/x-cdlink'],
            'vcf' => ['text/x-vcard'],
            'vcg' => ['application/vnd.groove-vcard'],
            'vcs' => ['text/x-vcalendar'],
            'vcx' => ['application/vnd.vcx'],
            'vda' => ['application/vda'],
            'vdo' => ['video/vdo'],
            'vew' => ['application/groupwise'],
            'vis' => ['application/vnd.visionary'],
            'viv' => ['video/vivo', 'video/vnd.vivo'],
            'vivo' => ['video/vivo', 'video/vnd.vivo'],
            'vmd' => ['application/vocaltec-media-desc'],
            'vmf' => ['application/vocaltec-media-file'],
            'vob' => ['video/x-ms-vob'],
            'voc' => ['audio/voc', 'audio/x-voc'],
            'vor' => ['application/vnd.stardivision.writer'],
            'vos' => ['video/vosaic'],
            'vox' => ['application/x-authorware-bin'],
            'vqe' => ['audio/x-twinvq-plugin'],
            'vqf' => ['audio/x-twinvq'],
            'vql' => ['audio/x-twinvq-plugin'],
            'vrml' => ['application/x-vrml', 'model/vrml', 'x-world/x-vrml'],
            'vrt' => ['x-world/x-vrt'],
            'vsd' => ['application/vnd.visio'],
            'vsf' => ['application/vnd.vsf'],
            'vss' => ['application/vnd.visio'],
            'vst' => ['application/vnd.visio'],
            'vsw' => ['application/vnd.visio'],
            'vtt' => ['text/vtt'],
            'vtu' => ['model/vnd.vtu'],
            'vxml' => ['application/voicexml+xml'],
            'w3d' => ['application/x-director'],
            'w60' => ['application/wordperfect6.0'],
            'w61' => ['application/wordperfect6.1'],
            'w6w' => ['application/msword'],
            'wad' => ['application/x-doom'],
            'wav' => ['audio/wav', 'audio/x-wav'],
            'wax' => ['audio/x-ms-wax'],
            'wb1' => ['application/x-qpro'],
            'wbmp' => ['image/vnd.wap.wbmp'],
            'wbs' => ['application/vnd.criticaltools.wbs+xml'],
            'wbxml' => ['application/vnd.wap.wbxml'],
            'wcm' => ['application/vnd.ms-works'],
            'wdb' => ['application/vnd.ms-works'],
            'wdp' => ['image/vnd.ms-photo'],
            'web' => ['application/vnd.xara'],
            'weba' => ['audio/webm'],
            'webapp' => ['application/x-web-app-manifest+json'],
            'webm' => ['video/webm'],
            'webp' => ['image/webp'],
            'wg' => ['application/vnd.pmi.widget'],
            'wgt' => ['application/widget'],
            'wiz' => ['application/msword'],
            'wk1' => ['application/x-123'],
            'wks' => ['application/vnd.ms-works'],
            'wm' => ['video/x-ms-wm'],
            'wma' => ['audio/x-ms-wma'],
            'wmd' => ['application/x-ms-wmd'],
            'wmf' => ['application/x-msmetafile'],
            'wml' => ['text/vnd.wap.wml'],
            'wmlc' => ['application/vnd.wap.wmlc'],
            'wmls' => ['text/vnd.wap.wmlscript'],
            'wmlsc' => ['application/vnd.wap.wmlscriptc'],
            'wmv' => ['video/x-ms-wmv'],
            'wmx' => ['video/x-ms-wmx'],
            'wmz' => ['application/x-msmetafile'],
            'woff' => ['application/x-font-woff'],
            'word' => ['application/msword'],
            'wp' => ['application/wordperfect'],
            'wp5' => ['application/wordperfect', 'application/wordperfect6.0'],
            'wp6' => ['application/wordperfect'],
            'wpd' => ['application/wordperfect', 'application/x-wpwin'],
            'wpl' => ['application/vnd.ms-wpl'],
            'wps' => ['application/vnd.ms-works'],
            'wq1' => ['application/x-lotus'],
            'wqd' => ['application/vnd.wqd'],
            'wri' => ['application/mswrite', 'application/x-wri'],
            'wrl' => ['application/x-world', 'model/vrml', 'x-world/x-vrml'],
            'wrz' => ['model/vrml', 'x-world/x-vrml'],
            'wsc' => ['text/scriplet'],
            'wsdl' => ['application/wsdl+xml'],
            'wspolicy' => ['application/wspolicy+xml'],
            'wsrc' => ['application/x-wais-source'],
            'wtb' => ['application/vnd.webturbo'],
            'wtk' => ['application/x-wintalk'],
            'wvx' => ['video/x-ms-wvx'],
            'x-png' => ['image/png'],
            'x32' => ['application/x-authorware-bin'],
            'x3d' => ['model/x3d+xml'],
            'x3db' => ['model/x3d+binary'],
            'x3dbz' => ['model/x3d+binary'],
            'x3dv' => ['model/x3d+vrml'],
            'x3dvz' => ['model/x3d+vrml'],
            'x3dz' => ['model/x3d+xml'],
            'xaml' => ['application/xaml+xml'],
            'xap' => ['application/x-silverlight-app'],
            'xar' => ['application/vnd.xara'],
            'xbap' => ['application/x-ms-xbap'],
            'xbd' => ['application/vnd.fujixerox.docuworks.binder'],
            'xbm' => ['image/x-xbitmap', 'image/x-xbm', 'image/xbm'],
            'xdf' => ['application/xcap-diff+xml'],
            'xdm' => ['application/vnd.syncml.dm+xml'],
            'xdp' => ['application/vnd.adobe.xdp+xml'],
            'xdr' => ['video/x-amt-demorun'],
            'xdssc' => ['application/dssc+xml'],
            'xdw' => ['application/vnd.fujixerox.docuworks'],
            'xenc' => ['application/xenc+xml'],
            'xer' => ['application/patch-ops-error+xml'],
            'xfdf' => ['application/vnd.adobe.xfdf'],
            'xfdl' => ['application/vnd.xfdl'],
            'xgz' => ['xgl/drawing'],
            'xht' => ['application/xhtml+xml'],
            'xhtml' => ['application/xhtml+xml'],
            'xhvml' => ['application/xv+xml'],
            'xif' => ['image/vnd.xiff'],
            'xl' => ['application/excel'],
            'xla' => ['application/excel', 'application/x-excel', 'application/x-msexcel'],
            'xlam' => ['application/vnd.ms-excel.addin.macroenabled.12'],
            'xlb' => ['application/excel', 'application/vnd.ms-excel', 'application/x-excel'],
            'xlc' => ['application/excel', 'application/vnd.ms-excel', 'application/x-excel'],
            'xld' => ['application/excel', 'application/x-excel'],
            'xlf' => ['application/x-xliff+xml'],
            'xlk' => ['application/excel', 'application/x-excel'],
            'xll' => ['application/excel', 'application/vnd.ms-excel', 'application/x-excel'],
            'xlm' => ['application/excel', 'application/vnd.ms-excel', 'application/x-excel'],
            'xls' => ['application/excel', 'application/vnd.ms-excel', 'application/x-excel', 'application/x-msexcel'],
            'xlsb' => ['application/vnd.ms-excel.sheet.binary.macroenabled.12'],
            'xlsm' => ['application/vnd.ms-excel.sheet.macroenabled.12'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'xlt' => ['application/excel', 'application/x-excel'],
            'xltm' => ['application/vnd.ms-excel.template.macroenabled.12'],
            'xltx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.template'],
            'xlv' => ['application/excel', 'application/x-excel'],
            'xlw' => ['application/excel', 'application/vnd.ms-excel', 'application/x-excel', 'application/x-msexcel'],
            'xm' => ['audio/xm'],
            'xml' => ['application/xml', 'text/xml'],
            'xmz' => ['xgl/movie'],
            'xo' => ['application/vnd.olpc-sugar'],
            'xop' => ['application/xop+xml'],
            'xpdl' => ['application/xml'],
            'xpi' => ['application/x-xpinstall'],
            'xpix' => ['application/x-vnd.ls-xpix'],
            'xpl' => ['application/xproc+xml'],
            'xpm' => ['image/x-xpixmap', 'image/xpm'],
            'xpr' => ['application/vnd.is-xpr'],
            'xps' => ['application/vnd.ms-xpsdocument'],
            'xpw' => ['application/vnd.intercon.formnet'],
            'xpx' => ['application/vnd.intercon.formnet'],
            'xsl' => ['application/xml'],
            'xslt' => ['application/xslt+xml'],
            'xsm' => ['application/vnd.syncml+xml'],
            'xspf' => ['application/xspf+xml'],
            'xsr' => ['video/x-amt-showrun'],
            'xul' => ['application/vnd.mozilla.xul+xml'],
            'xvm' => ['application/xv+xml'],
            'xvml' => ['application/xv+xml'],
            'xwd' => ['image/x-xwd', 'image/x-xwindowdump'],
            'xyz' => ['chemical/x-xyz'],
            'xz' => ['application/x-xz'],
            'yang' => ['application/yang'],
            'yin' => ['application/yin+xml'],
            'z' => ['application/x-compress', 'application/x-compressed'],
            'z1' => ['application/x-zmachine'],
            'z2' => ['application/x-zmachine'],
            'z3' => ['application/x-zmachine'],
            'z4' => ['application/x-zmachine'],
            'z5' => ['application/x-zmachine'],
            'z6' => ['application/x-zmachine'],
            'z7' => ['application/x-zmachine'],
            'z8' => ['application/x-zmachine'],
            'zaz' => ['application/vnd.zzazz.deck+xml'],
            'zip' => ['application/x-compressed', 'application/x-zip-compressed', 'application/zip', 'multipart/x-zip'],
            'zir' => ['application/vnd.zul'],
            'zirz' => ['application/vnd.zul'],
            'zmm' => ['application/vnd.handheld-entertainment+xml'],
            'zoo' => ['application/octet-stream'],
            'zsh' => ['text/x-script.zsh'],
            '123' => ['application/vnd.lotus-1-2-3']
        ];
    }

}