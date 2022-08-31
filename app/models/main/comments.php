<?php

namespace Models\Main;

class Comments
{
    public static function get_access_choices()
    {
        return [
            '' => \K::$fw->TEXT_NO,
            'view_create_update_delete' => \K::$fw->TEXT_YES,
            'view_create' => \K::$fw->TEXT_CREATE_ONLY_ACCESS,
            'view' => \K::$fw->TEXT_VIEW_ONLY_ACCESS,
            'view_create_update_delete_full' => \K::$fw->TEXT_FULL_ACCESS,
        ];
    }

    public static function get_available_filedtypes_in_comments()
    {
        return [
            'fieldtype_input',
            'fieldtype_input_url',
            'fieldtype_input_numeric',
            'fieldtype_input_numeric_comments',
            'fieldtype_input_date',
            'fieldtype_input_datetime',
            'fieldtype_boolean',
            'fieldtype_checkboxes',
            'fieldtype_radioboxes',
            'fieldtype_dropdown',
            'fieldtype_dropdown_multiple',
            'fieldtype_grouped_users',
            'fieldtype_progress',
            'fieldtype_textarea',
            'fieldtype_users',
            'fieldtype_users_ajax',
            'fieldtype_entity',
            'fieldtype_entity_ajax',
            'fieldtype_tags',
            'fieldtype_stages',
            'fieldtype_time',
        ];
    }

    public static function get_last_comment_info($entities_id, $items_id, $path, $fields_access_schema)
    {
        global $app_users_cache;

        $comments_query_sql = "select * from app_comments where entities_id='" . $entities_id . "' and items_id='" . $items_id . "'  order by date_added desc limit 1";
        $items_query = db_query($comments_query_sql);
        if ($item = db_fetch_array($items_query)) {
            $descripttion = htmlspecialchars(
                strlen($description = strip_tags($item['description'])) > 255 ? substr(
                        $description,
                        0,
                        255
                    ) . '...' : $description
            );

            //include attachments
            if (strlen($item['attachments'])) {
                $descripttion .= "<ul style='padding: 7px 0 0 0'>";
                foreach (explode(',', $item['attachments']) as $row) {
                    $file = attachments::parse_filename($row);
                    $descripttion .= "<li style='list-style: none; padding:0;'><img src='" . url_for_file(
                            $file['icon']
                        ) . "'>&nbsp;" . $file['name'] . " (" . $file['size'] . ")</li>";
                }
                $descripttion .= "</ul>";
            }

            $html_fields = '';
            $comments_fields_query = db_query(
                "select f.*,ch.fields_value from app_comments_history ch, app_fields f where comments_id='" . db_input(
                    $item['id']
                ) . "' and f.id=ch.fields_id order by ch.id"
            );
            while ($field = db_fetch_array($comments_fields_query)) {
                //check field access
                if (isset($fields_access_schema[$field['id']])) {
                    if ($fields_access_schema[$field['id']] == 'hide') {
                        continue;
                    }
                }

                $output_options = [
                    'class' => $field['type'],
                    'value' => $field['fields_value'],
                    'field' => $field,
                    'is_export' => true,
                    'is_print' => true,
                    'path' => $path
                ];

                $html_fields .= "
            <tr>
      				<th style='text-align: left;vertical-align: top; font-size: 11px;'>&bull;&nbsp;" . htmlspecialchars(
                        $field['name']
                    ) . ":&nbsp;</th>
      				<td style='font-size: 11px;'>" . htmlspecialchars(
                        strip_tags(fields_types::output($output_options))
                    ) . "</td>
      			</tr>
        ";
            }

            //include comments fileds
            if (strlen($html_fields)) {
                $descripttion .= "<table style='padding-top: 7px;'>" . $html_fields . "</table>";
            }

            if (strlen($descripttion)) {
                return '<sup class="last_comment_info" data-toggle="popover" title="' . format_date_time(
                        $item['date_added']
                    ) . '" data-content="' . str_replace(["\n", "\r", "\n\r"],
                        ' ',
                        $descripttion) . '" onClick="location.href=\'' . url_for(
                        'items/info',
                        'path=' . $path
                    ) . '\'" >' . (isset(\K::$fw->app_users_cache[$item['created_by']]) ? \K::$fw->app_users_cache[$item['created_by']]['name'] : '') . '</sup>';;
            }
        }

        return '';
    }

    public static function delete_item_comments($entity_id, $item_id)
    {
        $comments_query = db_query(
            "select * from app_comments where entities_id='" . db_input($entity_id) . "' and items_id='" . db_input(
                $item_id
            ) . "'"
        );
        while ($comments = db_fetch_array($comments_query)) {
            db_query("delete from app_comments_history where comments_id = '" . db_input($comments['id']) . "'");

            attachments::delete_comments_attachments($comments['id']);
        }

        db_query(
            "delete from app_comments where entities_id='" . db_input($entity_id) . "' and items_id='" . db_input(
                $item_id
            ) . "'"
        );
    }

    public static function render_content_box($entity_id, $item_id, $user_id)
    {
        $user_info = \K::model()->db_find('app_entity_1', $user_id);

        $fields_access_schema = \Models\Main\Users\Users::get_fields_access_schema($entity_id, $user_info['field_6']);
        //$choices_cache = fields_choices::get_cache();//Not used

        $count = 0;
        $html = '<table width="100%">';
        $limit = (int)\K::$fw->CFG_EMAIL_AMOUNT_PREVIOUS_COMMENTS;

        /*$listing_sql = "select * from app_comments where entities_id='" . db_input(
                $entity_id
            ) . "' and items_id='" . db_input($item_id) . "' order by id desc limit " . ($limit + 1);
        $items_query = db_query($listing_sql);*/

        $items_query = \K::model()->db_fetch('app_comments', [
            'entities_id = ? and items_id = ?',
            $entity_id,
            $item_id
        ], ['order' => 'id desc', 'limit' => $limit + 1]);

        //while ($item = db_fetch_array($items_query)) {
        foreach ($items_query as $item) {
            $item = $item->cast();

            $html_fields = '';
            $comments_fields_query = \K::model()->db_query_exec(
                "select f.*,ch.fields_value from app_comments_history ch, app_fields f where comments_id = ? and f.id = ch.fields_id order by ch.id",
                $item['id'],
                'app_comments_history,app_fields'
            );

            //while ($field = db_fetch_array($comments_fields_query)) {
            foreach ($comments_fields_query as $field) {
                //check field access
                if (isset($fields_access_schema[$field['id']])) {
                    if ($fields_access_schema[$field['id']] == 'hide') {
                        continue;
                    }
                }

                $output_options = [
                    'class' => $field['type'],
                    'value' => $field['fields_value'],
                    'field' => $field,
                    'is_listing' => true,
                    'path' => \K::$fw->current_path,
                    'is_comments_listing' => true,
                ];

                $html_fields .= '                      
            <tr><th style="text-align: left; font-family:Arial;font-size:13px; vertical-align: top">&bull;&nbsp;' . \Models\Main\Fields_types::get_option(
                        $field['type'],
                        'name',
                        $field['name']
                    ) . ':&nbsp;</th><td style="font-family:Arial;font-size:13px;">' . \Models\Main\Fields_types::output(
                        $output_options
                    ) . '</td></tr>           
        ';
            }

            if (strlen($html_fields) > 0) {
                $html_fields = '<table style="padding-top: 7px;">' . $html_fields . '</table>';
            }

            $attachments = \Models\Main\Fields_types::output(
                [
                    'class' => 'fieldtype_attachments',
                    'path' => \K::$fw->current_path,
                    'value' => $item['attachments'],
                    'field' => ['entities_id' => $entity_id],
                    'item' => ['id' => $item_id]
                ]
            );

            if ($count == 1) {
                $html .= '
          <tr>
            <td colspan="2" style="padding-top: 10px;"><h4>' . \K::$fw->TEXT_PREVIOUS_COMMENTS . '</h4></td>            
          </tr>
        ';
            }

            $html .= '
        <tr>
          <td style="vertical-align:top;font-family:Arial;font-size:13px;color:black;padding:2px;border-bottom:1px dashed LightGray">' . \Helpers\Urls::auto_link_text(
                    $item['description']
                ) . $attachments . $html_fields . '</td>
          <td align="right" style="vertical-align:top;font-family:Arial;font-size:13px;color:black;padding:2px;border-bottom:1px dashed LightGray;white-space:nowrap;">' . date(
                    \K::$fw->CFG_APP_DATETIME_FORMAT,
                    $item['date_added']
                ) . '<br>' . (isset(\K::$fw->app_users_cache[$item['created_by']]) ? \K::$fw->app_users_cache[$item['created_by']]['name'] . '<br>' . \Helpers\App::render_user_photo(
                        \K::$fw->app_users_cache[$item['created_by']]['photo']
                    ) : '') . '</td>
        </tr>
      ';

            $count++;
        }

        $html .= '</table>';

        return $html;
    }

    static function add_comment_notify_when_fields_changed($entities_id, $items_id, $changed_fields = [])
    {
        global $app_user;

        if (count($changed_fields)) {
            $sql_data = [
                'entities_id' => $entities_id,
                'items_id' => $items_id,
                'date_added' => time(),
                'created_by' => $app_user['id'],
            ];

            db_perform('app_comments', $sql_data);

            $comments_id = db_insert_id();

            foreach ($changed_fields as $fields) {
                db_perform(
                    'app_comments_history',
                    [
                        'comments_id' => $comments_id,
                        'fields_id' => $fields['fields_id'],
                        'fields_value' => $fields['fields_value']
                    ]
                );
            }
        }
    }
}