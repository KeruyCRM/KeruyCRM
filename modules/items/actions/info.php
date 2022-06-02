<?php

//check if item it not empty
if ($current_item_id == 0 and !strlen($app_module_action)) {
    redirect_to('dashboard/page_not_found');
}

//keep current listing page
if (isset($_GET['gotopage'])) {
    $listing_page_keeper[key($_GET['gotopage'])] = current($_GET['gotopage']);
}

$entity_info = db_find('app_entities', $current_entity_id);
$entity_cfg = new entities_cfg($current_entity_id);
$item_info = db_find('app_entity_' . $current_entity_id, $current_item_id);

//force redirect to report
if ($app_redirect_to == 'subentity') {
    items_redirects::redirect_to_report($entity_cfg->get('redirect_after_click_heading'), $app_path);
}

if ($app_redirect_to == 'subentity' and $entity_cfg->get('redirect_after_click_heading', 'subentity') == 'subentity') {
    $entity_query = db_query(
        "select id from app_entities where parent_id='" . db_input($current_entity_id) . "' order by sort_order, name"
    );
    while ($entity = db_fetch_array($entity_query)) {
        if (isset($app_users_access[$entity['id']]) or $app_user['group_id'] == 0) {
            redirect_to('items/items', 'path=' . $_GET['path'] . '/' . $entity['id']);
        }
    }
}

//reset users notifications
users_notifications::reset($current_entity_id, $current_item_id);

$app_title = app_set_title($app_breadcrumb[count($app_breadcrumb) - 1]['title']);

switch ($app_module_action) {
    case 'preview_user_photo':
        $file = base64_decode($_GET['file']);

        $file = attachments::parse_filename($file);

        if (is_file(DIR_WS_USERS . $file['file_sha1'])) {
            $size = getimagesize(DIR_WS_USERS . $file['file_sha1']);
            echo '<img width="' . $size[0] . '" height="' . $size[1] . '" src="' . DIR_WS_USERS . $file['file_sha1'] . '">';
        }
        exit();
        break;
    case 'download_user_photo':
        $file = attachments::parse_filename(base64_decode($_GET['file']));

        if (is_file(DIR_WS_USERS . $file['file_sha1'])) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $file['name']);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');

            flush();

            readfile(DIR_WS_USERS . $file['file_sha1']);
        }

        exit();
        break;
    case 'preview_attachment_image':
        $file = attachments::parse_filename(base64_decode($_GET['file']));

        if (is_file($file['file_path']) and $file['is_image']) {
            if (isset($_GET['rotate'])) {
                attachments::rotate_image($file['file_path'], $_GET['rotate']);

                if (attachments::has_image_preview($file)) {
                    attachments::delete_image_preview($file);
                    attachments::prepare_image_preview($file);
                }
            }

            if (!$size = getimagesize($file['file_path'])) {
                exit();
            }

            $width = $size[0];
            $height = $size[1];

            $html = '';

            if (isset($_GET['windowWidth'])) {
                $maxWidth = _GET('windowWidth') - (is_mobile() ? 70 : 170);

                if ($width > $maxWidth) {
                    //get percen differecne
                    $diff = ($width - $maxWidth) / $width * 100;

                    $width = $width - (($width / 100) * $diff);
                    $height = $height - (($height / 100) * $diff);
                }

                if ($file['mime_type'] != 'image/gif') {
                    //menu
                    $file = urlencode(base64_encode(base64_decode($_GET['file'])));
                    $html .= '
                    <div id="img_previw_menu_box" style="display:none">
                        <div class="img-preview-menu">
                            <a title="' . TEXT_ROTATE . '" class="btn btn-default btn-img-rotate" href="' . url_for(
                            'items/info',
                            'path=' . $app_path . '&rotate=left&windowWidth=' . _GET(
                                'windowWidth'
                            ) . '&windowHeight=' . _GET(
                                'windowHeight'
                            ) . '&action=preview_attachment_image&file=' . $file
                        ) . '"><i class="las la-undo-alt"></i></a>
                            <a title="' . TEXT_DOWNLOAD . '" class="btn btn-default" href="' . url_for(
                            'items/info',
                            'path=' . $app_path . '&action=download_attachment&file=' . $file
                        ) . '"><i class="las la-download"></i></a>
                            <a title="' . TEXT_ROTATE . '" class="btn btn-default btn-img-rotate" href="' . url_for(
                            'items/info',
                            'path=' . $app_path . '&rotate=right&windowWidth=' . _GET(
                                'windowWidth'
                            ) . '&windowHeight=' . _GET(
                                'windowHeight'
                            ) . '&action=preview_attachment_image&file=' . $file
                        ) . '"><i class="las la-redo-alt"></i></a>
                        </div>
                    </div>
                ';

                    $html .= '
                <script>
                $(function(){
                    $(".fancybox-inner").before($("#img_previw_menu_box").html())
                    $("#img_previw_menu_box").remove();
                    
                    $(".btn-img-rotate").click(function(){
                        $(this).attr("disabled",true)
                    })
                    
                    $(".btn-img-rotate").fancybox({
                         type: "ajax",
                         helpers : {
                            title : null            
                        }
                    });
                })                
                </script>
                ';
                }
            }


            $html .= '<img  width="' . $width . '" height="' . $height . '"  src="' . url_for(
                    'items/info&path=' . $_GET['path'],
                    '&action=download_attachment&preview=1&file=' . urlencode(
                        $_GET['file']
                    ) . (isset($_GET['rotate']) ? '&time=' . time() : '')
                ) . '">';

            echo $html;
        }

        exit();
        break;
    case 'download_attachment':
        $file = attachments::parse_filename(base64_decode($_GET['file']));

        //check if using file storage for feild
        if (class_exists('file_storage') and isset($_GET['field'])) {
            file_storage::download_file(_get::int('field'), base64_decode($_GET['file']));
        }

        if (is_file($file['file_path'])) {
            if ($file['is_image'] and isset($_GET['preview'])) {
                if ($_GET['preview'] == 'small' and CFG_CREATE_ATTACHMENTS_PREVIEW == 1) {
                    $file['file_path'] = attachments::prepare_image_preview($file);
                }

                header("Content-type: " . $file['mime_type']);
                header('Content-Disposition: filename="' . $file['name'] . '"');

                flush();

                readfile($file['file_path']);
            } elseif ($file['is_audio'] and isset($_GET['preview'])) {
                $type = mime_content_type($path);
                header("Content-type: " . $type);
                header('Content-Disposition: filename="' . $file['name'] . '"');

                flush();

                readfile($file['file_path']);
            } elseif ($file['is_pdf'] and isset($_GET['preview'])) {
                header("Content-type: application/pdf");
                header('Content-Disposition: filename="' . $file['name'] . '"');

                flush();

                readfile($file['file_path']);
            } else {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . $file['name']);
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file['file_path']));

                flush();

                readfile($file['file_path']);
            }
        } else {
            echo TEXT_FILE_NOT_FOUD;
        }

        exit();
        break;

    case 'download_all_attachments':
        $item_info = db_find('app_entity_' . $current_entity_id, $current_item_id);

        //check if attachments exist
        if (strlen($attachments = $item_info['field_' . $_GET['id']]) > 0) {
            //check if using file storage for feild
            if (class_exists('file_storage')) {
                file_storage::download_files(_get::int('id'), $attachments);
            }

            $zip = new ZipArchive();
            $zip_filename = "attachments-{$current_item_id}.zip";
            $zip_filepath = DIR_FS_UPLOADS . $zip_filename;

            //open zip archive
            $zip->open($zip_filepath, ZipArchive::CREATE);

            //add files to archive
            foreach (explode(',', $attachments) as $filename) {
                $file = attachments::parse_filename($filename);
                $zip->addFile($file['file_path'], "/" . $file['name']);
            }

            $zip->close();

            //check if zip archive created
            if (!is_file($zip_filepath)) {
                exit("Error: cannot create zip archive in " . $zip_filepath);
            }

            //download file
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $zip_filename);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($zip_filepath));

            flush();

            readfile($zip_filepath);

            //delete temp zip archive file
            @unlink($zip_filepath);
        }

        exit();
        break;
}