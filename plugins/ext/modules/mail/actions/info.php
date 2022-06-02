<?php

$email_info_query = db_query(
    "select * from app_ext_mail where groups_id='" . _get::int(
        'id'
    ) . "' and accounts_id in (select accounts_id from app_ext_mail_accounts_users where users_id='" . $app_user['id'] . "') order by date_added limit 1"
);
if (!$email_info = db_fetch_array($email_info_query)) {
    redirect_to('dashboard/access_forbidden');
}

$app_title = $email_info['subject_cropped'];


switch ($app_module_action) {
    case 'set_star':
        db_query("update app_ext_mail set is_star=1 where id='" . _post::int('mail_id') . "'");
        exit();
        break;
    case 'unset_star':
        db_query("update app_ext_mail set is_star=0 where id='" . _post::int('mail_id') . "'");
        exit();
        break;
    case 'delete_mail':
        $mail_info_query = db_query(
            "select id,in_trash,attachments,groups_id from app_ext_mail where id='" . _get::int('mail_id') . "'"
        );
        if ($mail_info = db_fetch_array($mail_info_query)) {
            if ($mail_info['in_trash'] == 0) {
                db_query("update app_ext_mail set in_trash=1 where id='" . _get::int('mail_id') . "'");

                redirect_to('ext/mail/info', 'id=' . $email_info['groups_id']);
            } else {
                if (strlen($mail_info['attachments'])) {
                    foreach (explode(',', $mail_info['attachments']) as $filename) {
                        $file = mail_info::parse_attachment_filename($filename);

                        if (is_file($file['file_path'])) {
                            unlink($file['file_path']);
                        }
                    }
                }

                db_delete_row('app_ext_mail', $mail_info['id']);

                mail_accounts::delete_mail_group_by_id($mail_info['groups_id']);

                redirect_to('ext/mail/accounts');
            }
        }

        break;
    case 'delete_mail_group':

        if ($app_mail_filters['folder'] == 'trash') {
            mail_info::delete_by_group_id(_get::int('id'));
        } else {
            db_query("update app_ext_mail set in_trash=1 where groups_id='" . _get::int('id') . "'");
        }

        redirect_to('ext/mail/accounts');
        break;

    case 'download_all_attachment':
        $mail_info_query = db_query("select * from app_ext_mail where id='" . _get::int('mail_id') . "'");
        if ($mail_info = db_fetch_array($mail_info_query)) {
            if (strlen($mail_info['attachments'])) {
                $zip = new ZipArchive();
                $zip_filename = "attachments.zip";
                $zip_filepath = DIR_FS_UPLOADS . $zip_filename;

                //open zip archive
                $zip->open($zip_filepath, ZipArchive::CREATE);

                foreach (explode(',', $mail_info['attachments']) as $filename) {
                    $file_info = mail_info::parse_attachment_filename($filename);
                    $zip->addFile($file_info['file_path'], "/" . $file_info['name']);
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
        }
        exit();
        break;
    case 'download_attachment':
        $filename = base64_decode($_GET['file']);

        $file_info = mail_info::parse_attachment_filename($filename);

        if (is_file($file_info['file_path'])) {
            if (is_image($file_info['file_path']) and isset($_GET['preview'])) {
                $size = getimagesize($file_info['file_path']);
                header("Content-type: " . $size['mime']);
                header('Content-Disposition: filename="' . $file_info['name'] . '"');

                flush();

                readfile($file_info['file_path']);
            } elseif (is_pdf($file_info['file_path']) and isset($_GET['preview'])) {
                header("Content-type: application/pdf");
                header('Content-Disposition: filename="' . $file_info['name'] . '"');

                flush();

                readfile($file_info['file_path']);
            } else {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . $file_info['name']);
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file_info['file_path']));

                flush();

                readfile($file_info['file_path']);
            }
        }

        exit();
        break;

    case 'preview_attachment_image':
        $file = mail_info::parse_attachment_filename(base64_decode($_GET['file']));

        if (is_file($file['file_path'])) {
            $size = getimagesize($file['file_path']);
            echo '<img width="' . $size[0] . '" height="' . $size[1] . '"  src="' . url_for(
                    'ext/mail/info&id=' . _get::int('id'),
                    '&action=download_attachment&preview=1&file=' . urlencode($_GET['file'])
                ) . '">';
        }

        exit();
        break;
    case 'get_body':

        $mail_info_query = db_query(
            "select body,id,attachments,groups_id from app_ext_mail where id='" . _get::int('mail_id') . "'"
        );
        if ($mail_info = db_fetch_array($mail_info_query)) {
            $custom_head = '
				<link href="template/plugins/font-awesome/css/font-awesome.min.css?v=4.7.0" rel="stylesheet" type="text/css"/>
					
				<style>
					body { 
					  color: #000; 
					  font-family: areal, "Open Sans", sans-serif;
					  padding: 0px !important;
					  margin: 0px !important;
					  font-size:13px; 					  
					}
					
					blockquote{
						margin:0px 0px 0px 0.8ex;
						border-left:1px solid rgb(204,204,204);
						padding-left:1ex;
						display:none;
					}
					
					.button-more{
						background-color: #f1f1f1;
				    border: 1px solid #ddd;				    
				    line-height: 6px;
				    outline: none;
				    position: relative;
				    width: 20px;
						font-size: 14px;
    				text-align: center;
						cursor: pointer;
					}
				</style>
					
				<script src="template/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
					
				<script>
					$(function(){
						
					
					
						$("blockquote").before("<div class=\"button-more\"><i class=\"fa fa-ellipsis-h\"></i></div>")
					
						var original_body_height = $("body").height();
						$(".button-more").click(function(){
							
							if($("blockquote").first().css("display")=="none")
							{
								$("blockquote").show();	
								height = $("body").height();
							}
							else
							{
								$("blockquote").hide();
								height = original_body_height;
							}
																									
							window.parent.postMessage({"height":height, "id":' . $mail_info['id'] . '}, "*")
						})
                                                
                                                $("a").attr("target","_blank")
					
					})
				</script>	
			';

            $mail_info['body'] = mail_info::prepare_body_attachments($mail_info);

            $html = $mail_info['body'];

            $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
            $html = preg_replace('#<iframe(.*?)>(.*?)</iframe>#is', '', $html);

            if (strstr($html, '<head>')) {
                $html = str_replace('<head>', '<head>' . $custom_head, $html);
            } else {
                $html .= $custom_head;
            }


            echo $html;
        }

        exit();
        break;
    default:
        $new_emails_list = [];
        $mail_query = db_query("select id from app_ext_mail where groups_id='" . _get::int('id') . "'");
        while ($mail = db_fetch_array($mail_query)) {
            $new_emails_list[] = $mail['id'];
        }

        //reset is_new after display
        db_query("update app_ext_mail set is_new=0 where groups_id='" . _get::int('id') . "'");
        break;
}		


