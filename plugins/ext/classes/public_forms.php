<?php

class public_forms
{

    static function send_client_notification($entities_id, $prev_item_info, $is_comment = false)
    {
        $item_info = db_find('app_entity_' . $entities_id, $prev_item_info['id']);

        $forms_query = db_query(
            "select f.*, e.name as entities_name from app_ext_public_forms f, app_entities e where e.id=f.entities_id and e.id='" . $entities_id . "' and notify_field_change>0"
        );
        while ($forms = db_fetch_array($forms_query)) {
            $field_id = $forms['notify_field_change'];

            if (isset($item_info['field_' . $field_id]) and isset($prev_item_info['field_' . $field_id])) {
                if ($item_info['field_' . $field_id] != $prev_item_info['field_' . $field_id] or ($is_comment and $forms['check_page_comments'])) {
                    self::send_notifictaion($forms, $item_info, $field_id);
                }
            }
        }
    }

    static function prepare_email_content($public_form, $current_entity_id, $item_id, $email_type = 'new')
    {
        //prepare forumulas query
        $listing_sql_query_select = fieldtype_formula::prepare_query_select($current_entity_id, '');

        $item_query = db_query(
            "select e.* " . $listing_sql_query_select . " from app_entity_" . $current_entity_id . " e where id='" . $item_id . "'",
            false
        );
        $item = db_fetch_array($item_query);

        $html = '';
        $attachments = [];

        $count = 0;
        $tabs_query = db_fetch_all(
            'app_forms_tabs',
            "entities_id='" . db_input($current_entity_id) . "' order by  sort_order, name"
        );
        $count_tabs = db_num_rows($tabs_query);
        while ($tabs = db_fetch_array($tabs_query)) {
            if ($email_type == 'new') {
                $where_sql = (strlen(
                    $public_form['hidden_fields']
                ) ? " and f.id not in (" . $public_form['hidden_fields'] . ")" : '');
            } else {
                $where_sql = (strlen(
                    $public_form['check_page_fields']
                ) ? " and f.id in (" . $public_form['check_page_fields'] . ")" : ' and f.id=0');
            }

            $fields_query = db_query(
                "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_type_list_excluded_in_form(
                ) . ") {$where_sql} and f.entities_id='" . db_input(
                    $current_entity_id
                ) . "' and f.forms_tabs_id=t.id and f.forms_tabs_id='" . db_input(
                    $tabs['id']
                ) . "' order by t.sort_order, t.name, f.sort_order, f.name"
            );
            while ($field = db_fetch_array($fields_query)) {
                //prepare field value
                $value = items::prepare_field_value_by_type($field, $item);

                $output_options = [
                    'class' => $field['type'],
                    'value' => $value,
                    'field' => $field,
                    'item' => $item,
                    'is_export' => true,
                    'is_print' => true,
                    'is_public_form' => $public_form['id'],
                    'display_user_photo' => false,
                    'path' => $current_entity_id
                ];

                $cfg = new fields_types_cfg($field['configuration']);

                if (($cfg->get('hide_field_if_empty') == 1 and strlen($value) == 0) or (in_array(
                            $field['type'],
                            ['fieldtype_created_by', 'fieldtype_input_date', 'fieldtype_input_datetime']
                        ) and $value == 0)) {
                    continue;
                }

                $field_name = fields_types::get_option($field['type'], 'name', $field['name']);

                if ($field['type'] == 'fieldtype_section') {
                    $html .= '
			            <tr>
			              <th align="left" colspan="2" style="padding-top: 10px;">' . $field_name . '</th>
			            </tr>';
                } else {
                    $html .= '
			            <tr>
			              <th align="left"  valign="top">' . $field_name . '</th>
			              <td  valign="top">' . fields_types::output($output_options) . '</td>
			            </tr>
			          ';
                }

                //prepare attachments
                if (in_array($field['type'], ['fieldtype_attachments', 'fieldtype_input_file']) and strlen($value)) {
                    foreach (explode(',', $value) as $filename) {
                        $file = attachments::parse_filename($filename);

                        $attachments[$file['file_path']] = $file['name'];
                    }
                }
            }
            $count++;
        }

        if (strlen($html)) {
            $html = '
				      <table>
				      		' . $html . '
				      </table>';
        }

        return ['html' => $html, 'attachments' => $attachments, 'item' => $item];
    }

    static function prepare_comments($public_form, $entities_id, $item_info)
    {
        $html = '';
        $attachments_array = [];

        $entity_cfg = new entities_cfg($entities_id);

        $listing_sql = "select * from app_comments where entities_id='" . db_input(
                $entities_id
            ) . "' and items_id='" . db_input($item_info['id']) . "' order by date_added ";
        $items_query = db_query($listing_sql);
        while ($item = db_fetch_array($items_query)) {
            $html_fields = '';
            $comments_fields_query = db_query(
                "select f.*,ch.fields_value from app_comments_history ch, app_fields f where comments_id='" . db_input(
                    $item['id']
                ) . "' and f.id=ch.fields_id order by ch.id"
            );
            while ($field = db_fetch_array($comments_fields_query)) {
                if (!in_array($field['id'], explode(',', $public_form['check_page_comments_fields']))) {
                    continue;
                }

                $output_options = [
                    'class' => $field['type'],
                    'value' => $field['fields_value'],
                    'field' => $field,
                    'path' => $entities_id,
                    'is_public_form' => $public_form['id'],
                ];

                $html_fields .= '
        <tr><th>&bull;&nbsp;' . $field['name'] . ':&nbsp;</th><td>' . fields_types::output($output_options) . '</td></tr>
    		';
            }

            if (strlen($html_fields) > 0) {
                $html_fields = '<table>' . $html_fields . '</table>';
            }

            if ($entity_cfg->get('use_editor_in_comments') != 1) {
                $item['description'] = nl2br($item['description']);
            }

            //prepare comment attachments
            if (strlen($item['attachments'])) {
                foreach (explode(',', $item['attachments']) as $filename) {
                    $file = attachments::parse_filename($filename);

                    $attachments_array[$file['file_path']] = $file['name'];
                }
            }

            $output_options = [
                'class' => 'fieldtype_attachments',
                'value' => $item['attachments'],
                'path' => $entities_id,
                'is_public_form' => $public_form['id'],
                'field' => ['entities_id' => $entities_id, 'id' => $item['id'] . '-comment', 'configuration' => ''],
                'item' => ['id' => $item_info['id']]
            ];

            $attachments = fields_types::output($output_options);

            if (strlen($attachments)) {
                $attachments = '<br>' . $attachments;
            }

            if (strlen($item['description']) or strlen($html_fields) or strlen($attachments)) {
                $html .= '
					<tr>
						<td valign="top"><nobr>' . format_date_time($item['date_added']) . '</nobr></td>
						<td valign="top">' . $item['description'] . '' . $html_fields . $attachments . '</td>
					</tr>
					';
            }
        }

        if (strlen($html)) {
            $html = '
					<h4>' . (strlen(
                    $public_form['check_page_comments_heading']
                ) ? $public_form['check_page_comments_heading'] : TEXT_COMMENTS) . '</h4>
					<table class="table table-striped table-bordered table-hover" >
						' . $html . '
					</table>';
        }

        return ['html' => $html, 'attachments' => $attachments_array];
    }

    static function send_notifictaion($public_form, $item, $choices_id)
    {
        if (strlen($public_form['customer_name']) and strlen($public_form['customer_email'])) {
            $customer_email = $item['field_' . $public_form['customer_email']];

            $customer_name = [];

            foreach (explode(',', $public_form['customer_name']) as $field_id) {
                $customer_name[] = $item['field_' . $field_id];
            }

            $customer_name = implode(' ', $customer_name);

            $email_content = self::prepare_email_content(
                $public_form,
                $public_form['entities_id'],
                $item['id'],
                'notify'
            );
            $html = $email_content['html'];
            $item_info = $email_content['item'];
            $attachments = $email_content['attachments'];

            $email_content = self::prepare_comments($public_form, $public_form['entities_id'], $item_info);
            $html .= '<br>' . $email_content['html'];
            $attachments = array_merge($attachments, $email_content['attachments']);

            //send email if valid
            if (app_validate_email($customer_email)) {
                $breadcrumb = items::get_breadcrumb_by_item_id($public_form['entities_id'], $item['id']);
                $item_name = $breadcrumb['text'];


                $output_options = ['item' => $item];
                $output_options['field']['configuration'] = '';
                $output_options['field']['entities_id'] = $public_form['entities_id'];
                $output_options['path'] = $public_form['entities_id'] . '-' . $item['id'];

                $fieldtype_text_pattern = new fieldtype_text_pattern();

                //subject
                $notify_message_title = '';

                if (strlen($public_form['notify_message_title'])) {
                    $output_options['custom_pattern'] = $public_form['notify_message_title'];
                    $notify_message_title = $fieldtype_text_pattern->output($output_options);
                }

                $subject = (strlen(
                    $notify_message_title
                ) ? $notify_message_title : TEXT_DEFAULT_EMAIL_SUBJECT_UPDATED_ITEM . ' ' . $item_name);

                //prepare body
                $notify_message_body = '';
                if (strlen($public_form['notify_message_body'])) {
                    $output_options['custom_pattern'] = nl2br($public_form['notify_message_body']);
                    $notify_message_body = $fieldtype_text_pattern->output($output_options);
                }

                $body = (strlen($notify_message_body) ? $notify_message_body . '<br>' . $html : $html);

                $body = users::use_email_pattern('single_column', ['email_single_column' => $body]);

                $options = [
                    'to' => $customer_email,
                    'to_name' => $customer_name,
                    'subject' => $subject,
                    'body' => $body,
                    'attachments' => $attachments,
                    'force_send_from' => true,
                ];

                //Set form address. If set form admin then use it
                if (strlen($public_form['admin_name']) and strlen($public_form['admin_email'])) {
                    $options['from'] = $public_form['admin_email'];
                    $options['from_name'] = $public_form['admin_name'];
                } else {
                    $options['from'] = CFG_EMAIL_ADDRESS_FROM;
                    $options['from_name'] = CFG_EMAIL_NAME_FROM;
                }


                users::send_email($options);

                //echo '<pre>';
                //print_r($options);
                //exit();
            }
        }
    }
}

