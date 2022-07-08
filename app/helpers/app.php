<?php

namespace Helpers;

class App
{
    public static function app_set_nested_selected_items($reports_id, $entities_id, $item_id)
    {
        global $app_selected_items;

        $items_query = \K::model()->db_query_exec(
            "select * from app_entity_{$entities_id}  where parent_id = :item_id order by sort_order, id",
            [':item_id' => $item_id]
        );
        //while ($item = db_fetch_array($items_query)) {
        foreach ($items_query as $item) {
            $app_selected_items[$reports_id][] = $item['id'];

            self::app_set_nested_selected_items($reports_id, $entities_id, $item['id']);
        }
    }

    public static function app_select2_nested_items_result($entities_id, $item_id, $results, $level = 1)
    {
        $items_query = db_query(
            "select * from app_entity_{$entities_id}  where parent_id = '" . db_input(
                $item_id
            ) . "' order by sort_order, id"
        );
        while ($item = db_fetch_array($items_query)) {
            $text = str_repeat(' - ', $level) . items::get_heading_field($entities_id, $item['id'], $item);

            $results[] = ['id' => $item['id'], 'text' => $text, 'html' => '<div>' . $text . '</div>'];

            $results = self::app_select2_nested_items_result($entities_id, $item['id'], $results, $level + 1);
        }

        return $results;
    }

    public static function app_favicon()
    {
        $html = '';

        if (is_file(\K::$fw->DIR_FS_UPLOADS . \K::$fw->CFG_APP_FAVICON)) {
            $type = mime_content_type(\K::$fw->DIR_FS_UPLOADS . \K::$fw->CFG_APP_FAVICON);

            switch ($type) {
                case 'image/x-icon':
                case 'image/vnd.microsoft.icon':
                    $html = '<link  type="image/x-icon" rel="shortcut icon" href="' . \K::$fw->DIR_WS_UPLOADS . \K::$fw->CFG_APP_FAVICON . '" />';
                    break;
                case 'image/jpeg':
                case 'image/png':
                case 'image/gif':
                    $html = '<link  type="' . $type . '" rel="icon" href="' . \K::$fw->DIR_WS_UPLOADS . \K::$fw->CFG_APP_FAVICON . '" />';
                    break;
            }
        } else {
            $html = '<link  type="image/x-icon" rel="shortcut icon" href="favicon.ico" />';
        }

        return $html;
    }

    // Write out serialized data.
    //  write_cache uses serialize() to store $var in $filename.
    //  $var      -  The variable to be written out.
    //  $filename -  The name of the file to write to.
    public static function app_write_cache(&$var, $filename, $is_cache = true)
    {
        //check if cache is enabled
        if (!$is_cache) {
            return false;
        }

        $filename = \K::$fw->DIR_FS_CACHE . $filename;
        $success = false;

        // try to open the file
        if ($fp = @fopen($filename, 'w')) {
            // obtain a file lock to stop corruptions occuring
            flock($fp, 2); // LOCK_EX
            // write serialized data
            fputs($fp, serialize($var));
            // release the file lock
            flock($fp, 3); // LOCK_UN
            fclose($fp);
            $success = true;
        }

        return $success;
    }

    //  Read in seralized data.
    //  read_cache reads the serialized data in $filename and
    //  fills $var using unserialize().
    //  $var      -  The variable to be filled.
    //  $filename -  The name of the file to read.
    public static function app_read_cache(&$var, $filename, $auto_expire = false, $is_cache = true)
    {
        $filename = \K::$fw->DIR_FS_CACHE . $filename;

        //check if cache is enabled
        if (!$is_cache or !file_exists($filename)) {
            return false;
        }

        $success = false;

        if (($auto_expire > 0) && file_exists($filename)) {
            $now = time();
            $filetime = filemtime($filename);
            $difference = $now - $filetime;

            if ($difference >= $auto_expire) {
                return false;
            }
        }

        // try to open file
        if ($fp = @fopen($filename, 'r')) {
            // read in serialized data
            $szdata = fread($fp, filesize($filename));
            fclose($fp);
            // unserialze the data
            $var = unserialize($szdata);

            $success = true;
        }

        return $success;
    }

    public static function app_path_get_parent_path($path)
    {
        $current_path_array = explode('/', $path);
        $parent_patth_array = [];
        for ($i = 0; $i < count($current_path_array) - 1; $i++) {
            $parent_patth_array[] = $current_path_array[$i];
        }

        return implode('/', $parent_patth_array);
    }

    public static function render_login_page_background()
    {
        $html = '';
        $filename = '';

        if (\K::$fw->CFG_MAINTENANCE_MODE == 1 and is_file(
                \K::$fw->DIR_FS_UPLOADS . '/' . \K::$fw->CFG_APP_LOGIN_MAINTENANCE_BACKGROUND
            )) {
            $filename = \K::$fw->CFG_APP_LOGIN_MAINTENANCE_BACKGROUND;
        } elseif (is_file(\K::$fw->DIR_FS_UPLOADS . '/' . \K::$fw->CFG_APP_LOGIN_PAGE_BACKGROUND)) {
            $filename = \K::$fw->CFG_APP_LOGIN_PAGE_BACKGROUND;
        }

        if (strlen($filename)) {
            $html = '
				<style>
					.login {
					  background: url(uploads/' . $filename . ') no-repeat center center fixed;
					  -webkit-background-size: cover;
					  -moz-background-size: cover;
					  -o-background-size: cover;
					  background-size: cover;
					}    
					
					.login-fade-in{
					  position: fixed;
					  top: 0;
					  right: 0;
					  bottom: 0;
					  left: 0;
					  background-color: #333;
					  opacity: 0.2;
					  z-index: -1; 
					}
					
					.copyright{
					  color: white !important;
					}
					
				</style>
				';
        }

        return $html;
    }

    public static function app_set_title($title)
    {
        global $app_title;

        return $app_title . ' | ' . $title;
    }

    public static function format_date($d, $date_format = false)
    {
        if (!$date_format) {
            $date_format = \K::$fw->CFG_APP_DATE_FORMAT;
        }

        if (strlen($date_format) == 0) {
            $date_format = 'm/d/Y';
        }

        if (strlen($d) > 0) {
            return i18n_date($date_format, $d);
        } else {
            return '';
        }
    }

    public static function format_date_time($d, $date_format = false)
    {
        if (!$date_format) {
            $date_format = \K::$fw->CFG_APP_DATETIME_FORMAT;
        }

        if (strlen($date_format) == 0) {
            $date_format = 'm/d/Y H:i';
        }

        if (strlen($d) > 0) {
            return i18n_date($date_format, $d);
        } else {
            return '';
        }
    }

    public static function get_date_timestamp($date)
    {
        if (strlen($date) > 0) {
            $v = date_parse($date);

            $timestamp = mktime(
                (int)$v['hour'],
                (int)$v['minute'],
                (int)$v['second'],
                $v['month'],
                $v['day'],
                $v['year']
            );

            return $timestamp;
        } else {
            return '';
        }
    }

    public static function day_diff($start, $end, $exclude = [])
    {
        $count = 0;

        while (date('Y-m-d', $start) < date('Y-m-d', $end)) {
            if (!in_array(date('N', $start), $exclude)) {
                $count++;
            }

            $start = strtotime("+1 day", $start);
        }

        return $count;
    }

    public static function hour_diff($start, $end, $exclude = [])
    {
        $count = 0;

        while (date('Y-m-d H:i', $start) < date('Y-m-d H:i', $end)) {
            if (!in_array(date('H', $start), $exclude)) {
                $count++;
            }

            $start = strtotime("+1 hour", $start);
        }

        return $count;
    }

    public static function render_bool_value($v, $is_label = true)
    {
        if ($is_label) {
            return ($v == 1 ? '<span class="label label-success">' . \K::$fw->TEXT_YES . '</span>' : '<span class="label label-default">' . \K::$fw->TEXT_NO . '</span>');
        } else {
            return ($v == 1 ? '<span class="text-yes">' . \K::$fw->TEXT_YES . '</span>' : '<span class="text-no">' . \K::$fw->TEXT_NO . '</span>');
        }
    }

    public static function render_listing_search_form(
        $entity_id,
        $listing_container,
        $reports_id,
        $input_width = 'input-medium'
    ) {
        if (count($search_fields = fields::get_search_feidls($entity_id)) == 0) {
            return '';
        }

        $listing_search = new listing_search($reports_id);

        //print_r($listing_search->search_settings);  

        $search_fiels_html = '';
        //add checkboxes if more then one fields for search
        if (count($search_fields) > 1) {
            foreach ($search_fields as $v) {
                $attributes = [];

                //checked heading field by default
                if ($v['is_heading'] == 1 or in_array($v['id'], $listing_search->get('use_search_fields'))) {
                    $attributes = ['checked' => 'checked'];
                }

                $search_fiels_html .= '<label>' . input_checkbox_tag(
                        $listing_container . '_use_search_fields[]',
                        $v['id'],
                        array_merge(['class' => $listing_container . '_use_search_fields'], $attributes)
                    ) . $v['name'] . '</label>';
            }

            $search_fiels_html .= '<label class="divider"></label>';
        }

        $search_fiels_html .= '
  			<label>' . input_checkbox_tag(
                $listing_container . '_search_in_all',
                '1',
                $listing_search->get_input_attributes('search_in_all')
            ) . TEXT_SEARCH_IN_ALL . '</label>
  			<label>' . input_checkbox_tag(
                $listing_container . '_search_type_and',
                '1',
                $listing_search->get_input_attributes('search_type_and')
            ) . TEXT_SEARCH_TYPE_AND . '</label>
  			<label>' . input_checkbox_tag(
                $listing_container . '_search_type_match',
                '1',
                $listing_search->get_input_attributes('search_type_match')
            ) . TEXT_SEARCH_TYPE_MATCH . '</label>
  			<label class="divider"></label>';

        $search_fiels_html .= input_hidden_tag($listing_container . '_search_reset');

        $entity_cfg = new entities_cfg($entity_id);

        if ($entity_cfg->get('use_comments') == 1) {
            $search_fiels_html .= '<label>' . input_checkbox_tag(
                    $listing_container . '_search_in_comments',
                    '1',
                    $listing_search->get_input_attributes('search_in_comments')
                ) . TEXT_SEARCH_IN_COMMENTS . '</label>';
            $search_fiels_html .= '<label class="divider"></label>';
        }

        $html = '
  <form id="' . $listing_container . '_search_form" class="navbar-search listing-search-form" onSubmit="load_items_listing(\'' . $listing_container . '\',1); return false;">
    
    <div class="input-group ' . $input_width . '">
    
      <div class="input-group-btn">
  			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"><i class="fa fa-angle-down"></i></button>
  			<div class="dropdown-menu hold-on-click dropdown-checkboxes" role="menu">
  				' . $search_fiels_html . '
          <label>' . link_to_modalbox(TEXT_SEARCH_HELP, url_for('dashboard/search_help')) . '</label>
  			</div>
  		</div>
        	
  		' . input_tag(
                $listing_container . '_search_keywords',
                $listing_search->get('search_keywords'),
                ['placeholder' => TEXT_SEARCH, 'class' => 'form-control ' . $input_width]
            ) . '
  		<span class="input-group-btn">  			
        <button ' . tag_attributes_to_html(['title' => TEXT_BUTTON_SEARCH]) . ' class="btn btn-info" ><i class="fa fa-search"></i></button>
  		</span>
  	</div>
  </form>';

        return $html;
    }

    public static function render_comments_search_form($entity_id, $listing_container)
    {
        $html = '
  <form id="' . $listing_container . '_search_form" class="navbar-search pull-right" onSubmit="load_comments_listing(\'' . $listing_container . '\',1); return false;">    
    <div class="input-group input-small">      
  		<input id="' . $listing_container . '_search_keywords" type="text" placeholder="' . TEXT_SEARCH . '" class="form-control input-small input-sm">
  		<span class="input-group-btn">  			
        <button' . tag_attributes_to_html(['title' => TEXT_BUTTON_SEARCH]
            ) . ' class="btn btn-info btn-sm" onClick="$(\'#' . $listing_container . '_search_form\').submit();"><i class="fa fa-search"></i></button>
  		</span>
  	</div>
  </form>';

        return $html;
    }

    public static function tooltip_text($text)
    {
        if (strlen($text) > 0) {
            $text = ($text != strip_tags($text) ? $text : nl2br($text));

            return '<span class="help-block">' . $text . '</span>';
        } else {
            return '';
        }
    }

    public static function tooltip_icon($text, $placement = 'right')
    {
        if (strlen($text) > 0) {
            $text = ($text != strip_tags($text) ? $text : nl2br($text));

            return '<i class="fa fa-info-circle tooltip-icon" data-toggle="tooltip" data-placement="' . $placement . '" data-html="true" title="' . htmlspecialchars(
                    $text
                ) . '"></i> ';
        } else {
            return '';
        }
    }

    public static function app_error_handler($errno, $errmsg, $filename, $linenum)
    {
        $time = date("d M Y H:i:s");

        // Get the error type from the error number 
        $errortype = [
            1 => "Error",
            2 => "Warning",
            4 => "Parsing Error",
            8 => "Notice",
            16 => "Core Error",
            32 => "Core Warning",
            64 => "Compile Error",
            128 => "Compile Warning",
            256 => "User Error",
            512 => "User Warning",
            1024 => "User Notice",
            2048 => "Error Strict",
            8192 => "Deprecated"
        ];

        if (isset($errortype[$errno])) {
            $errlevel = $errortype[$errno];
        } else {
            $errlevel = $errortype[2];
        }

        $errfile = fopen(DIR_FS_CATALOG . "log/Errors_" . date("M_Y") . ".txt", "a+");
        if ($errfile) {
            fputs($errfile, $time . " " . $errlevel . "\n" . $errmsg . "\n" . $filename . ':' . $linenum . "\n\n");
            fclose($errfile);
        }
    }

    public static function print_rp($v)
    {
        return '<table><tr><td style="white-space:pre-wrap; ">' . print_r($v, true) . '</td></tr></table>';
    }

    public static function is_image($path)
    {
        if (is_file($path)) {
            $type = mime_content_type($path);

            if (in_array($type, ['image/jpeg', 'image/png', 'image/gif', 'image/vnd.microsoft.icon', 'image/x-icon'])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function is_audio($path)
    {
        if (is_file($path)) {
            $type = mime_content_type($path);

            if (in_array(
                $type,
                ['audio/mpeg', 'audio/mpeg3', 'audio/x-mpeg-3', 'video/x-mpeg', 'audio/ogg', 'audio/wav', 'audio/x-wav']
            )) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function is_pdf($filename)
    {
        return strtolower(substr($filename, -4)) == '.pdf';
    }

    public static function is_excel($filename)
    {
        return (strtolower(substr($filename, -4)) == '.xls' || strtolower(substr($filename, -5)) == '.xlsx');
    }

    public static function is_html($string)
    {
        return ($string != strip_tags($string));
    }

    public static function image_resize(
        $filename,
        $filename_small,
        $resize_image_widht = 150,
        $resize_image_height = ''
    ) {
        if (file_exists($filename)) {
            $image = getimagesize($filename);

            switch ($image[2]) {
                case 1:
                    $src_img = imagecreatefromgif($filename);
                    break;
                case 2:
                    $src_img = imagecreatefromjpeg($filename);
                    break;
                case 3:
                    $src_img = imagecreatefrompng($filename);
                    break;
            }

            $width = $image[0];
            $height = $image[1];

            if ($resize_image_widht > 0 && $resize_image_height == '' and $width > $resize_image_widht) {
                $cof = $width / $resize_image_widht;
                $width_small = $resize_image_widht;
                $height_small = $height / $cof;
            } elseif ($resize_image_height > 0 && $resize_image_widht == '' and $height > $resize_image_height) {
                $cof = $height / $resize_image_height;
                $height_small = $resize_image_height;
                $width_small = $width / $cof;
            } else {
                return false;
            }

            $tmp_img_small = imagecreatetruecolor($width_small, $height_small);

            //to resize png with transparent
            if ($image[2] == 3) {
                imagealphablending($tmp_img_small, false);
                imagesavealpha($tmp_img_small, true);
            }

            ImageCopyResampled($tmp_img_small, $src_img, 0, 0, 0, 0, $width_small, $height_small, $width, $height);

            @touch($filename_small);
            @chmod($filename_small, 0777);

            switch ($image[2]) {
                case 1:
                    imagegif($tmp_img_small, $filename_small);
                    break;
                case 2:
                    imagejpeg($tmp_img_small, $filename_small, 80);
                    break;
                case 3:
                    imagepng($tmp_img_small, $filename_small, 9);
                    break;
            }

            return true;
        }
    }

    public static function ajax_modal_template($title, $content)
    {
        $html = '  		
			<div class="modal-header">  				
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
  			<button type="button" class="close modal-collapse" aria-hidden="true"></button>
				<h4 class="modal-title">' . $title . '</h4>
			</div>
			<div >
				 ' . $content . '
			</div>				
  ';

        return $html;
    }

    public static function ajax_modal_template_header($title)
    {
        $html = '  		
			<div class="modal-header">  			
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
  			<button type="button" class="close modal-collapse" aria-hidden="true"></button>
				<h4 class="modal-title">' . $title . '</h4>
			</div>							
  ';

        return $html;
    }

    public static function ajax_modal_template_footer(
        $action_button_tille = false,
        $extra_buttons = '',
        $extra_text = ''
    ) {
        $html = '              
    <div class="modal-footer">
        <div id="form-error-container"></div>
        <div class="modal-footer-left">' . $extra_text . '</div>
    
  	<div class="fa fa-spinner fa-spin primary-modal-action-loading"></div>	
        ' . $extra_buttons . '
        <button type="button" class="btn btn-default btn-sub-dialog-back" style="display:none"><i class="fa fa-angle-left"></i> ' . TEXT_BUTTON_BACK . '</button>        
        ' . ($action_button_tille != 'hide-save-button' ? '<button type="submit" class="btn btn-primary btn-primary-modal-action">' . ($action_button_tille ? $action_button_tille : TEXT_BUTTON_SAVE) . '</button>' : '') . '
        <button type="button" class="btn btn-default btn-close" data-dismiss="modal">' . TEXT_BUTTON_CLOSE . '</button>    
    </div>';

        $html .= '
  <script>
    jQuery(document).ready(function() {                  
       appHandleUniform()  
               
        if(is_sub_dialog())
        {
            $(".btn-sub-dialog-back").show();
            
            $(".btn-sub-dialog-back").click(function(){
                close_sub_dialog()
            })
        }
    });
  </script>';

        return $html;
    }

    public static function ajax_modal_template_footer_simple()
    {
        $html = '
  <div class="modal-footer">    
    <button type="button" class="btn btn-default" data-dismiss="modal">' . TEXT_BUTTON_CLOSE . '</button>
  </div>';

        $html .= '
  <script>
    jQuery(document).ready(function() {                  
       appHandleUniform()                     
    });
  </script>';

        return $html;
    }

    public static function render_bg_color_block($color, $value = false)
    {
        if (strlen(trim($color)) > 0) {
            if (!$value) {
                $value = $color;
            }

            $rgb = convert_html_color_to_RGB($color);

            if (($rgb[0] + $rgb[1] + $rgb[2]) < 480) {
                return '<div class="bg-color-value" style="background: ' . $color . '; color: white;">' . $value . '</div>';
            } else {
                return '<div class="bg-color-value" style="background: ' . $color . ';">' . $value . '</div>';
            }
        } elseif ($value) {
            return $value;
        } else {
            return '';
        }
    }

    public static function convert_html_color_to_RGB($color)
    {
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        if (strlen($color) == 6) {
            [$r, $g, $b] = [$color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]];
        } elseif (strlen($color) == 3) {
            [$r, $g, $b] = [$color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]];
        } else {
            return [];
        }

        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        return [$r, $g, $b];
    }

    public static function render_user_photo($filename)
    {
        if (is_file(DIR_WS_USERS . $filename)) {
            $photo = image_tag(url_for_file(DIR_WS_USERS . $filename), ['class' => 'user-photo-content', 'width' => 50]
            );
        } else {
            $photo = image_tag(url_for_file('images/' . 'no_photo.png'), ['class' => 'user-photo-content']);
        }

        return $photo;
    }

    public static function app_get_languages_choices()
    {
        $list = [];

        $dir = 'includes/languages/';

        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && is_file($dir . $file) and substr($file, -4) == '.php') {
                    $name = implode(' ', array_map('ucfirst', explode('K', substr($file, 0, -4))));

                    $list[$file] = $name;
                }
            }
        }

        return $list;
    }

    public static function app_get_skins_choices($add_empty = true)
    {
        $list = [];

        if ($add_empty) {
            $list[''] = '';
        }

        $dir = 'css/skins/';

        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && is_dir($dir . $file)) {
                    $list[$file] = ucfirst($file);
                }
            }
        }

        return $list;
    }

    public static function app_send_new_comment_notification($comments_id, $item_id, $entity_id)
    {
        global $app_user, $app_users_cache;

        $item = db_find('app_entity_' . $entity_id, $item_id);

        $send_to = items::get_send_to($entity_id, $item_id, $item);

        //print_r($send_to);
        //exit();

        $cfg = new entities_cfg($entity_id);

        //check flag send notification to assinged flag
        if ($cfg->get('send_notification_to_assigned', 0) == 0) {
            //send to suers who made comments and not assigned to item
            $comments_query = db_query(
                "select * from app_comments where entities_id='" . db_input($entity_id) . "' and items_id='" . db_input(
                    $item_id
                ) . "'" . (count($send_to) ? " and created_by not in (" . implode(',', $send_to) . ")" : '')
            );
            while ($comments = db_fetch_array($comments_query)) {
                if (!in_array($comments['created_by'], $send_to) and isset($app_users_cache[$comments['created_by']])) {
                    //check if user has access to item    	
                    if (users::has_access_to_entity(
                        $entity_id,
                        'view_assigned',
                        $app_users_cache[$comments['created_by']]['group_id']
                    )) {
                        if (!in_array($comments['created_by'], $send_to)) {
                            continue;
                        }
                    } elseif (users::has_access_to_entity(
                        $entity_id,
                        'view',
                        $app_users_cache[$comments['created_by']]['group_id']
                    )) {
                        $send_to[] = $comments['created_by'];
                    }
                }
            }
        }

        //add item created user to notification
        if (fieldtype_created_by::is_notification_enabled($entity_id)) {
            $send_to[] = $item['created_by'];
        }

        $send_to = array_unique($send_to);

        //add current user to notification
        if (CFG_EMAIL_COPY_SENDER == 1) {
            $send_to[] = $app_user['id'];
        } else {
            if ($key = array_search($app_user['id'], $send_to)) {
                unset($send_to[$key]);
            }
        }

        if (count($send_to) > 0) {
            //$heading_field_id = fields::get_heading_id($entity_id);
            //$item_name = ($heading_field_id>0 ? $item['field_' . $heading_field_id] : $item['id']);

            $breadcrumb = items::get_breadcrumb_by_item_id($entity_id, $item['id']);
            $item_name = $breadcrumb['text'];

            $cfg = entities::get_cfg($entity_id);

            $path_info = items::get_path_info($entity_id, $item_id);

            $subject = (strlen(
                $cfg['email_subject_new_comment']
            ) > 0 ? $cfg['email_subject_new_comment'] . ' ' . $item_name : TEXT_DEFAULT_EMAIL_SUBJECT_NEW_COMMENT . ' ' . $item_name);
            $heading = users::use_email_pattern_style(
                '<div><a href="' . url_for(
                    'items/info',
                    'path=' . $path_info['full_path'],
                    true
                ) . '"><h3>' . $subject . '</h3></a></div>',
                'email_heading_content'
            );

            foreach (array_unique($send_to) as $user_id) {
                //check comments access and exclude users which don't have access to comments
                if (isset($app_users_cache[$user_id]['group_id'])) {
                    if ($app_users_cache[$user_id]['group_id'] > 0) {
                        if (!users::has_comments_access(
                            'view',
                            users::get_comments_access_schema($entity_id, $app_users_cache[$user_id]['group_id']),
                            false
                        )) {
                            continue;
                        }
                    }
                }

                $body = users::use_email_pattern(
                    'single',
                    [
                        'email_body_content' => comments::render_content_box($entity_id, $item_id, $user_id),
                        'email_sidebar_content' => items::render_info_box($entity_id, $item_id, $user_id)
                    ]
                );

                if (users_cfg::get_value_by_users_id($user_id, 'disable_notification') != 1) {
                    users::send_to([$user_id], $subject, $heading . $body);
                }

                //add users notification
                users_notifications::add($subject, 'new_comment', $user_id, $entity_id, $item_id);
            }
        }
    }

    public static function app_reset_selected_items()
    {
        global $app_selected_items;

        $app_selected_items = [];
    }

    public static function i18n_date()
    {
        $days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        $daysshort = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
        $daysmin = ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"];
        $month = [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        ];
        $monthshort = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        $translate = [];

        $days_i18n = explode(',', str_replace('"', '', TEXT_DATEPICKER_DAYS));
        foreach ($days as $k => $v) {
            $translate[$v] = trim($days_i18n[$k]);
        }

        $daysshort_i18n = explode(',', str_replace('"', '', TEXT_DATEPICKER_DAYSSHORT));
        foreach ($daysshort as $k => $v) {
            $translate[$v] = trim($daysshort_i18n[$k]);
        }

        $daysmin_i18n = explode(',', str_replace('"', '', TEXT_DATEPICKER_DAYSMIN));
        foreach ($daysmin as $k => $v) {
            $translate[$v] = trim($daysmin_i18n[$k]);
        }

        $month_i18n = explode(',', str_replace('"', '', TEXT_DATEPICKER_MONTHS));
        foreach ($month as $k => $v) {
            $translate[$v] = trim($month_i18n[$k]);
        }

        $monthshort_i18n = explode(',', str_replace('"', '', TEXT_DATEPICKER_MONTHSSHORT));
        foreach ($monthshort as $k => $v) {
            $translate[$v] = trim($monthshort_i18n[$k]);
        }

        if (func_num_args() > 1) {
            $timestamp = func_get_arg(1);
            return strtr(date(func_get_arg(0), $timestamp), $translate);
        } else {
            return strtr(date(func_get_arg(0)), $translate);
        }
    }

    public static function i18n_js()
    {
        global $app_session_token;

        $list = [];
        $list['TEXT_NO_RESULTS_MATCH'] = \K::$fw->TEXT_NO_RESULTS_MATCH;
        $list['TEXT_ARE_YOU_SURE'] = \K::$fw->TEXT_ARE_YOU_SURE;
        $list['TEXT_SELECT_AN_OPTION'] = \K::$fw->TEXT_SELECT_AN_OPTION;
        $list['TEXT_SELECT_SOME_OPTIONS'] = \K::$fw->TEXT_SELECT_SOME_OPTIONS;
        $list['TEXT_FILE_TOO_LARGE'] = \K::$fw->TEXT_FILE_TOO_LARGE;
        $list['TEXT_MAXIMUM_UPLOAD_LIMIT'] = \K::$fw->TEXT_MAXIMUM_UPLOAD_LIMIT;
        $list['TEXT_COMPLETED'] = \K::$fw->TEXT_COMPLETED;
        $list['TEXT_CANCELLED'] = \K::$fw->TEXT_CANCELLED;
        $list['TEXT_SCALE'] = \K::$fw->TEXT_SCALE;
        $list['TEXT_RESET_MAP_CONFIRM'] = \K::$fw->TEXT_RESET_MAP_CONFIRM;
        $list['TEXT_ON_TOP'] = \K::$fw->TEXT_ON_TOP;
        $list['TEXT_ON_BOTTOM'] = \K::$fw->TEXT_ON_BOTTOM;
        $list['TEXT_ON_RIGHT'] = \K::$fw->TEXT_ON_RIGHT;
        $list['TEXT_ON_LEFT'] = \K::$fw->TEXT_ON_LEFT;
        $list['TEXT_GRAPH'] = \K::$fw->TEXT_GRAPH;
        $list['TEXT_TREE'] = \K::$fw->TEXT_TREE;
        $list['TEXT_MAP'] = \K::$fw->TEXT_MAP;
        $list['TEXT_BOX'] = \K::$fw->TEXT_BOX;
        $list['TEXT_ELLIPSE'] = \K::$fw->TEXT_ELLIPSE;
        $list['TEXT_UNDERLINE'] = \K::$fw->TEXT_UNDERLINE;
        $list['TEXT_INSERT_CHILD'] = \K::$fw->TEXT_INSERT_CHILD;
        $list['TEXT_INSERT_SIBLING'] = \K::$fw->TEXT_INSERT_SIBLING;
        $list['TEXT_DELETE'] = \K::$fw->TEXT_DELETE;
        $list['TEXT_EDIT'] = \K::$fw->TEXT_EDIT;
        $list['TEXT_SET_VALUE'] = \K::$fw->TEXT_SET_VALUE;
        $list['TEXT_UNDO'] = \K::$fw->TEXT_UNDO;
        $list['TEXT_REDO'] = \K::$fw->TEXT_REDO;
        $list['TEXT_CENTER_MAP'] = \K::$fw->TEXT_CENTER_MAP;
        $list['TEXT_DESCRIPTION'] = \K::$fw->TEXT_DESCRIPTION;
        $list['TEXT_START'] = \K::$fw->TEXT_START;
        $list['TEXT_ERROR_FILE_EXTENSION'] = \K::$fw->TEXT_ERROR_FILE_EXTENSION;
        $list['TEXT_APPLY'] = \K::$fw->TEXT_APPLY;
        $list['TEXT_CANCEL'] = \K::$fw->TEXT_CANCEL;
        $list['TEXT_TOGGLE_ON'] = \K::$fw->TEXT_TOGGLE_ON;
        $list['TEXT_TOGGLE_OFF'] = \K::$fw->TEXT_TOGGLE_OFF;

        $html = '
    <script>
     var i18n = new Array()
  ';

        foreach ($list as $k => $v) {
            $html .= 'i18n[\'' . $k . '\']="' . addslashes($v) . '"' . "\n";
        }

        $html .= '
        public static function url_for(module,params)
        {
            return "' . \Helpers\Urls::url_for('to_replace', 'token=' . urlencode($app_session_token)) . '".replace("to_replace",module).concat("&",params)
        }
        ';

        $html .= '</script>';

        return $html;
    }

    public static function app_get_path_to_parent_item($path_array)
    {
        if (count($path_array) > 0) {
            unset($path_array[count($path_array) - 1]);

            return implode('/', $path_array);
        } else {
            return '';
        }
    }

////
// Parse search string into indivual objects
    public static function app_parse_search_string($search_str = '', &$objects = [], $search_operator = 'or')
    {
        /* if (function_exists('mb_strtolower')) 
          {
          mb_internal_encoding('UTF-8');
          $search_str = trim(mb_strtolower($search_str));
          }
          else
          {
          $search_str = trim(strtolower($search_str));
          } */

        $search_str = trim(str_replace(['AND', 'OR'], ['and', 'or'], $search_str));

// Break up $search_str on whitespace; quoted string will be reconstructed later
        $pieces = preg_split('/[[:space:]]+/u', $search_str);
        $objects = [];
        $tmpstring = '';
        $flag = '';

        //print_rr($pieces);

        for ($k = 0; $k < count($pieces); $k++) {
            while (substr($pieces[$k], 0, 1) == '(') {
                $objects[] = '(';
                if (strlen($pieces[$k]) > 1) {
                    $pieces[$k] = substr($pieces[$k], 1);
                } else {
                    $pieces[$k] = '';
                }
            }

            $post_objects = [];

            while (substr($pieces[$k], -1) == ')') {
                $post_objects[] = ')';
                if (strlen($pieces[$k]) > 1) {
                    $pieces[$k] = substr($pieces[$k], 0, -1);
                } else {
                    $pieces[$k] = '';
                }
            }

// Check individual words

            if ((substr($pieces[$k], -1) != '"') && (substr($pieces[$k], 0, 1) != '"')) {
                $objects[] = trim($pieces[$k]);

                for ($j = 0; $j < count($post_objects); $j++) {
                    $objects[] = $post_objects[$j];
                }
            } else {
                /* This means that the $piece is either the beginning or the end of a string.
                  So, we'll slurp up the $pieces and stick them together until we get to the
                  end of the string or run out of pieces.
                 */

// Add this word to the $tmpstring, starting the $tmpstring
                $tmpstring = trim(preg_replace('/"/', ' ', $pieces[$k]));

// Check for one possible exception to the rule. That there is a single quoted word.
                if (substr($pieces[$k], -1) == '"') {
// Turn the flag off for future iterations
                    $flag = 'off';

                    $objects[] = trim(preg_replace('/"/', ' ', $pieces[$k]));

                    for ($j = 0; $j < count($post_objects); $j++) {
                        $objects[] = $post_objects[$j];
                    }

                    unset($tmpstring);

// Stop looking for the end of the string and move onto the next word.
                    continue;
                }

// Otherwise, turn on the flag to indicate no quotes have been found attached to this word in the string.
                $flag = 'on';

// Move on to the next word
                $k++;

// Keep reading until the end of the string as long as the $flag is on

                while (($flag == 'on') && ($k < count($pieces))) {
                    while (substr($pieces[$k], -1) == ')') {
                        $post_objects[] = ')';
                        if (strlen($pieces[$k]) > 1) {
                            $pieces[$k] = substr($pieces[$k], 0, -1);
                        } else {
                            $pieces[$k] = '';
                        }
                    }

// If the word doesn't end in double quotes, append it to the $tmpstring.
                    if (substr($pieces[$k], -1) != '"') {
// Tack this word onto the current string entity
                        $tmpstring .= ' ' . $pieces[$k];

// Move on to the next word
                        $k++;
                        continue;
                    } else {
                        /* If the $piece ends in double quotes, strip the double quotes, tack the
                          $piece onto the tail of the string, push the $tmpstring onto the $haves,
                          kill the $tmpstring, turn the $flag "off", and return.
                         */
                        $tmpstring .= ' ' . trim(preg_replace('/"/', ' ', $pieces[$k]));

// Push the $tmpstring onto the array of stuff to search for
                        $objects[] = trim($tmpstring);

                        for ($j = 0; $j < count($post_objects); $j++) {
                            $objects[] = $post_objects[$j];
                        }

                        unset($tmpstring);

// Turn off the flag to exit the loop
                        $flag = 'off';
                    }
                }
            }
        }

// add default logical operators if needed
        $temp = [];
        for ($i = 0; $i < (count($objects) - 1); $i++) {
            $temp[] = $objects[$i];
            if (($objects[$i] != 'and') &&
                ($objects[$i] != 'or') &&
                ($objects[$i] != '(') &&
                ($objects[$i + 1] != 'and') &&
                ($objects[$i + 1] != 'or') &&
                ($objects[$i + 1] != ')')) {
                $temp[] = $search_operator;
            }
        }
        $temp[] = $objects[$i];
        $objects = $temp;

        $keyword_count = 0;
        $operator_count = 0;
        $balance = 0;
        for ($i = 0; $i < count($objects); $i++) {
            if ($objects[$i] == '(') {
                $balance--;
            }
            if ($objects[$i] == ')') {
                $balance++;
            }
            if (($objects[$i] == 'and') || ($objects[$i] == 'or')) {
                $operator_count++;
            } elseif (($objects[$i]) && ($objects[$i] != '(') && ($objects[$i] != ')')) {
                $keyword_count++;
            }
        }

        if (($operator_count < $keyword_count) && ($balance == 0)) {
            return true;
        } else {
            return false;
        }
    }

//For PHP5.3 this public static function replace json_encode($v,JSON_UNESCAPED_UNICODE) in 5.4.0   
    public static function app_json_encode($arr)
    {
        //convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
        array_walk_recursive($arr, function (&$item, $key) {
            if (is_string($item)) {
                $item = (mb_encode_numericentity($item, [0x80, 0xffff, 0, 0xffff], 'UTF-8'));
            }
        });
        return mb_decode_numericentity(json_encode($arr), [0x80, 0xffff, 0, 0xffff], 'UTF-8');
    }

    public static function app_render_fields_popup_html($fields_in_popup, $reports_info = [])
    {
        global $app_module_action;

        if (strlen($app_module_action) > 0) {
            return '';
        }

        //print_r($fields_in_popup);

        $popup_html = '<table class=popover-table-data>';
        foreach ($fields_in_popup as $fields) {
            if (in_array($fields['type'], ['fieldtype_image', 'fieldtype_image_ajax', 'fieldtype_user_photo'])) {
                $value = str_replace('"', '', $fields['value']);
            } else {
                $value = htmlspecialchars(strip_tags($fields['value']));
            }

            $popup_html .= '
      <tr>
        <td valign=top>' . htmlspecialchars(strip_tags($fields['name'])) . '</td>
        <td valign=top>' . $value . '</td>
      </tr>
    ';
        }
        $popup_html .= '</table>';

        $popup_html = 'data-toggle="popover" data-content="' . addslashes(
                str_replace(["\n", "\r", "\n\r"], ' ', $popup_html)
            ) . '"';

        if (isset($reports_info['reports_type'])) {
            if (strstr($reports_info['reports_type'], 'mail_related_items_')) {
                $popup_html .= ' placement="left"';
            }
        }

        return $popup_html;
    }

    public static function app_sanitize_string($string)
    {
        $patterns = ['/ +/', '/[<>]/'];
        $replace = [' ', 'K'];
        return preg_replace($patterns, $replace, trim($string));
    }

    public static function app_render_status_label($stauts)
    {
        return ($stauts == true ? '<span class="label label-success">' . TEXT_ACTIVE . '</span>' : '<span class="label label-default">' . TEXT_INACTIVE . '</span>');
    }

    public static function app_get_days_choices()
    {
        $days = explode(',', str_replace('"', '', TEXT_DATEPICKER_DAYS));
        $days[7] = $days[0];
        unset($days[0]);
        return $days;
    }

    public static function app_get_hours_choices()
    {
        $choices = [];

        for ($i = 0; $i <= 23; $i++) {
            $hour = ($i < 10 ? '0' : '') . $i;
            $choices[$hour] = $hour . ':00';
        }

        return $choices;
    }

    public static function app_check_form_token($redirect_to = '')
    {
        global $app_session_token, $alerts;

        if (!isset($_POST['form_session_token']) or $_POST['form_session_token'] != $app_session_token) {
            if (strlen($redirect_to)) {
                $alerts->add(TEXT_FROM_SESSION_ERROR, 'error');
                redirect_to($redirect_to);
            } else {
                die(TEXT_FROM_SESSION_ERROR);
            }
        } else {
            return true;
        }
    }

    public static function is_ext_installed()
    {
        return \K::fw()->exists('CFG_PLUGIN_EXT_INSTALLED');
    }

    public static function is_cron()
    {
        return \K::$fw->IS_CRON;
    }

    public static function is_mobile()
    {
        return \Audit::instance()->ismobile();
    }

    public static function app_get_mysql_days_choices()
    {
        $choices = [];
        foreach (explode(",", \K::$fw->TEXT_DATEPICKER_DAYS) as $k => $d) {
            $d = str_replace('"', '', $d);

            $choices[$k + 1] = $d;
        }

        ksort($choices);

        return $choices;
    }

    public static function alert_error($text)
    {
        return '<div class="alert alert-danger">' . $text . '</div>';
    }

    public static function alert_success($text)
    {
        return '<div class="alert alert-success">' . $text . '</div>';
    }

    public static function app_get_path_to_report($entities_id)
    {
        global $app_path, $app_entities_cache;

        $path_array = explode('/', \K::$fw->app_path);
        $last_path_item = explode('-', $path_array[count($path_array) - 1]);
        $current_entity_id = $last_path_item[0];

        if ($current_entity_id == $entities_id or ($current_entity_id != $entities_id and \K::$fw->app_entities_cache[$current_entity_id]['parent_id'] == \K::$fw->app_entities_cache[$entities_id]['parent_id'])) {
            unset($path_array[count($path_array) - 1]);
            $path = implode('/', $path_array);

            return $path . '/' . $entities_id;
        } else {
            return \K::$fw->app_path . '/' . $entities_id;
        }
    }

    public static function app_render_icon($icon, $params = '')
    {
        $icon = trim($icon);

        if (!strlen($icon)) {
            return '';
        }

        if (substr($icon, 0, 3) == 'la-') {
            return '<i ' . $params . ' class="la ' . $icon . '" aria-hidden="true"></i>';
        } else {
            return '<i ' . $params . ' class="fa ' . $icon . '" aria-hidden="true"></i>';
        }
    }

    public static function print_rr($v)
    {
        echo '<pre>';
        print_r($v);
        echo '</pre>';
    }

    public static function app_alert_warning($text)
    {
        return '<div class="alert alert-warning">' . $text . '</div>';
    }

    public static function app_smtp_error_log($text)
    {
        error_log(date("Y-m-d H:i:s") . " - " . $text . "\n", 3, "log/smtp_log.txt");
    }

    public static function app_button_color_css($color, $css_class = '')
    {
        $css = '';

        if (strlen($color)) {
            $rgb = convert_html_color_to_RGB($color);
            $light = $rgb[0] + $rgb[1] + $rgb[2];
            $rgb[0] = $rgb[0] - 25;
            $rgb[1] = $rgb[1] - 25;
            $rgb[2] = $rgb[2] - 25;
            $css = '
            <style>
                    .' . $css_class . '{
                        background-color: ' . $color . ';
                        border-color: ' . $color . ';
                        ' . ($light < 480 ? 'color: white' : '') . '        
                    }
                    .btn-primary.' . $css_class . ':hover,
                    .btn-primary.' . $css_class . ':focus,
                    .btn-primary.' . $css_class . ':active,
                    .btn-primary.' . $css_class . '.active,
                    .btn-default.' . $css_class . ':hover,
                    .btn-default.' . $css_class . ':focus,
                    .btn-default.' . $css_class . ':active,
                    .btn-default.' . $css_class . '.active,
                    .open .dropdown-toggle.' . $css_class . '
                    {
                        background-color: rgba(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ',1);
                        border-color: rgba(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ',1);
                        ' . ($light < 480 ? 'color: white' : '') . '        
                    }
            </style>
			';
        }

        return $css;
    }

    public static function app_include_codemirror($modes = [])
    {
        $version = '5.63.3';

        $html = '
                <script src="js/codemirror/' . $version . '/lib/codemirror.js"></script>	        
		<link rel="stylesheet" href="js/codemirror/' . $version . '/lib/codemirror.css">
	        <link rel="stylesheet" href="js/codemirror/' . $version . '/addon/display/fullscreen.css">
	        <script src="js/codemirror/' . $version . '/addon/display/fullscreen.js"></script>                
                <script src="js/codemirror/' . $version . '/addon/edit/matchbrackets.js"></script>

			';
        foreach ($modes as $mode) {
            $html .= '<script src="js/codemirror/' . $version . '/mode/' . $mode . '/' . $mode . '.js"></script>';
        }

        return $html;
    }

    public static function app_get_boolean_choices()
    {
        return ['1' => \K::$fw->TEXT_YES, '0' => \K::$fw->TEXT_NO];
    }

    public static function app_transliterate_string($string)
    {
        $string = transliterator_transliterate("Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC;", $string);
        return trim($string);
    }

    public static function app_remove_special_characters($string)
    {
        return preg_replace('/-+/', '-', preg_replace('/[^\w_-]+/u', '', preg_replace('/\s+/', '-', trim($string))));
    }

    public static function _GET($v)
    {
        if (isset($_GET[$v])) {
            return (int)$_GET[$v];
        } else {
            die('Error: $_GET[' . $v . '] is not available!');
        }
    }

    public static function _POST($v)
    {
        if (isset($_POST[$v])) {
            return (int)$_POST[$v];
        } else {
            die('Error: $_POST[' . $v . '] is not available!');
        }
    }

    public static function app_include_custom_css()
    {
        if (!defined('DIR_WS_CUSTOM_CSS_FILE')) {
            define('DIR_WS_CUSTOM_CSS_FILE', 'css/custom.css');
        }

        if (is_file(DIR_WS_CUSTOM_CSS_FILE)) {
            return '<link rel="stylesheet" type="text/css" href="' . DIR_WS_CUSTOM_CSS_FILE . (defined(
                    'CFG_CUSTOM_CSS_TIME'
                ) ? '?time=' . CFG_CUSTOM_CSS_TIME : '') . '">';
        }

        return '';
    }

    public static function app_powered_by_text()
    {
        if (\K::$fw->CFG_HIDE_POWERED_BY_TEXT == 1) {
            return '';
        }

        $text = '<small>' . \K::$fw->TEXT_POWERED_BY . '&nbsp;<a rel="nofollow" target="_blank" href="https://www.keruy.com.ua" title="' . \K::$fw->TEXT_POWERED_BY_TITLE . '">KeruyCRM</a></small>';

        return $text;
    }

    public static function app_author_text()
    {
        if (\K::$fw->CFG_HIDE_POWERED_BY_TEXT == 1) {
            return '';
        }

        return '<meta content="www.keruy.com.ua" name="author"/>' . "\n";
    }

    public static function app_crop_str($str)
    {
        if (mb_strlen($str) > 53) {
            return '<span class="croped-str" title="' . addslashes($str) . '">' . mb_substr(
                    $str,
                    0,
                    25
                ) . '...' . mb_substr($str, -25) . '</span>';
        } else {
            return $str;
        }
    }

    public static function xml_str($string)
    {
        return htmlspecialchars($string, ENT_XML1, 'UTF-8');
    }

    public static function app_exit()
    {
        db_dev_log();

        exit();
    }

    public static function app_name2case($text, $case = 1)
    {
        $nc = new NCLNameCaseRu();

        $result = $nc->q(str_replace('&nbsp;', ' ', $text));

        //print_rr($result);

        return $result[$case - 1] ?? $text;
    }

    public static function app_name2case_ua($text, $case = 1)
    {
        $nc = new NCLNameCaseUa();

        $result = $nc->q(str_replace('&nbsp;', ' ', $text));

        //print_rr($result);

        return $result[$case - 1] ?? $text;
    }

    public static function app_truncate_text($text, $max_text_length = 60, $text_part_length = 25)
    {
        if (strlen($text) > 60) {
            $text = substr($text, 0, $text_part_length) . '...' . substr($text, -$text_part_length);
        }

        return $text;
    }

    public static function app_validate_email($email)
    {
        $email = trim($email);

        if (strlen($email) > 255) {
            $valid_address = false;
        } elseif (function_exists('filter_var') && defined('FILTER_VALIDATE_EMAIL')) {
            $valid_address = (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
        } else {
            if (substr_count($email, '@') > 1) {
                $valid_address = false;
            }

            if (preg_match(
                "/[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/i",
                $email
            )) {
                $valid_address = true;
            } else {
                $valid_address = false;
            }
        }

        return $valid_address;
    }
}